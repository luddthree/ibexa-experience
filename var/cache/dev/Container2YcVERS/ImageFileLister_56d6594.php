<?php

namespace Container2YcVERS;
include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/bundle/IO/Migration/MigrationHandlerInterface.php';
include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/bundle/IO/Migration/MigrationHandler.php';
include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/bundle/IO/Migration/FileListerInterface.php';
include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/bundle/IO/Migration/FileLister/ImageFileLister.php';

class ImageFileLister_56d6594 extends \Ibexa\Bundle\IO\Migration\FileLister\ImageFileLister implements \ProxyManager\Proxy\VirtualProxyInterface
{
    /**
     * @var \Ibexa\Bundle\IO\Migration\FileLister\ImageFileLister|null wrapped object, if the proxy is initialized
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

    public function countFiles()
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'countFiles', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->countFiles();
    }

    public function loadMetadataList($limit = null, $offset = null)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadMetadataList', array('limit' => $limit, 'offset' => $offset), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadMetadataList($limit, $offset);
    }

    public function setIODataHandlersByIdentifiers($fromMetadataHandlerIdentifier, $fromBinarydataHandlerIdentifier, $toMetadataHandlerIdentifier, $toBinarydataHandlerIdentifier)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'setIODataHandlersByIdentifiers', array('fromMetadataHandlerIdentifier' => $fromMetadataHandlerIdentifier, 'fromBinarydataHandlerIdentifier' => $fromBinarydataHandlerIdentifier, 'toMetadataHandlerIdentifier' => $toMetadataHandlerIdentifier, 'toBinarydataHandlerIdentifier' => $toBinarydataHandlerIdentifier), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->setIODataHandlersByIdentifiers($fromMetadataHandlerIdentifier, $fromBinarydataHandlerIdentifier, $toMetadataHandlerIdentifier, $toBinarydataHandlerIdentifier);
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

        unset($instance->fromMetadataHandler, $instance->fromBinarydataHandler, $instance->toMetadataHandler, $instance->toBinarydataHandler);

        \Closure::bind(function (\Ibexa\Bundle\IO\Migration\FileLister\ImageFileLister $instance) {
            unset($instance->imageFileList, $instance->variationPathGenerator, $instance->filterConfiguration, $instance->imagesDir);
        }, $instance, 'Ibexa\\Bundle\\IO\\Migration\\FileLister\\ImageFileLister')->__invoke($instance);

        \Closure::bind(function (\Ibexa\Bundle\IO\Migration\MigrationHandler $instance) {
            unset($instance->metadataHandlerRegistry, $instance->binarydataHandlerRegistry, $instance->logger);
        }, $instance, 'Ibexa\\Bundle\\IO\\Migration\\MigrationHandler')->__invoke($instance);

        $instance->initializerf0d6f = $initializer;

        return $instance;
    }

    public function __construct(\Ibexa\Bundle\IO\ApiLoader\HandlerRegistry $metadataHandlerRegistry, \Ibexa\Bundle\IO\ApiLoader\HandlerRegistry $binarydataHandlerRegistry, ?\Psr\Log\LoggerInterface $logger, \Iterator $imageFileList, \Ibexa\Contracts\Core\Variation\VariationPathGenerator $variationPathGenerator, \Liip\ImagineBundle\Imagine\Filter\FilterConfiguration $filterConfiguration, $imagesDir)
    {
        static $reflection;

        if (! $this->valueHolder3c8ba) {
            $reflection = $reflection ?? new \ReflectionClass('Ibexa\\Bundle\\IO\\Migration\\FileLister\\ImageFileLister');
            $this->valueHolder3c8ba = $reflection->newInstanceWithoutConstructor();
        unset($this->fromMetadataHandler, $this->fromBinarydataHandler, $this->toMetadataHandler, $this->toBinarydataHandler);

        \Closure::bind(function (\Ibexa\Bundle\IO\Migration\FileLister\ImageFileLister $instance) {
            unset($instance->imageFileList, $instance->variationPathGenerator, $instance->filterConfiguration, $instance->imagesDir);
        }, $this, 'Ibexa\\Bundle\\IO\\Migration\\FileLister\\ImageFileLister')->__invoke($this);

        \Closure::bind(function (\Ibexa\Bundle\IO\Migration\MigrationHandler $instance) {
            unset($instance->metadataHandlerRegistry, $instance->binarydataHandlerRegistry, $instance->logger);
        }, $this, 'Ibexa\\Bundle\\IO\\Migration\\MigrationHandler')->__invoke($this);

        }

        $this->valueHolder3c8ba->__construct($metadataHandlerRegistry, $binarydataHandlerRegistry, $logger, $imageFileList, $variationPathGenerator, $filterConfiguration, $imagesDir);
    }

    public function & __get($name)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, '__get', ['name' => $name], $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        if (isset(self::$publicProperties2eb7b[$name])) {
            return $this->valueHolder3c8ba->$name;
        }

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Bundle\\IO\\Migration\\FileLister\\ImageFileLister');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Bundle\\IO\\Migration\\FileLister\\ImageFileLister');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Bundle\\IO\\Migration\\FileLister\\ImageFileLister');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Bundle\\IO\\Migration\\FileLister\\ImageFileLister');

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
        unset($this->fromMetadataHandler, $this->fromBinarydataHandler, $this->toMetadataHandler, $this->toBinarydataHandler);

        \Closure::bind(function (\Ibexa\Bundle\IO\Migration\FileLister\ImageFileLister $instance) {
            unset($instance->imageFileList, $instance->variationPathGenerator, $instance->filterConfiguration, $instance->imagesDir);
        }, $this, 'Ibexa\\Bundle\\IO\\Migration\\FileLister\\ImageFileLister')->__invoke($this);

        \Closure::bind(function (\Ibexa\Bundle\IO\Migration\MigrationHandler $instance) {
            unset($instance->metadataHandlerRegistry, $instance->binarydataHandlerRegistry, $instance->logger);
        }, $this, 'Ibexa\\Bundle\\IO\\Migration\\MigrationHandler')->__invoke($this);
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

if (!\class_exists('ImageFileLister_56d6594', false)) {
    \class_alias(__NAMESPACE__.'\\ImageFileLister_56d6594', 'ImageFileLister_56d6594', false);
}
