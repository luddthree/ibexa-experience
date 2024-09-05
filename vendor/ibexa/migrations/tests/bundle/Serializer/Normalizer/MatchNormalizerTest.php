<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Serializer\Normalizer;

use Ibexa\Migration\ValueObject;
use Ibexa\Migration\ValueObject\AbstractMatcher;
use Ibexa\Tests\Bundle\Migration\IbexaKernelTestCase;
use PHPUnit\Framework\Assert;

/**
 * @covers \Ibexa\Bundle\Migration\Serializer\Normalizer\MatchNormalizer
 */
final class MatchNormalizerTest extends IbexaKernelTestCase
{
    private const SERIALIZED_MATCH_PATTERN = <<<expect
        field: %s
        value: __VALUE__
        
        expect;

    /** @var \Symfony\Component\Serializer\SerializerInterface */
    private $serializer;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->serializer = self::getMigrationSerializer();
    }

    /**
     * @dataProvider provideForDeserialization
     *
     * @param class-string<\Ibexa\Migration\ValueObject\AbstractMatcher> $expectedType
     */
    public function testDeserialization(string $field, string $expectedType): void
    {
        /** @var \Ibexa\Migration\ValueObject\AbstractMatcher $matchObject */
        $matchObject = $this->serializer->deserialize(
            self::prepareSerializedMatch($field),
            $expectedType,
            'yaml'
        );

        Assert::assertInstanceOf($expectedType, $matchObject);
        Assert::assertEquals($field, $matchObject->field);
        Assert::assertEquals('__VALUE__', $matchObject->value);
    }

    /**
     * @return iterable<array{string, class-string<\Ibexa\Migration\ValueObject\AbstractMatcher>}>
     */
    public function provideForDeserialization(): iterable
    {
        yield [
            'id',
            ValueObject\UserGroup\Matcher::class,
        ];

        yield [
            'remoteId',
            ValueObject\UserGroup\Matcher::class,
        ];

        yield [
            'location_remote_id',
            ValueObject\Location\Matcher::class,
        ];

        yield [
            'location_id',
            ValueObject\Location\Matcher::class,
        ];

        yield [
            'id',
            ValueObject\Section\Matcher::class,
        ];

        yield [
            'identifier',
            ValueObject\Section\Matcher::class,
        ];
    }

    /**
     * @dataProvider provideForFailingDeserialization
     *
     * @param class-string<\Ibexa\Migration\ValueObject\AbstractMatcher> $expectedType
     */
    public function testFailingDeserialization(string $field, string $expectedType, string $message): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($message);
        $this->serializer->deserialize(
            self::prepareSerializedMatch($field),
            $expectedType,
            'yaml'
        );
    }

    /**
     * @return iterable<array{string, class-string<\Ibexa\Migration\ValueObject\AbstractMatcher>, string}>
     */
    public function provideForFailingDeserialization(): iterable
    {
        yield [
            'foo',
            ValueObject\UserGroup\Matcher::class,
            'Unknown field name: foo. Supported fields: [id|remoteId]',
        ];

        yield [
            'foo',
            ValueObject\Location\Matcher::class,
            'Unknown field name: foo. Supported fields: [location_remote_id|location_id]',
        ];

        yield [
            'foo',
            ValueObject\Section\Matcher::class,
            'Unknown field name: foo. Supported fields: [identifier|id]',
        ];
    }

    public function testSerialization(): void
    {
        $match = $this->createMatchInstance();

        self::assertSame(
            self::prepareSerializedMatch('__FIELD__'),
            $this->serializer->serialize($match, 'yaml')
        );
    }

    private function createMatchInstance(): AbstractMatcher
    {
        return new class('__FIELD__', '__VALUE__') extends AbstractMatcher {
            protected function getSupportedFields(): array
            {
                return [
                    '__FIELD__' => '__FIELD__',
                ];
            }
        };
    }

    private static function prepareSerializedMatch(string $field): string
    {
        return sprintf(self::SERIALIZED_MATCH_PATTERN, $field);
    }
}

class_alias(MatchNormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Serializer\Normalizer\MatchNormalizerTest');
