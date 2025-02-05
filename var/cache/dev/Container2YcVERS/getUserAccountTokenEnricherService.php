<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getUserAccountTokenEnricherService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\ConnectorQualifio\Security\JWT\Token\Enricher\UserAccountTokenEnricher' shared autowired service.
     *
     * @return \Ibexa\ConnectorQualifio\Security\JWT\Token\Enricher\UserAccountTokenEnricher
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/connector-qualifio/src/lib/Security/JWT/Token/TokenEnricherInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/connector-qualifio/src/lib/Security/JWT/Token/AbstractTokenEnricher.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/connector-qualifio/src/lib/Security/JWT/Token/Enricher/UserAccountTokenEnricher.php';

        $a = ($container->services['ibexa.api.repository'] ?? $container->getIbexa_Api_RepositoryService());

        if (isset($container->privates['Ibexa\\ConnectorQualifio\\Security\\JWT\\Token\\Enricher\\UserAccountTokenEnricher'])) {
            return $container->privates['Ibexa\\ConnectorQualifio\\Security\\JWT\\Token\\Enricher\\UserAccountTokenEnricher'];
        }

        return $container->privates['Ibexa\\ConnectorQualifio\\Security\\JWT\\Token\\Enricher\\UserAccountTokenEnricher'] = new \Ibexa\ConnectorQualifio\Security\JWT\Token\Enricher\UserAccountTokenEnricher(($container->privates['Ibexa\\ConnectorQualifio\\Service\\QualifioFieldMapResolver'] ?? $container->load('getQualifioFieldMapResolverService')), $a);
    }
}
