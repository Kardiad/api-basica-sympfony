<?php

namespace App\Entity;

use App\Repository\HeroesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HeroesRepository::class)]
class Heroes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 200)]
    private ?string $nombre = null;

    #[ORM\Column(length: 200)]
    private ?string $codigo = null;

    #[ORM\Column(length: 200)]
    private ?string $alterego = null;

    #[ORM\Column(length: 200)]
    private ?string $aparicion = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    public function setCodigo(string $codigo): static
    {
        $this->codigo = $codigo;

        return $this;
    }

    public function getAlterego(): ?string
    {
        return $this->alterego;
    }

    public function setAlterego(string $alterego): static
    {
        $this->alterego = $alterego;

        return $this;
    }

    public function getAparicion(): ?string
    {
        return $this->aparicion;
    }

    public function setAparicion(string $aparicion): static
    {
        $this->aparicion = $aparicion;

        return $this;
    }

    public function __toString()
    {
        return "{ nombre: $this->nombre, codigo: $this->codigo, alterego:$this->alterego, aparicion:$this->aparicion}";
    }
}
