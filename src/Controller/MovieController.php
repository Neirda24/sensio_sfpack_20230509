<?php

namespace App\Controller;

use App\Entity\Movie as MovieEntity;
use App\Entity\User;
use App\Event\Security\UnderragedAccess;
use App\Form\MovieType;
use App\Model\Movie;
use App\Omdb\Api\OmdbApiClientInterface;
use App\Repository\MovieRepository;
use App\Security\Voter\MovieVoter;
use Psr\Clock\ClockInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MovieController extends AbstractController
{
    public function __construct(
        private readonly OmdbApiClientInterface $omdbApiClient,
        private readonly ClockInterface $clock,
        private readonly MovieRepository $movieRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    #[Route('/movies', name: 'app_movies_list', methods: ['GET'])]
    public function list(): Response
    {
        return $this->render('movie/list.html.twig', [
            'movies' => Movie::fromEntities($this->movieRepository->list()),
        ]);
    }

    #[Route(
        '/movies/{movieSlug}',
        name: 'app_movies_details',
        requirements: [
            'movieSlug' => MovieEntity::SLUG_PATTERN,
        ],
        methods: ['GET']
    )]
    public function details(string $movieSlug): Response
    {
        $movie = Movie::fromEntity($this->movieRepository->getBySlug($movieSlug));

        if (!$this->isGranted(MovieVoter::VIEW_DETAILS, $movie)) {
            /** @var User $user */
            $user = $this->getUser();

            if (!$user->isOlderThanOrEqual($movie->rated->minAgeRequired(), $this->clock->now())) {
                $this->eventDispatcher->dispatch(
                    new UnderragedAccess(
                        $user,
                        $movie,
                    )
                );
            }

            throw $this->createAccessDeniedException();
        }

        return $this->render('movie/details.html.twig', [
            'movie' => $movie,
        ]);
    }

    #[Route(
        '/movies/{imdbId}',
        name: 'app_movies_details_omdb',
        requirements: [
            'imdbId' => 'tt\d+',
        ],
        methods: ['GET']
    )]
    public function detailsFromOmdb(string $imdbId): Response
    {
        $movie = Movie::fromOmdb($this->omdbApiClient->getById($imdbId));

        if (!$this->isGranted(MovieVoter::VIEW_DETAILS, $movie)) {
            /** @var User $user */
            $user = $this->getUser();

            if (!$user->isOlderThanOrEqual($movie->rated->minAgeRequired(), $this->clock->now())) {
                $this->eventDispatcher->dispatch(
                    new UnderragedAccess(
                        $user,
                        $movie,
                    )
                );
            }

            throw $this->createAccessDeniedException();
        }

        return $this->render('movie/details.html.twig', [
            'movie' => $movie,
        ]);
    }

    #[Route(
        '/admin/movies/new',
        name: 'app_movies_new',
        methods: ['GET', 'POST']
    )]
    #[Route(
        '/admin/movies/{movieSlug}/edit',
        name: 'app_movies_edit',
        requirements: [
            'movieSlug' => MovieEntity::SLUG_PATTERN,
        ],
        methods: ['GET', 'POST']
    )]
    public function newOrEdit(Request $request, string|null $movieSlug = null): Response
    {
        $movie = new MovieEntity();

        if (null !== $movieSlug) {
            $movie = $this->movieRepository->getBySlug($movieSlug);
        }

        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->movieRepository->save($movie, true);

            return $this->redirectToRoute('app_movies_details', ['movieSlug' => $movie->getSlug()]);
        }

        return $this->render('movie/new_or_edit.html.twig', [
            'new_or_edit_movie_form' => $form,
            'is_editing' => null !== $movieSlug,
        ]);
    }
}
