<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\OAuth2Client\Twig;

use Ibexa\Bundle\OAuth2Client\Twig\OAuth2Extension;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Test\IntegrationTestCase;

final class OAuth2ExtensionTest extends IntegrationTestCase
{
    protected function getExtensions(): array
    {
        return [
            new OAuth2Extension($this->getUrlGenerator()),
        ];
    }

    protected function getFixturesDir(): string
    {
        return __DIR__ . '/_fixtures';
    }

    private function getUrlGenerator(): UrlGeneratorInterface
    {
        $generateCallback = static function ($name, $parameters, $referenceType): string {
            return json_encode([
                '$name' => $name,
                '$parameters' => $parameters,
                '$referenceType' => $referenceType,
            ]);
        };

        $generator = $this->createMock(UrlGeneratorInterface::class);
        $generator->method('generate')->willReturnCallback($generateCallback);

        return $generator;
    }
}

class_alias(OAuth2ExtensionTest::class, 'Ibexa\Platform\Tests\Bundle\OAuth2Client\Twig\OAuth2ExtensionTest');
