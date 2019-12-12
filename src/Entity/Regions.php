<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RegionsRepository")
 */
class Regions
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $zip;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Ad", mappedBy="regions")
     */
    private $article;

    public function __construct()
    {
        $this->article = new ArrayCollection();
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

    public function getZip(): ?int
    {
        return $this->zip;
    }

    public function setZip(int $zip): self
    {
        $this->zip = $zip;

        return $this;
    }

    /**
     * @return Collection|Ad[]
     */
    public function getArticle(): Collection
    {
        return $this->article;
    }

    public function addArticle(Ad $article): self
    {
        if (!$this->article->contains($article)) {
            $this->article[] = $article;
            $article->setRegions($this);
        }

        return $this;
    }

    public function removeArticle(Ad $article): self
    {
        if ($this->article->contains($article)) {
            $this->article->removeElement($article);
            // set the owning side to null (unless already changed)
            if ($article->getRegions() === $this) {
                $article->setRegions(null);
            }
        }

        return $this;
    }
}
