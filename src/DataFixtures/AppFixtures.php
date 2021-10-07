<?php

namespace App\DataFixtures;

use Faker;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Author;
use App\Entity\Category;
use App\Entity\Book;
use App\Entity\Cd;
use App\Entity\Document;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $generator = Faker\Factory::create();
        $populator = new Faker\ORM\Doctrine\Populator($generator, $manager);
        $populator->addEntity(Author::class, 5, [
            'lastName' => function () use ($generator) {
                return $generator->lastName();
            },
            'firstName' => function () use ($generator) {
                return $generator->firstName();
            }
        ]);

        /* $categoryArray = ['novel', 'bd', 'fantasy', 'medieval', 'suspens', 'true story', 'jazz', 'classic'];
        foreach ($categoryArray as $key => $categoryName) {
            $populator->addEntity(Category::class, 7, [
                'name' => $categoryArray[$key]
            ]);
        } */

        $categoryArray = ['novel', 'bd', 'fantasy', 'medieval', 'suspens', 'true story', 'jazz', 'classic'];
        $objectCategoryArray = [];
        foreach ($categoryArray as $categoryName) {
            $category = new Category();
            $category->setName($categoryName);
            $manager->persist($category);
            $objectCategoryArray[] = $category;
        }


        $populator->addEntity(Book::class, 20, [
            'nb_page' => function () use ($generator) {
                return $generator->numberBetween(80, 500);;
            },
            'title' => function () use ($generator) {
                return $generator->sentence(3);
            },
            'code' => function () use ($generator) {
                return $generator->bothify('?????-#####');
            }
        ]);

        $populator->addEntity(Cd::class, 15, [
            'duration' => function () use ($generator) {
                return $generator->numberBetween(180, 4200);;
            },
            'title' => function () use ($generator) {
                return $generator->sentence(2);
            },
            'code' => function () use ($generator) {
                return $generator->bothify('?????-#####');
            }
        ]);
        $insertedPKs = $populator->execute();

        $manager->flush();

        $DocumentRepository = $manager->getRepository(Document::class);
        $allDocuments = $DocumentRepository->findAll();

        foreach ($allDocuments as $document) {
            $randomCategory = array_rand($objectCategoryArray, 1);
            $document->addCategory($objectCategoryArray[$randomCategory]);
        }

        $manager->flush();
    }
}
