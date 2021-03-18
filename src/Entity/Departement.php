<?php

namespace App\Entity;

use App\Repository\DepartementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * @ORM\Entity(repositoryClass=DepartementRepository::class)
 * @ORM\Table(name="Departements")
 */
class Departement implements JsonSerializable
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
    private $name;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $old_region;

    /**
     * @ORM\ManyToOne(targetEntity=Region::class, inversedBy="departements")
     * @ORM\JoinColumn(nullable=false)
     */
    private $region;

    /**
     * @ORM\OneToMany(targetEntity=StatValue::class, mappedBy="departement")
     */
    private $stats;

    public function __construct()
    {
        $this->stats = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

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

    public function getRegion(): ?Region
    {
        return $this->region;
    }

    public function setRegion(?Region $region): self
    {
        $this->region = $region;

        return $this;
    }

    /**
     * @return Collection|StatValue[]
     */
    public function getStats(): Collection
    {
        return $this->stats;
    }

    public function addStat(StatValue $stat): self
    {
        if (!$this->stats->contains($stat)) {
            $this->stats[] = $stat;
            $stat->setDepartement($this);
        }

        return $this;
    }

    public function removeStat(StatValue $stat): self
    {
        if ($this->stats->removeElement($stat)) {
            // set the owning side to null (unless already changed)
            if ($stat->getDepartement() === $this) {
                $stat->setDepartement(null);
            }
        }

        return $this;
    }

    public function toCSV(): array
    {
        $values = [$this->getName(), $this->getCode()];

        $stats = $this->getStats();

        foreach ($stats as $stat)
        {
            $values[] = $stat->getValue();
        }

        return $values;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            "name" => $this->getName(),
            "Code" => $this->getCode(),
            "old_region" => $this->getOldRegion(),
            "region" => $this->getRegion(),
            "stats" => $this->getStats()->getValues()
        ];
    }
}
