<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    private $user;

    public function __construct()
    {
        $this->denyAccessUnlessGranted(('ROLE_ADMIN'));
        $this->user = $this->getUSer();
    }

    /**
     * Méhode déclenchée par l'url: '/admin'
     * Route servie: app_admin
     * Appelle la vue: 'admin/dashboard.html.twig'
     *
     * Fonction:
     * Affiche la page dashboard du back office de l'espace administrateur
     *
     * @return Response
     */

    public function dashboard(): Response

    {
        return $this->render('admin/dashboard.html.twig', []);
    }


    /**
     * Méhode déclenchée par l'url: '/admin/members'
     * Route servie: app_admin
     * Appelle la vue: 'admin/member-management.html.twig'
     *
     * Fonction:
     * Affiche la page gestion des membres
     * du back office de l'espace administrateur
     *
     * @return Response
     */

    public function memberManagement(): Response

    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        return $this->render('admin/member-management.html.twig', []);
    }
}
