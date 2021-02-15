<?php

namespace App\Entity;

use App\Repository\StateUniteRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StateUniteRepository::class)
 */
class StateUnite
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
    private $region;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $groupement;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=4)
     */
    private $satisfaction;

    /**
     * @ORM\Column(type="string", length=4)
     */
    private $satisfaction_victime;

    /**
     * @ORM\Column(type="string", length=4)
     */
    private $brigade_numerique;

    /**
     * @ORM\Column(type="string", length=4)
     */
    private $delai_brigade_numerique;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(string $region): self
    {
        $this->region = $region;

        return $this;
    }

    public function getGroupement(): ?string
    {
        return $this->groupement;
    }

    public function setGroupement(string $groupement): self
    {
        $this->groupement = $groupement;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getSatisfaction(): ?string
    {
        return $this->satisfaction;
    }

    public function setSatisfaction(string $satisfaction): self
    {
        $this->satisfaction = $satisfaction;

        return $this;
    }

    public function getSatisfactionVictime(): ?string
    {
        return $this->satisfaction_victime;
    }

    public function setSatisfactionVictime(string $satisfaction_victime): self
    {
        $this->satisfaction_victime = $satisfaction_victime;

        return $this;
    }

    public function getBrigadeNumerique(): ?string
    {
        return $this->brigade_numerique;
    }

    public function setBrigadeNumerique(string $brigade_numerique): self
    {
        $this->brigade_numerique = $brigade_numerique;

        return $this;
    }

    public function getDelaiBrigadeNumerique(): ?string
    {
        return $this->delai_brigade_numerique;
    }

    public function setDelaiBrigadeNumerique(string $delai_brigade_numerique): self
    {
        $this->delai_brigade_numerique = $delai_brigade_numerique;

        return $this;
    }
}
