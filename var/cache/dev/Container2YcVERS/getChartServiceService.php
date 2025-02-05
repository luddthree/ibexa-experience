<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getChartServiceService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\Personalization\Service\Chart\ChartService' shared autowired service.
     *
     * @return \Ibexa\Personalization\Service\Chart\ChartService
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/personalization/src/lib/Service/Chart/ChartServiceInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/personalization/src/lib/Service/Chart/ChartService.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/personalization/src/lib/Factory/Chart/ChartFactoryInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/personalization/src/lib/Factory/Chart/ChartFactory.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/personalization/src/lib/Provider/Chart/ChartDataStructProviderInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/personalization/src/lib/Provider/Chart/ChartDataStructProvider.php';

        return $container->privates['Ibexa\\Personalization\\Service\\Chart\\ChartService'] = new \Ibexa\Personalization\Service\Chart\ChartService(($container->privates['Ibexa\\Personalization\\Permission\\CustomerTypeChecker'] ?? $container->load('getCustomerTypeCheckerService')), ($container->privates['Ibexa\\Personalization\\Cache\\CachedRecommendationPerformanceService'] ?? $container->load('getCachedRecommendationPerformanceServiceService')), ($container->privates['Ibexa\\Personalization\\Cache\\CachedScenarioService'] ?? $container->load('getCachedScenarioServiceService')), new \Ibexa\Personalization\Factory\Chart\ChartFactory(), new \Ibexa\Personalization\Provider\Chart\ChartDataStructProvider(($container->privates['Ibexa\\Personalization\\Formatter\\NumberFormatter'] ?? $container->getNumberFormatterService()), ($container->services['Symfony\\Contracts\\Translation\\TranslatorInterface'] ?? $container->getTranslatorInterfaceService())));
    }
}
