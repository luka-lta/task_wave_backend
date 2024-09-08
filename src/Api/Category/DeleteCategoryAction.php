<?php

declare(strict_types=1);

namespace TaskWaveBackend\Api\Category;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TaskWaveBackend\Service\CategoryService;
use TaskWaveBackend\Slim\TaskWaveAction;
use TaskWaveBackend\Value\AuthToken\DecodedToken;
use TaskWaveBackend\Value\JsonResult;
use TaskWaveBackend\Value\TaskWaveResult;

class DeleteCategoryAction extends TaskWaveAction
{
    public function __construct(
        private readonly CategoryService $categoryService,
    ) {
    }

    protected function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $categoryId = (int) $request->getAttribute('categoryId');
        $decodedToken = DecodedToken::fromArray($request->getAttribute('jwt'));

        $this->categoryService->deleteCategory($decodedToken->getUserId(), $categoryId);

        return TaskWaveResult::from(JsonResult::from('Category deleted'))->getResponse($response);
    }
}
