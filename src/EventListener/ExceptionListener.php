<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Exception\BusinessException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Validator\Exception\ValidationFailedException;

#[AsEventListener(event: ExceptionEvent::class, priority: 10)]
final class ExceptionListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof BusinessException) {
            $response = new JsonResponse(
                $exception->getResponseData(),
                $exception->getStatusCode()
            );

            $event->setResponse($response);
            return;
        }

        $validationException = $this->extractValidationException($exception);
        if ($validationException !== null) {
            $errors = [];
            foreach ($validationException->getViolations() as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }

            $response = new JsonResponse(
                ['errors' => $errors],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );

            $event->setResponse($response);
        }
    }

    private function extractValidationException(\Throwable $exception): null|\Throwable
    {
        if ($exception instanceof ValidationFailedException) {
            return $exception;
        }

        return $exception->getPrevious() instanceof ValidationFailedException
            ? $exception->getPrevious()
            : null;
    }
}