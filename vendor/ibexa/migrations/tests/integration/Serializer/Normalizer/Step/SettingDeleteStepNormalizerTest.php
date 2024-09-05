<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Migration\Serializer\Normalizer\Step;

use Ibexa\Migration\ValueObject\Step\SettingDeleteStep;
use Ibexa\Tests\Bundle\Migration\IbexaKernelTestCase;
use Symfony\Component\Serializer\SerializerInterface;

final class SettingDeleteStepNormalizerTest extends IbexaKernelTestCase
{
    private SerializerInterface $serializer;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->serializer = self::getMigrationSerializer();
    }

    /**
     * @dataProvider provideFailingData
     *
     * @param class-string<\Throwable> $exceptionClass
     */
    public function testDenormalizeFailure(
        string $data,
        string $exceptionClass,
        ?string $exceptionMessage = null
    ): void {
        $this->expectException($exceptionClass);
        if ($exceptionMessage !== null) {
            $this->expectExceptionMessage($exceptionMessage);
        }

        $this->serializer->deserialize($data, SettingDeleteStep::class, 'yaml');
    }

    /**
     * @dataProvider provideSuccessfulData
     */
    public function testDenormalizeSuccess(string $data, SettingDeleteStep $matcher): void
    {
        $result = $this->serializer->deserialize($data, SettingDeleteStep::class, 'yaml');
        self::assertEquals($matcher, $result);
    }

    public function testNormalize(): void
    {
        $matcher = new SettingDeleteStep('foo_group', 'foo_value');
        $serialized = $this->serializer->serialize($matcher, 'yaml');

        self::assertSame(
            <<<YAML
            type: setting
            mode: delete
            group: foo_group
            identifier: foo_value
            
            YAML,
            $serialized
        );
    }

    /**
     * @return iterable<array{string, \Ibexa\Migration\ValueObject\Step\SettingDeleteStep}>
     */
    public function provideSuccessfulData(): iterable
    {
        yield [
            <<<YAML
            type: setting
            mode: delete
            group: foo_group
            identifier: foo_identifier
            YAML,
            new SettingDeleteStep('foo_group', 'foo_identifier'),
        ];
    }

    /**
     * @return iterable<array{string, class-string<\Throwable>, 2?: string}>
     */
    public function provideFailingData(): iterable
    {
        yield [
            <<<YAML
            type: setting
            mode: delete
            group: foo_group
            YAML,
            \LogicException::class,
            'Expected the key "identifier" to exist.',
        ];

        yield [
            <<<YAML
            type: setting
            mode: delete
            identifier: foo_value
            YAML,
            \LogicException::class,
            'Expected the key "group" to exist.',
        ];
    }
}
