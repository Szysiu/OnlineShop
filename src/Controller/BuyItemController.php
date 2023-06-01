<?php

namespace App\Controller;

use App\Exceptions\NotEnoughMoney;
use App\Service\ItemService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

#[IsGranted('ROLE_USER')]
class BuyItemController extends AbstractController
{
    #[Route('/buy/item/{id}', name: 'app_buy_item')]
    public function index(int $id,ItemService $itemService): RedirectResponse
    {
        try {
            $itemService->buyItem($id);
            $this->addFlash('success','Zakupiono przedmiot');
        }catch (UnsupportedUserException) {
            $this->addFlash('error','Nie możesz kupić własnego przedmiotu');
        }catch (NotFoundResourceException) {
            $this->addFlash('error','Nie zmaleziono ogłoszenia o takim id');
        }catch (NotEnoughMoney) {
            $this->addFlash('error','Nie masz wystarczającej ilości środków');
        } finally {
            return $this->redirectToRoute('app_index');
        }
    }
}
