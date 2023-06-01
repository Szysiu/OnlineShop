<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;



class UserService
{
    private EntityManagerInterface $entityManager;
    private Security $security;


    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->security=$security;
        $this->entityManager=$entityManager;
    }

    public function addMoney(int $ammount):void
    {
        $user = $this->security->getUser();

        if($user instanceof User) {
            $user->addMoney($ammount);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

    }
}