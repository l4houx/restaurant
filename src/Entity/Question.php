<?php

namespace App\Entity;

use App\Entity\Traits\HasIdTrait;
use App\Entity\Traits\HasIsOnlineTrait;
use App\Entity\Traits\HasTimestampableTrait;
use App\Repository\QuestionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: QuestionRepository::class)]
class Question
{
    use HasIdTrait;
    use HasIsOnlineTrait;
    use HasTimestampableTrait;
    //use HasGedmoTimestampTrait;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    private string $question = '';

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    private string $answer = '';

    public function __toString(): string
    {
        return (string) $this->getQuestion() ?: '';
    }

    public function getQuestion(): string
    {
        return $this->question;
    }

    public function setQuestion(string $question): static
    {
        $this->question = $question;

        return $this;
    }

    public function getAnswer(): string
    {
        return $this->answer;
    }

    public function setAnswer(string $answer): static
    {
        $this->answer = $answer;

        return $this;
    }
}
