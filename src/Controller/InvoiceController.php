<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Form\InvoiceFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class InvoiceController extends AbstractController
{
    #[Route('/invoice', name: 'app_invoice')]
    public function index(): Response
    {
        $user = $this->getUser();
        $invoices = $user->getInvoices();

        return $this->render('invoice/index.html.twig', [
            'invoices' => $invoices
        ]);
    }

    #[Route('/invoice/add', name: 'app_invoice_add')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $invoice = new Invoice();

        $form = $this->createForm(InvoiceFormType::class, $invoice);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $invoice = $form->getData();
            $invoice->setUser($this->getUser());
            $invoice->setCreatedAt(new \DateTimeImmutable());
            $em->persist($invoice);
            $em->flush();
            // dd($invoice);
            return $this->redirectToRoute('app_invoice');
        }
        
        return $this->render('invoice/add.html.twig', [
            'controller_name' => 'InvoiceController',
            'form' => $form->createView()
        ]);
    }
}
