<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

/**
 * @ORM\Entity(repositoryClass=App\Repository\AppelRepository::class)
 */
class Appel implements JsonSerializable
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
    private $compte;

    /**
     * @ORM\Column(type="integer")
     */
    private $n_facture;

    /**
     * @ORM\Column(type="integer")
     */
    private $n_abonne;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_heure;

    /**
     * @ORM\Column(type="float")
     */
    private $volume_reel;

    /**
     * @ORM\Column(type="float")
     */
    private $volume_facture;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompte(): ?int
    {
        return $this->compte;
    }

    public function setCompte(int $compte): self
    {
        $this->compte = $compte;

        return $this;
    }

    public function getNFacture(): ?int
    {
        return $this->n_facture;
    }

    public function setNFacture(int $n_facture): self
    {
        $this->n_facture = $n_facture;

        return $this;
    }

    public function getNAbonne(): ?int
    {
        return $this->n_abonne;
    }

    public function setNAbonne(int $n_abonne): self
    {
        $this->n_abonne = $n_abonne;

        return $this;
    }

    public function getDateHeure(): ?\DateTimeInterface
    {
        return $this->date_heure;
    }

    public function setDateHeure(\DateTimeInterface $date): self
    {
        $this->date_heure = $date;

        return $this;
    }

    public function getVolumeReel(): ?float
    {
        return $this->volume_reel;
    }

    public function setVolumeReel(float $volume_reel): self
    {
        $this->volume_reel = $volume_reel;

        return $this;
    }

    public function getVolumeFacture(): ?float
    {
        return $this->volume_facture;
    }

    public function setVolumeFacture(float $volume_facture): self
    {
        $this->volume_facture = $volume_facture;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('compte', new NotBlank());
        $metadata->addPropertyConstraint('compte', new NotNull());
        $metadata->addPropertyConstraint('n_facture', new NotBlank());
        $metadata->addPropertyConstraint('n_facture', new NotNull());
        $metadata->addPropertyConstraint('n_abonne', new NotBlank());
        $metadata->addPropertyConstraint('n_abonne', new NotNull());
        $metadata->addPropertyConstraint('date_heure', new NotBlank());
        $metadata->addPropertyConstraint('date_heure', new NotNull());
        $metadata->addPropertyConstraint('volume_reel', new NotBlank());
        $metadata->addPropertyConstraint('volume_reel', new NotNull());
        $metadata->addPropertyConstraint('volume_facture', new NotBlank());
        $metadata->addPropertyConstraint('volume_facture', new NotNull());
        $metadata->addPropertyConstraint('type', new NotBlank());
        $metadata->addPropertyConstraint('type', new NotNull());
    }

    public function jsonSerialize()
    {
        return array(
            'id' => $this->id,
            'compte' => $this->compte,
            'n_facture' => $this->n_facture,
            'n_abonne' => $this->n_abonne,
            'date_heure' => $this->date_heure,
            'volume_reel' => $this->volume_reel,
            'volume_facture' => $this->volume_facture,
            'type' => $this->type
        );
    }
}
