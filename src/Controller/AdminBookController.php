<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Event\DocumentPrintEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[Route('admin/books')]
class AdminBookController extends AbstractController
{

    #[Route('/', name: 'book_form_index', methods: ['GET'])]
    public function index(BookRepository $bookRepository): Response
    {
        return $this->render('back-office/book/index.html.twig', [
            'books' => $bookRepository->findAll(),
        ]);
    }

    #[IsGranted('ROLE_LIBRARIAN')]
    #[Route('/new', name: 'book_form_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($book);
            $entityManager->flush();

            return $this->redirectToRoute('book_form_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back-office/book/new.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'book_form_show', methods: ['GET'])]
    public function show(Book $book, EventDispatcherInterface $dispatcher): Response
    {

        // Fire event
        $event = new DocumentPrintEvent($book);
        $dispatcher->dispatch($event, DocumentPrintEvent::NAME);


        return $this->render('back-office/book/show.html.twig', [
            'book' => $book,
        ]);
    }

    #[Route('/{id}/edit', name: 'book_form_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Book $book): Response
    {
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('book_form_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back-office/book/edit.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'book_form_delete', methods: ['POST'])]
    public function delete(Request $request, Book $book): Response
    {
        if ($this->isCsrfTokenValid('delete' . $book->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($book);
            $entityManager->flush();
        }

        return $this->redirectToRoute('book_form_index', [], Response::HTTP_SEE_OTHER);
    }
}
