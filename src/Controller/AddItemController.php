<?php

namespace App\Controller;

use App\Form\AddItemType;
use App\Service\ItemService;
use PHPUnit\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

#[IsGranted('ROLE_USER')]
class AddItemController extends AbstractController
{
    private ItemService $itemService;

    public function __construct(ItemService $itemService)
    {
        $this->itemService = $itemService;
    }


    #[Route('/add/item', name: 'app_add_item')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(AddItemType::class);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $data = $this->itemService->getData($form);
            $imgFileName = $data['image'];

            if ($imgFileName) {
                try {
                    $this->itemService->addItem($data);
                    $this->addFlash('success', 'Dodano nowe ogłoszenie');
                    return $this->redirectToRoute('app_index');
                } catch (Exception $exception) {
                    $this->addFlash('error', 'Nie udało się dodać ogłoszenia');
                    return $this->redirectToRoute('app_index');
                }
            }
        }

        return $this->render('add_item/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
