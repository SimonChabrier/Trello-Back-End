<?php

namespace App\Entity;

use App\Repository\ColumnRepository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
// API
use Symfony\Component\Serializer\Annotation\Groups;

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
     * @Groups({"task_read", "task_write"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     * @Groups({"task_read", "task_write"})
     */
    private $column_name;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"task_read", "task_write"})
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

    /**
     * @ORM\OneToMany(targetEntity=Task::class, mappedBy="task_column", orphanRemoval=true, cascade={"persist"})
     * @Groups({"task_read"})
     * @ORM\OrderBy({"card_number" = "ASC"})
     */
    private $tasks;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Task>
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    /**
     * @param Task $task
     * Ajoute une tâche à la colonne
     */
    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
            $task->setTaskColumn($this);
        }

        return $this;
    }

    /**
     * @param Task $task
     * Supprime une tâche de la colonne
     */
    public function removeTask(Task $task): self
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getTaskColumn() === $this) {
                $task->setTaskColumn(null);
            }
        }

        return $this;
    }
}
