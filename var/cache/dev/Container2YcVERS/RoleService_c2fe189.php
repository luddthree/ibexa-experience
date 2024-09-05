<?php

namespace Container2YcVERS;
include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/lib/Repository/RoleService.php';

class RoleService_c2fe189 extends \Ibexa\Core\Repository\RoleService implements \ProxyManager\Proxy\VirtualProxyInterface
{
    /**
     * @var \Ibexa\Core\Repository\RoleService|null wrapped object, if the proxy is initialized
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

    public function createRole(\Ibexa\Contracts\Core\Repository\Values\User\RoleCreateStruct $roleCreateStruct) : \Ibexa\Contracts\Core\Repository\Values\User\RoleDraft
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'createRole', array('roleCreateStruct' => $roleCreateStruct), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->createRole($roleCreateStruct);
    }

    public function createRoleDraft(\Ibexa\Contracts\Core\Repository\Values\User\Role $role) : \Ibexa\Contracts\Core\Repository\Values\User\RoleDraft
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'createRoleDraft', array('role' => $role), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->createRoleDraft($role);
    }

    public function copyRole(\Ibexa\Contracts\Core\Repository\Values\User\Role $role, \Ibexa\Contracts\Core\Repository\Values\User\RoleCopyStruct $roleCopyStruct) : \Ibexa\Contracts\Core\Repository\Values\User\Role
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'copyRole', array('role' => $role, 'roleCopyStruct' => $roleCopyStruct), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->copyRole($role, $roleCopyStruct);
    }

    public function loadRoleDraft(int $id) : \Ibexa\Contracts\Core\Repository\Values\User\RoleDraft
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadRoleDraft', array('id' => $id), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadRoleDraft($id);
    }

    public function loadRoleDraftByRoleId(int $roleId) : \Ibexa\Contracts\Core\Repository\Values\User\RoleDraft
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadRoleDraftByRoleId', array('roleId' => $roleId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadRoleDraftByRoleId($roleId);
    }

    public function updateRoleDraft(\Ibexa\Contracts\Core\Repository\Values\User\RoleDraft $roleDraft, \Ibexa\Contracts\Core\Repository\Values\User\RoleUpdateStruct $roleUpdateStruct) : \Ibexa\Contracts\Core\Repository\Values\User\RoleDraft
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'updateRoleDraft', array('roleDraft' => $roleDraft, 'roleUpdateStruct' => $roleUpdateStruct), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->updateRoleDraft($roleDraft, $roleUpdateStruct);
    }

    public function addPolicyByRoleDraft(\Ibexa\Contracts\Core\Repository\Values\User\RoleDraft $roleDraft, \Ibexa\Contracts\Core\Repository\Values\User\PolicyCreateStruct $policyCreateStruct) : \Ibexa\Contracts\Core\Repository\Values\User\RoleDraft
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'addPolicyByRoleDraft', array('roleDraft' => $roleDraft, 'policyCreateStruct' => $policyCreateStruct), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->addPolicyByRoleDraft($roleDraft, $policyCreateStruct);
    }

    public function removePolicyByRoleDraft(\Ibexa\Contracts\Core\Repository\Values\User\RoleDraft $roleDraft, \Ibexa\Contracts\Core\Repository\Values\User\PolicyDraft $policyDraft) : \Ibexa\Contracts\Core\Repository\Values\User\RoleDraft
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'removePolicyByRoleDraft', array('roleDraft' => $roleDraft, 'policyDraft' => $policyDraft), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->removePolicyByRoleDraft($roleDraft, $policyDraft);
    }

    public function updatePolicyByRoleDraft(\Ibexa\Contracts\Core\Repository\Values\User\RoleDraft $roleDraft, \Ibexa\Contracts\Core\Repository\Values\User\PolicyDraft $policy, \Ibexa\Contracts\Core\Repository\Values\User\PolicyUpdateStruct $policyUpdateStruct) : \Ibexa\Contracts\Core\Repository\Values\User\PolicyDraft
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'updatePolicyByRoleDraft', array('roleDraft' => $roleDraft, 'policy' => $policy, 'policyUpdateStruct' => $policyUpdateStruct), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->updatePolicyByRoleDraft($roleDraft, $policy, $policyUpdateStruct);
    }

    public function deleteRoleDraft(\Ibexa\Contracts\Core\Repository\Values\User\RoleDraft $roleDraft) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'deleteRoleDraft', array('roleDraft' => $roleDraft), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->deleteRoleDraft($roleDraft);
return;
    }

    public function publishRoleDraft(\Ibexa\Contracts\Core\Repository\Values\User\RoleDraft $roleDraft) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'publishRoleDraft', array('roleDraft' => $roleDraft), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->publishRoleDraft($roleDraft);
return;
    }

    public function loadRole(int $id) : \Ibexa\Contracts\Core\Repository\Values\User\Role
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadRole', array('id' => $id), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadRole($id);
    }

    public function loadRoleByIdentifier(string $identifier) : \Ibexa\Contracts\Core\Repository\Values\User\Role
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadRoleByIdentifier', array('identifier' => $identifier), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadRoleByIdentifier($identifier);
    }

    public function loadRoles() : iterable
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadRoles', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadRoles();
    }

    public function deleteRole(\Ibexa\Contracts\Core\Repository\Values\User\Role $role) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'deleteRole', array('role' => $role), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->deleteRole($role);
return;
    }

    public function assignRoleToUserGroup(\Ibexa\Contracts\Core\Repository\Values\User\Role $role, \Ibexa\Contracts\Core\Repository\Values\User\UserGroup $userGroup, ?\Ibexa\Contracts\Core\Repository\Values\User\Limitation\RoleLimitation $roleLimitation = null) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'assignRoleToUserGroup', array('role' => $role, 'userGroup' => $userGroup, 'roleLimitation' => $roleLimitation), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->assignRoleToUserGroup($role, $userGroup, $roleLimitation);
return;
    }

    public function assignRoleToUser(\Ibexa\Contracts\Core\Repository\Values\User\Role $role, \Ibexa\Contracts\Core\Repository\Values\User\User $user, ?\Ibexa\Contracts\Core\Repository\Values\User\Limitation\RoleLimitation $roleLimitation = null) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'assignRoleToUser', array('role' => $role, 'user' => $user, 'roleLimitation' => $roleLimitation), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->assignRoleToUser($role, $user, $roleLimitation);
return;
    }

    public function removeRoleAssignment(\Ibexa\Contracts\Core\Repository\Values\User\RoleAssignment $roleAssignment) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'removeRoleAssignment', array('roleAssignment' => $roleAssignment), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->removeRoleAssignment($roleAssignment);
return;
    }

    public function loadRoleAssignment(int $roleAssignmentId) : \Ibexa\Contracts\Core\Repository\Values\User\RoleAssignment
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadRoleAssignment', array('roleAssignmentId' => $roleAssignmentId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadRoleAssignment($roleAssignmentId);
    }

    public function getRoleAssignments(\Ibexa\Contracts\Core\Repository\Values\User\Role $role) : iterable
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getRoleAssignments', array('role' => $role), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getRoleAssignments($role);
    }

    public function loadRoleAssignments(\Ibexa\Contracts\Core\Repository\Values\User\Role $role, int $offset = 0, ?int $limit = null) : iterable
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadRoleAssignments', array('role' => $role, 'offset' => $offset, 'limit' => $limit), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadRoleAssignments($role, $offset, $limit);
    }

    public function countRoleAssignments(\Ibexa\Contracts\Core\Repository\Values\User\Role $role) : int
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'countRoleAssignments', array('role' => $role), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->countRoleAssignments($role);
    }

    public function getRoleAssignmentsForUser(\Ibexa\Contracts\Core\Repository\Values\User\User $user, bool $inherited = false) : iterable
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getRoleAssignmentsForUser', array('user' => $user, 'inherited' => $inherited), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getRoleAssignmentsForUser($user, $inherited);
    }

    public function getRoleAssignmentsForUserGroup(\Ibexa\Contracts\Core\Repository\Values\User\UserGroup $userGroup) : iterable
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getRoleAssignmentsForUserGroup', array('userGroup' => $userGroup), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getRoleAssignmentsForUserGroup($userGroup);
    }

    public function newRoleCreateStruct(string $name) : \Ibexa\Contracts\Core\Repository\Values\User\RoleCreateStruct
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'newRoleCreateStruct', array('name' => $name), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->newRoleCreateStruct($name);
    }

    public function newRoleCopyStruct(string $name) : \Ibexa\Contracts\Core\Repository\Values\User\RoleCopyStruct
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'newRoleCopyStruct', array('name' => $name), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->newRoleCopyStruct($name);
    }

    public function newPolicyCreateStruct(string $module, string $function) : \Ibexa\Contracts\Core\Repository\Values\User\PolicyCreateStruct
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'newPolicyCreateStruct', array('module' => $module, 'function' => $function), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->newPolicyCreateStruct($module, $function);
    }

    public function newPolicyUpdateStruct() : \Ibexa\Contracts\Core\Repository\Values\User\PolicyUpdateStruct
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'newPolicyUpdateStruct', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->newPolicyUpdateStruct();
    }

    public function newRoleUpdateStruct() : \Ibexa\Contracts\Core\Repository\Values\User\RoleUpdateStruct
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'newRoleUpdateStruct', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->newRoleUpdateStruct();
    }

    public function getLimitationType(string $identifier) : \Ibexa\Contracts\Core\Limitation\Type
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getLimitationType', array('identifier' => $identifier), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getLimitationType($identifier);
    }

    public function getLimitationTypesByModuleFunction(string $module, string $function) : iterable
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getLimitationTypesByModuleFunction', array('module' => $module, 'function' => $function), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getLimitationTypesByModuleFunction($module, $function);
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

        unset($instance->repository, $instance->userHandler, $instance->limitationService, $instance->roleDomainMapper, $instance->settings);

        \Closure::bind(function (\Ibexa\Core\Repository\RoleService $instance) {
            unset($instance->permissionResolver);
        }, $instance, 'Ibexa\\Core\\Repository\\RoleService')->__invoke($instance);

        $instance->initializerf0d6f = $initializer;

        return $instance;
    }

    public function __construct(\Ibexa\Contracts\Core\Repository\Repository $repository, \Ibexa\Contracts\Core\Persistence\User\Handler $userHandler, \Ibexa\Core\Repository\Permission\LimitationService $limitationService, \Ibexa\Core\Repository\Mapper\RoleDomainMapper $roleDomainMapper, array $settings = [])
    {
        static $reflection;

        if (! $this->valueHolder3c8ba) {
            $reflection = $reflection ?? new \ReflectionClass('Ibexa\\Core\\Repository\\RoleService');
            $this->valueHolder3c8ba = $reflection->newInstanceWithoutConstructor();
        unset($this->repository, $this->userHandler, $this->limitationService, $this->roleDomainMapper, $this->settings);

        \Closure::bind(function (\Ibexa\Core\Repository\RoleService $instance) {
            unset($instance->permissionResolver);
        }, $this, 'Ibexa\\Core\\Repository\\RoleService')->__invoke($this);

        }

        $this->valueHolder3c8ba->__construct($repository, $userHandler, $limitationService, $roleDomainMapper, $settings);
    }

    public function & __get($name)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, '__get', ['name' => $name], $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        if (isset(self::$publicProperties2eb7b[$name])) {
            return $this->valueHolder3c8ba->$name;
        }

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\Repository\\RoleService');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\Repository\\RoleService');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\Repository\\RoleService');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\Repository\\RoleService');

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
        unset($this->repository, $this->userHandler, $this->limitationService, $this->roleDomainMapper, $this->settings);

        \Closure::bind(function (\Ibexa\Core\Repository\RoleService $instance) {
            unset($instance->permissionResolver);
        }, $this, 'Ibexa\\Core\\Repository\\RoleService')->__invoke($this);
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

if (!\class_exists('RoleService_c2fe189', false)) {
    \class_alias(__NAMESPACE__.'\\RoleService_c2fe189', 'RoleService_c2fe189', false);
}
