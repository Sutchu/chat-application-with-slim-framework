<?php

declare(strict_types=1);

namespace sutchu\chatserver\Action;

use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use sutchu\chatserver\Domain\Chat;
use sutchu\chatserver\Domain\Message;

use slim\Psr7\Response;


use function json_encode;


final class SendMessageAction implements RequestHandlerInterface
{
    private EntityManager $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $sender_user = $request->getAttribute('user');
        $receiver_user_username = $request->getAttribute('username');

        $receiver_user = $this->em->getRepository(User::class)->findOneBy([
            'username' => $receiver_user_username,
        ]);

        // Check if user exists
        if ($receiver_user === null) {
            $response = new Response(404);
            $response->getBody()->write('User not found');
            return $response;
        }

        $chat = Chat::getOrCreateChat($this->em, $sender_user, $receiver_user);
        $message_content = $request->getParsedBody()['message_content'];

        $message = new Message(
            chat: $chat,
            sender: $sender_user,
            content: $message_content);

        $this->em->persist($message);
        $this->em->flush();
        
        $body = json_encode($message, JSON_PRETTY_PRINT) . PHP_EOL;

        $response = new Response(200);
        $response->getBody()->write($body);
        return $response;
    }
}
