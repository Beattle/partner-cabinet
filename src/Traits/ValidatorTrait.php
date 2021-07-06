<?php

namespace App\Traits;

use Nalogka\ApiExceptions\Response\DuplicateError;
use Nalogka\ApiExceptions\Response\ValidationError;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Validator\Constraints\GroupSequence;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

trait ValidatorTrait
{
    /** @var NameConverterInterface */
    protected $nameConverter;

    /** @var ValidatorInterface */
    protected $validator;

    /**
     * @param ValidatorInterface $validator
     * @required
     */
    public function setValidator(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param NameConverterInterface $nameConverter
     * @required
     */
    public function setNameConverter(NameConverterInterface $nameConverter)
    {
        $this->nameConverter = $nameConverter;
    }

    /**
     * @param $entity
     * @param string|GroupSequence|(string|GroupSequence)[]|null $validationGroups  Группы валидации. Если не переданы, будет использовано "Default" (@see https://symfony.com/doc/current/validation/groups.html)
     */
    public function validateAndThrowError($entity, $validationGroups = null): void
    {
        $errors = $this->validator->validate($entity, null, $validationGroups);

        if ($errors->count() > 0) {
            throw $this->createValidationError($errors);
        }
    }

    public function createValidationError(ConstraintViolationListInterface $errorsContainer)
    {
        $response = new ValidationError();
        foreach ($errorsContainer as $validationError) {
            /* @var $validationError ConstraintViolation */
            if ($validationError->getConstraint() instanceof UniqueEntity) {
                return new DuplicateError($validationError->getMessage(), $validationError->getCause()[0]);
            }

            $pathParts = explode('.', $validationError->getPropertyPath());
            foreach ($pathParts as $pathKey => $pathPart) {
                $pathParts[$pathKey] = $this->nameConverter->normalize($pathPart);
            }

            $response->addError(implode('.', $pathParts), $validationError->getMessage());
        }

        return $response;
    }
}