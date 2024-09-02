<?php

declare(strict_types=1);

namespace TaskWaveBackend\Api\Category;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TaskWaveBackend\Exception\TaskWaveCategoryNotFoundException;
use TaskWaveBackend\Service\CategoryService;
use TaskWaveBackend\Slim\TaskWaveAction;
use TaskWaveBackend\Value\AuthToken\DecodedToken;
use TaskWaveBackend\Value\JsonResult;
use TaskWaveBackend\Value\TaskWaveResult;

class GetCategories extends TaskWaveAction
{
    public function __construct(
        private readonly CategoryService $categoryService,
    ) {
    }

    protected function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $decodedToken = DecodedToken::fromArray($request->getAttribute('jwt'));

        try {
            $categories = $this->categoryService->getCategoriesByOwnerId($decodedToken->getUserId());

            $allCategories = [];

            foreach ($categories as $category) {
                $allCategories[] = $category->toArray();
            }

        } catch (TaskWaveCategoryNotFoundException $e) {
            return TaskWaveResult::from(JsonResult::from($e->getMessage(), $e->getCode()))->getResponse($response);
        }

        return TaskWaveResult::from(JsonResult::from('Categories found', [
            'categories' => $allCategories,
        ]))->getResponse($response);
    }
}
