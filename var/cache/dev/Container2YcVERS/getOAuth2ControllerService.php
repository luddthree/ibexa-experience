<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getOAuth2ControllerService extends App_KernelDevDebugContainer
{
    /**
     * Gets the public 'Ibexa\Bundle\OAuth2Client\Controller\OAuth2Controller' shared autowired service.
     *
     * @return \Ibexa\Bundle\OAuth2Client\Controller\OAuth2Controller
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfony/framework-bundle/Controller/AbstractController.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/oauth2-client/src/bundle/Controller/OAuth2Controller.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/oauth2-client/src/contracts/Client/ClientRegistry.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/oauth2-client/src/lib/Client/ClientRegistry.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/oauth2-client/src/lib/Config/OAuth2ConfigurationInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/oauth2-client/src/lib/Config/OAuth2Configuration.php';
        include_once \dirname(__DIR__, 4).'/vendor/knpuniversity/oauth2-client-bundle/src/Client/ClientRegistry.php';

        $container->services['Ibexa\\Bundle\\OAuth2Client\\Controller\\OAuth2Controller'] = $instance = new \Ibexa\Bundle\OAuth2Client\Controller\OAuth2Controller(new \Ibexa\OAuth2Client\Client\ClientRegistry(($container->services['knpu.oauth2.registry'] ?? ($container->services['knpu.oauth2.registry'] = new \KnpU\OAuth2ClientBundle\Client\ClientRegistry($container, []))), new \Ibexa\OAuth2Client\Config\OAuth2Configuration(($container->services['Ibexa\\Bundle\\Core\\DependencyInjection\\Configuration\\ChainConfigResolver'] ?? $container->getChainConfigResolverService()))), true);

        $instance->setContainer(($container->privates['.service_locator.mx0UMmY'] ?? $container->load('get_ServiceLocator_Mx0UMmYService'))->withContext('Ibexa\\Bundle\\OAuth2Client\\Controller\\OAuth2Controller', $container));

        return $instance;
    }
}
