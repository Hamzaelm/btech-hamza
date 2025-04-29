<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CreateTodoDTO
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $title;

    #[Assert\NotBlank]
    public string $description;
}
