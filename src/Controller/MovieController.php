<?php

namespace App\Controller;

use App\Form\MovieType;
use App\Model\Movie;
use App\Repository\MovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MovieController extends AbstractController
{
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
            'movieSlug' => '\d{4}-\w+(-\w+)*',
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
    public function new(): Response
    {
        $form = $this->createForm(MovieType::class);

        return $this->render('movie/new.html.twig', [
            'new_movie_form' => $form,
        ]);
    }
}
