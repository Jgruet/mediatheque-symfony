<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/books', name: 'public-books-')]

class BooksController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {

        $finder = new Finder();
        //$finder->name('books');
        $finder->files()->name('books.json')->in("../public/json");

        foreach ($finder as $file) {
            $contents = json_decode($file->getContents());
        }

        return $this->render('public/books/index.html.twig', [
            'controller_name' => 'PublicController',
            'allBooks' => $contents,
        ]);
    }


    #[Route('/criterias', name: 'book-by-style')]
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

        return $this->render('public/books/single-book.html.twig', [
            'book' => $book,
        ]);
    }

    #[Route('/{id}/{_format}', name: 'book-by-id', requirements: ['id' => '^\d+$'], priority: 10)]
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
                return $this->render('public/books/single-book.html.twig', [
                    'book' => $book,
                ]);
                break;
        }
    }

    #[Route('/{id}', name: 'book-by-id', requirements: ['id' => '^\d+$'], priority: 10)]
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
        return $this->render('public/books/single-book.html.twig', [
            'book' => $book,
        ]);
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
        return $this->render('public/books/book-same-style.html.twig', [
            'bookSameStyle' => array_unique($bookSameStyle, SORT_REGULAR),
        ]);
    }
}
