<?php

namespace App\Controller;

use App\Form\SearchFormType;
use App\Service\ItemService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use function PHPUnit\Framework\isEmpty;

class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(ItemService $itemService, Request $request): Response
    {
        $searchForm = $this->createForm(SearchFormType::class);
        $searchForm->handleRequest($request);
        $items = $itemService->getUnsoldItems();

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $params = $searchForm->get('params')->getData();
            $phrase = $searchForm->get('phrase')->getData();

            try {
                $items = $itemService->searchItems($params, $phrase);
                return $this->render('search/search.html.twig', [
                    'foundItems' => $items
                ]);
            } catch (NotFoundResourceException) {
                $this->addFlash('warning', 'Brak wyników wyszukiwania...');
                return $this->redirectToRoute('app_index');
            }
        }

        return $this->render('index/index.html.twig', [
            'searchForm' => $searchForm->createView(),
            'items' => $items,

        ]);
    }

}
