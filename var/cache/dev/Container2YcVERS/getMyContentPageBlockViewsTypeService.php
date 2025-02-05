<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getMyContentPageBlockViewsTypeService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Overblog\GraphQLBundle\__DEFINITIONS__\MyContentPageBlockViewsType' shared service.
     *
     * @return object An instance returned by Overblog\GraphQLBundle\Definition\Builder\TypeFactory::create()
     */
    public static function do($container, $lazyLoad = true)
    {
        $a = ($container->privates['Overblog\\GraphQLBundle\\Definition\\Builder\\TypeFactory'] ?? $container->load('getTypeFactoryService'));

        if (isset($container->privates['Overblog\\GraphQLBundle\\__DEFINITIONS__\\MyContentPageBlockViewsType'])) {
            return $container->privates['Overblog\\GraphQLBundle\\__DEFINITIONS__\\MyContentPageBlockViewsType'];
        }

        return $container->privates['Overblog\\GraphQLBundle\\__DEFINITIONS__\\MyContentPageBlockViewsType'] = $a->create('Overblog\\GraphQLBundle\\__DEFINITIONS__\\MyContentPageBlockViewsType');
    }
}
