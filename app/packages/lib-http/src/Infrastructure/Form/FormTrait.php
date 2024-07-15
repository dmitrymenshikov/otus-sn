<?php

declare(strict_types=1);

namespace Hustle\Http\Infrastructure\Form;

use Hustle\Http\Presentation\Response\JsonResponse;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolation;

trait FormTrait
{
    protected function createFormValidationErrorResponse(FormInterface $form): JsonResponse
    {
        $violations = $this->serializeFormErrors($form);

        return $this->createErrorResponse(
        Response::HTTP_BAD_REQUEST,
            [
                'title' => 'Validation Failed',
                'detail' => \implode("\n", \array_map(function (array $error): string {
                    return $error['title'];
                }, $violations)),
            ],
            $violations
        );
    }

    protected function serializeFormErrors(FormInterface $form): array
    {
        return $this->_serializeFormErrors($form->getErrors(true, false));
    }

    private function _serializeFormErrors(FormErrorIterator $iterator, array $paths = []): array
    {
        if ('' !== $name = $iterator->getForm()->getName()) {
            $paths[] = $name;
        }
        $id = \implode('_', $paths);
        $path = \implode('.', $paths);

        $errors = [];
        foreach ($iterator as $formErrorIterator) {
            if ($formErrorIterator instanceof FormErrorIterator) {
                $errors = \array_merge($errors, $this->_serializeFormErrors($formErrorIterator, $paths));
                continue;
            }

            /* @var FormError $formErrorIterator */
            $violationEntry = [
                'id' => $id,
                'title' => $formErrorIterator->getMessage(),
                'parameters' => $formErrorIterator->getMessageParameters(),
                'propertyPath' => $path,
            ];

            $cause = $formErrorIterator->getCause();
            if ($cause instanceof ConstraintViolation) {
                if (null !== $code = $cause->getCode()) {
                    $violationEntry['type'] = \sprintf('urn:uuid:%s', $code);
                }
            }
            $errors[] = $violationEntry;
        }

        return $errors;
    }
}