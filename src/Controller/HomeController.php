<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home.index')]
    public function index(): Response
    {
        // Render a Twig template
        return $this->render('index.html.twig', [
            'controller_name' => 'HomeController',
        ]);

        // Or return a simple response
        // return new Response('Welcome to the homepage');
    }
}
