<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('admin/author')]
class AdminAuthorController extends AbstractController
{
    #[Route('/', name: 'author_form_index', methods: ['GET'])]
    public function index(AuthorRepository $authorRepository): Response
    {
        return $this->render('back-office/author/index.html.twig', [
            'authors' => $authorRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'author_form_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $author = new Author();

        // default data
        $author->setLastName('Toto');
        $author->setFirstName('Gentil');
        // end default data

        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($author);
            $entityManager->flush();

            return $this->redirectToRoute('author_form_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back-office/author/new.html.twig', [
            'author' => $author,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'author_form_show', methods: ['GET'])]
    public function show(Author $author): Response
    {
        return $this->render('back-office/author/show.html.twig', [
            'author' => $author,
        ]);
    }

    // param converter, ici capable de faire le lien entre id de la route et id de $author - magic -
    #[Route('/{id}/edit', name: 'author_form_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Author $author): Response
    {
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('author_form_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back-office/author/edit.html.twig', [
            'author' => $author,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'author_form_delete', methods: ['POST'])]
    public function delete(Request $request, Author $author): Response
    {
        if ($this->isCsrfTokenValid('delete' . $author->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($author);
            $entityManager->flush();
        }

        return $this->redirectToRoute('author_form_index', [], Response::HTTP_SEE_OTHER);
    }
}
