<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * based on @see UniqueValueValidator
 */
class UniqueValueValidator extends ConstraintValidator
{
    private $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @inheritDoc
     */
    public function validate($fieldValue, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueValue) {
            throw new UnexpectedTypeException($constraint, UniqueValue::class);
        }

        if (!\is_array($constraint->fields) && !\is_string($constraint->fields)) {
            throw new UnexpectedTypeException($constraint->fields, 'array');
        }

        if (null !== $constraint->errorPath && !\is_string($constraint->errorPath)) {
            throw new UnexpectedTypeException($constraint->errorPath, 'string or null');
        }

        $fields = (array) $constraint->fields;

        if (0 === \count($fields)) {
            throw new ConstraintDefinitionException('At least one field has to be specified.');
        }

        if (null === $fieldValue) {
            return;
        }

        if ($constraint->em) {
            $em = $this->registry->getManager($constraint->em);

            if (!$em) {
                throw new ConstraintDefinitionException(\sprintf('Object manager "%s" does not exist.', $constraint->em));
            }
        } else {
            $em = $this->registry->getManagerForClass($constraint->entityClass);

            if (!$em) {
                throw new ConstraintDefinitionException(\sprintf('Unable to find the object manager associated with an entity of class "%s".', \get_class($constraint->entityClass)));
            }
        }

        $class = $em->getClassMetadata($constraint->entityClass);
        /* @var $class ClassMetadata */

        $criteria = [];

        foreach ($fields as $field) {
            if ($field instanceof UniqueValueDto) {
                $fieldName = $field->getName();
            } else {
                $fieldName = $field;
            }

            if (!$class->hasField($fieldName) && !$class->hasAssociation($fieldName)) {
                throw new ConstraintDefinitionException(
                    \sprintf('The field "%s" is not mapped by Doctrine, so it cannot be validated for uniqueness.', $fieldName));
            }

            if ($field instanceof UniqueValueDto) {
                $criteria[$field->getName()] = $field->getValue();
            } else {
                $criteria[$fieldName] = $fieldValue;
            }

            if (null !== $criteria[$fieldName] && $class->hasAssociation($fieldName)) {
                /* Ensure the Proxy is initialized before using reflection to
                 * read its identifiers. This is necessary because the wrapped
                 * getter methods in the Proxy are being bypassed.
                 */
                $em->initializeObject($criteria[$fieldName]);
            }
        }

        // skip validation if there are no criteria (this can happen when the
        // "ignoreNull" option is enabled and fields to be checked are null
        if (empty($criteria)) {
            return;
        }

        if (null !== $constraint->entityClass) {
            /* Retrieve repository from given entity name.
             * We ensure the retrieved repository can handle the entity
             * by checking the entity is the same, or subclass of the supported entity.
             */
            $repository = $em->getRepository($constraint->entityClass);
            $supportedClass = $repository->getClassName();

            if ($constraint->entityClass !== $supportedClass) {
                throw new ConstraintDefinitionException(
                    \sprintf('The "%s" entity repository does not support the "%s" entity. The entity should be an instance of or extend "%s".', $constraint->entityClass, $class->getName(), $supportedClass));
            }
        } else {
            throw new ConstraintDefinitionException(
                \sprintf(
                    'entityClass must be set in Constraint: %s',
                    \get_class
                    (
                        $constraint
                    )
                )
            );
        }

        $result = $repository->{$constraint->repositoryMethod}($criteria);

        if ($result instanceof \IteratorAggregate) {
            $result = $result->getIterator();
        }

        /* If the result is a MongoCursor, it must be advanced to the first
         * element. Rewinding should have no ill effect if $result is another
         * iterator implementation.
         */
        if ($result instanceof \Iterator) {
            $result->rewind();
            if ($result instanceof \Countable && 1 < \count($result)) {
                $result = [$result->current(), $result->current()];
            } else {
                $result = $result->current();
                $result = null === $result ? [] : [$result];
            }
        } elseif (\is_array($result)) {
            \reset($result);
        } else {
            $result = null === $result ? [] : [$result];
        }

        /* If no entity matched the query criteria or a single entity matched,
         * which is the same as the entity being validated, the criteria is
         * unique.
         */
        if (!$result) {
            return;
        }

        /**
         * do not make error for current value when updating
         */
        if ($constraint->excludeCurrent !== null && \count($result) === 1 && \current($result) === $constraint->excludeCurrent) {
            return;
        }

        $errorPath = $constraint->errorPath ?? $fields[0];
        $invalidValue = $criteria[$errorPath] ?? $criteria[$fields[0]];

        $this->context->buildViolation($constraint->message)
            ->atPath($errorPath)
            ->setParameter('{{ value }}', $this->formatWithIdentifiers($em, $class, $invalidValue))
            ->setInvalidValue($invalidValue)
            ->setCode(UniqueEntity::NOT_UNIQUE_ERROR)
            ->setCause($result)
            ->addViolation();
    }

    /**
     * @inheritDoc
     */
    private function formatWithIdentifiers(ObjectManager $em, ClassMetadata $class, $value): string
    {
        if (!\is_object($value) || $value instanceof \DateTimeInterface) {
            return $this->formatValue($value, self::PRETTY_DATE);
        }

        if (\method_exists($value, '__toString')) {
            return (string) $value;
        }

        if ($class->getName() !== $idClass = \get_class($value)) {
            // non unique value might be a composite PK that consists of other entity objects
            if ($em->getMetadataFactory()->hasMetadataFor($idClass)) {
                $identifiers = $em->getClassMetadata($idClass)->getIdentifierValues($value);
            } else {
                // this case might happen if the non unique column has a custom doctrine type and its value is an object
                // in which case we cannot get any identifiers for it
                $identifiers = [];
            }
        } else {
            $identifiers = $class->getIdentifierValues($value);
        }

        if (!$identifiers) {
            return \sprintf('object("%s")', $idClass);
        }

        \array_walk($identifiers, function (&$id, $field): void {
            if (!\is_object($id) || $id instanceof \DateTimeInterface) {
                $idAsString = $this->formatValue($id, self::PRETTY_DATE);
            } else {
                $idAsString = \sprintf('object("%s")', \get_class($id));
            }

            $id = \sprintf('%s => %s', $field, $idAsString);
        });

        return \sprintf('object("%s") identified by (%s)', $idClass, \implode(', ', $identifiers));
    }
}
