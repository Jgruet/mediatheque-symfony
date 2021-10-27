<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Form\EmployeeType;
use App\Repository\EmployeeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/employee')]
class AdminEmployeeController extends AbstractController
{
    #[Route('/', name: 'admin_employee_index', methods: ['GET'])]
    public function index(EmployeeRepository $employeeRepository): Response
    {
        return $this->render('back-office/employee/index.html.twig', [
            'employees' => $employeeRepository->findAll(),
        ]);
    }

    /* #[Route('/new', name: 'admin_employee_new', methods: ['GET','POST'])]
    public function new(Request $request): Response
    {
        $employee = new Employee();
        $form = $this->createForm(EmployeeType::class, $employee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($employee);
            $entityManager->flush();

            return $this->redirectToRoute('admin_employee_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back-office/employee/new.html.twig', [
            'employee' => $employee,
            'form' => $form,
        ]);
    } */

    #[Route('/{id}', name: 'admin_employee_show', methods: ['GET'])]
    public function show(Employee $employee): Response
    {
        return $this->render('back-office/employee/show.html.twig', [
            'employee' => $employee,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_employee_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Employee $employee): Response
    {
        $form = $this->createForm(EmployeeType::class, $employee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_employee_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back-office/employee/edit.html.twig', [
            'employee' => $employee,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_employee_delete', methods: ['POST'])]
    public function delete(Request $request, Employee $employee): Response
    {
        if ($this->isCsrfTokenValid('delete' . $employee->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($employee);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_employee_index', [], Response::HTTP_SEE_OTHER);
    }
}
