<?php

declare(strict_types=1);

namespace sutchu\chatserver\Util;

use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Response;

class ResponseUtil
{
    public static function errorResponse(int $statusCode, string $message): ResponseInterface
    {
        $response = new Response($statusCode);
        $response->getBody()->write($message);
        return $response;
    }
}