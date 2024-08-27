<?php

declare(strict_types=1);

namespace TaskWaveBackend\Api\Category;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TaskWaveBackend\Api\Validator\RequestValidator;
use TaskWaveBackend\Service\CategoryService;
use TaskWaveBackend\Slim\TaskWaveAction;
use TaskWaveBackend\Value\JsonResult;
use TaskWaveBackend\Value\TaskWaveResult;

class CreateCategoryAction extends TaskWaveAction
{
    public function __construct(
        private readonly CategoryService  $categoryService,
        private readonly RequestValidator $validator,
    ) {
    }

    protected function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $ownerId = (int)$request->getAttribute('ownerId') ?? null;
        $data = $request->getParsedBody();
        $jwt = $request->getAttribute('jwt');

        if ($jwt['sub'] !== $ownerId) {
            return TaskWaveResult::from(
                JsonResult::from('Unauthorized access.'),
                StatusCodeInterface::STATUS_UNAUTHORIZED
            )->getResponse($response);
        }

        $name = $data['name'] ?? null;
        $description = $data['description'] ?? null;

        $validationResult = $this->validator->validate([
            'name' => $name,
            'description' => $description,
        ]);

        if ($validationResult) {
            return TaskWaveResult::from(
                JsonResult::from('Invalid input', ['error' => $validationResult]),
                StatusCodeInterface::STATUS_BAD_REQUEST
            )->getResponse($response);
        }

        $this->categoryService->createCategorie(
            $ownerId,
            $name,
            $description,
            $data['color'] ?? null
        );

        return TaskWaveResult::from(
            JsonResult::from('Category created!'),
            StatusCodeInterface::STATUS_CREATED
        )->getResponse($response);
    }
}
