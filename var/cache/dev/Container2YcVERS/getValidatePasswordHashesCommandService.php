<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getValidatePasswordHashesCommandService extends App_KernelDevDebugContainer
{
    /**
     * Gets the public 'console.command.public_alias.Ibexa\Bundle\RepositoryInstaller\Command\ValidatePasswordHashesCommand' shared service.
     *
     * @return \Ibexa\Bundle\RepositoryInstaller\Command\ValidatePasswordHashesCommand
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfony/console/Command/Command.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/bundle/Core/Command/BackwardCompatibleCommand.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/bundle/RepositoryInstaller/Command/ValidatePasswordHashesCommand.php';

        $container->services['console.command.public_alias.Ibexa\\Bundle\\RepositoryInstaller\\Command\\ValidatePasswordHashesCommand'] = $instance = new \Ibexa\Bundle\RepositoryInstaller\Command\ValidatePasswordHashesCommand(($container->privates['Ibexa\\Core\\FieldType\\User\\UserStorage'] ?? $container->getUserStorageService()), ($container->privates['Ibexa\\OAuth2Client\\Repository\\User\\PasswordHashService'] ?? $container->getPasswordHashServiceService()));

        $instance->addOption('siteaccess', NULL, 4, 'SiteAccess to use for operations. If not provided, default siteaccess will be used');

        return $instance;
    }
}
