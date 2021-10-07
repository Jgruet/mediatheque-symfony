<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/authors', name: 'public-authors-')]

class AuthorsController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        $finder = new Finder();
        $finder->files()->name('authors.json')->in("../public/json");

        foreach ($finder as $file) {
            $contents = json_decode($file->getContents());
        }
        return $this->render('public/authors/index.html.twig', [
            'allAuthors' => $contents,
        ]);
    }

    #[Route('/{id}', name: 'author-by-id')]
    public function getAuthorById($id): Response
    {

        $finder = new Finder();
        //$finder->name('books');
        $finder->files()->name('authors.json')->in("../public/json");
        $author = [];
        foreach ($finder as $file) {
            $contents = json_decode($file->getContents(), true);
            foreach ($contents as $content) {
                if ($content['id'] == $id) {
                    $author[] = $content;
                }
            }
        }

        return $this->render('public/authors/single-author.html.twig', [
            'author' => $author,
        ]);
    }

    public function getAuthorInfos($id)
    {
        $finder = new Finder();
        $finder->files()->name('authors.json')->in("../public/json");
        $author = [];
        foreach ($finder as $file) {
            $contents = json_decode($file->getContents(), true);
            foreach ($contents as $content) {
                if ($content['id'] == $id) {
                    $author[] = $content;
                }
            }
        }

        return $this->render('public/authors/author-min.html.twig', [
            'author' => $author,
        ]);
    }
}
