<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 * @Table(name="categorie")
 */
class Category
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getName(): ?string
    {
        return $this->name;
    }

    public function formatName(){
        $name = trim($this->name);
        $name = preg_replace('/-/', '', $name);
        $name = preg_replace('/:/', '', $name);
        return preg_replace('/\s+/', '-', $name);
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
