<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\OAuth2Client\DependencyInjection\Configuration\Parser;

use Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension;
use Ibexa\Bundle\OAuth2Client\DependencyInjection\Configuration\Parser\OAuth2Parser;
use Ibexa\Bundle\OAuth2Client\DependencyInjection\IbexaOAuth2ClientExtension;
use Ibexa\Tests\Bundle\Core\DependencyInjection\Configuration\Parser\AbstractParserTestCase;

final class OAuth2ParserTest extends AbstractParserTestCase
{
    private const EXAMPLE_OAUTH2_PROVIDERS = ['google', 'microsoft', 'github'];

    protected function getContainerExtensions(): array
    {
        return [
            new IbexaCoreExtension([
                new OAuth2Parser(),
            ]),
            new IbexaOAuth2ClientExtension(),
        ];
    }

    protected function getMinimalConfiguration(): array
    {
        return [
            'system' => [
                'default' => [
                    'oauth2' => [
                        'enabled' => false,
                        'clients' => [],
                    ],
                ],
            ],
        ];
    }

    public function testDefaultSettings(): void
    {
        $this->load();

        $this->assertConfigResolverParameterValue('oauth2.enabled', false, 'ibexa_demo_site');
        $this->assertConfigResolverParameterValue('oauth2.clients', [], 'ibexa_demo_site');
    }

    public function testOverwrittenConfig()
    {
        $this->load([
            'system' => [
                'ibexa_demo_site' => [
                    'oauth2' => [
                        'enabled' => true,
                        'clients' => self::EXAMPLE_OAUTH2_PROVIDERS,
                    ],
                ],
            ],
        ]);

        $this->assertConfigResolverParameterValue('oauth2.enabled', true, 'ibexa_demo_site');
        $this->assertConfigResolverParameterValue('oauth2.clients', self::EXAMPLE_OAUTH2_PROVIDERS, 'ibexa_demo_site');
    }
}

class_alias(OAuth2ParserTest::class, 'Ibexa\Platform\Tests\Bundle\OAuth2Client\DependencyInjection\Configuration\Parser\OAuth2ParserTest');
