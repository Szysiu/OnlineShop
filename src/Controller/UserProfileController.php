<?php

namespace App\Controller;

use App\Service\ItemService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

#[IsGranted('ROLE_USER')]
class UserProfileController extends AbstractController
{
    #[Route('/user/profile', name: 'app_user_profile')]
    public function index(ItemService $itemService): Response
    {
        $userItems = $itemService->getUserItems($this->getUser());

        return $this->render('user_profile/index.html.twig',[
            'userItems' => $userItems
        ]);
    }
}
