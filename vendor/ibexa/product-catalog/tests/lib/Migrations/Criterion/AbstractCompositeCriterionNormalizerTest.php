<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Migrations\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\AbstractCompositeCriterion;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\CriterionInterface;
use Ibexa\ProductCatalog\Migrations\Criterion\AbstractCompositeCriterionNormalizer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @template T of \Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\AbstractCompositeCriterion
 */
abstract class AbstractCompositeCriterionNormalizerTest extends TestCase
{
    /**
     * @phpstan-var \Ibexa\ProductCatalog\Migrations\Criterion\AbstractCompositeCriterionNormalizer<
     *     T,
     * >
     */
    protected AbstractCompositeCriterionNormalizer $normalizer;

    abstract protected function getHandledType(): string;

    /**
     * @return class-string<T>
     */
    abstract protected function getHandledClass(): string;

    final public function testDenormalize(): void
    {
        $data = [
            'type' => $this->getHandledType(),
            'criteria' => [
                ['foo' => true],
                ['bar' => false],
            ],
        ];

        $criteria = [
            $this->createMock(CriterionInterface::class),
            $this->createMock(CriterionInterface::class),
        ];
        $innerDenormalizer = $this->createMock(DenormalizerInterface::class);
        $innerDenormalizer
            ->expects(self::exactly(2))
            ->method('denormalize')
            ->withConsecutive(
                [
                    self::identicalTo([
                        'foo' => true,
                    ]),
                    self::identicalTo(CriterionInterface::class),
                ],
                [
                    self::identicalTo([
                        'bar' => false,
                    ]),
                    self::identicalTo(CriterionInterface::class),
                ],
            )
            ->willReturnOnConsecutiveCalls(
                $criteria[0],
                $criteria[1],
            );

        $this->normalizer->setDenormalizer($innerDenormalizer);

        $result = $this->normalizer->denormalize($data, CriterionInterface::class);

        self::assertInstanceOf(AbstractCompositeCriterion::class, $result);
        self::assertInstanceOf($this->getHandledClass(), $result);
        self::assertSame($criteria, $result->getCriteria());
    }

    final public function testNormalize(): void
    {
        $innerCriterion = $this->createMock(CriterionInterface::class);
        $innerNormalizer = $this->createMock(NormalizerInterface::class);
        $innerNormalizer
            ->expects(self::once())
            ->method('normalize')
            ->with(
                self::identicalTo([$innerCriterion]),
            )
            ->willReturn([
                ['foo' => true],
            ]);
        $this->normalizer->setNormalizer($innerNormalizer);

        $class = $this->getHandledClass();
        $result = $this->normalizer->normalize(new $class($innerCriterion));

        self::assertSame([
            'type' => $this->getHandledType(),
            'criteria' => [
                ['foo' => true],
            ],
        ], $result);
    }

    public function testSupportsNormalization(): void
    {
        self::assertFalse($this->normalizer->supportsNormalization((object)[]));
        self::assertFalse($this->normalizer->supportsNormalization($this->createMock(CriterionInterface::class)));
        self::assertFalse($this->normalizer->supportsNormalization($this->createMock(AbstractCompositeCriterion::class)));
        self::assertTrue($this->normalizer->supportsNormalization($this->createMock($this->getHandledClass())));
    }

    public function testSupportsDenormalization(): void
    {
        self::assertFalse($this->normalizer->supportsDenormalization(null, 'foo'));
        self::assertFalse($this->normalizer->supportsDenormalization(null, AbstractCompositeCriterion::class));
        self::assertFalse($this->normalizer->supportsDenormalization(null, CriterionInterface::class));
        self::assertTrue($this->normalizer->supportsDenormalization(
            ['type' => $this->getHandledType()],
            CriterionInterface::class,
        ));
    }
}
