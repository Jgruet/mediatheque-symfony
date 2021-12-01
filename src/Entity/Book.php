<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Document;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=BookRepository::class)
 */
class Book extends Document
{

    /**
     * @ORM\Column(type="integer")
     */
    #[Assert\LessThan(2001, null, 'please its too big')]
    #[Assert\GreaterThan(10, null, 'please its too small')]
    private $nb_page;

    public function getNbPage(): ?int
    {
        return $this->nb_page;
    }

    public function setNbPage(int $nb_page): self
    {
        $this->nb_page = $nb_page;

        return $this;
    }
}
