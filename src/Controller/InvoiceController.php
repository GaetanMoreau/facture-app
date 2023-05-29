<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Form\InvoiceFormType;
use App\Repository\InvoiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
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
    public function show(InvoiceRepository $ir, Request $request): Response
    {

        $invoiceId = $request->get('id');
        $invoice = $ir->find($invoiceId);

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
            return $this->redirectToRoute('app_invoice');
        }
        
        return $this->render('invoice/edit.html.twig', [
            'invoice' => $invoice,
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
    public function exportToPdfAction(InvoiceRepository $ir, $id)
    {
        $invoice = $ir->find($id);

        if (!$invoice) {
            throw $this->createNotFoundException('Invoice not found');
        }

        $html = $this->renderView('invoice/exportpdf.html.twig', [
            'invoice' => $invoice,
        ]);

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->render();
        $pdfContent = $dompdf->output();

        $response = new Response($pdfContent);
        $response->headers->set('Content-Type', 'application/pdf');
        $createdAt = $invoice->getCreatedAt();
        $formattedCreatedAt = $createdAt->format('Y-m-d');

        $filename = sprintf('facture-numero-%s-%s.pdf', $invoice->getId(), $formattedCreatedAt);
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }
}
