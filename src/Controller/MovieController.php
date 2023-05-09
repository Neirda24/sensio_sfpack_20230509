<?php

namespace App\Controller;

use App\Model\MovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MovieController extends AbstractController
{
    #[Route('/movies', name: 'app_movies_list', methods: ['GET'])]
    public function list(): Response
    {
        return $this->render('movie/list.html.twig', [
            'movies' => MovieRepository::listAll(),
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
    public function details(string $movieSlug): Response
    {
        return $this->render('movie/details.html.twig', [
            'movie' => MovieRepository::getBySlug($movieSlug)
        ]);
    }
}
