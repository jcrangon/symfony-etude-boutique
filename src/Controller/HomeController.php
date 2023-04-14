<?php

namespace App\Controller;

use App\Entity\Carousel;
use App\Entity\Categorie;
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
        // on vérifie que la Bdd est prête.
        $this->app->installBdd();

        // récupération des images de carousel dans la BDD
        $carouselImages = $this->db->getRepository(Carousel::class)->findBy(["emplacement" => "home"], ["position" => "ASC"]);

        // marquage de la première image
        // qui devra être notée comme 'active'
        if (isset($carouselImages[0])) {
            $carouselImages[0]->etat = 'active';
        }

        // récupération des catégories dans la BDD
        $categories = $this->db->getRepository(Categorie::class)->findAll();

        // render
        return $this->render('home/index.html.twig', [
            'bodyId' => $this->bodyId,
            'carouselImg' => $carouselImages,
            'carouselId' => 'homeCarousel',
            'userInfo' => $this->userInfo,
            'categories' => $categories,

        ]);
    }
}
