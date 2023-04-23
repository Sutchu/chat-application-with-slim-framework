<?php

declare(strict_types=1);

namespace sutchu\chatserver\Action;

use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use sutchu\chatserver\Domain\Chat;

use slim\Psr7\Response;

use function json_encode;


final class ListChatsAction implements RequestHandlerInterface
{
    private EntityManager $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $user = $request->getAttribute('user');

        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder->select('c')
            ->from(Chat::class, 'c')
            ->where('c.user1 = :user OR c.user2 = :user')
            ->setParameter('user', $user);

        $chats = $queryBuilder->getQuery()->getResult();

        $response = new Response(200);
        $response->getBody()->write(json_encode($chats));
        return $response;
    }
}
