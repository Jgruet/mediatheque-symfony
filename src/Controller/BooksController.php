<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Document;
use App\Entity\User;
use App\Repository\BookRepository;
use App\Repository\BorrowRepository;
use App\Repository\DocumentRepository;
use App\Repository\PenaltyRepository;
use App\Repository\UserRepository;
use App\Service\BorrowService;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/books', name: 'front-office-books-')]

class BooksController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(BookRepository $bookRepository): Response
    {
        return $this->render('front-office/books/index.html.twig', [
            'books' => $bookRepository->findAll(),
        ]);
    }

    #[Route('/finder/books', name: 'finder-books')]
    public function allBooksUsingFinder(): Response
    {

        $finder = new Finder();
        $finder->files()->name('books.json')->in("../public/json");

        foreach ($finder as $file) {
            $contents = json_decode($file->getContents());
        }

        return $this->render('front-office/books/finder-books.html.twig', [
            'controller_name' => 'PublicController',
            'allBooks' => $contents,
        ]);
    }


    #[Route('/finder/criterias', name: 'finder-books-by-style')]
    public function getBookByStyle(Request $request): Response
    {
        $styles = $request->query->has('styles') ? $request->query->get('styles') : null;
        $styles = explode('-', $styles);

        $finder = new Finder();

        $finder->files()->name('books.json')->in("../public/json");
        $book = [];
        foreach ($finder as $file) {
            $contents = json_decode($file->getContents(), true);
            foreach ($contents as $content) {
                foreach ($styles as $style) {
                    if (in_array($style, $content['styles'])) {
                        $book[] = $content;
                    }
                }
            }
        }

        $book = array_unique($book, SORT_REGULAR);

        return $this->render('front-office/books/single-book.html.twig', [
            'book' => $book,
        ]);
    }

    #[Route('/nb_page', name: 'book-by-nb-page')]
    public function getBookByNbPage(Request $request, BookRepository $bookRepository): Response
    {
        $nb_page = $request->query->has('min') ? $request->query->get('min') : null;
        if ($nb_page != null) {
            $bigBooks = $bookRepository->findBigBooks($nb_page);
            return $this->render('front-office/books/big-book.html.twig', [
                'nb_page' => $nb_page,
                'bigBooks' => $bigBooks,
            ]);
        }
    }

    #[Route('/id_range', name: 'book-by-id-range')]
    public function getBookByIdRange(Request $request, BookRepository $bookRepository): Response
    {
        $start = $request->query->has('min') ? $request->query->get('min') : null;
        $stop = $request->query->has('max') ? $request->query->get('max') : null;
        if ($start != null && $stop != null) {
            $titles = $bookRepository->findBooksTitleByRangeId($start, $stop);
            return $this->render('front-office/books/book-id-range.html.twig', [
                'titles' => $titles,
                'start' => $start,
                'stop' => $stop,
            ]);
        }
    }

    #[Route('/author_name_start_by', name: 'doc-author-name-start-by')]
    public function getDocByStart(Request $request, DocumentRepository $documentRepository): Response
    {
        $start = $request->query->has('s') ? $request->query->get('s') : null;
        if ($start != null) {
            $documents = $documentRepository->findDocumentsByAuthorFirstLetter($start);
            return $this->render('front-office/books/author-start-by.html.twig', [
                "toto" => $documents,
                "tata" => $start,
            ]);
        }
    }

    #[Route('/author_name_start_by_and_category', name: 'doc-author-name-start-by-and-cat')]
    public function getDocByStartAndCat(Request $request, DocumentRepository $documentRepository): Response
    {
        $start = $request->query->has('s') ? $request->query->get('s') : null;
        $cat = $request->query->has('c') ? $request->query->get('c') : null;

        if ($start != null) {
            $documents = $documentRepository->findDocumentsByAuthorFirstLetterAndCat($start, $cat);
            return $this->render('front-office/books/author-start-by-and-category.html.twig', [
                "toto" => $documents,
                "tata" => $start,
                "tutu" => $cat
            ]);
        }
    }

    #[Route('/cd-great-duration', name: 'cd-great-duration')]
    public function findCdByDuration(DocumentRepository $documentRepository): Response
    {
        $cds = $documentRepository->findCdByDuration();
        return $this->render('front-office/books/cd-great-duration.html.twig', [
            "cds" => $cds,

        ]);
    }

    #[Route('/{id}/{_format}', name: 'finder-book-by-id-and-format', requirements: ['id' => '^\d+$'], priority: 10)]
    public function getBookByIdAndFormat($id, Request $request): Response
    {

        $format = $request->getRequestFormat();

        $finder = new Finder();
        $finder->files()->name('books.json')->in("../public/json");
        $book = [];
        foreach ($finder as $file) {
            $contents = json_decode($file->getContents(), true);
            foreach ($contents as $content) {
                if ($content['id'] == $id) {
                    $book[] = $content;
                }
            }
        }

        switch ($format) {
            case 'json':
                return $this->json($book);
                break;
            case 'file':
                $book[0]['styles'] = implode(" - ", $book[0]['styles']);
                $book[0] = implode(" \n\r ", $book[0]);
                $responseFile = new Response($book[0]);
                $disposition = HeaderUtils::makeDisposition(
                    HeaderUtils::DISPOSITION_ATTACHMENT,
                    'book.txt'
                );
                $responseFile->headers->set('Content-Disposition', $disposition);
                return $responseFile;
                break;
            default:
                return $this->render('front-office/books/single-book.html.twig', [
                    'book' => $book,
                ]);
                break;
        }
    }

    #[Route('/finder/{id}', name: 'finder-book-by-id', requirements: ['id' => '^\d+$'], priority: 10)]
    public function getBookById($id): Response
    {
        $finder = new Finder();
        $finder->files()->name('books.json')->in("../public/json");
        $book = [];
        foreach ($finder as $file) {
            $contents = json_decode($file->getContents(), true);
            foreach ($contents as $content) {
                if ($content['id'] == $id) {
                    $book[] = $content;
                }
            }
        }
        return $this->render('front-office/books/single-book.html.twig', [
            'book' => $book,
        ]);
    }

    // appel de la single book
    #[Route('/{id}', name: 'public_book_form_show', methods: ['GET'])]
    public function show(Book $book,  BorrowRepository $borrowRepository, Security $security, PenaltyRepository $penaltyRepository): Response
    {
        $available = true;
        $underPenalty = false;
        $userConnected = false;
        $userGranted = false;

        // L'utilisateur est-il connecté ?
        if ($security->getUser() != NULL) {
            $userConnected = true;
            $userId = $security->getUser()->getId();
            $role_user = $security->getUser()->getRoles();

            // L'utilisateur est-il membre ?
            if (in_array('ROLE_MEMBER', $role_user)) {
                $userGranted = true;
            }

            if (!$userGranted) {
                $this->addFlash(
                    'borrowStatus',
                    'Vous devez être membre pour emprunter un document'
                );
            }

            // L'utilisateur est-il sous le coup d'une pénalité ?


            $checkPenalty = $penaltyRepository->findOneBy(['user' => $security->getUser()]);
            if ($checkPenalty != NULL && $checkPenalty->getEndAt() > new DateTime()) {
                $underPenalty = true;
            }
            if ($underPenalty) {
                $this->addFlash(
                    'borrowStatus',
                    'Vous êtes sous le coup d\'une pénalité'
                );
            }
        }
        // Si l'utilisateur n'est pas connecté
        else {
            $this->addFlash(
                'borrowStatus',
                'Vous devez être connecté pour emprunter un document'
            );
        }

        // Le document est-il déjà en cours d'emprunt ?
        $borrow = $borrowRepository->findOneBy(['document' => $book->getId(), 'active' => true]);
        if (isset($borrow) && $borrow != NULL) {
            $available = false;
            $this->addFlash(
                'borrowStatus',
                'Document en cours d\'emprunt'
            );
        }

        return $this->render('front-office/books/show.html.twig', [
            'book' => $book,
            'user' => isset($userId) ? $userId : '',
            'disable' => (!$available || !$userGranted || $underPenalty || !$userConnected) ? 1 : 0,
            'titleBtn' => (!$available || !$userGranted || $underPenalty || !$userConnected) ? 'Document indisponible' : NULL,
            'underPenalty' => ($underPenalty) ? true : false
        ]);
    }

    #[Route('/{id}/emprunter', name: 'public_book_borrow', methods: ['POST'], priority: 20)]
    public function borrowBook(Security $security, BorrowRepository $borrowRepository, Document $document, BorrowService $borrowService, PenaltyRepository $penaltyRepository): JsonResponse
    {
        $user = $security->getUser();


        if ($user != NULL) {
            $role_user = $user->getRoles();
            // Si utilisateur est dans groupe membre
            if (in_array('ROLE_MEMBER', $role_user) && $penaltyRepository->findOneBy(['user' => $user]) == NULL) {
                // Bouquin pas déjà emprunté
                $borrow = $borrowRepository->findOneBy(['document' => $document->getId(), 'active' => true]);
                if (!isset($borrow) || $borrow == NULL) {

                    // Appeler le service qui va créer l'emprunt
                    //$borrowService = new BorrowService($user, $document, $doctrine); //$document est passé tout seul grâce à la conversion de paramètre
                    $borrowService->createBorrow($user, $document);

                    return new JsonResponse(['message' => 'Emprunt validé'], 200);
                } else {
                    return new JsonResponse(['message' => 'Document indisponible'], 403);
                }
            } else {
                return new JsonResponse(['message' => 'Vous n\'êtes pas authorisé à emprunter un document'], 403);
            }
        } else {
            return new JsonResponse(['message' => 'Vous n\'êtes pas authorisé à emprunter un document'], 403);
        }
    }

    public function getBooksByStyle(array $styles, int $exclude)
    {
        $finder = new Finder();
        $finder->files()->name('books.json')->in("../public/json");
        $bookSameStyle = [];
        foreach ($finder as $file) {
            $contents = json_decode($file->getContents(), true);
            foreach ($contents as $content) {
                foreach ($styles as $style) {
                    if (in_array($style, $content['styles']) && $content['id'] != $exclude) {
                        $bookSameStyle[] = $content;
                    }
                }
            }
        }
        return $this->render('front-office/books/book-same-style.html.twig', [
            'bookSameStyle' => array_unique($bookSameStyle, SORT_REGULAR),
        ]);
    }
}
