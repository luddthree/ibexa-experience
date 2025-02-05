<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getIsDashboardMatcherService extends App_KernelDevDebugContainer
{
    /**
     * Gets the public 'Ibexa\Bundle\Dashboard\ViewMatcher\IsDashboardMatcher' shared autowired service.
     *
     * @return \Ibexa\Bundle\Dashboard\ViewMatcher\IsDashboardMatcher
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/lib/MVC/Symfony/Matcher/ViewMatcherInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/lib/MVC/Symfony/Matcher/ContentBased/MatcherInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/dashboard/src/bundle/ViewMatcher/IsDashboardMatcher.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/contracts/Specification/SpecificationInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/contracts/Specification/AbstractSpecification.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/dashboard/src/lib/Specification/IsDashboardContentType.php';

        return $container->services['Ibexa\\Bundle\\Dashboard\\ViewMatcher\\IsDashboardMatcher'] = new \Ibexa\Bundle\Dashboard\ViewMatcher\IsDashboardMatcher(new \Ibexa\Dashboard\Specification\IsDashboardContentType(($container->services['Ibexa\\Bundle\\Core\\DependencyInjection\\Configuration\\ChainConfigResolver'] ?? $container->getChainConfigResolverService())));
    }
}
