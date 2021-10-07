<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Document;

/**
 * @ORM\Entity(repositoryClass=BookRepository::class)
 */
class Book extends Document
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $nb_page;

    public function getId(): ?int
    {
        return $this->id;
    }

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
