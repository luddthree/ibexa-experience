<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Migration\Serializer\Normalizer\Step\ContentType;

use Ibexa\Migration\ValueObject\ContentType\Matcher;
use Ibexa\Tests\Bundle\Migration\IbexaKernelTestCase;
use LogicException;

/**
 * @covers \Ibexa\Bundle\Migration\Serializer\Normalizer\Step\ContentType\MatcherNormalizer
 */
final class MatcherNormalizerTest extends IbexaKernelTestCase
{
    /** @var \Symfony\Component\Serializer\SerializerInterface */
    private $serializer;

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
    public function testDenormalizeFailure(string $data, string $exceptionClass, ?string $exceptionMessage = null): void
    {
        $this->expectException($exceptionClass);
        if ($exceptionMessage !== null) {
            $this->expectExceptionMessage($exceptionMessage);
        }

        $this->serializer->deserialize($data, Matcher::class, 'yaml');
    }

    /**
     * @dataProvider provideSuccessfulData
     */
    public function testDenormalizeSuccess(string $data, Matcher $matcher): void
    {
        $result = $this->serializer->deserialize($data, Matcher::class, 'yaml');
        self::assertEquals($matcher, $result);
    }

    public function testNormalize(): void
    {
        $matcher = new Matcher('foo', 'bar');
        $serialized = $this->serializer->serialize($matcher, 'yaml');

        self::assertSame(
            <<<YAML
        field: foo
        value: bar
        
        YAML,
            $serialized
        );
    }

    /**
     * @return iterable<array{string, \Ibexa\Migration\ValueObject\ContentType\Matcher}>
     */
    public function provideSuccessfulData(): iterable
    {
        yield [
            <<<YAML
            field: content_type_identifier
            value: bar
            YAML,
            new Matcher('content_type_identifier', 'bar'),
        ];

        yield [
            <<<YAML
            field: location_remote_id
            value: bar
            YAML,
            new Matcher('location_remote_id', 'bar'),
        ];
    }

    /**
     * @return iterable<array{string, class-string<\Throwable>, 2?: string}>
     */
    public function provideFailingData(): iterable
    {
        yield [
            <<<YAML
            field: foo
            value: bar
            YAML,
            LogicException::class,
            'Unable to find "Ibexa\Migration\StepExecutor\ContentType\ContentTypeFinderInterface" supporting "foo" field matching.',
        ];
    }
}
