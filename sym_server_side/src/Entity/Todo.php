<?php

namespace App\Entity;

use App\Repository\TodoRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TodoRepository::class)]
class Todo
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    #[Groups(['todo:read', 'user:read'])]
    private int $id;

    #[ORM\Column(type: 'string')]
    #[Groups(['todo:read', 'user:read'])]
    private string $title;

    #[ORM\Column(type: 'text')]
    #[Groups(['todo:read'])]
    private string $description;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['todo:read'])]
    private bool $completed;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    public function __construct()
    {
        $this->completed = false;
    }

    public function getId(): int
    {
        return $this->id;
    }
    public function getTitle(): string
    {
        return $this->title;
    }
    public function setTitle($title): void
    {
        $this->title = $title;
    }
    public function getDescription(): string
    {
        return $this->description;
    }
    public function setDescription($description): void
    {
        $this->description = $description;
    }
    public function setUser(User $user): void
    {
        $this->user = $user;
    }
    public function getCompleted(): bool
    {
        return $this->completed;
    }
    public function setCompleted(bool $completed = true): void
    {
        $this->completed = $completed;
    }
    public function getUser(): User
    {
        return $this->user;
    }
}
