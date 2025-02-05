<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getMembersGroupIdResolverService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\CorporateAccount\Migrations\StepExecutor\ReferenceDefinition\MembersGroupIdResolver' shared autowired service.
     *
     * @return \Ibexa\CorporateAccount\Migrations\StepExecutor\ReferenceDefinition\MembersGroupIdResolver
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/corporate-account/src/lib/Migrations/StepExecutor/ReferenceDefinition/MembersGroupIdResolver.php';

        return $container->privates['Ibexa\\CorporateAccount\\Migrations\\StepExecutor\\ReferenceDefinition\\MembersGroupIdResolver'] = new \Ibexa\CorporateAccount\Migrations\StepExecutor\ReferenceDefinition\MembersGroupIdResolver();
    }
}
