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


final class LoginUserAction implements RequestHandlerInterface
{
    private EntityManager $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $body = $request->getParsedBody();

        $user = $this->em->getRepository(User::class)->findOneBy([
            'username' => $body['username'],
        ]);

        // Check if user exists
        if ($user === null) {
            $response = new Response(404);
            $response->getBody()->write('User not found');
            return $response;
        }

        // Check if password is correct
        if (!$user->verifyPassword($body['password'])) {
            $response = new Response(401);
            $response->getBody()->write('Invalid password');
            return $response;
        }

        // Create new AuthToken
        $authToken = $user->createAuthToken();

        $this->em->persist($authToken);
        $this->em->flush();

        $body = json_encode($authToken, JSON_PRETTY_PRINT) . PHP_EOL;

        $response = new Response(200);
        $response->getBody()->write($body);
        return $response;
    }
}
