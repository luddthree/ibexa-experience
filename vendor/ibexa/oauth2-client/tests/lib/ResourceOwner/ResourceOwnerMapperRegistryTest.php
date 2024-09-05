<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\OAuth2Client\ResourceOwner;

use Ibexa\Contracts\OAuth2Client\Exception\ResourceOwner\MapperNotFoundException;
use Ibexa\Contracts\OAuth2Client\ResourceOwner\ResourceOwnerMapper;
use Ibexa\OAuth2Client\ResourceOwner\ResourceOwnerMapperRegistry;
use PHPUnit\Framework\TestCase;

final class ResourceOwnerMapperRegistryTest extends TestCase
{
    public function testGetResourceOwnerMapper(): void
    {
        $github = $this->createMock(ResourceOwnerMapper::class);
        $gitlab = $this->createMock(ResourceOwnerMapper::class);
        $google = $this->createMock(ResourceOwnerMapper::class);

        $registry = new ResourceOwnerMapperRegistry([
            'github' => $github,
            'gitlab' => $gitlab,
            'google' => $google,
        ]);

        self::assertEquals($github, $registry->getResourceOwnerMapper('github'));
        self::assertEquals($gitlab, $registry->getResourceOwnerMapper('gitlab'));
        self::assertEquals($google, $registry->getResourceOwnerMapper('google'));
    }

    public function testGetResourceOwnerMapperThrowsMapperNotFoundException(): void
    {
        $this->expectException(MapperNotFoundException::class);

        $registry = new ResourceOwnerMapperRegistry([
            'github' => $this->createMock(ResourceOwnerMapper::class),
            'gitlab' => $this->createMock(ResourceOwnerMapper::class),
            'google' => $this->createMock(ResourceOwnerMapper::class),
        ]);

        $registry->getResourceOwnerMapper('microsoft');
    }
}

class_alias(ResourceOwnerMapperRegistryTest::class, 'Ibexa\Platform\Tests\OAuth2Client\ResourceOwner\ResourceOwnerMapperRegistryTest');
