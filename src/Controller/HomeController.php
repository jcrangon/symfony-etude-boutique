<?php

namespace App\Controller;

use App\Service\AppHelpers;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController
{
    private $app;

    public function __construct(AppHelpers $app)
    {
        $this->app = $app;
    }

    public function index(): Response
    {
        // dump($this->app->getUser());
        // exit();
        return $this->render('home/index.html.twig', []);
    }
}
