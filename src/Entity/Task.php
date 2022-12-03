<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 */
class Task
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $task_title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $task_content;

    /**
     * @ORM\Column(type="boolean")
     */
    private $task_done = false;

    /**
     * @ORM\Column(type="string", length=2)
     */
    private $column_number;

    /**
     * @ORM\Column(type="string", length=4)
     */
    private $card_number;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $card_color;

    /**
     * @ORM\Column(type="string", length=4, nullable=true)
     */
    private $textarea_height;

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

    public function getTaskTitle(): ?string
    {
        return $this->task_title;
    }

    public function setTaskTitle(?string $task_title): self
    {
        $this->task_title = $task_title;

        return $this;
    }

    public function getTaskContent(): ?string
    {
        return $this->task_content;
    }

    public function setTaskContent(?string $task_content): self
    {
        $this->task_content = $task_content;

        return $this;
    }

    public function isTaskDone(): ?bool
    {
        return $this->task_done;
    }

    public function setTaskDone(bool $task_done): self
    {
        $this->task_done = $task_done;

        return $this;
    }

    public function getColumnNumber(): ?string
    {
        return $this->column_number;
    }

    public function setColumnNumber(string $column_number): self
    {
        $this->column_number = $column_number;

        return $this;
    }

    public function getCardNumber(): ?string
    {
        return $this->card_number;
    }

    public function setCardNumber(string $card_number): self
    {
        $this->card_number = $card_number;

        return $this;
    }

    public function getCardColor(): ?string
    {
        return $this->card_color;
    }

    public function setCardColor(string $card_color): self
    {
        $this->card_color = $card_color;

        return $this;
    }

    public function getTextareaHeight(): ?string
    {
        return $this->textarea_height;
    }

    public function setTextareaHeight(?string $textarea_height): self
    {
        $this->textarea_height = $textarea_height;

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
