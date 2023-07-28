<?php

namespace App\Entity;

use App\Repository\HeroesRepository;
use Doctrine\DBAL\Types\Types;
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

    #[ORM\Column(type: Types::BLOB, nullable: true)]
    private $img = null;

    #[ORM\Column(nullable: true)]
    private ?int $img_size = null;

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

    public function getImg()
    {
        return $this->img;
    }

    public function setImg($img): static
    {
        $this->img = $img;

        return $this;
    }

    public function getImgSize(): ?int
    {
        return $this->img_size;
    }

    public function setImgSize(?int $img_size): static
    {
        $this->img_size = $img_size;

        return $this;
    }
}
