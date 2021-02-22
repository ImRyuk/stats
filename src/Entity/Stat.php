<?php

namespace App\Entity;

use App\Repository\StatRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StatRepository::class)
 */
class Stat
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $groupement;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $satisfaction_usagers;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $satisfaction_victimes;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $brigade_numerique;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $delai_brigade;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $old_region;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    private $code;

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

    public function setGroupement(?string $groupement): self
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

    public function getSatisfactionUsagers(): ?string
    {
        return $this->satisfaction_usagers;
    }

    public function setSatisfactionUsagers(string $satisfaction_usagers): self
    {
        $this->satisfaction_usagers = $satisfaction_usagers;

        return $this;
    }

    public function getSatisfactionVictimes(): ?string
    {
        return $this->satisfaction_victimes;
    }

    public function setSatisfactionVictimes(string $satisfaction_victimes): self
    {
        $this->satisfaction_victimes = $satisfaction_victimes;

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

    public function getDelaiBrigade(): ?string
    {
        return $this->delai_brigade;
    }

    public function setDelaiBrigade(string $delai_brigade): self
    {
        $this->delai_brigade = $delai_brigade;

        return $this;
    }

    public function getOldRegion(): ?string
    {
        return $this->old_region;
    }

    public function setOldRegion(?string $old_region): self
    {
        $this->old_region = $old_region;

        return $this;
    }

    public function toArray(): array
    {
        return [$this->region, $this->groupement,$this->getCode(), $this->satisfaction_usagers, $this->satisfaction_victimes, $this->brigade_numerique, $this->delai_brigade];
    }
}
