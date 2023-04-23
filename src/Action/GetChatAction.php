<?php

declare(strict_types=1);

namespace sutchu\chatserver\Action;

use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use sutchu\chatserver\Domain\Chat;
use sutchu\chatserver\Domain\Message;
use sutchu\chatserver\Domain\User;

use slim\Psr7\Response;
use Slim\Routing\RouteContext;


use function json_encode;


final class GetChatAction implements RequestHandlerInterface
{
    private EntityManager $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $sender_user = $request->getAttribute('user');
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        
        $receiver_user_username = $route->getArgument('username');

        $receiver_user = $this->em->getRepository(User::class)->findOneBy([
            'username' => $receiver_user_username,
        ]);

        // Check if user exists
        if ($receiver_user === null) {
            $response = new Response(404);
            $response->getBody()->write('There is no user with that username');
            return $response;
        }

        $chat = Chat::getChat($this->em, $sender_user, $receiver_user);
        $messages = $this->em->getRepository(Message::class)->findBy([
            'chat' => $chat,
        ]);

        $response = new Response(200);
        $response->getBody()->write(json_encode($messages));
        return $response;
    }
}
