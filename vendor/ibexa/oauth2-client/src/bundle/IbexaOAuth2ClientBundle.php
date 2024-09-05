<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\OAuth2Client;

use Ibexa\Bundle\OAuth2Client\DependencyInjection\Configuration\Parser\OAuth2Parser;
use Ibexa\Bundle\OAuth2Client\DependencyInjection\IbexaOAuth2ClientExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class IbexaOAuth2ClientBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        /** @var \Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension */
        $core = $container->getExtension('ibexa');
        $core->addConfigParser(new OAuth2Parser());
        $core->addDefaultSettings(__DIR__ . '/Resources/config', ['defaults.yaml']);
    }

    public function getContainerExtension(): ExtensionInterface
    {
        return new IbexaOAuth2ClientExtension();
    }
}

class_alias(IbexaOAuth2ClientBundle::class, 'Ibexa\Platform\Bundle\OAuth2Client\IbexaPlatformOAuth2ClientBundle');
