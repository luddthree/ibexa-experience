<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Serializer\Normalizer\Step\ContentType;

use Ibexa\Bundle\Migration\Serializer\Normalizer\Step\ContentType\MatcherNormalizer;
use Ibexa\Migration\StepExecutor\ContentType\ContentTypeFinderRegistryInterface;
use Ibexa\Migration\ValueObject\AbstractMatcher;
use Ibexa\Migration\ValueObject\ContentType\Matcher;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Bundle\Migration\Serializer\Normalizer\Step\ContentType\MatcherNormalizer
 */
final class MatcherNormalizerTest extends TestCase
{
    /** @var \Ibexa\Bundle\Migration\Serializer\Normalizer\Step\ContentType\MatcherNormalizer */
    private $normalizer;

    /** @var \Ibexa\Migration\StepExecutor\ContentType\ContentTypeFinderRegistryInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $registry;

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ContentTypeFinderRegistryInterface::class);
        $this->normalizer = new MatcherNormalizer($this->registry);
    }

    public function testDenormalizeWithEmptyRegistry(): void
    {
        $this->registry
            ->expects(self::once())
            ->method('getFinder')
            ->with('foo')
            ->willThrowException(new \LogicException());

        $this->expectException(\LogicException::class);
        $this->normalizer->denormalize([
            'field' => 'foo',
            'value' => 'bar',
        ], Matcher::class);
    }

    public function testDenormalizeWithFinderExistingInRegistry(): void
    {
        $this->registry
            ->expects(self::once())
            ->method('getFinder')
            ->with('foo');

        $matcher = $this->normalizer->denormalize([
            'field' => 'foo',
            'value' => 'bar',
        ], Matcher::class);

        self::assertSame('foo', $matcher->getField());
        self::assertSame('bar', $matcher->getValue());
    }

    public function testNormalize(): void
    {
        $matcher = new Matcher('foo', 'bar');

        $normalized = $this->normalizer->normalize($matcher);

        self::assertSame([
            'field' => 'foo',
            'value' => 'bar',
        ], $normalized);
    }

    public function testSupportsDenormalization(): void
    {
        self::assertFalse($this->normalizer->supportsDenormalization(null, \stdClass::class));
        self::assertFalse($this->normalizer->supportsDenormalization(null, AbstractMatcher::class));
        self::assertTrue($this->normalizer->supportsDenormalization(null, Matcher::class));
    }

    public function testSupportsNormalization(): void
    {
        self::assertFalse($this->normalizer->supportsNormalization(new \stdClass()));

        $abstractMatcher = $this->createMock(AbstractMatcher::class);
        self::assertFalse($this->normalizer->supportsNormalization($abstractMatcher));

        self::assertTrue($this->normalizer->supportsNormalization(new Matcher('foo', 'bar')));
    }
}

class_alias(MatcherNormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Serializer\Normalizer\Step\ContentType\MatcherNormalizerTest');
