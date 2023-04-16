<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class MemberController extends AbstractController
{
    /**

     * Méhode déclenchée par l'url: '/member'
     * Route servie: app_member
     * Appelle la vue: 'member/dashboard.html.twig'
     *
     * Fonction:
     * Affiche la page dashboard du back office de l'espace membre
     *
     * @return Response
     */

    public function dashboard(): Response

    {
        return $this->render('member/dashboard.html.twig', []);
    }
}
