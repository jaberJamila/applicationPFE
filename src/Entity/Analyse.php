<?php

namespace App\Entity;

use App\Repository\AnalyseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnalyseRepository::class)]
class Analyse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $methode = null;

    #[ORM\Column(length: 255)]
    private ?string $nature_analyse = null;

    #[ORM\Column(length: 255)]
    private ?string $analyse_demander = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre_echantillons = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMethode(): ?string
    {
        return $this->methode;
    }

    public function setMethode(string $methode): self
    {
        $this->methode = $methode;

        return $this;
    }

    public function getNatureAnalyse(): ?string
    {
        return $this->nature_analyse;
    }

    public function setNatureAnalyse(string $nature_analyse): self
    {
        $this->nature_analyse = $nature_analyse;

        return $this;
    }

    public function getAnalyseDemander(): ?string
    {
        return $this->analyse_demander;
    }

    public function setAnalyseDemander(string $analyse_demander): self
    {
        $this->analyse_demander = $analyse_demander;

        return $this;
    }

    public function getNombreEchantillons(): ?string
    {
        return $this->nombre_echantillons;
    }

    public function setNombreEchantillons(string $nombre_echantillons): self
    {
        $this->nombre_echantillons = $nombre_echantillons;

        return $this;
    }
}
