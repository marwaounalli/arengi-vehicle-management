<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

final class CarSearchCriteria
{
    public ?string $brand = null;

    #[Assert\Positive]
    public ?int $minPassengers = null;

    #[Assert\Choice(choices: ['createdAt', 'brand', 'model', 'passengers'])]
    public string $sort = 'createdAt';

    #[Assert\Choice(choices: ['asc', 'desc'])]
    public string $dir = 'desc';
}
