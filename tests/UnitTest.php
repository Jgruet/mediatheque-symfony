<?php

namespace App\Tests;

use App\Entity\User;
use App\Service\AdminStatService;
use App\Service\MemberAccess;
use Doctrine\ORM\EntityManager;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Security;

/**
 * Exemple de classe de test unitaire qui test des méthodes de la classe AdminStatService de manière isolée
 */
class UnitTest extends TestCase
{

    private static $dateList;
    private $adminStatServiceInstance;

    // création d'un tableau pour tous les tests
    public static function setUpBeforeClass(): void
    {
        self::$dateList = ['1910-10-29', '1921-01-01', '1963-10-29', '1753-01-01', '1321-01-01', '1988-10-29', '1521-01-01', '2005-01-01', '1721-01-01', '1654-01-01', '1876-10-29', '2015-01-01',];
    }

    // initialise un objet à chaque tests
    protected function setUp(): void
    {
        $this->adminStatServiceInstance = new AdminStatService();
    }

    // détruit l'instance entre 2 tests
    protected function tearDown(): void
    {
        unset($this->adminStatServiceInstance);
    }


    public function testGetCentenaryThisYear(): void
    {
        $o = $this->adminStatServiceInstance;
        $result = $o->getCentenaryThisYear(self::$dateList);
        $this->assertCount(4, $result, 'Le tableau aurait du comprendre 4 valeurs');
        $this->assertEquals('1921-01-01', $result[0], 'La première date attendue est 1921-01-01');
        $this->assertEquals(
            ['1921-01-01', '1321-01-01', '1521-01-01', '1721-01-01'],
            $result,
            'Les tableaux doivent être égaux'
        );
    }


    public function testGetAnniversary(): void
    {
        $o = $this->adminStatServiceInstance;
        $result = $o->getAnniversary(self::$dateList);
        $this->assertCount(4, $result, 'Le tableau aurait du comprendre 4 valeurs');
        $this->assertEquals('1910-10-29', $result[0], 'La première date attendue est 1910-10-29');
        $this->assertEquals(
            [
                '1910-10-29',
                '1963-10-29',
                '1988-10-29',
                '1876-10-29'
            ],
            $result,
            'Les tableaux doivent être égaux'
        );
    }

    public function testStrResearch()
    {
        $strings = ["toto", "plage", "semaine"];
        $text = "Cette semaine, toto est parti à la plage.";

        $o = $this->adminStatServiceInstance;
        $result = $o->strResearch($strings, $text);
        $this->assertEquals($text, $result);
    }

    public function testEmptyStrResearch()
    {
        $strings = ["chien", "ballon", "Sébastien"];
        $text = "Cette semaine, toto est parti à la plage.";

        $o = $this->adminStatServiceInstance;
        // Ici on attend que cela se passe mal
        $this->expectException(\Exception::class);
        $result = $o->strResearch($strings, $text);
    }

    public function testCommonWords()
    {
        //$text1 = "Praesent aliquam, leo eget volutpat auctor, libero mi dapibus leo, non consequat nisi nisl sed nunc. Sed tincidunt ex sed dolor interdum imperdiet. Nunc vulputate egestas nisi, sed vestibulum lectus fermentum eget. Mauris consectetur nunc ut turpis rhoncus sagittis. Phasellus pellentesque lorem ante, eget ultrices elit malesuada eu. Cras mollis est dignissim turpis placerat ullamcorper. Ut ullamcorper lobortis lacus, ut finibus dolor sagittis eu. Suspendisse mattis massa vel gravida egestas. Fusce ut nulla hendrerit, consequat nisi eu, dictum nisi. Nulla vel massa fermentum, semper tellus ut, consequat turpis. Ut vehicula sollicitudin quam, ut sodales orci cursus ut. Vivamus odio justo, ultrices sed varius nec, rutrum eget dui. Nulla sollicitudin nunc pharetra hendrerit porta. ";
        //$text2 = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut vehicula vestibulum congue. Ut dignissim felis nec magna suscipit posuere. Integer ac nisl quam. Proin tincidunt vitae mi id dictum. Ut sit amet eros libero. Etiam maximus diam vel ante viverra, a elementum sem consequat. Maecenas iaculis arcu et tortor volutpat aliquam. In molestie ornare commodo. Pellentesque accumsan arcu at orci tincidunt lobortis. Cras tincidunt accumsan rhoncus. Ut varius ipsum eget sem mollis dignissim. Proin ante urna, vulputate ac bibendum sed, luctus et orci. Etiam pellentesque velit nibh, quis vehicula enim blandit nec. Duis ut arcu turpis. Mauris maximus nibh at lectus faucibus rhoncus. ";
        $text1 = "maman j'ai raté l'avion";
        $text2 = "au secours maman, l'avion fonce dans 2 patates";

        $o = $this->adminStatServiceInstance;
        $result = $o->commonWords($text1, $text2);
        $result = array_values($result);
        $this->assertEquals(["l'avion"], $result);
        return $result;
    }

    // méthode qui dépend du résultat de la méthode du dessus
    /**
     * @depends testCommonWords
     */
    public function testNbCommonsWords(array $commonWords)
    {
        $this->assertEquals(1, count($commonWords));
    }

    public function userProvider()
    {
        $schema = ['user1' => [55, 55000, 55, '33700', 6], 'user2' => [99, 1000, 6, '3700', 1]];
        return $schema;
    }

    /* TEST DE MEMBERACCES en mode test unitaire - CLASSE QUI A DES DEPENDANCES */
    /* CHAQUE DEPENDANCE SERA SIMULÉE : ON APPELLE CA DES MOCKS */

    /**
     * @dataProvider userProvider
     */
    public function testMemberAccessAmountToPay($w, $x, $y, $z, $result)
    {
        // création des dépendances nécessaire au constructeur de la classe
        $security =  $this->createMock(Security::class); // n'est pas un vrai objet Security, il va falloir feinter pour récupérer l'utilisateur connecté
        $em = $this->createMock(EntityManager::class);

        $user = new User();
        $user->setAge($w)
            ->setAnnualSalary($x)
            ->setNbChildren($y)
            ->setPostalCode($z);
        // Création d'une fausse méthode getUser() dans le faux objet Security qui nous retournera un vrai utilisateur
        $security->method('getUser')->willReturn($user);

        $em->method('flush');

        $memberAcess = new MemberAccess($security, 50, $em);
        $amount = $memberAcess->amountToPay();
        $this->assertEquals($result, $amount);
    }
}
