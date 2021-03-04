<?php

declare(strict_types=1);

namespace App\Service\System\HealthCheck\HealthChecker\Settings\Mock;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\RequestHandlerInterface;
use Symfony\Component\Form\ResolvedFormTypeInterface;

/**
 * @psalm-suppress all
 * @noinspection ALL
 * @codingStandardsIgnoreStart
 * @phpstan-ignore-next-line
 */
class MockFormBuilder implements \IteratorAggregate, FormBuilderInterface
{
    /**
     * @var array<string,array>
     */
    public $formChildrenList = [];

    /**
     * @param FormBuilderInterface|string $child
     * @param null|mixed|string $type
     * @param array<array-key,mixed> $options
     */
    public function add($child, $type = null, array $options = []): FormBuilderInterface
    {
        if ($child instanceof FormBuilderInterface) {
            $childName = $child->getName();
        } else {
            $childName = $child;
        }
        $this->formChildrenList[$childName] = $options;

        return $this;
    }

    public function count(): int
    {
        return 0;
    }

    public function create($name, $type = null, array $options = []): void
    {
    }

    public function get($name): void
    {
    }

    public function remove($name): void
    {
    }

    public function has($name): void
    {
    }

    public function all(): void
    {
    }

    public function getForm(): void
    {
    }

    public function addEventListener($eventName, $listener, $priority = 0): void
    {
    }

    public function addEventSubscriber(EventSubscriberInterface $subscriber): void
    {
    }

    public function addViewTransformer(DataTransformerInterface $viewTransformer, $forcePrepend = false): void
    {
    }

    public function resetViewTransformers(): void
    {
    }

    public function addModelTransformer(DataTransformerInterface $modelTransformer, $forceAppend = false): void
    {
    }

    public function resetModelTransformers(): void
    {
    }

    public function setAttribute($name, $value): void
    {
    }

    public function setAttributes(array $attributes): void
    {
    }

    public function setDataMapper(DataMapperInterface $dataMapper = null): void
    {
    }

    public function setDisabled($disabled): void
    {
    }

    public function setEmptyData($emptyData): void
    {
    }

    public function setErrorBubbling($errorBubbling): void
    {
    }

    public function setRequired($required): void
    {
    }

    public function setPropertyPath($propertyPath): void
    {
    }

    public function setMapped($mapped): void
    {
    }

    public function setByReference($byReference): void
    {
    }

    public function setInheritData($inheritData): void
    {
    }

    public function setCompound($compound): void
    {
    }

    public function setType(ResolvedFormTypeInterface $type): void
    {
    }

    public function setData($data): void
    {
    }

    public function setDataLocked($locked): void
    {
    }

    public function setFormFactory(FormFactoryInterface $formFactory): void
    {
    }

    public function setAction($action): void
    {
    }

    public function setMethod($method): void
    {
    }

    public function setRequestHandler(RequestHandlerInterface $requestHandler): void
    {
    }

    public function setAutoInitialize($initialize): void
    {
    }

    public function getFormConfig(): void
    {
    }

    public function getEventDispatcher(): void
    {
    }

    public function getName(): void
    {
    }

    public function getPropertyPath(): void
    {
    }

    public function getMapped(): void
    {
    }

    public function getByReference(): void
    {
    }

    public function getInheritData(): void
    {
    }

    public function getCompound(): void
    {
    }

    public function getType(): void
    {
    }

    public function getViewTransformers(): void
    {
    }

    public function getModelTransformers(): void
    {
    }

    public function getDataMapper(): void
    {
    }

    public function getRequired(): void
    {
    }

    public function getDisabled(): void
    {
    }

    public function getErrorBubbling(): void
    {
    }

    public function getEmptyData(): void
    {
    }

    public function getAttributes(): void
    {
    }

    public function hasAttribute($name): void
    {
    }

    public function getAttribute($name, $default = null): void
    {
    }

    public function getData(): void
    {
    }

    public function getDataClass(): void
    {
    }

    public function getDataLocked(): void
    {
    }

    public function getFormFactory(): void
    {
    }

    public function getAction(): void
    {
    }

    public function getMethod(): void
    {
    }

    public function getRequestHandler(): void
    {
    }

    public function getAutoInitialize(): void
    {
    }

    public function getOptions(): void
    {
    }

    public function hasOption($name): void
    {
    }

    public function getOption($name, $default = null): void
    {
    }

    public function getIterator(): void
    {
    }
}
