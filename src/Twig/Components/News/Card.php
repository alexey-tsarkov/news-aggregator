<?php

namespace App\Twig\Components\News;

use App\Entity\News;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class Card
{
    public News $news;
}
