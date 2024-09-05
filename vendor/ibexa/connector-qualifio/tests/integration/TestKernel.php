<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ConnectorQualifio;

use DAMA\DoctrineTestBundle\DAMADoctrineTestBundle;
use Ibexa\Bundle\ConnectorQualifio\IbexaConnectorQualifioBundle;
use Ibexa\Bundle\FieldTypeRichText\IbexaFieldTypeRichTextBundle;
use Ibexa\Contracts\Test\Core\IbexaTestKernel;
use Lexik\Bundle\JWTAuthenticationBundle\LexikJWTAuthenticationBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class TestKernel extends IbexaTestKernel
{
    public function registerBundles(): iterable
    {
        yield from parent::registerBundles();

        yield new IbexaConnectorQualifioBundle();
        yield new IbexaFieldTypeRichTextBundle();
        yield new LexikJWTAuthenticationBundle();
        yield new DAMADoctrineTestBundle();
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        parent::registerContainerConfiguration($loader);

        $loader->load(__DIR__ . '/Resources/framework.yaml');
        $loader->load(__DIR__ . '/Resources/ibexa_connector_qualifio.yaml');
        $loader->load(__DIR__ . '/Resources/jwk_authentication.yaml');
        $loader->load(__DIR__ . '/Resources/services.yaml');

        $loader->load(static function (ContainerBuilder $container): void {
            $container->setParameter('form.type_extension.csrf.enabled', false);
        });
    }
}
