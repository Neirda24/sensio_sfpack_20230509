<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloController extends AbstractController
{
    private const REQUIREMENT_NAME_REGEX = '\w+(-\w+)*';

    #[Route(
        '/hello/{name}',
        name: 'app_hello',
        requirements: [
            'name' => self::REQUIREMENT_NAME_REGEX,
        ],
        methods: ['GET']
    )]
    public function index(string $name = 'Adrien'): Response
    {
        return new Response(
            content: <<<"HTML"
            <body>Hello {$name} !</body>
            HTML
        );
    }
}
