<?php

namespace App\Entity;

use App\Repository\TypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TypeRepository::class)
 * @ORM\Table(name="Types")
 */
class Type implements \JsonSerializable
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
    private $libelle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $suffixe;

    /**
     * @ORM\OneToMany(targetEntity=StatValue::class, mappedBy="type")
     */
    private $stats;

    /**
     * @ORM\ManyToOne(targetEntity=Source::class, inversedBy="types")
     */
    private $source;

    public function __construct()
    {
        $this->stats = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getSuffixe(): ?string
    {
        return $this->suffixe;
    }

    public function setSuffixe(string $suffixe): self
    {
        $this->suffixe = $suffixe;

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
            $stat->setType($this);
        }

        return $this;
    }

    public function removeStat(StatValue $stat): self
    {
        if ($this->stats->removeElement($stat)) {
            // set the owning side to null (unless already changed)
            if ($stat->getType() === $this) {
                $stat->setType(null);
            }
        }

        return $this;
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
            "libelle" => $this->getLibelle()
        ];
    }

    public function getSource(): ?Source
    {
        return $this->source;
    }

    public function setSource(?Source $source): self
    {
        $this->source = $source;

        return $this;
    }
}
