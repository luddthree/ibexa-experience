<?php

namespace Container2YcVERS;
include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/lib/Repository/LanguageService.php';

class LanguageService_8abd383 extends \Ibexa\Core\Repository\LanguageService implements \ProxyManager\Proxy\VirtualProxyInterface
{
    /**
     * @var \Ibexa\Core\Repository\LanguageService|null wrapped object, if the proxy is initialized
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

    public function createLanguage(\Ibexa\Contracts\Core\Repository\Values\Content\LanguageCreateStruct $languageCreateStruct) : \Ibexa\Contracts\Core\Repository\Values\Content\Language
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'createLanguage', array('languageCreateStruct' => $languageCreateStruct), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->createLanguage($languageCreateStruct);
    }

    public function updateLanguageName(\Ibexa\Contracts\Core\Repository\Values\Content\Language $language, string $newName) : \Ibexa\Contracts\Core\Repository\Values\Content\Language
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'updateLanguageName', array('language' => $language, 'newName' => $newName), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->updateLanguageName($language, $newName);
    }

    public function enableLanguage(\Ibexa\Contracts\Core\Repository\Values\Content\Language $language) : \Ibexa\Contracts\Core\Repository\Values\Content\Language
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'enableLanguage', array('language' => $language), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->enableLanguage($language);
    }

    public function disableLanguage(\Ibexa\Contracts\Core\Repository\Values\Content\Language $language) : \Ibexa\Contracts\Core\Repository\Values\Content\Language
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'disableLanguage', array('language' => $language), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->disableLanguage($language);
    }

    public function loadLanguage(string $languageCode) : \Ibexa\Contracts\Core\Repository\Values\Content\Language
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadLanguage', array('languageCode' => $languageCode), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadLanguage($languageCode);
    }

    public function loadLanguages() : iterable
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadLanguages', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadLanguages();
    }

    public function loadLanguageById(int $languageId) : \Ibexa\Contracts\Core\Repository\Values\Content\Language
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadLanguageById', array('languageId' => $languageId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadLanguageById($languageId);
    }

    public function loadLanguageListByCode(array $languageCodes) : iterable
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadLanguageListByCode', array('languageCodes' => $languageCodes), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadLanguageListByCode($languageCodes);
    }

    public function loadLanguageListById(array $languageIds) : iterable
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadLanguageListById', array('languageIds' => $languageIds), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadLanguageListById($languageIds);
    }

    public function deleteLanguage(\Ibexa\Contracts\Core\Repository\Values\Content\Language $language) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'deleteLanguage', array('language' => $language), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->deleteLanguage($language);
return;
    }

    public function getDefaultLanguageCode() : string
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getDefaultLanguageCode', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getDefaultLanguageCode();
    }

    public function getPrioritizedLanguageCodeList()
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getPrioritizedLanguageCodeList', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getPrioritizedLanguageCodeList();
    }

    public function newLanguageCreateStruct() : \Ibexa\Contracts\Core\Repository\Values\Content\LanguageCreateStruct
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'newLanguageCreateStruct', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->newLanguageCreateStruct();
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

        unset($instance->repository, $instance->languageHandler, $instance->settings);

        \Closure::bind(function (\Ibexa\Core\Repository\LanguageService $instance) {
            unset($instance->permissionResolver);
        }, $instance, 'Ibexa\\Core\\Repository\\LanguageService')->__invoke($instance);

        $instance->initializerf0d6f = $initializer;

        return $instance;
    }

    public function __construct(\Ibexa\Contracts\Core\Repository\Repository $repository, \Ibexa\Contracts\Core\Persistence\Content\Language\Handler $languageHandler, \Ibexa\Contracts\Core\Repository\PermissionResolver $permissionResolver, array $settings = [])
    {
        static $reflection;

        if (! $this->valueHolder3c8ba) {
            $reflection = $reflection ?? new \ReflectionClass('Ibexa\\Core\\Repository\\LanguageService');
            $this->valueHolder3c8ba = $reflection->newInstanceWithoutConstructor();
        unset($this->repository, $this->languageHandler, $this->settings);

        \Closure::bind(function (\Ibexa\Core\Repository\LanguageService $instance) {
            unset($instance->permissionResolver);
        }, $this, 'Ibexa\\Core\\Repository\\LanguageService')->__invoke($this);

        }

        $this->valueHolder3c8ba->__construct($repository, $languageHandler, $permissionResolver, $settings);
    }

    public function & __get($name)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, '__get', ['name' => $name], $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        if (isset(self::$publicProperties2eb7b[$name])) {
            return $this->valueHolder3c8ba->$name;
        }

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\Repository\\LanguageService');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\Repository\\LanguageService');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\Repository\\LanguageService');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\Repository\\LanguageService');

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
        unset($this->repository, $this->languageHandler, $this->settings);

        \Closure::bind(function (\Ibexa\Core\Repository\LanguageService $instance) {
            unset($instance->permissionResolver);
        }, $this, 'Ibexa\\Core\\Repository\\LanguageService')->__invoke($this);
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

if (!\class_exists('LanguageService_8abd383', false)) {
    \class_alias(__NAMESPACE__.'\\LanguageService_8abd383', 'LanguageService_8abd383', false);
}
