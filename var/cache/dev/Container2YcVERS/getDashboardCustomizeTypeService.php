<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getDashboardCustomizeTypeService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\Bundle\Dashboard\Form\Type\DashboardCustomizeType' shared autowired service.
     *
     * @return \Ibexa\Bundle\Dashboard\Form\Type\DashboardCustomizeType
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfony/form/FormTypeInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/symfony/form/AbstractType.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/dashboard/src/bundle/Form/Type/DashboardCustomizeType.php';

        return $container->privates['Ibexa\\Bundle\\Dashboard\\Form\\Type\\DashboardCustomizeType'] = new \Ibexa\Bundle\Dashboard\Form\Type\DashboardCustomizeType();
    }
}
