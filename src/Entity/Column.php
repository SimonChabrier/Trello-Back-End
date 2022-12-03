<?php

namespace App\Entity;

use App\Repository\ColumnRepository;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ColumnRepository::class)
 * @ORM\Table(name="`column`")
 */
class Column
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     */
    private $column_name;

    /**
     * @ORM\Column(type="integer")
     */
    private $column_number;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getColumnName(): ?string
    {
        return $this->column_name;
    }

    public function setColumnName(?string $column_name): self
    {
        $this->column_name = $column_name;

        return $this;
    }

    public function getColumnNumber(): ?int
    {
        return $this->column_number;
    }

    public function setColumnNumber(int $column_number): self
    {
        $this->column_number = $column_number;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
