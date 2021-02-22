<?php

namespace App\Entity;

use App\Repository\OldRegionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OldRegionRepository::class)
 */
class OldRegion
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $Code;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ecusson;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->Code;
    }

    public function setCode(string $Code): self
    {
        $this->Code = $Code;

        return $this;
    }

    public function getEcusson(): ?string
    {
        return $this->ecusson;
    }

    public function setEcusson(string $ecusson): self
    {
        $this->ecusson = $ecusson;

        return $this;
    }
}
