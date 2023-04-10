<?php

namespace App\Controller;

use App\Entity\Carousel;
use App\Service\AppHelpers;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

class HomeController extends AbstractController
{
    private string $bodyId;
    private $app;
    private $db;
    private $userInfo;

    public function __construct(Security $security, ManagerRegistry $doctrine,  AppHelpers $app)
    {
        $this->app = $app;
        $this->bodyId = $app->getBodyId('HOME_PAGE');
        $this->db = $doctrine->getManager();
        $this->userInfo = $app->getUser();
    }

    public function index(): Response
    {
        $carouselImages = $this->db->getRepository(Carousel::class)->findBy(["emplacement" => "home"], ["position" => "ASC"]);

        if (isset($carouselImages[0])) {
            $carouselImages[0]->etat = 'active';
        }

        if (!count($carouselImages)) {
            $this->app->installCarousel('home');
        }

        return $this->render('home/index.html.twig', [
            'bodyId' => $this->bodyId,
            'carouselImg' => $carouselImages,
            'carouselId' => 'homeCarousel',
            'userInfo' => $this->userInfo,

        ]);
    }
}
