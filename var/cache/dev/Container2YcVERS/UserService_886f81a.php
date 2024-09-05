<?php

namespace Container2YcVERS;
include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/lib/Repository/UserService.php';

class UserService_886f81a extends \Ibexa\Core\Repository\UserService implements \ProxyManager\Proxy\VirtualProxyInterface
{
    /**
     * @var \Ibexa\Core\Repository\UserService|null wrapped object, if the proxy is initialized
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

    public function setLogger(?\Psr\Log\LoggerInterface $logger = null)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'setLogger', array('logger' => $logger), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->setLogger($logger);
    }

    public function createUserGroup(\Ibexa\Contracts\Core\Repository\Values\User\UserGroupCreateStruct $userGroupCreateStruct, \Ibexa\Contracts\Core\Repository\Values\User\UserGroup $parentGroup) : \Ibexa\Contracts\Core\Repository\Values\User\UserGroup
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'createUserGroup', array('userGroupCreateStruct' => $userGroupCreateStruct, 'parentGroup' => $parentGroup), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->createUserGroup($userGroupCreateStruct, $parentGroup);
    }

    public function loadUserGroup(int $id, array $prioritizedLanguages = []) : \Ibexa\Contracts\Core\Repository\Values\User\UserGroup
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadUserGroup', array('id' => $id, 'prioritizedLanguages' => $prioritizedLanguages), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadUserGroup($id, $prioritizedLanguages);
    }

    public function loadUserGroupByRemoteId(string $remoteId, array $prioritizedLanguages = []) : \Ibexa\Contracts\Core\Repository\Values\User\UserGroup
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadUserGroupByRemoteId', array('remoteId' => $remoteId, 'prioritizedLanguages' => $prioritizedLanguages), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadUserGroupByRemoteId($remoteId, $prioritizedLanguages);
    }

    public function loadSubUserGroups(\Ibexa\Contracts\Core\Repository\Values\User\UserGroup $userGroup, int $offset = 0, int $limit = 25, array $prioritizedLanguages = []) : iterable
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadSubUserGroups', array('userGroup' => $userGroup, 'offset' => $offset, 'limit' => $limit, 'prioritizedLanguages' => $prioritizedLanguages), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadSubUserGroups($userGroup, $offset, $limit, $prioritizedLanguages);
    }

    public function deleteUserGroup(\Ibexa\Contracts\Core\Repository\Values\User\UserGroup $userGroup) : iterable
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'deleteUserGroup', array('userGroup' => $userGroup), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->deleteUserGroup($userGroup);
    }

    public function moveUserGroup(\Ibexa\Contracts\Core\Repository\Values\User\UserGroup $userGroup, \Ibexa\Contracts\Core\Repository\Values\User\UserGroup $newParent) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'moveUserGroup', array('userGroup' => $userGroup, 'newParent' => $newParent), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->moveUserGroup($userGroup, $newParent);
return;
    }

    public function updateUserGroup(\Ibexa\Contracts\Core\Repository\Values\User\UserGroup $userGroup, \Ibexa\Contracts\Core\Repository\Values\User\UserGroupUpdateStruct $userGroupUpdateStruct) : \Ibexa\Contracts\Core\Repository\Values\User\UserGroup
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'updateUserGroup', array('userGroup' => $userGroup, 'userGroupUpdateStruct' => $userGroupUpdateStruct), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->updateUserGroup($userGroup, $userGroupUpdateStruct);
    }

    public function createUser(\Ibexa\Contracts\Core\Repository\Values\User\UserCreateStruct $userCreateStruct, array $parentGroups) : \Ibexa\Contracts\Core\Repository\Values\User\User
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'createUser', array('userCreateStruct' => $userCreateStruct, 'parentGroups' => $parentGroups), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->createUser($userCreateStruct, $parentGroups);
    }

    public function loadUser(int $userId, array $prioritizedLanguages = []) : \Ibexa\Contracts\Core\Repository\Values\User\User
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadUser', array('userId' => $userId, 'prioritizedLanguages' => $prioritizedLanguages), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadUser($userId, $prioritizedLanguages);
    }

    public function checkUserCredentials(\Ibexa\Contracts\Core\Repository\Values\User\User $user, string $credentials) : bool
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'checkUserCredentials', array('user' => $user, 'credentials' => $credentials), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->checkUserCredentials($user, $credentials);
    }

    public function loadUserByLogin(string $login, array $prioritizedLanguages = []) : \Ibexa\Contracts\Core\Repository\Values\User\User
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadUserByLogin', array('login' => $login, 'prioritizedLanguages' => $prioritizedLanguages), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadUserByLogin($login, $prioritizedLanguages);
    }

    public function loadUserByEmail(string $email, array $prioritizedLanguages = []) : \Ibexa\Contracts\Core\Repository\Values\User\User
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadUserByEmail', array('email' => $email, 'prioritizedLanguages' => $prioritizedLanguages), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadUserByEmail($email, $prioritizedLanguages);
    }

    public function loadUsersByEmail(string $email, array $prioritizedLanguages = []) : iterable
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadUsersByEmail', array('email' => $email, 'prioritizedLanguages' => $prioritizedLanguages), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadUsersByEmail($email, $prioritizedLanguages);
    }

    public function loadUserByToken(string $hash, array $prioritizedLanguages = []) : \Ibexa\Contracts\Core\Repository\Values\User\User
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadUserByToken', array('hash' => $hash, 'prioritizedLanguages' => $prioritizedLanguages), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadUserByToken($hash, $prioritizedLanguages);
    }

    public function deleteUser(\Ibexa\Contracts\Core\Repository\Values\User\User $user) : iterable
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'deleteUser', array('user' => $user), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->deleteUser($user);
    }

    public function updateUser(\Ibexa\Contracts\Core\Repository\Values\User\User $user, \Ibexa\Contracts\Core\Repository\Values\User\UserUpdateStruct $userUpdateStruct) : \Ibexa\Contracts\Core\Repository\Values\User\User
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'updateUser', array('user' => $user, 'userUpdateStruct' => $userUpdateStruct), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->updateUser($user, $userUpdateStruct);
    }

    public function updateUserPassword(\Ibexa\Contracts\Core\Repository\Values\User\User $user, string $newPassword) : \Ibexa\Contracts\Core\Repository\Values\User\User
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'updateUserPassword', array('user' => $user, 'newPassword' => $newPassword), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->updateUserPassword($user, $newPassword);
    }

    public function updateUserToken(\Ibexa\Contracts\Core\Repository\Values\User\User $user, \Ibexa\Contracts\Core\Repository\Values\User\UserTokenUpdateStruct $userTokenUpdateStruct) : \Ibexa\Contracts\Core\Repository\Values\User\User
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'updateUserToken', array('user' => $user, 'userTokenUpdateStruct' => $userTokenUpdateStruct), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->updateUserToken($user, $userTokenUpdateStruct);
    }

    public function expireUserToken(string $hash) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'expireUserToken', array('hash' => $hash), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->expireUserToken($hash);
return;
    }

    public function assignUserToUserGroup(\Ibexa\Contracts\Core\Repository\Values\User\User $user, \Ibexa\Contracts\Core\Repository\Values\User\UserGroup $userGroup) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'assignUserToUserGroup', array('user' => $user, 'userGroup' => $userGroup), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->assignUserToUserGroup($user, $userGroup);
return;
    }

    public function unAssignUserFromUserGroup(\Ibexa\Contracts\Core\Repository\Values\User\User $user, \Ibexa\Contracts\Core\Repository\Values\User\UserGroup $userGroup) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'unAssignUserFromUserGroup', array('user' => $user, 'userGroup' => $userGroup), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->unAssignUserFromUserGroup($user, $userGroup);
return;
    }

    public function loadUserGroupsOfUser(\Ibexa\Contracts\Core\Repository\Values\User\User $user, int $offset = 0, int $limit = 25, array $prioritizedLanguages = []) : iterable
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadUserGroupsOfUser', array('user' => $user, 'offset' => $offset, 'limit' => $limit, 'prioritizedLanguages' => $prioritizedLanguages), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadUserGroupsOfUser($user, $offset, $limit, $prioritizedLanguages);
    }

    public function loadUsersOfUserGroup(\Ibexa\Contracts\Core\Repository\Values\User\UserGroup $userGroup, int $offset = 0, int $limit = 25, array $prioritizedLanguages = []) : iterable
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadUsersOfUserGroup', array('userGroup' => $userGroup, 'offset' => $offset, 'limit' => $limit, 'prioritizedLanguages' => $prioritizedLanguages), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadUsersOfUserGroup($userGroup, $offset, $limit, $prioritizedLanguages);
    }

    public function isUser(\Ibexa\Contracts\Core\Repository\Values\Content\Content $content) : bool
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'isUser', array('content' => $content), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->isUser($content);
    }

    public function isUserGroup(\Ibexa\Contracts\Core\Repository\Values\Content\Content $content) : bool
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'isUserGroup', array('content' => $content), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->isUserGroup($content);
    }

    public function newUserCreateStruct(string $login, string $email, string $password, string $mainLanguageCode, ?\Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType = null) : \Ibexa\Contracts\Core\Repository\Values\User\UserCreateStruct
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'newUserCreateStruct', array('login' => $login, 'email' => $email, 'password' => $password, 'mainLanguageCode' => $mainLanguageCode, 'contentType' => $contentType), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->newUserCreateStruct($login, $email, $password, $mainLanguageCode, $contentType);
    }

    public function newUserGroupCreateStruct(string $mainLanguageCode, ?\Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType = null) : \Ibexa\Contracts\Core\Repository\Values\User\UserGroupCreateStruct
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'newUserGroupCreateStruct', array('mainLanguageCode' => $mainLanguageCode, 'contentType' => $contentType), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->newUserGroupCreateStruct($mainLanguageCode, $contentType);
    }

    public function newUserUpdateStruct() : \Ibexa\Contracts\Core\Repository\Values\User\UserUpdateStruct
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'newUserUpdateStruct', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->newUserUpdateStruct();
    }

    public function newUserGroupUpdateStruct() : \Ibexa\Contracts\Core\Repository\Values\User\UserGroupUpdateStruct
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'newUserGroupUpdateStruct', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->newUserGroupUpdateStruct();
    }

    public function validatePassword(string $password, ?\Ibexa\Contracts\Core\Repository\Values\User\PasswordValidationContext $context = null) : array
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'validatePassword', array('password' => $password, 'context' => $context), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->validatePassword($password, $context);
    }

    public function getPasswordInfo(\Ibexa\Contracts\Core\Repository\Values\User\User $user) : \Ibexa\Contracts\Core\Repository\Values\User\PasswordInfo
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getPasswordInfo', array('user' => $user), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getPasswordInfo($user);
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

        unset($instance->repository, $instance->userHandler, $instance->settings, $instance->logger);

        \Closure::bind(function (\Ibexa\Core\Repository\UserService $instance) {
            unset($instance->locationHandler, $instance->permissionResolver, $instance->passwordHashService, $instance->passwordValidator, $instance->configResolver);
        }, $instance, 'Ibexa\\Core\\Repository\\UserService')->__invoke($instance);

        $instance->initializerf0d6f = $initializer;

        return $instance;
    }

    public function __construct(\Ibexa\Contracts\Core\Repository\Repository $repository, \Ibexa\Contracts\Core\Repository\PermissionResolver $permissionResolver, \Ibexa\Contracts\Core\Persistence\User\Handler $userHandler, \Ibexa\Contracts\Core\Persistence\Content\Location\Handler $locationHandler, \Ibexa\Contracts\Core\Repository\PasswordHashService $passwordHashGenerator, \Ibexa\Core\Repository\User\PasswordValidatorInterface $passwordValidator, \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface $configResolver, array $settings = [])
    {
        static $reflection;

        if (! $this->valueHolder3c8ba) {
            $reflection = $reflection ?? new \ReflectionClass('Ibexa\\Core\\Repository\\UserService');
            $this->valueHolder3c8ba = $reflection->newInstanceWithoutConstructor();
        unset($this->repository, $this->userHandler, $this->settings, $this->logger);

        \Closure::bind(function (\Ibexa\Core\Repository\UserService $instance) {
            unset($instance->locationHandler, $instance->permissionResolver, $instance->passwordHashService, $instance->passwordValidator, $instance->configResolver);
        }, $this, 'Ibexa\\Core\\Repository\\UserService')->__invoke($this);

        }

        $this->valueHolder3c8ba->__construct($repository, $permissionResolver, $userHandler, $locationHandler, $passwordHashGenerator, $passwordValidator, $configResolver, $settings);
    }

    public function & __get($name)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, '__get', ['name' => $name], $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        if (isset(self::$publicProperties2eb7b[$name])) {
            return $this->valueHolder3c8ba->$name;
        }

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\Repository\\UserService');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\Repository\\UserService');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\Repository\\UserService');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\Repository\\UserService');

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
        unset($this->repository, $this->userHandler, $this->settings, $this->logger);

        \Closure::bind(function (\Ibexa\Core\Repository\UserService $instance) {
            unset($instance->locationHandler, $instance->permissionResolver, $instance->passwordHashService, $instance->passwordValidator, $instance->configResolver);
        }, $this, 'Ibexa\\Core\\Repository\\UserService')->__invoke($this);
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

if (!\class_exists('UserService_886f81a', false)) {
    \class_alias(__NAMESPACE__.'\\UserService_886f81a', 'UserService_886f81a', false);
}
