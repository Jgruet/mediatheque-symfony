<?php

namespace App\Entity;

use App\Repository\CdRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Document;

/**
 * @ORM\Entity(repositoryClass=CdRepository::class)
 */
class Cd extends Document
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $duration;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDuration(): ?string
    {
        return $this->duration;
    }

    public function setDuration(string $duration): self
    {
        $this->duration = $duration;

        return $this;
    }
}
