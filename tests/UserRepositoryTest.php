<?php

namespace App\Tests;

use App\Repository\UserRepository;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Exemple de classe de test qui a accès aux fonctionnalités core de symfony pour par exemple tester une méthode d'un repository
 */
class UserRepositoryTest extends KernelTestCase
{
    public function testSomething(): void
    {
        $kernel = self::bootKernel();
        $manager = self::getContainer()->get(ManagerRegistry::class);

        $userRepo = new UserRepository($manager);
        $result = $userRepo->findByWealthyUser(30000);

        $this->assertEquals(4, count($result));
    }
}
