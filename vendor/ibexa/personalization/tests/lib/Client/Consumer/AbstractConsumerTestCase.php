<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Client\Consumer;

use Ibexa\Personalization\Client\PersonalizationClientInterface;
use PHPUnit\Framework\TestCase;

abstract class AbstractConsumerTestCase extends TestCase
{
    /** @var \Ibexa\Personalization\Client\PersonalizationClientInterface|\PHPUnit\Framework\MockObject\MockObject */
    protected $client;

    public function setUp(): void
    {
        parent::setUp();

        $this->client = $this->createMock(PersonalizationClientInterface::class);
    }
}

class_alias(AbstractConsumerTestCase::class, 'Ibexa\Platform\Tests\Personalization\Client\Consumer\AbstractConsumerTestCase');
