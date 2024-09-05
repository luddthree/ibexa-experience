<?php

namespace Container2YcVERS;
include_once \dirname(__DIR__, 4).'/vendor/ibexa/admin-ui/src/lib/UI/Value/ValueFactory.php';

class ValueFactory_15bf3b0 extends \Ibexa\AdminUi\UI\Value\ValueFactory implements \ProxyManager\Proxy\VirtualProxyInterface
{
    /**
     * @var \Ibexa\AdminUi\UI\Value\ValueFactory|null wrapped object, if the proxy is initialized
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

    public function createVersionInfo(\Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo $versionInfo) : \Ibexa\AdminUi\UI\Value\Content\VersionInfo
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'createVersionInfo', array('versionInfo' => $versionInfo), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->createVersionInfo($versionInfo);
    }

    public function createLanguage(\Ibexa\Contracts\Core\Repository\Values\Content\Language $language, \Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo $versionInfo) : \Ibexa\AdminUi\UI\Value\Content\Language
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'createLanguage', array('language' => $language, 'versionInfo' => $versionInfo), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->createLanguage($language, $versionInfo);
    }

    public function createRelation(\Ibexa\Contracts\Core\Repository\Values\Content\Relation $relation, \Ibexa\Contracts\Core\Repository\Values\Content\Content $content) : \Ibexa\AdminUi\UI\Value\Content\Relation
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'createRelation', array('relation' => $relation, 'content' => $content), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->createRelation($relation, $content);
    }

    public function createRelationItem(\Ibexa\Contracts\Core\Repository\Values\Content\RelationList\Item\RelationListItem $relationListItem, \Ibexa\Contracts\Core\Repository\Values\Content\Content $content) : \Ibexa\AdminUi\UI\Value\Content\Relation
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'createRelationItem', array('relationListItem' => $relationListItem, 'content' => $content), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->createRelationItem($relationListItem, $content);
    }

    public function createUnauthorizedRelationItem(\Ibexa\Contracts\Core\Repository\Values\Content\RelationList\Item\UnauthorizedRelationListItem $relationListItem) : \Ibexa\AdminUi\UI\Value\Content\RelationInterface
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'createUnauthorizedRelationItem', array('relationListItem' => $relationListItem), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->createUnauthorizedRelationItem($relationListItem);
    }

    public function createLocation(\Ibexa\Contracts\Core\Repository\Values\Content\Location $location) : \Ibexa\AdminUi\UI\Value\Content\Location
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'createLocation', array('location' => $location), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->createLocation($location);
    }

    public function createObjectState(\Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo $contentInfo, \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroup $objectStateGroup) : \Ibexa\AdminUi\UI\Value\ObjectState\ObjectState
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'createObjectState', array('contentInfo' => $contentInfo, 'objectStateGroup' => $objectStateGroup), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->createObjectState($contentInfo, $objectStateGroup);
    }

    public function createUrlAlias(\Ibexa\Contracts\Core\Repository\Values\Content\URLAlias $urlAlias) : \Ibexa\AdminUi\UI\Value\Content\UrlAlias
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'createUrlAlias', array('urlAlias' => $urlAlias), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->createUrlAlias($urlAlias);
    }

    public function createRole(\Ibexa\Contracts\Core\Repository\Values\User\RoleAssignment $roleAssignment) : \Ibexa\AdminUi\UI\Value\User\Role
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'createRole', array('roleAssignment' => $roleAssignment), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->createRole($roleAssignment);
    }

    public function createPolicy(\Ibexa\Contracts\Core\Repository\Values\User\Policy $policy, \Ibexa\Contracts\Core\Repository\Values\User\RoleAssignment $roleAssignment) : \Ibexa\AdminUi\UI\Value\User\Policy
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'createPolicy', array('policy' => $policy, 'roleAssignment' => $roleAssignment), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->createPolicy($policy, $roleAssignment);
    }

    public function createBookmark(\Ibexa\Contracts\Core\Repository\Values\Content\Location $location) : \Ibexa\AdminUi\UI\Value\Location\Bookmark
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'createBookmark', array('location' => $location), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->createBookmark($location);
    }

    public function createLanguageFromContentType(\Ibexa\Contracts\Core\Repository\Values\Content\Language $language, \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType) : \Ibexa\AdminUi\UI\Value\Content\Language
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'createLanguageFromContentType', array('language' => $language, 'contentType' => $contentType), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->createLanguageFromContentType($language, $contentType);
    }

    public function createContentDraft(\Ibexa\Contracts\Core\Repository\Values\Content\DraftList\Item\ContentDraftListItem $contentDraftListItem, \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType) : \Ibexa\AdminUi\UI\Value\Content\ContentDraftInterface
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'createContentDraft', array('contentDraftListItem' => $contentDraftListItem, 'contentType' => $contentType), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->createContentDraft($contentDraftListItem, $contentType);
    }

    public function createUnauthorizedContentDraft(\Ibexa\Contracts\Core\Repository\Values\Content\DraftList\Item\UnauthorizedContentDraftListItem $contentDraftListItem) : \Ibexa\AdminUi\UI\Value\Content\ContentDraftInterface
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'createUnauthorizedContentDraft', array('contentDraftListItem' => $contentDraftListItem), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->createUnauthorizedContentDraft($contentDraftListItem);
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

        unset($instance->userService, $instance->languageService, $instance->locationService, $instance->contentTypeService, $instance->searchService, $instance->objectStateService, $instance->permissionResolver, $instance->datasetFactory, $instance->pathService, $instance->locationResolver);

        \Closure::bind(function (\Ibexa\AdminUi\UI\Value\ValueFactory $instance) {
            unset($instance->userLanguagePreferenceProvider);
        }, $instance, 'Ibexa\\AdminUi\\UI\\Value\\ValueFactory')->__invoke($instance);

        $instance->initializerf0d6f = $initializer;

        return $instance;
    }

    public function __construct(\Ibexa\Contracts\Core\Repository\UserService $userService, \Ibexa\Contracts\Core\Repository\LanguageService $languageService, \Ibexa\Contracts\Core\Repository\LocationService $locationService, \Ibexa\Contracts\Core\Repository\ContentTypeService $contentTypeService, \Ibexa\Contracts\Core\Repository\SearchService $searchService, \Ibexa\Contracts\Core\Repository\ObjectStateService $objectStateService, \Ibexa\Contracts\Core\Repository\PermissionResolver $permissionResolver, \Ibexa\AdminUi\UI\Service\PathService $pathService, \Ibexa\AdminUi\UI\Dataset\DatasetFactory $datasetFactory, \Ibexa\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface $userLanguagePreferenceProvider, \Ibexa\Core\Repository\LocationResolver\LocationResolver $locationResolver)
    {
        static $reflection;

        if (! $this->valueHolder3c8ba) {
            $reflection = $reflection ?? new \ReflectionClass('Ibexa\\AdminUi\\UI\\Value\\ValueFactory');
            $this->valueHolder3c8ba = $reflection->newInstanceWithoutConstructor();
        unset($this->userService, $this->languageService, $this->locationService, $this->contentTypeService, $this->searchService, $this->objectStateService, $this->permissionResolver, $this->datasetFactory, $this->pathService, $this->locationResolver);

        \Closure::bind(function (\Ibexa\AdminUi\UI\Value\ValueFactory $instance) {
            unset($instance->userLanguagePreferenceProvider);
        }, $this, 'Ibexa\\AdminUi\\UI\\Value\\ValueFactory')->__invoke($this);

        }

        $this->valueHolder3c8ba->__construct($userService, $languageService, $locationService, $contentTypeService, $searchService, $objectStateService, $permissionResolver, $pathService, $datasetFactory, $userLanguagePreferenceProvider, $locationResolver);
    }

    public function & __get($name)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, '__get', ['name' => $name], $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        if (isset(self::$publicProperties2eb7b[$name])) {
            return $this->valueHolder3c8ba->$name;
        }

        $realInstanceReflection = new \ReflectionClass('Ibexa\\AdminUi\\UI\\Value\\ValueFactory');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\AdminUi\\UI\\Value\\ValueFactory');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\AdminUi\\UI\\Value\\ValueFactory');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\AdminUi\\UI\\Value\\ValueFactory');

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
        unset($this->userService, $this->languageService, $this->locationService, $this->contentTypeService, $this->searchService, $this->objectStateService, $this->permissionResolver, $this->datasetFactory, $this->pathService, $this->locationResolver);

        \Closure::bind(function (\Ibexa\AdminUi\UI\Value\ValueFactory $instance) {
            unset($instance->userLanguagePreferenceProvider);
        }, $this, 'Ibexa\\AdminUi\\UI\\Value\\ValueFactory')->__invoke($this);
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

if (!\class_exists('ValueFactory_15bf3b0', false)) {
    \class_alias(__NAMESPACE__.'\\ValueFactory_15bf3b0', 'ValueFactory_15bf3b0', false);
}
