<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\OAuth2Client\DependencyInjection;

use Ibexa\Bundle\OAuth2Client\DependencyInjection\IbexaOAuth2ClientExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

final class IbexaOAuth2ClientExtensionTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions(): array
    {
        return [
            new IbexaOAuth2ClientExtension(),
        ];
    }

    public function testExtensionIsRegistered(): void
    {
        self::assertTrue($this->container->hasExtension('ibexa_oauth2_client'));
    }
}

class_alias(IbexaOAuth2ClientExtensionTest::class, 'Ibexa\Platform\Tests\Bundle\OAuth2Client\DependencyInjection\IbexaPlatformOAuth2ClientExtensionTest');
