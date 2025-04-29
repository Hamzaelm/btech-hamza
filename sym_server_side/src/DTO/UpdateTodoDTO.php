<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateTodoDTO
{

    #[Assert\Length(max: 255)]
    public ?string $title = null;

    public ?string $description = null;

    public ?bool $completed = null;
}