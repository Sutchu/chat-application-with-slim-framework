<?php

declare(strict_types=1);

namespace sutchu\chatserver\Action;

use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use sutchu\chatserver\Domain\User;

use slim\Psr7\Response;


use function json_encode;

final class ListUsers implements RequestHandlerInterface
{
    private EntityManager $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var User[] $users */
        $users = $this->em
            ->getRepository(User::class)
            ->findAll();

        $body = json_encode($users, JSON_PRETTY_PRINT);
        $response = new Response(200);
        $response->getBody()->write($body);
        return $response;
    }
}
