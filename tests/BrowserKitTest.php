<?php

namespace App\Tests;

use App\Repository\EmployeeRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BrowserKitTest extends WebTestCase
{

    private static $client;
    // initialise un objet à chaque tests
    public static function setUpBeforeClass(): void
    {
        self::$client = WebTestCase::createClient();
    }


    public function testFirstH1(): void
    {
        $crawler = self::$client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Merignatech');
    }

    public function testAdminLogin(): void
    {
        $employeeRepository = static::getContainer()->get(EmployeeRepository::class);
        // retrieve the test user
        $adminTest = $employeeRepository->findOneByUsername('admin');

        // simulate $adminTest being logged in
        self::$client->loginUser($adminTest);

        // test e.g. the profile page
        self::$client->request('GET', '/admin');
        $this->assertResponseIsSuccessful();
    }
}
