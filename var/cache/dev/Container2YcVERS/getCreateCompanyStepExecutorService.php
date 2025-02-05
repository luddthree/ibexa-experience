<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getCreateCompanyStepExecutorService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\CorporateAccount\Migrations\StepExecutor\Company\CreateCompanyStepExecutor' shared autowired service.
     *
     * @return \Ibexa\CorporateAccount\Migrations\StepExecutor\Company\CreateCompanyStepExecutor
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/migrations/src/lib/StepExecutor/StepExecutorInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/migrations/src/lib/StepExecutor/UserContextAwareStepExecutorInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/symfony/service-contracts/ServiceSubscriberTrait.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/migrations/src/lib/StepExecutor/UserContextAwareStepExecutorTrait.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/migrations/src/contracts/StepExecutor/AbstractStepExecutor.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/migrations/src/lib/Log/LoggerAwareTrait.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/corporate-account/src/lib/Migrations/StepExecutor/Company/CreateCompanyStepExecutor.php';

        $container->privates['Ibexa\\CorporateAccount\\Migrations\\StepExecutor\\Company\\CreateCompanyStepExecutor'] = $instance = new \Ibexa\CorporateAccount\Migrations\StepExecutor\Company\CreateCompanyStepExecutor(($container->privates['Ibexa\\CorporateAccount\\Event\\CompanyService'] ?? $container->getCompanyServiceService()), ($container->privates['Ibexa\\CorporateAccount\\Event\\ShippingAddressService'] ?? $container->getShippingAddressServiceService()), ($container->services['ibexa.api.service.content_type'] ?? $container->getIbexa_Api_Service_ContentTypeService()), ($container->privates['Ibexa\\Migration\\Service\\FieldTypeService'] ?? $container->load('getFieldTypeServiceService')), ($container->privates['Ibexa\\CorporateAccount\\Configuration\\CorporateAccount'] ?? $container->getCorporateAccountService()));

        $instance->setLogger(($container->privates['monolog.logger'] ?? $container->getMonolog_LoggerService()));
        $instance->setContainer((new \Symfony\Component\DependencyInjection\Argument\ServiceLocator($container->getService, [
            'Ibexa\\Contracts\\Core\\Persistence\\TransactionHandler' => ['privates', 'Ibexa\\Core\\Persistence\\Cache\\TransactionHandler', 'getTransactionHandlerService', false],
            'Ibexa\\Migration\\Reference\\CollectorInterface' => ['privates', 'Ibexa\\Migration\\Reference\\Collector', 'getCollectorService', true],
            'Ibexa\\Migration\\StepExecutor\\ReferenceDefinition\\ResolverInterface' => ['privates', 'Ibexa\\CorporateAccount\\Migrations\\StepExecutor\\ReferenceDefinition\\DelegatingReferenceResolver', 'getDelegatingReferenceResolverService', true],
        ], [
            'Ibexa\\Contracts\\Core\\Persistence\\TransactionHandler' => '?',
            'Ibexa\\Migration\\Reference\\CollectorInterface' => '?',
            'Ibexa\\Migration\\StepExecutor\\ReferenceDefinition\\ResolverInterface' => 'Ibexa\\Migration\\StepExecutor\\ReferenceDefinition\\ResolverInterface',
        ]))->withContext('Ibexa\\CorporateAccount\\Migrations\\StepExecutor\\Company\\CreateCompanyStepExecutor', $container));

        return $instance;
    }
}
