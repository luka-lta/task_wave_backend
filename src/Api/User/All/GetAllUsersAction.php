<?php

declare(strict_types=1);

namespace TaskWaveBackend\Api\User\All;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TaskWaveBackend\Service\UserService;
use TaskWaveBackend\Slim\TaskWaveAction;
use TaskWaveBackend\Value\AuthToken\DecodedToken;
use TaskWaveBackend\Value\JsonResult;
use TaskWaveBackend\Value\TaskWaveResult;

class GetAllUsersAction extends TaskWaveAction
{
    public function __construct(
        private readonly UserService $userService,
    ) {
    }

    protected function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $decodedToken = DecodedToken::fromArray($request->getAttribute('jwt'));
        $page = (int)$request->getQueryParams()['page'] ?? 1;
        $pageSize = (int)$request->getQueryParams()['pageSize'] ?? 10;

        $users = $this->userService->getAll($decodedToken->getUserId(), $page, $pageSize);

        if ($users->getTotalRecords() === 0) {
            return TaskWaveResult::from(
                JsonResult::from('No users found.'),
                StatusCodeInterface::STATUS_NOT_FOUND,
            )->getResponse($response);
        }

        if ($page < 1 || $page > $users->getMaxPages()) {
            return TaskWaveResult::from(
                JsonResult::from('Invalid page number.'),
                StatusCodeInterface::STATUS_BAD_REQUEST
            )->getResponse($response);
        }

        return TaskWaveResult::from(
            JsonResult::from('Users fetched successfully.', [
                'users' => $users->getData(),
                'pagination' => [
                    'page' => $users->getPage()->getPageNumber(),
                    'pageSize' => $users->getPageSize()->getPageSize(),
                    'totalRecords' => $users->getTotalRecords(),
                    'maxPages' => $users->getMaxPages(),
                    'minPages' => $users->getMinPages(),
                ]
            ]),
        )->getResponse($response);
    }
}
