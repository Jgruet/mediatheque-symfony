<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Form\EmployeeRegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin', name: 'admin_')]
class AdminRegistrationController extends AbstractController
{

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface): Response
    {
        $employee = new Employee();
        $form = $this->createForm(EmployeeRegistrationFormType::class, $employee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $employee->setPassword(
                $userPasswordHasherInterface->hashPassword(
                    $employee,
                    $form->get('password')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($employee);
            $entityManager->flush();

            return $this->redirectToRoute('public-home');
        }

        return $this->render('back-office/registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
