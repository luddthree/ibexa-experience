<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getSegmentDeleteStepExecutorService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\Segmentation\StepExecutor\SegmentDeleteStepExecutor' shared autowired service.
     *
     * @return \Ibexa\Segmentation\StepExecutor\SegmentDeleteStepExecutor
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/migrations/src/lib/StepExecutor/StepExecutorInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/migrations/src/lib/StepExecutor/UserContextAwareStepExecutorInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/symfony/service-contracts/ServiceSubscriberTrait.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/migrations/src/lib/StepExecutor/UserContextAwareStepExecutorTrait.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/migrations/src/contracts/StepExecutor/AbstractStepExecutor.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/segmentation/src/lib/StepExecutor/AbstractSegmentStepExecutor.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/migrations/src/lib/Log/LoggerAwareTrait.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/segmentation/src/lib/StepExecutor/SegmentDeleteStepExecutor.php';

        $container->privates['Ibexa\\Segmentation\\StepExecutor\\SegmentDeleteStepExecutor'] = $instance = new \Ibexa\Segmentation\StepExecutor\SegmentDeleteStepExecutor(($container->privates['Ibexa\\Segmentation\\Service\\Event\\SegmentationServiceEventDecorator'] ?? $container->getSegmentationServiceEventDecoratorService()));

        $instance->setPermissionResolver(($container->privates['Ibexa\\Core\\Repository\\Permission\\CachedPermissionService'] ?? $container->getCachedPermissionServiceService()));
        $instance->setContainer(($container->privates['.service_locator.qWTaPeW'] ?? $container->load('get_ServiceLocator_QWTaPeWService'))->withContext('Ibexa\\Segmentation\\StepExecutor\\SegmentDeleteStepExecutor', $container));

        return $instance;
    }
}
