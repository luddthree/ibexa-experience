<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Platform\Segmentation\Tests\integration;

use Ibexa\Contracts\Core\Test\IbexaKernelTestCase;
use Symfony\Component\Serializer\SerializerInterface;

abstract class AbstractSerializerTestCase extends IbexaKernelTestCase
{
    protected SerializerInterface $serializer;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->serializer = self::getServiceByClassName(
            SerializerInterface::class,
            'ibexa.migrations.serializer'
        );
    }
}
