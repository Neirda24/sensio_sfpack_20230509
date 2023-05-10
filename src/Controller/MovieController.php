<?php

namespace App\Controller;

use App\Entity\Movie as MovieEntity;
use App\Form\MovieType;
use App\Model\Movie;
use App\Repository\MovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MovieController extends AbstractController
{
    private const ROUTE_SLUG_REQUIREMENT = '\d{4}-\w+(-\w+)*';

    #[Route('/movies', name: 'app_movies_list', methods: ['GET'])]
    public function list(MovieRepository $movieRepository): Response
    {
        $form = $this->createForm(MovieType::class);

        return $this->render('movie/list.html.twig', [
            'movies' => Movie::fromEntities($movieRepository->list()),
            'new_movie_form' => $form,
        ]);
    }

    #[Route(
        '/movies/{movieSlug}',
        name: 'app_movies_details',
        requirements: [
            'movieSlug' => self::ROUTE_SLUG_REQUIREMENT,
        ],
        methods: ['GET']
    )]
    public function details(MovieRepository $movieRepository, string $movieSlug): Response
    {
        return $this->render('movie/details.html.twig', [
            'movie' => Movie::fromEntity($movieRepository->getBySlug($movieSlug)),
        ]);
    }

    #[Route(
        '/movies/new',
        name: 'app_movies_new',
        methods: ['GET']
    )]
    #[Route(
        '/movies/{movieSlug}/edit',
        name: 'app_movies_edit',
        requirements: [
            'movieSlug' => self::ROUTE_SLUG_REQUIREMENT,
        ],
        methods: ['GET']
    )]
    public function newOrEdit(MovieRepository $movieRepository, string|null $movieSlug = null): Response
    {
        $movie = new MovieEntity();

        if (null !== $movieSlug) {
            $movie = $movieRepository->getBySlug($movieSlug);
        }

        $form = $this->createForm(MovieType::class, $movie);

        return $this->render('movie/new_or_edit.html.twig', [
            'new_or_edit_movie_form' => $form,
            'is_editing' => null !== $movieSlug,
        ]);
    }
}
