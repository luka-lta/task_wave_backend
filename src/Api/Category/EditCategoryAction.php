<?php

declare(strict_types=1);

namespace TaskWaveBackend\Api\Category;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TaskWaveBackend\Api\Validator\RequestValidator;
use TaskWaveBackend\Service\CategoryService;
use TaskWaveBackend\Slim\TaskWaveAction;
use TaskWaveBackend\Value\AuthToken\DecodedToken;
use TaskWaveBackend\Value\Categories\Category;
use TaskWaveBackend\Value\JsonResult;
use TaskWaveBackend\Value\TaskWaveResult;

class EditCategoryAction extends TaskWaveAction
{
    public function __construct(
        private readonly CategoryService  $categoryService,
        private readonly RequestValidator $validator,
    ) {
    }

    protected function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $categoryId = (int)$request->getAttribute('categoryId');
        $decodedToken = DecodedToken::fromArray($request->getAttribute('jwt'));
        $data = $request->getParsedBody();


        $name = $data['name'] ?? null;
        $description = $data['description'] ?? null;

        $validationResult = $this->validator->validate([
            'name' => $data['name'] ?? null,
            'description' => $data['description'] ?? null,
        ]);

        if ($validationResult) {
            return TaskWaveResult::from(
                JsonResult::from('Invalid input', ['error' => $validationResult]),
                StatusCodeInterface::STATUS_BAD_REQUEST
            )->getResponse($response);
        }

        $this->categoryService->editCategory(
            $categoryId,
            $decodedToken->getUserId(),
            $name,
            $description,
            $data['color'] ?? null
        );

        return TaskWaveResult::from(
            JsonResult::from('Category edited'),
        )->getResponse($response);
    }
}
