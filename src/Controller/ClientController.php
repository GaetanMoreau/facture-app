<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Client;
use App\Form\ClientFormType;
use App\Repository\ClientRepository;
use Dompdf\Dompdf;


class ClientController extends AbstractController
{
    #[Route('/client', name: 'app_client')]
    public function index(ClientRepository $cr): Response
    {
        $user = $this->getUser();
        $clients = $user->getClients();

        return $this->render('client/index.html.twig', [
            'clients' => $clients,
        ]);
    }

    #[Route('/client/add', name: 'app_client_add')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $client = new Client();
        $client->setUser($this->getUser()); // Set the user first.

        $form = $this->createForm(ClientFormType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($client);
            $em->flush();

            $this->addFlash('success', 'Client added successfully.');

            return $this->redirectToRoute('app_client');
        }

        return $this->render('client/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/client/{id}', name: 'app_client_id')]
    public function show(ClientRepository $cr, Request $request): Response
    {
        $clientId = $request->get('id');
        $client = $cr->find($clientId);

        return $this->render('client/info.html.twig', [
            'client' => $client,
        ]);
    }

    #[Route('/client/{id}/delete', name: 'app_client_delete')]
    public function delete(ClientRepository $cr, Request $request, EntityManagerInterface $em): Response
    {
        $clientId = $request->get('id');
        $client = $cr->find($clientId);

        $em->remove($client);
        $em->flush();

        return $this->redirectToRoute('app_client');
    }

    #[Route('/client/{id}/edit', name: 'app_client_edit')]
    public function editClient(ClientRepository $cr, Request $request, EntityManagerInterface $em): Response
    {
        $clientId = $request->get('id');
        $client = $cr->find($clientId);
        $form = $this->createForm(ClientFormType::class, $client);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $client = $form->getData();
            $client->setUser($this->getUser());
            $em->persist($client);
            $em->flush();
            return $this->redirectToRoute('app_client');
        }

        return $this->render('client/edit.html.twig', [
            'form' => $form->createView(),
            'client' => $client
        ]);
    }

    public function exportToPdfAction(ClientRepository $cr, $id)
    {
        $client = $cr->find($id);

        if (!$client) {
            throw $this->createNotFoundException('Client not found');
        }

        $html = $this->renderView('client/exportpdf.html.twig', [
            'client' => $client,
        ]);

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->render();
        $pdfContent = $dompdf->output();

        $response = new Response($pdfContent);
        $response->headers->set('Content-Type', 'application/pdf');
        $filename = sprintf('fiche-client-%s-%s.pdf', $client->getLastname(), $client->getFirstname());
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');


        return $response;
    }
}
