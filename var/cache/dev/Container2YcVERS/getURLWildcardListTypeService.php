<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getURLWildcardListTypeService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\AdminUi\Form\Type\URLWildcard\URLWildcardListType' shared autowired service.
     *
     * @return \Ibexa\AdminUi\Form\Type\URLWildcard\URLWildcardListType
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfony/form/FormTypeInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/symfony/form/AbstractType.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/admin-ui/src/lib/Form/Type/URLWildcard/URLWildcardListType.php';

        return $container->privates['Ibexa\\AdminUi\\Form\\Type\\URLWildcard\\URLWildcardListType'] = new \Ibexa\AdminUi\Form\Type\URLWildcard\URLWildcardListType();
    }
}
