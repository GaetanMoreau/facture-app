<?php

namespace App\Controller;

use App\Entity\Estimate;
use App\Form\EstimateFormType;
use App\Repository\EstimateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EstimateController extends AbstractController
{
    #[Route('/estimate', name: 'app_estimate')]
    public function index(): Response
    {
        $user = $this->getUser();
        $estimates = $user->getEstimates();

        return $this->render('estimate/index.html.twig', [
            'estimates' => $estimates
        ]);
    }

    #[Route('/estimate/add', name: 'app_estimate_add')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $estimate = new Estimate();

        $form = $this->createForm(EstimateFormType::class, $estimate);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $estimate = $form->getData();
            $estimate->setUser($this->getUser());
            $estimate->setCreatedAt(new \DateTimeImmutable());
            $em->persist($estimate);
            $em->flush();
            return $this->redirectToRoute('app_estimate');
        }

        return $this->render('estimate/add.html.twig', [
            'controller_name' => 'EstimateController',
            'form' => $form->createView()
        ]);
    }

    #[Route('/estimate/{id}', name: 'app_estimate_show')]
    public function show(EstimateRepository $er, Request $request): Response
    {
        $estimateId = $request->get('id');
        $estimate = $er->find($estimateId);

        return $this->render('estimate/show.html.twig', [
            'estimate' => $estimate,
        ]);
    }

    #[Route('/estimate/{id}/edit', name: 'app_estimate_edit')]
    public function edit(Estimate $invoice, Request $request, EntityManagerInterface $em, EstimateRepository $er): Response
    {
        $form = $this->createForm(EstimateFormType::class, $invoice);

        $estimateId = $request->get('id');
        $estimate = $er->find($estimateId);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $estimate = $form->getData();
            $estimate->setUser($this->getUser());
            $estimate->setCreatedAt(new \DateTimeImmutable());
            $em->persist($estimate);
            $em->flush();
            return $this->redirectToRoute('app_estimate');
        }
        
        return $this->render('estimate/edit.html.twig', [
            'controller_name' => 'EstimateController',
            'form' => $form->createView(),
            'estimate' => $estimate,
        ]);
    }

    #[Route('/estimate/{id}/delete', name: 'app_estimate_delete')]
    public function delete(EstimateRepository $er, Request $request, EntityManagerInterface $em): Response
    {
        $estimateId = $request->get('id');
        $estimate = $er->find($estimateId);

        $em->remove($estimate);
        $em->flush();

        return $this->redirectToRoute('app_estimate');
    }
    public function exportToPdfAction(EstimateRepository $er, $id)
    {
        $estimate = $er->find($id);

        if (!$estimate) {
            throw $this->createNotFoundException('Estimate not found');
        }

        $html = $this->renderView('estimate/exportpdf.html.twig', [
            'estimate' => $estimate,
        ]);

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->render();
        $pdfContent = $dompdf->output();

        $response = new Response($pdfContent);
        $response->headers->set('Content-Type', 'application/pdf');
        $createdAt = $estimate->getCreatedAt();
        $formattedCreatedAt = $createdAt->format('Y-m-d');

        $filename = sprintf('devis-numero-%s-%s.pdf', $estimate->getId(), $formattedCreatedAt);
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }
}
