<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getContentIdResolverService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\Migration\StepExecutor\ReferenceDefinition\Content\ContentIdResolver' shared autowired service.
     *
     * @return \Ibexa\Migration\StepExecutor\ReferenceDefinition\Content\ContentIdResolver
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/migrations/src/lib/StepExecutor/ReferenceDefinition/Content/ContentResolverInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/migrations/src/lib/StepExecutor/ReferenceDefinition/Content/ContentIdResolver.php';

        return $container->privates['Ibexa\\Migration\\StepExecutor\\ReferenceDefinition\\Content\\ContentIdResolver'] = new \Ibexa\Migration\StepExecutor\ReferenceDefinition\Content\ContentIdResolver();
    }
}
