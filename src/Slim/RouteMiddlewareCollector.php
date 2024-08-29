<?php

declare(strict_types=1);

namespace TaskWaveBackend\Slim;

use Monolog\Logger;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use TaskWaveBackend\Api\Category\CreateCategoryAction;
use TaskWaveBackend\Api\Category\DeleteCategoryAction;
use TaskWaveBackend\Api\Category\EditCategoryAction;
use TaskWaveBackend\Api\Login\Action\LoginAction;
use TaskWaveBackend\Api\Register\Action\RegisterAction;
use TaskWaveBackend\Api\User\Delete\DeleteUserAction;
use TaskWaveBackend\Api\User\Edit\EditUserAction;
use TaskWaveBackend\Api\User\Password\ResetPasswordAction;
use TaskWaveBackend\Api\User\Password\UpdatePasswordAction;
use TaskWaveBackend\Slim\Middleware\AuthMiddleware;
use TaskWaveBackend\Slim\Middleware\CORSMiddleware;
use Throwable;

/**
 * @SuppressWarnings(PHPMD)
 */
class RouteMiddlewareCollector
{
    public function register(App $app): void
    {
        $app->addRoutingMiddleware();
        $app->addBodyParsingMiddleware();
        $app->add(new CORSMiddleware());
        $this->registerErrorHandler($app);
        $this->registerPreflight($app);
        $this->registerApiRoutes($app);
        $this->registerNotFoundRoutes($app);
    }

    public function registerErrorHandler(App $app): void
    {
        $container = $app->getContainer();

        $customErrorHandler = function (
            ServerRequestInterface $request,
            Throwable              $exception,
            bool                   $displayErrorDetails,
        ) use (
            $app,
            $container,
        ): ResponseInterface {
            $errorHandler = new ErrorHandler(
                $container->get(Logger::class)
            );

            $response = $app->getResponseFactory()->createResponse()->withStatus(500);
            $response = (new CorsResponseManager())->withCors($request, $response);

            return $errorHandler->handleError($exception, $response, $request, $displayErrorDetails);
        };

        $errorMiddleware = $app->addErrorMiddleware(true, true, true);
        $errorMiddleware->setDefaultErrorHandler($customErrorHandler);
        $errorMiddleware->setErrorHandler(Throwable::class, $customErrorHandler);
    }

    public function registerNotFoundRoutes(App $app): void
    {
        $app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', [$this, 'get404Response']);
    }

    public function get404Response(ResponseInterface $response): ResponseInterface
    {
        $content404 = json_encode([
            'error' => '404 Not Found',
        ]);

        $response->getBody()->write($content404);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
    }

    public function registerPreflight(App $app): void
    {
        $callback = function (ResponseInterface $response) {
            return $response;
        };

        $app->map(['OPTIONS'], '/{routes:.+}', $callback);
    }

    public function registerApiRoutes(App $app): void
    {
        $app->group('/api/v1', function (RouteCollectorProxy $app) {
            $app->post('/register', RegisterAction::class);
            $app->post('/login', LoginAction::class);

            $app->group('/user', function (RouteCollectorProxy $user) {
                $user->post('/edit/{userId:[0-9]+}', EditUserAction::class)
                    ->add(AuthMiddleware::class);
                $user->delete('/delete/{userId:[0-9]+}', DeleteUserAction::class)
                    ->add(AuthMiddleware::class);

                $user->post('/resetPassword/{email}', ResetPasswordAction::class);
                $user->post('/updatePassword', UpdatePasswordAction::class);
            });

            $app->group('/category', function (RouteCollectorProxy $category) {
                $category->post('/create/{ownerId:[0-9]+}', CreateCategoryAction::class);
                $category->post('/edit/{categoryId:[0-9]+}', EditCategoryAction::class);
                $category->delete('/delete/{categoryId:[0-9]+}', DeleteCategoryAction::class);
            })->add(AuthMiddleware::class);
        });
    }
}
