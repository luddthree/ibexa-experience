<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getUserType2Service extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\Bundle\Search\Form\Type\UserType' shared service.
     *
     * @return \Ibexa\Bundle\Search\Form\Type\UserType
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfony/form/FormTypeInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/symfony/form/AbstractType.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/search/src/bundle/Form/Type/UserType.php';

        $a = ($container->services['ibexa.api.service.user'] ?? $container->getIbexa_Api_Service_UserService());

        if (isset($container->privates['Ibexa\\Bundle\\Search\\Form\\Type\\UserType'])) {
            return $container->privates['Ibexa\\Bundle\\Search\\Form\\Type\\UserType'];
        }

        return $container->privates['Ibexa\\Bundle\\Search\\Form\\Type\\UserType'] = new \Ibexa\Bundle\Search\Form\Type\UserType($a);
    }
}
