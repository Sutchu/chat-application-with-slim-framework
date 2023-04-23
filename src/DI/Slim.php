<?php

declare(strict_types=1);

namespace sutchu\chatserver\DI;

use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Middleware\ContentLengthMiddleware;
use UMA\DIC\ServiceProvider;

use sutchu\chatserver\Action\ListUsers;
use sutchu\chatserver\Action\RegisterUserAction;
use sutchu\chatserver\Action\LoginUserAction;
use sutchu\chatserver\Action\SendMessageAction;

use sutchu\chatserver\Middleware\AuthorizationMiddleware;


final class Slim implements ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function provide(ContainerInterface $c): void
    {
        $c->set(ListUsers::class, static function(ContainerInterface $c): RequestHandlerInterface {
            return new ListUsers(
                $c->get(EntityManager::class)
            );
        });

        $c->set(RegisterUserAction::class, static function(ContainerInterface $c): RequestHandlerInterface {
            return new RegisterUserAction(
                $c->get(EntityManager::class)
            );
        });

        $c->set(LoginUserAction::class, static function(ContainerInterface $c): RequestHandlerInterface {
            return new LoginUserAction(
                $c->get(EntityManager::class)
            );
        });

        $c->set(App::class, static function (ContainerInterface $c): App {
            /** @var array $settings */
            $settings = $c->get('settings');

            $app = AppFactory::create(null, $c);

            $app->addErrorMiddleware(
                $settings['slim']['displayErrorDetails'],
                $settings['slim']['logErrors'],
                $settings['slim']['logErrorDetails']
            );

            $app->add(new ContentLengthMiddleware());

            $app->get('/users', ListUsers::class);
            $app->post('/register', RegisterUserAction::class);
            $app->post('/login', LoginUserAction::class);
            $app->post('/chat/{username}/message', SendMessageAction::class)
                ->addMiddleware($c->get(AuthorizationMiddleware::class));

            return $app;
        });
    }
}