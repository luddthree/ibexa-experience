<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getLogicalAndQueryBuilderService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\LogicalAndQueryBuilder' shared autowired service.
     *
     * @return \Ibexa\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\LogicalAndQueryBuilder
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/contracts/Repository/Values/Filter/CriterionQueryBuilder.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/lib/Persistence/Legacy/Filter/CriterionQueryBuilder/LogicalAndQueryBuilder.php';

        $a = ($container->privates['Ibexa\\Core\\Persistence\\Legacy\\Filter\\CriterionVisitor'] ?? $container->getCriterionVisitorService());

        if (isset($container->privates['Ibexa\\Core\\Persistence\\Legacy\\Filter\\CriterionQueryBuilder\\LogicalAndQueryBuilder'])) {
            return $container->privates['Ibexa\\Core\\Persistence\\Legacy\\Filter\\CriterionQueryBuilder\\LogicalAndQueryBuilder'];
        }

        return $container->privates['Ibexa\\Core\\Persistence\\Legacy\\Filter\\CriterionQueryBuilder\\LogicalAndQueryBuilder'] = new \Ibexa\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\LogicalAndQueryBuilder($a);
    }
}
