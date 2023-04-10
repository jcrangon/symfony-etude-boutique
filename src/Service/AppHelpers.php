<?php

namespace App\Service;

use Doctrine\Persistence\ManagerRegistry;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Security\Core\Security;

class AppHelpers
{
  private $params;
  private $doctrine;
  private $security;

  public function __construct(ContainerBagInterface $params, ManagerRegistry $doctrine, Security $security)
  {
    $this->params = $params;
    $this->doctrine = $doctrine;
    $this->security = $security;
  }

  public function getUser()
  {
    return $this->security->getUser();
  }
}
