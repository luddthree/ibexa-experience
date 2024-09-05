<?php

namespace Container2YcVERS;
include_once \dirname(__DIR__, 4).'/vendor/ibexa/scheduler/src/lib/Persistence/Gateway/DoctrineDatabase.php';

class DoctrineDatabase_611c869 extends \Ibexa\Scheduler\Persistence\Gateway\DoctrineDatabase implements \ProxyManager\Proxy\VirtualProxyInterface
{
    /**
     * @var \Ibexa\Scheduler\Persistence\Gateway\DoctrineDatabase|null wrapped object, if the proxy is initialized
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

    public function insertVersionEntry(\Ibexa\Scheduler\ValueObject\CreateScheduledEntryStruct $createScheduledEntryStruct) : bool
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'insertVersionEntry', array('createScheduledEntryStruct' => $createScheduledEntryStruct), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->insertVersionEntry($createScheduledEntryStruct);
    }

    public function insertContentEntry(\Ibexa\Scheduler\ValueObject\CreateScheduledEntryStruct $createScheduledEntryStruct) : bool
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'insertContentEntry', array('createScheduledEntryStruct' => $createScheduledEntryStruct), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->insertContentEntry($createScheduledEntryStruct);
    }

    public function updateEntry(\Ibexa\Scheduler\ValueObject\UpdateScheduledEntry $updateScheduledEntry) : bool
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'updateEntry', array('updateScheduledEntry' => $updateScheduledEntry), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->updateEntry($updateScheduledEntry);
    }

    public function hasVersionEntry(int $versionId, ?string $action) : bool
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'hasVersionEntry', array('versionId' => $versionId, 'action' => $action), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->hasVersionEntry($versionId, $action);
    }

    public function hasContentEntry(int $contentId, ?string $action) : bool
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'hasContentEntry', array('contentId' => $contentId, 'action' => $action), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->hasContentEntry($contentId, $action);
    }

    public function getVersionEntry(int $versionId, ?string $action) : ?\Ibexa\Scheduler\ValueObject\ScheduledEntry
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getVersionEntry', array('versionId' => $versionId, 'action' => $action), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getVersionEntry($versionId, $action);
    }

    public function getContentEntry(int $contentId, ?string $action) : ?\Ibexa\Scheduler\ValueObject\ScheduledEntry
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getContentEntry', array('contentId' => $contentId, 'action' => $action), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getContentEntry($contentId, $action);
    }

    public function getEntriesByIds(array $scheduleVersionIds) : iterable
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getEntriesByIds', array('scheduleVersionIds' => $scheduleVersionIds), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getEntriesByIds($scheduleVersionIds);
    }

    public function getVersionsEntries(?string $action, int $page = 0, int $limit = 25) : iterable
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getVersionsEntries', array('action' => $action, 'page' => $page, 'limit' => $limit), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getVersionsEntries($action, $page, $limit);
    }

    public function getUserVersionsEntries(int $userId, ?string $action, int $page = 0, int $limit = 25) : iterable
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getUserVersionsEntries', array('userId' => $userId, 'action' => $action, 'page' => $page, 'limit' => $limit), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getUserVersionsEntries($userId, $action, $page, $limit);
    }

    public function getAllTypesEntries(int $contentId, ?string $action, int $page = 0, ?int $limit = 25) : iterable
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getAllTypesEntries', array('contentId' => $contentId, 'action' => $action, 'page' => $page, 'limit' => $limit), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getAllTypesEntries($contentId, $action, $page, $limit);
    }

    public function getVersionsEntriesOlderThan(int $timestamp, ?string $action, int $page = 0, int $limit = 25) : iterable
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getVersionsEntriesOlderThan', array('timestamp' => $timestamp, 'action' => $action, 'page' => $page, 'limit' => $limit), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getVersionsEntriesOlderThan($timestamp, $action, $page, $limit);
    }

    public function getContentsEntriesOlderThan(int $timestamp, ?string $action, int $page = 0, int $limit = 25) : iterable
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getContentsEntriesOlderThan', array('timestamp' => $timestamp, 'action' => $action, 'page' => $page, 'limit' => $limit), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getContentsEntriesOlderThan($timestamp, $action, $page, $limit);
    }

    public function getVersionsEntriesByDateRange(int $start, int $end, ?string $action, array $languages = [], ?int $sinceId = null, int $limit = 25) : iterable
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getVersionsEntriesByDateRange', array('start' => $start, 'end' => $end, 'action' => $action, 'languages' => $languages, 'sinceId' => $sinceId, 'limit' => $limit), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getVersionsEntriesByDateRange($start, $end, $action, $languages, $sinceId, $limit);
    }

    public function getContentsEntriesByDateRange(int $start, int $end, ?string $action, array $languages = [], ?int $sinceId = null, int $limit = 25) : iterable
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getContentsEntriesByDateRange', array('start' => $start, 'end' => $end, 'action' => $action, 'languages' => $languages, 'sinceId' => $sinceId, 'limit' => $limit), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getContentsEntriesByDateRange($start, $end, $action, $languages, $sinceId, $limit);
    }

    public function getVersionsEntriesForContent(int $contentId, ?string $action, int $page = 0, int $limit = 25) : iterable
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getVersionsEntriesForContent', array('contentId' => $contentId, 'action' => $action, 'page' => $page, 'limit' => $limit), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getVersionsEntriesForContent($contentId, $action, $page, $limit);
    }

    public function countVersionsEntries(?string $action) : int
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'countVersionsEntries', array('action' => $action), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->countVersionsEntries($action);
    }

    public function countContentEntries(?string $action) : int
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'countContentEntries', array('action' => $action), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->countContentEntries($action);
    }

    public function countVersionsEntriesForContent(int $contentId, ?string $action) : int
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'countVersionsEntriesForContent', array('contentId' => $contentId, 'action' => $action), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->countVersionsEntriesForContent($contentId, $action);
    }

    public function countUserVersionsEntries(int $userId, ?string $action) : int
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'countUserVersionsEntries', array('userId' => $userId, 'action' => $action), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->countUserVersionsEntries($userId, $action);
    }

    public function countVersionsEntriesOlderThan(int $timestamp, ?string $action) : int
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'countVersionsEntriesOlderThan', array('timestamp' => $timestamp, 'action' => $action), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->countVersionsEntriesOlderThan($timestamp, $action);
    }

    public function countContentsEntriesOlderThan(int $timestamp, ?string $action) : int
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'countContentsEntriesOlderThan', array('timestamp' => $timestamp, 'action' => $action), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->countContentsEntriesOlderThan($timestamp, $action);
    }

    public function countVersionsEntriesInDateRange(int $start, int $end, ?string $action, array $languages = [], ?int $sinceId = null) : int
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'countVersionsEntriesInDateRange', array('start' => $start, 'end' => $end, 'action' => $action, 'languages' => $languages, 'sinceId' => $sinceId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->countVersionsEntriesInDateRange($start, $end, $action, $languages, $sinceId);
    }

    public function countContentsEntriesInDateRange(int $start, int $end, ?string $action, array $languages = [], ?int $sinceId = null) : int
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'countContentsEntriesInDateRange', array('start' => $start, 'end' => $end, 'action' => $action, 'languages' => $languages, 'sinceId' => $sinceId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->countContentsEntriesInDateRange($start, $end, $action, $languages, $sinceId);
    }

    public function deleteVersionEntry(int $versionId, ?string $action) : bool
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'deleteVersionEntry', array('versionId' => $versionId, 'action' => $action), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->deleteVersionEntry($versionId, $action);
    }

    public function deleteContentEntry(int $contentId, ?string $action) : bool
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'deleteContentEntry', array('contentId' => $contentId, 'action' => $action), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->deleteContentEntry($contentId, $action);
    }

    public function deleteAllTypesEntries(int $contentId, ?string $action) : bool
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'deleteAllTypesEntries', array('contentId' => $contentId, 'action' => $action), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->deleteAllTypesEntries($contentId, $action);
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

        \Closure::bind(function (\Ibexa\Scheduler\Persistence\Gateway\DoctrineDatabase $instance) {
            unset($instance->connection, $instance->dbPlatform);
        }, $instance, 'Ibexa\\Scheduler\\Persistence\\Gateway\\DoctrineDatabase')->__invoke($instance);

        $instance->initializerf0d6f = $initializer;

        return $instance;
    }

    public function __construct(\Doctrine\DBAL\Connection $connection)
    {
        static $reflection;

        if (! $this->valueHolder3c8ba) {
            $reflection = $reflection ?? new \ReflectionClass('Ibexa\\Scheduler\\Persistence\\Gateway\\DoctrineDatabase');
            $this->valueHolder3c8ba = $reflection->newInstanceWithoutConstructor();
        \Closure::bind(function (\Ibexa\Scheduler\Persistence\Gateway\DoctrineDatabase $instance) {
            unset($instance->connection, $instance->dbPlatform);
        }, $this, 'Ibexa\\Scheduler\\Persistence\\Gateway\\DoctrineDatabase')->__invoke($this);

        }

        $this->valueHolder3c8ba->__construct($connection);
    }

    public function & __get($name)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, '__get', ['name' => $name], $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        if (isset(self::$publicProperties2eb7b[$name])) {
            return $this->valueHolder3c8ba->$name;
        }

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Scheduler\\Persistence\\Gateway\\DoctrineDatabase');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Scheduler\\Persistence\\Gateway\\DoctrineDatabase');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Scheduler\\Persistence\\Gateway\\DoctrineDatabase');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Scheduler\\Persistence\\Gateway\\DoctrineDatabase');

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
        \Closure::bind(function (\Ibexa\Scheduler\Persistence\Gateway\DoctrineDatabase $instance) {
            unset($instance->connection, $instance->dbPlatform);
        }, $this, 'Ibexa\\Scheduler\\Persistence\\Gateway\\DoctrineDatabase')->__invoke($this);
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

if (!\class_exists('DoctrineDatabase_611c869', false)) {
    \class_alias(__NAMESPACE__.'\\DoctrineDatabase_611c869', 'DoctrineDatabase_611c869', false);
}
