<?php

namespace Container2YcVERS;
include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/lib/IO/IOServiceInterface.php';
include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/contracts/MVC/EventSubscriber/ConfigScopeChangeSubscriber.php';
include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/lib/IO/ConfigScopeChangeAwareIOService.php';

class ConfigScopeChangeAwareIOService_a337c72 extends \Ibexa\Core\IO\ConfigScopeChangeAwareIOService implements \ProxyManager\Proxy\VirtualProxyInterface
{
    /**
     * @var \Ibexa\Core\IO\ConfigScopeChangeAwareIOService|null wrapped object, if the proxy is initialized
     */
    private $valueHolder3c8ba = null;

    /**
     * @var \Closure|null initializer responsible for generating the wrapped object
     */
    private $initializerf0d6f = null;

    /**
     * @var bool[] map of public properties of the parent class
     */
    private static $publicProperties2eb7b = [
        
    ];

    public function setPrefix($prefix) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'setPrefix', array('prefix' => $prefix), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->setPrefix($prefix);
return;
    }

    public function getExternalPath($internalId) : string
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getExternalPath', array('internalId' => $internalId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getExternalPath($internalId);
    }

    public function newBinaryCreateStructFromLocalFile($localFile) : \Ibexa\Core\IO\Values\BinaryFileCreateStruct
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'newBinaryCreateStructFromLocalFile', array('localFile' => $localFile), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->newBinaryCreateStructFromLocalFile($localFile);
    }

    public function exists($binaryFileId) : bool
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'exists', array('binaryFileId' => $binaryFileId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->exists($binaryFileId);
    }

    public function getInternalPath($externalId) : string
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getInternalPath', array('externalId' => $externalId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getInternalPath($externalId);
    }

    public function loadBinaryFile($binaryFileId) : \Ibexa\Core\IO\Values\BinaryFile
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadBinaryFile', array('binaryFileId' => $binaryFileId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadBinaryFile($binaryFileId);
    }

    public function loadBinaryFileByUri($binaryFileUri) : \Ibexa\Core\IO\Values\BinaryFile
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadBinaryFileByUri', array('binaryFileUri' => $binaryFileUri), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadBinaryFileByUri($binaryFileUri);
    }

    public function getFileContents(\Ibexa\Core\IO\Values\BinaryFile $binaryFile) : string
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getFileContents', array('binaryFile' => $binaryFile), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getFileContents($binaryFile);
    }

    public function createBinaryFile(\Ibexa\Core\IO\Values\BinaryFileCreateStruct $binaryFileCreateStruct) : \Ibexa\Core\IO\Values\BinaryFile
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'createBinaryFile', array('binaryFileCreateStruct' => $binaryFileCreateStruct), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->createBinaryFile($binaryFileCreateStruct);
    }

    public function getUri($binaryFileId) : string
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getUri', array('binaryFileId' => $binaryFileId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getUri($binaryFileId);
    }

    public function getMimeType($binaryFileId) : ?string
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getMimeType', array('binaryFileId' => $binaryFileId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getMimeType($binaryFileId);
    }

    public function getFileInputStream(\Ibexa\Core\IO\Values\BinaryFile $binaryFile)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getFileInputStream', array('binaryFile' => $binaryFile), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getFileInputStream($binaryFile);
    }

    public function deleteBinaryFile(\Ibexa\Core\IO\Values\BinaryFile $binaryFile) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'deleteBinaryFile', array('binaryFile' => $binaryFile), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->deleteBinaryFile($binaryFile);
return;
    }

    public function newBinaryCreateStructFromUploadedFile(array $uploadedFile) : \Ibexa\Core\IO\Values\BinaryFileCreateStruct
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'newBinaryCreateStructFromUploadedFile', array('uploadedFile' => $uploadedFile), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->newBinaryCreateStructFromUploadedFile($uploadedFile);
    }

    public function deleteDirectory($path) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'deleteDirectory', array('path' => $path), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->deleteDirectory($path);
return;
    }

    public function onConfigScopeChange(\Ibexa\Core\MVC\Symfony\Event\ScopeChangeEvent $event) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'onConfigScopeChange', array('event' => $event), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->onConfigScopeChange($event);
return;
    }

    /**
     * Constructor for lazy initialization
     *
     * @param \Closure|null $initializer
     */
    public static function staticProxyConstructor($initializer)
    {
        static $reflection;

        $reflection = $reflection ?? new \ReflectionClass(__CLASS__);
        $instance   = $reflection->newInstanceWithoutConstructor();

        \Closure::bind(function (\Ibexa\Core\IO\ConfigScopeChangeAwareIOService $instance) {
            unset($instance->configResolver, $instance->innerIOService, $instance->prefixParameterName);
        }, $instance, 'Ibexa\\Core\\IO\\ConfigScopeChangeAwareIOService')->__invoke($instance);

        $instance->initializerf0d6f = $initializer;

        return $instance;
    }

    public function __construct(\Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface $configResolver, \Ibexa\Core\IO\IOServiceInterface $innerIOService, string $prefixParameterName)
    {
        static $reflection;

        if (! $this->valueHolder3c8ba) {
            $reflection = $reflection ?? new \ReflectionClass('Ibexa\\Core\\IO\\ConfigScopeChangeAwareIOService');
            $this->valueHolder3c8ba = $reflection->newInstanceWithoutConstructor();
        \Closure::bind(function (\Ibexa\Core\IO\ConfigScopeChangeAwareIOService $instance) {
            unset($instance->configResolver, $instance->innerIOService, $instance->prefixParameterName);
        }, $this, 'Ibexa\\Core\\IO\\ConfigScopeChangeAwareIOService')->__invoke($this);

        }

        $this->valueHolder3c8ba->__construct($configResolver, $innerIOService, $prefixParameterName);
    }

    public function & __get($name)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, '__get', ['name' => $name], $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        if (isset(self::$publicProperties2eb7b[$name])) {
            return $this->valueHolder3c8ba->$name;
        }

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\IO\\ConfigScopeChangeAwareIOService');

        if (! $realInstanceReflection->hasProperty($name)) {
            $targetObject = $this->valueHolder3c8ba;

            $backtrace = debug_backtrace(false, 1);
            trigger_error(
                sprintf(
                    'Undefined property: %s::$%s in %s on line %s',
                    $realInstanceReflection->getName(),
                    $name,
                    $backtrace[0]['file'],
                    $backtrace[0]['line']
                ),
                \E_USER_NOTICE
            );
            return $targetObject->$name;
        }

        $targetObject = $this->valueHolder3c8ba;
        $accessor = function & () use ($targetObject, $name) {
            return $targetObject->$name;
        };
        $backtrace = debug_backtrace(true, 2);
        $scopeObject = isset($backtrace[1]['object']) ? $backtrace[1]['object'] : new \ProxyManager\Stub\EmptyClassStub();
        $accessor = $accessor->bindTo($scopeObject, get_class($scopeObject));
        $returnValue = & $accessor();

        return $returnValue;
    }

    public function __set($name, $value)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, '__set', array('name' => $name, 'value' => $value), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\IO\\ConfigScopeChangeAwareIOService');

        if (! $realInstanceReflection->hasProperty($name)) {
            $targetObject = $this->valueHolder3c8ba;

            $targetObject->$name = $value;

            return $targetObject->$name;
        }

        $targetObject = $this->valueHolder3c8ba;
        $accessor = function & () use ($targetObject, $name, $value) {
            $targetObject->$name = $value;

            return $targetObject->$name;
        };
        $backtrace = debug_backtrace(true, 2);
        $scopeObject = isset($backtrace[1]['object']) ? $backtrace[1]['object'] : new \ProxyManager\Stub\EmptyClassStub();
        $accessor = $accessor->bindTo($scopeObject, get_class($scopeObject));
        $returnValue = & $accessor();

        return $returnValue;
    }

    public function __isset($name)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, '__isset', array('name' => $name), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\IO\\ConfigScopeChangeAwareIOService');

        if (! $realInstanceReflection->hasProperty($name)) {
            $targetObject = $this->valueHolder3c8ba;

            return isset($targetObject->$name);
        }

        $targetObject = $this->valueHolder3c8ba;
        $accessor = function () use ($targetObject, $name) {
            return isset($targetObject->$name);
        };
        $backtrace = debug_backtrace(true, 2);
        $scopeObject = isset($backtrace[1]['object']) ? $backtrace[1]['object'] : new \ProxyManager\Stub\EmptyClassStub();
        $accessor = $accessor->bindTo($scopeObject, get_class($scopeObject));
        $returnValue = $accessor();

        return $returnValue;
    }

    public function __unset($name)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, '__unset', array('name' => $name), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\IO\\ConfigScopeChangeAwareIOService');

        if (! $realInstanceReflection->hasProperty($name)) {
            $targetObject = $this->valueHolder3c8ba;

            unset($targetObject->$name);

            return;
        }

        $targetObject = $this->valueHolder3c8ba;
        $accessor = function () use ($targetObject, $name) {
            unset($targetObject->$name);

            return;
        };
        $backtrace = debug_backtrace(true, 2);
        $scopeObject = isset($backtrace[1]['object']) ? $backtrace[1]['object'] : new \ProxyManager\Stub\EmptyClassStub();
        $accessor = $accessor->bindTo($scopeObject, get_class($scopeObject));
        $accessor();
    }

    public function __clone()
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, '__clone', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba = clone $this->valueHolder3c8ba;
    }

    public function __sleep()
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, '__sleep', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return array('valueHolder3c8ba');
    }

    public function __wakeup()
    {
        \Closure::bind(function (\Ibexa\Core\IO\ConfigScopeChangeAwareIOService $instance) {
            unset($instance->configResolver, $instance->innerIOService, $instance->prefixParameterName);
        }, $this, 'Ibexa\\Core\\IO\\ConfigScopeChangeAwareIOService')->__invoke($this);
    }

    public function setProxyInitializer(?\Closure $initializer = null) : void
    {
        $this->initializerf0d6f = $initializer;
    }

    public function getProxyInitializer() : ?\Closure
    {
        return $this->initializerf0d6f;
    }

    public function initializeProxy() : bool
    {
        return $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'initializeProxy', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;
    }

    public function isProxyInitialized() : bool
    {
        return null !== $this->valueHolder3c8ba;
    }

    public function getWrappedValueHolderValue()
    {
        return $this->valueHolder3c8ba;
    }
}

if (!\class_exists('ConfigScopeChangeAwareIOService_a337c72', false)) {
    \class_alias(__NAMESPACE__.'\\ConfigScopeChangeAwareIOService_a337c72', 'ConfigScopeChangeAwareIOService_a337c72', false);
}
