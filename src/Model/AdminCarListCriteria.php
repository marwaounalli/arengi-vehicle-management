<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

final class AdminCarListCriteria
{
    #[Assert\Choice(choices: ['createdAt', 'brand', 'model', 'passengers'])]
    public string $sort = 'createdAt';

    #[Assert\Choice(choices: ['asc', 'desc'])]
    public string $dir = 'desc';

    #[Assert\Positive]
    public int $page = 1;

    #[Assert\Range(min: 5, max: 100)]
    public int $limit = 10;

    public function normalize(): void
    {
        $this->dir = strtolower($this->dir) === 'asc' ? 'asc' : 'desc';
        $this->page = max(1, $this->page);
        $this->limit = max(5, min(100, $this->limit));

        $allowedSort = ['createdAt', 'brand', 'model', 'passengers'];
        if (!in_array($this->sort, $allowedSort, true)) {
            $this->sort = 'createdAt';
        }
    }
}
