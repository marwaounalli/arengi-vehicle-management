<?php

namespace App\Entity;

use App\Repository\CarRepository;
use App\Enum\CarTypeEnum;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: CarRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Car
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $brand = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $model = null;

    #[ORM\Column(length: 30)]
    #[Assert\NotBlank]
    private ?string $type = null;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\Positive]
    private ?int $passengers = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private ?string $color = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Positive]
    private ?int $ptra = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Assert\NotNull]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    #[Assert\Callback]
    public function validatePtraForUtilitaire(ExecutionContextInterface $context): void
    {
        if ($this->type === CarTypeEnum::Utilitaire->value && $this->ptra === null) {
            $context->buildViolation('Le PTRA est obligatoire pour un véhicule utilitaire.')
                ->atPath('ptra')
                ->addViolation();
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getType(): ?CarTypeEnum
    {
        return $this->type !== null ? CarTypeEnum::from($this->type) : null;
    }

    public function setType(CarTypeEnum|string|null $type): static
    {
        if ($type === null) {
            $this->type = null;
            return $this;
        }

        if ($type instanceof CarTypeEnum) {
            $this->type = $type->value;
            return $this;
        }

        $this->type = CarTypeEnum::from($type)->value;

        return $this;
    }

    public function getPassengers(): ?int
    {
        return $this->passengers;
    }

    public function setPassengers(int $passengers): static
    {
        $this->passengers = $passengers;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function getPtra(): ?int
    {
        return $this->ptra;
    }

    public function setPtra(?int $ptra): static
    {
        $this->ptra = $ptra;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }
}
