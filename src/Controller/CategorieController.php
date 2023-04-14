<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Produit;
use App\Service\AppHelpers;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

class CategorieController extends AbstractController
{
    private $app;
    private $db;
    private $userInfo;

    public function __construct(Security $security, ManagerRegistry $doctrine,  AppHelpers $app)
    {
        $this->app = $app;
        $this->db = $doctrine->getManager();
        $this->userInfo = $app->getUser();
    }

    public function index(?string $cat, ?string $gender): Response
    {
        // récupération des catégories dans la BDD
        $categories = $this->db->getRepository(Categorie::class)->findAll();

        // élaboration des critères de recherche
        $critere = [];
        $hasFilter = false;

        // si le filtre est présent dans l'url mais qu'il
        // n'y a pas de catégorie présente, alors on 
        // redirige sur la page de choix de categorie
        if (null !== $gender && null === $cat) {
            return $this->redirectToRoute('app_category');
        }

        // sinon on elabore les critères pour lancer
        // la requete afin de récupérer les produits
        // correspondants:

        // Si la catégorie est spécifiée il nous faut son id
        // comme critère pour la requete
        // utilisons le tableau de catégories récupéré plus haut.
        if (null !== $cat) {
            $found = false;
            foreach ($categories as $ctg) {
                if ($ctg->getNom() === $cat) {
                    $critere = array_merge($critere, ['categorie' => $ctg->getId()]);
                    $found = true;
                }
            }
            if (!$found) {
                return $this->redirectToRoute('app_category');
            }
        }

        // si le gender est présent dans l'url,
        // faisons correspondre homme à m 
        // et femme à f
        // pour la requete et inserons ce nouveau
        // critere dans le tableau des critères pour la
        // requete
        if (null !== $gender) {
            $g = '';
            $hasFilter = true;
            switch ($gender) {
                case 'homme':
                    $g = 'm';
                    break;
                default:
                    $g = 'f';
            }
            $critere = array_merge($critere, ['sexe' => $g]);
        }

        // lancement de la requete:
        $products = $this->db->getRepository(Produit::class)->findBy($critere, ['prix' => 'ASC'], null, null);

        // dump($critere);
        // exit();

        // dump($products);
        // exit();

        // Render:
        return $this->render('categorie/index.html.twig', [
            'bodyId' => $this->app->getBodyId('CATEGORY_PAGE'),
            'userInfo' => $this->userInfo,
            'cat' => $cat,
            'gender' => $gender,
            'categories' => $categories,
            'hasFilters' => $hasFilter,
            'products' => $products,
        ]);
    }
}
