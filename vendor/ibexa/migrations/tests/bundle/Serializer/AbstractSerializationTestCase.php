<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Serializer;

use Ibexa\Core\Repository\Values\User\UserReference;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Tests\Bundle\Migration\IbexaKernelTestCase;
use Ibexa\Tests\Migration\FileLoadTrait;

abstract class AbstractSerializationTestCase extends IbexaKernelTestCase
{
    use FileLoadTrait;

    private const FIXTURE_ADMIN_ID = 14;

    /** @var \Symfony\Component\Serializer\SerializerInterface */
    private $serializer;

    protected function setUp(): void
    {
        self::bootKernel();
        self::getPermissionResolver()->setCurrentUserReference(new UserReference(self::FIXTURE_ADMIN_ID));
        $this->serializer = self::getMigrationSerializer();
    }

    /**
     * @dataProvider provideForDeserialization
     */
    public function testDeserialization(string $sourceString, callable $expectation): void
    {
        $data = $this->serializer->deserialize($sourceString, StepInterface::class . '[]', 'yaml');

        $expectation($data);
    }

    /**
     * @dataProvider provideForSerialization
     *
     * @param array{\Ibexa\Migration\ValueObject\Step\StepInterface[]} $serializedData
     */
    public function testSerialization(array $serializedData, string $expect): void
    {
        self::assertSame($expect, $this->serializer->serialize($serializedData, 'yaml'));
    }

    /**
     * @return iterable<array{\Ibexa\Migration\ValueObject\Step\StepInterface[], string}>
     */
    abstract public function provideForSerialization(): iterable;

    /**
     * @return iterable<array{string, callable(mixed): void}>
     */
    abstract public function provideForDeserialization(): iterable;
}

class_alias(AbstractSerializationTestCase::class, 'Ibexa\Platform\Tests\Bundle\Migration\Serializer\AbstractSerializationTestCase');
