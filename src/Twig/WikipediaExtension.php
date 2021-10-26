<?php

namespace App\Twig;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class WikipediaExtension extends AbstractExtension
{

    private $client;
    private $twig;

    // L'extension a besoin de services externes, on les apporte grâce au constructeur
    public function __construct(HttpClientInterface $client, Environment $twig)
    {
        $this->client = $client;
        $this->twig = $twig;
    }


    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('wiki', [$this, 'displayWikipediaLink'], ['is_safe' => ['html']]),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('function_name', [$this, 'doSomething']),
        ];
    }


    public function displayWikipediaLink(string $value)
    {
        $response = $this->client->request('GET', 'https://en.wikipedia.org/wiki/' . $value);
        $code = $response->getStatusCode();

        if ($code == 200) {
            $link = 'https://en.wikipedia.org/wiki/' . $value;
            return $this->twig->render('/front-office/twig/wikipedia_link.html.twig', [
                'link' => $link,
                'title' => $value,
            ]);
        } else {
            return $this->twig->render('/front-office/twig/wikipedia_link.html.twig');
        }
    }
}
