<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;
use Exception;

class BusinessException extends Exception
{
    public function __construct(
        string $message,
        private readonly string $field = 'error',
        private readonly int $statusCode = Response::HTTP_BAD_REQUEST
    ) {
        parent::__construct($message);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
    public function getResponseData(): array
    {
        return [
            'errors' => [
                $this->field => $this->getMessage()
            ]
        ];
    }
}