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


final class RegisterUserAction implements RequestHandlerInterface
{
    private EntityManager $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $body = $request->getParsedBody();

        $user = new User(
            email: $body['email'],
            username: $body['username'],            
            password: $body['password']
        );

        $this->em->persist($user);
        $this->em->flush();

        $body = json_encode($user, JSON_PRETTY_PRINT) . PHP_EOL;

        $response = new Response(201);
        $response->getBody()->write($body);
        return $response;
    }
}
