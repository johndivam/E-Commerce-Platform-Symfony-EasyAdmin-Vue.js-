<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FrontController extends AbstractController
{
    #[Route('/{any}', name: 'welcome', requirements: ['any' => '^(?!api|admin).+'], defaults: ['any' => null])]
    public function index(): Response
    {
        return $this->render('welcome.html.twig');
    }
}
