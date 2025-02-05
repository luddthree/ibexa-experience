<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getHasUniqueAssetRelationService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\AdminUi\Form\TrashLocationOptionProvider\HasUniqueAssetRelation' shared autowired service.
     *
     * @return \Ibexa\AdminUi\Form\TrashLocationOptionProvider\HasUniqueAssetRelation
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/admin-ui/src/lib/Form/TrashLocationOptionProvider/TrashLocationOptionProvider.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/admin-ui/src/lib/Form/TrashLocationOptionProvider/HasUniqueAssetRelation.php';

        $a = ($container->services['ibexa.api.service.content'] ?? $container->getIbexa_Api_Service_ContentService());

        if (isset($container->privates['Ibexa\\AdminUi\\Form\\TrashLocationOptionProvider\\HasUniqueAssetRelation'])) {
            return $container->privates['Ibexa\\AdminUi\\Form\\TrashLocationOptionProvider\\HasUniqueAssetRelation'];
        }

        return $container->privates['Ibexa\\AdminUi\\Form\\TrashLocationOptionProvider\\HasUniqueAssetRelation'] = new \Ibexa\AdminUi\Form\TrashLocationOptionProvider\HasUniqueAssetRelation($a, ($container->services['Symfony\\Contracts\\Translation\\TranslatorInterface'] ?? $container->getTranslatorInterfaceService()));
    }
}
