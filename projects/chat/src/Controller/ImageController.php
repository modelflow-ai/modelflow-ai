<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ImageController
{
    #[Route('/image/{hash}.jpeg', name: 'image')]
    public function __invoke(string $hash): Response
    {
        return new BinaryFileResponse(dirname(__DIR__, 2) . '/var/images/' . $hash . '.jpeg');
    }
}
