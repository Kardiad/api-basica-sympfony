<?php

namespace App\Entity;

use App\Repository\SerieRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SerieRepository::class)]
class Serie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 200)]
    private ?string $editorial = null;

    #[ORM\Column(length: 200)]
    private ?string $nombre = null;

    #[ORM\Column]
    private ?int $capitulos = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEditorial(): ?string
    {
        return $this->editorial;
    }

    public function setEditorial(string $editorial): static
    {
        $this->editorial = $editorial;

        return $this;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getCapitulos(): ?int
    {
        return $this->capitulos;
    }

    public function setCapitulos(int $capitulos): static
    {
        $this->capitulos = $capitulos;

        return $this;
    }
}
