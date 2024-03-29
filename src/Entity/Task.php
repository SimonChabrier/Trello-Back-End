<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 */
class Task
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"tasks_read", "task_delete"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Regex(
     *     pattern="/^[^<>]*$/",
     *     message="Le titre de la carte n'est pas valide."
     * )
     * @Groups({"tasks_read", "task_write"})
     */
    private $task_title;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Regex(
     *     pattern="/^[^<>]*$/",
     *     message="Le nom de la carte n'est pas valide."
     * )
     * @Groups({"tasks_read", "task_write"})
     */
    private $task_content;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"tasks_read", "task_write"})
     */
    private $task_done = false;

    /**
     * @ORM\Column(type="string", length=2)
     * @Groups({"tasks_read", "task_write"})
     */
    private $column_number;

    /**
     * @ORM\Column(type="string", length=4)
     * @Groups({"tasks_read", "task_write"})
     */
    private $card_number;

    /**
     * @ORM\Column(type="string", length=50)
     * @Groups({"tasks_read", "task_write"})
     */
    private $card_color;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     * @Groups({"tasks_read", "task_write"})
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

    /**
     * @ORM\ManyToOne(targetEntity=Column::class, inversedBy="tasks", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"task_write"})
     */
    private $task_column;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="tasks", cascade={"persist"})
     * @Groups({"tasks_read"})
     */
    private $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

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

    public function getTaskColumn(): ?Column
    {
        return $this->task_column;
    }

    public function setTaskColumn(?Column $task_column): self
    {
        $this->task_column = $task_column;

        return $this;
    }

    /**
     * Return the users associated to the task
     * 
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    /**
     * Add a user associated to the task
     * 
     * @param User $user
     * @return self
     */
    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
        }

        return $this;
    }

    /**
     * Remove a user associated to the task
     * 
     * @param User $user
     * @return self
     */
    public function removeUser(User $user): self
    {
        $this->users->removeElement($user);

        return $this;
    }
}
