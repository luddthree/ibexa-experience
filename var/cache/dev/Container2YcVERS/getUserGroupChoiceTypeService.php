<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getUserGroupChoiceTypeService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\AdminUi\Form\Type\UserGroupChoiceType' shared autowired service.
     *
     * @return \Ibexa\AdminUi\Form\Type\UserGroupChoiceType
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfony/form/FormTypeInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/symfony/form/AbstractType.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/admin-ui/src/lib/Form/Type/UserGroupChoiceType.php';

        $a = ($container->services['ibexa.api.repository'] ?? $container->getIbexa_Api_RepositoryService());

        if (isset($container->privates['Ibexa\\AdminUi\\Form\\Type\\UserGroupChoiceType'])) {
            return $container->privates['Ibexa\\AdminUi\\Form\\Type\\UserGroupChoiceType'];
        }
        $b = ($container->services['ibexa.api.service.user'] ?? $container->getIbexa_Api_Service_UserService());

        if (isset($container->privates['Ibexa\\AdminUi\\Form\\Type\\UserGroupChoiceType'])) {
            return $container->privates['Ibexa\\AdminUi\\Form\\Type\\UserGroupChoiceType'];
        }

        return $container->privates['Ibexa\\AdminUi\\Form\\Type\\UserGroupChoiceType'] = new \Ibexa\AdminUi\Form\Type\UserGroupChoiceType($a, ($container->services['ibexa.api.service.search'] ?? $container->getIbexa_Api_Service_SearchService()), $b);
    }
}
