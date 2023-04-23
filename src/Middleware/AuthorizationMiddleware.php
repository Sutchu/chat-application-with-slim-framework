<?php

declare(strict_types=1);

namespace sutchu\chatserver\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use sutchu\chatserver\Domain\AuthToken;
use Doctrine\ORM\EntityManager;

use Slim\Psr7\Response;


class AuthorizationMiddleware implements MiddlewareInterface
{
    private EntityManager $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $authHeader = $request->getHeaderLine('Authorization');

        if (!$authHeader) {
            $response = new Response(401);
            $response->getBody()->write('Unauthorized: Missing Authorization header');
            return $response;
        }

        $token = substr($authHeader, 7); // Remove "Bearer " prefix in the Authorization header

        if (!$token) {
            $response = new Response(401);
            $response->getBody()->write('Unauthorized: Missing token');
            return $response;
        }

        $authToken = $this->em->getRepository(AuthToken::class)->findOneBy(['token' => $token]);

        if (!$authToken || !$authToken->isValid()) {
            $response = new Response(401);
            $response->getBody()->write('Unauthorized: Invalid token');
            return $response;
        }

        // Add the user object to the request's attributes for actions to use
        $request = $request->withAttribute('user', $authToken->getUser());

        return $handler->handle($request);
    }
}
