<?php

namespace App\Controller;

use App\Entity\Expense;
use App\Form\ExpenseFormType;
use App\Repository\ExpenseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExpenseController extends AbstractController
{
    #[Route('/expense', name: 'app_expense')]
    public function index(): Response
    {
        $user = $this->getUser();
        $expenses = $user->getExpenses();

        return $this->render('expense/index.html.twig', [
            'expenses' => $expenses,
        ]);
    }
    #[Route('/expense/add', name: 'app_expense_add')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $expense = new Expense();
        $form = $this->createForm(ExpenseFormType::class, $expense);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $expense = $form->getData();
            $expense->setUser($this->getUser());
            $em->persist($expense);
            $em->flush();
            return $this->redirectToRoute('app_expense');
        }

        return $this->render('expense/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/expense/{id}', name: 'app_expense_id')]
    public function show(ExpenseRepository $er, Request $request): Response
    {
        $expenseId = $request->get('id');
        $expense = $er->find($expenseId);

        return $this->render('expense/info.html.twig', [
            'expense' => $expense,
        ]);
    }
    #[Route('/expense/{id}/delete', name: 'app_expense_delete')]
    public function delete(ExpenseRepository $er, Request $request, EntityManagerInterface $em): Response
    {
        $expenseId = $request->get('id');
        $expense = $er->find($expenseId);

        $em->remove($expense);
        $em->flush();

        return $this->redirectToRoute('app_expense');
    }

    #[Route('/expense/{id}/edit', name: 'app_expense_edit')]
    public function editExpense(ExpenseRepository $er, Request $request, EntityManagerInterface $em): Response
    {
        $expenseId = $request->get('id');
        $expense = $er->find($expenseId);
        $form = $this->createForm(ExpenseFormType::class, $expense);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $expense = $form->getData();
            $expense->setUser($this->getUser());
            $em->persist($expense);
            $em->flush();
            return $this->redirectToRoute('app_expense');
        }

        return $this->render('expense/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
