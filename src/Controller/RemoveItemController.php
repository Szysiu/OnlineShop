<?php

namespace App\Controller;

use App\Service\ItemService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class RemoveItemController extends AbstractController
{
    #[Route('/remove/item/{id}', name: 'app_remove_item')]
    public function index(ItemService $itemService, int $id): Response
    {
        try {
            $itemService->removeItem($id);
            $this->addFlash('success','Usunięto ogłoszenie');
            return $this->redirectToRoute('app_user_profile');
        }catch (UnsupportedUserException){
            $this->addFlash('error','Nie jesteś właścicielem ogłoszenia');
            return $this->redirectToRoute('app_index');
        }
    }
}
