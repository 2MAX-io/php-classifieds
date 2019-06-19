<?php /** @noinspection PhpInconsistentReturnPointsInspection */

/** @noinspection ReturnTypeCanBeDeclaredInspection */

/** @noinspection PhpCSValidationInspection */

declare(strict_types=1);

namespace App\Service\System\HealthCheck\HealthChecker\Settings;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\RequestHandlerInterface;
use Symfony\Component\Form\ResolvedFormTypeInterface;

class MockFormBuilder implements \IteratorAggregate, FormBuilderInterface
{
    public $formChildrenList = [];

    public function add($child, $type = null, array $options = [])
    {
        $this->formChildrenList[$child] = $options;
    }

    public function count() { }

    public function create($name, $type = null, array $options = []) { }

    public function get($name) { }

    public function remove($name) { }

    public function has($name) { }

    public function all() { }

    public function getForm() { }

    public function addEventListener($eventName, $listener, $priority = 0) { }

    public function addEventSubscriber(EventSubscriberInterface $subscriber) { }

    public function addViewTransformer(DataTransformerInterface $viewTransformer, $forcePrepend = false) { }

    public function resetViewTransformers() { }

    public function addModelTransformer(DataTransformerInterface $modelTransformer, $forceAppend = false) { }

    public function resetModelTransformers() { }

    public function setAttribute($name, $value) { }

    public function setAttributes(array $attributes) { }

    public function setDataMapper(DataMapperInterface $dataMapper = null) { }

    public function setDisabled($disabled) { }

    public function setEmptyData($emptyData) { }

    public function setErrorBubbling($errorBubbling) { }

    public function setRequired($required) { }

    public function setPropertyPath($propertyPath) { }

    public function setMapped($mapped) { }

    public function setByReference($byReference) { }

    public function setInheritData($inheritData) { }

    public function setCompound($compound) { }

    public function setType(ResolvedFormTypeInterface $type) { }

    public function setData($data) { }

    public function setDataLocked($locked) { }

    public function setFormFactory(FormFactoryInterface $formFactory) { }

    public function setAction($action) { }

    public function setMethod($method) { }

    public function setRequestHandler(RequestHandlerInterface $requestHandler) { }

    public function setAutoInitialize($initialize) { }

    public function getFormConfig() { }

    public function getEventDispatcher() { }

    public function getName() { }

    public function getPropertyPath() { }

    public function getMapped() { }

    public function getByReference() { }

    public function getInheritData() { }

    public function getCompound() { }

    public function getType() { }

    public function getViewTransformers() { }

    public function getModelTransformers() { }

    public function getDataMapper() { }

    public function getRequired() { }

    public function getDisabled() { }

    public function getErrorBubbling() { }

    public function getEmptyData() { }

    public function getAttributes() { }

    public function hasAttribute($name) { }

    public function getAttribute($name, $default = null) { }

    public function getData() { }

    public function getDataClass() { }

    public function getDataLocked() { }

    public function getFormFactory() { }

    public function getAction() { }

    public function getMethod() { }

    public function getRequestHandler() { }

    public function getAutoInitialize() { }

    public function getOptions() { }

    public function hasOption($name) { }

    public function getOption($name, $default = null) { }

    public function getIterator() { }
}
