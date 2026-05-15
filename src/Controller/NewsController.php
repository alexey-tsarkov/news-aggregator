<?php

namespace App\Controller;

use App\Repository\NewsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class NewsController extends AbstractController
{
    public function __construct(
        private NewsRepository $repository,
    ) {}

    #[Route('/', name: 'app_news')]
    public function index(): Response
    {
        return $this->render('news/index.html.twig');
    }
}
