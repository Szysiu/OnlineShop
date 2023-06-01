<?php

namespace App\Controller;

use App\Service\TransactionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class AdminPanelController extends AbstractController
{
    #[Route('/admin/panel', name: 'app_admin_panel')]
    public function index(TransactionService $transactionService): Response
    {

        try {
            $transactions = $transactionService->getTransactions();
        }catch (AccessDeniedHttpException) {
            $this->addFlash('error','Nie posiadasz wystarczających uprawnień');
            return $this->redirectToRoute('app_index');
        }



        return $this->render('admin_panel/index.html.twig', [
            'transactions' => $transactions
        ]);
    }
}
