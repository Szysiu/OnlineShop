<?php

namespace App\Controller;

use App\Service\ItemService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class ShowItemController extends AbstractController
{
    #[Route('/item/{id}', name: 'app_show_item')]
    public function index(int $id, ItemService $itemService): Response
    {
        try {
            $item = $itemService->findProductById($id);
        }catch (NotFoundResourceException){
            $this->addFlash('warning','Nie znaleziono ogłoszenia o takim id');
            return $this->redirectToRoute('app_index');
        }

        return $this->render('show_item/index.html.twig', [
            'item' => $item
        ]);
    }
}
