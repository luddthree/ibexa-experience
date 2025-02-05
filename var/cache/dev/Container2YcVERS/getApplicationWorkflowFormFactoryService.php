<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getApplicationWorkflowFormFactoryService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\CorporateAccount\Form\ApplicationWorkflowFormFactory' shared autowired service.
     *
     * @return \Ibexa\CorporateAccount\Form\ApplicationWorkflowFormFactory
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/corporate-account/src/lib/Form/ContentFormFactory.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/corporate-account/src/lib/Form/ApplicationWorkflowFormFactory.php';

        $a = ($container->services['.container.private.form.factory'] ?? $container->get_Container_Private_Form_FactoryService());

        if (isset($container->privates['Ibexa\\CorporateAccount\\Form\\ApplicationWorkflowFormFactory'])) {
            return $container->privates['Ibexa\\CorporateAccount\\Form\\ApplicationWorkflowFormFactory'];
        }

        return $container->privates['Ibexa\\CorporateAccount\\Form\\ApplicationWorkflowFormFactory'] = new \Ibexa\CorporateAccount\Form\ApplicationWorkflowFormFactory($a, ($container->privates['Ibexa\\CorporateAccount\\Configuration\\CorporateAccount'] ?? $container->getCorporateAccountService()));
    }
}
