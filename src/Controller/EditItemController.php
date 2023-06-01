<?php

namespace App\Controller;

use App\Form\AddItemType;
use App\Service\ItemService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Translation\Exception\NotFoundResourceException;


#[IsGranted('ROLE_USER')]
class EditItemController extends AbstractController
{
    #[Route('/edit/item/{id}', name: 'app_edit_item')]
    public function index(ItemService $itemService,Request $request, int $id): Response
    {
        $editForm = $this->createForm(AddItemType::class);
        $editForm->handleRequest($request);

        try {
            $item = $itemService->findProductById($id);
            $itemService->checkItemOwner($item);
            if($editForm->isSubmitted() && $editForm->isValid()){
                $newData = $itemService->getData($editForm);
                $itemService->editItem($item,$newData);

                $this->addFlash('success','Zaaktualizowano dane ogłoszenia');
                return $this->redirectToRoute('app_index');
            }
        }catch (UnsupportedUserException){
            $this->addFlash('error','Nie jesteś właścicielem ogłoszenia');
            return $this->redirectToRoute('app_index');
        }catch (NotFoundResourceException){
            $this->addFlash('error','Nie znaleziono ogłoszenia o takim id');
            return $this->redirectToRoute('app_index');
        }

        return $this->render('edit_item/index.html.twig', [
            'editForm' => $editForm->createView(),
            'item' => $item
        ]);
    }
}
