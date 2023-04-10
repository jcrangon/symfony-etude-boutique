<?php

namespace App\Service;

use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Carousel;
use stdClass;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Security\Core\Security;

class AppHelpers
{
  private $params;
  private $doctrine;
  private $security;
  private $db;

  public function __construct(ContainerBagInterface $params, ManagerRegistry $doctrine, Security $security)
  {
    $this->params = $params;
    $this->doctrine = $doctrine;
    $this->db = $doctrine->getManager();
    $this->security = $security;
  }

  public function getUser()
  {
    $user = $this->security->getUser();
    if ($user) {
      $isLoggedIn = true;
    } else {
      $isLoggedIn = false;
    }
    if ($this->security->isGranted('ROLE_ADMIN')) {
      $isAdmin = true;
    } else {
      $isAdmin = false;
    }
    $userObj = new stdClass();
    $userObj->user = $user;
    $userObj->isAdmin = $isAdmin;
    $userObj->isLoggedIn = $isLoggedIn;
    return $userObj;
  }

  public function getBodyId(string $page): string
  {
    return $this->params->get($page);
  }


  public function installCarousel($emplacement): void
  {
    $carouselList = $this->getCarouselList();

    foreach ($carouselList as $singleImg) {
      $img = new Carousel();
      $img->setEmplacement($emplacement);
      $img->setPosition($singleImg['position']);
      $img->setImage($singleImg['image']);
      $this->db->persist($img);
    }
    $this->db->flush();
  }

  private function getCarouselList(): array
  {
    return [
      [
        "image" => "2.jpg",
        "position" => 2,
        "emplacement" => "home",
      ],
      [
        "image" => "1.jpg",
        "position" => 1,
        "emplacement" => "home",
      ],
      [
        "image" => "3.jpg",
        "position" => 3,
        "emplacement" => "home",
      ],
    ];
  }
}
