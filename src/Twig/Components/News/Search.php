<?php

namespace App\Twig\Components\News;

use App\Entity\News;
use App\Repository\NewsRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class Search
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public string $query = '';

    #[LiveProp]
    public ?int $limit = null;

    public function __construct(
        private NewsRepository $newsRepository,
    ) {}

    /**
     * @return News[]
     */
    public function getNews(): array
    {
        $limit = \min($limit ?? 100, 100);

        return $this->newsRepository->findByQuery($this->query, $limit);
    }
}
