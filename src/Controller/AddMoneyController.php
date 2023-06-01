<?php

namespace App\Controller;

use App\Form\AddMoneyType;
use App\Service\UserService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AddMoneyController extends AbstractController
{
    #[Route('/add/money', name: 'app_add_money')]
    public function index(Request $request,UserService $userService): Response
    {
        $addMoneyForm = $this->createForm(AddMoneyType::class);
        $addMoneyForm->handleRequest($request);

        if($addMoneyForm->isSubmitted() && $addMoneyForm->isValid()) {
            $amount = $addMoneyForm->get('amount')->getData();

            try {
                $userService->addMoney($amount);

                $this->addFlash('success','Pomyślnie dodano środki');
                return $this->redirectToRoute('app_user_profile');
            }catch (Exception){
                $this->addFlash('error','Wystąpił błąd');
                return $this->redirectToRoute('app_user_profile');
            }
        }

        return $this->render('add_money/index.html.twig', [
            'addMoneyForm' => $addMoneyForm->createView()
        ]);
    }
}
