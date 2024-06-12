<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\HasIdTrait;
use App\Entity\Traits\HasMetaTrait;
use App\Entity\Traits\HasViewsTrait;
use App\Entity\Traits\HasIsOnlineTrait;
use App\Entity\Traits\HasDeletedAtTrait;
use App\Repository\HelpCenterFaqRepository;
use App\Entity\Traits\HasTimestampableTrait;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: HelpCenterFaqRepository::class)]
class HelpCenterFaq
{
    use HasIdTrait;
    use HasMetaTrait;
    use HasViewsTrait;
    use HasIsOnlineTrait;
    use HasTimestampableTrait;
    use HasDeletedAtTrait;

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
