<?php

namespace App\Controller;

use App\Form\ProfileFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccountController extends AbstractController
{
    #[Route('/', name: 'app_account')]
    public function index(): Response
    {
        $user = $this->getUser();
        $clients = $user->getClients();
        $invoices = $user->getInvoices()    ;

        return $this->render('account/index.html.twig', [
            'controller_name' => 'AccountController',
            'clients' => $clients,
            'invoices' => $invoices
        ]);
    }
}
