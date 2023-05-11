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
        $invoices = $user->getInvoices();
        $expenses = $user->getExpenses();
        $estimates = $user->getEstimates();
  
        if (!$clients) {
          $clients = [];
        }
        if (!$user) {
          return $this->redirectToRoute('app_login');
        }

        $totalInvoices = 0;
        foreach ($invoices as $invoice) {
            $totalInvoices += $invoice->getTotalAmount();
        }
        $totalExpenses = 0;
        foreach ($expenses as $expense) {
            $totalExpenses += $expense->getAmount();
        }
        $totalEstimates = 0;
        foreach ($estimates as $estimate) {
            $totalEstimates += $estimate->getAmount();
        }

        return $this->render('account/index.html.twig', [
            'controller_name' => 'AccountController',
            'clients' => $clients,
            'invoices' => $invoices,
            'expenses' => $expenses,
            'estimates' => $estimates,
            'totalExpense' => $totalExpenses,
            'totalInvoices' => $totalInvoices,
            'totalEstimates' => $totalEstimates
        ]);
      }
}
