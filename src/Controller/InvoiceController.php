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

    #[Route('/invoice/{id}', name: 'app_invoice_show')]
    public function show(Invoice $invoice): Response
    {
        $invoice = $this->getDoctrine()
            ->getRepository(Invoice::class)
            ->find($invoice);

        return $this->render('invoice/show.html.twig', [
            'invoice' => $invoice
        ]);
    }

    #[Route('/invoice/{id}/edit', name: 'app_invoice_edit')]
    public function edit(Invoice $invoice, Request $request, EntityManagerInterface $em): Response
    {
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
        
        return $this->render('invoice/edit.html.twig', [
            'controller_name' => 'InvoiceController',
            'form' => $form->createView()
        ]);
    }

    #[Route('/invoice/{id}/delete', name: 'app_invoice_delete')]
    public function delete(Invoice $invoice, EntityManagerInterface $em): Response
    {
        $em->remove($invoice);
        $em->flush();

        return $this->redirectToRoute('app_invoice');
    }
    
}
