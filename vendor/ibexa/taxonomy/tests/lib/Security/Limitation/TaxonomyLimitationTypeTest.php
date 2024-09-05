<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Taxonomy\Security\Limitation;

use Ibexa\Contracts\Core\Limitation\Type;
use Ibexa\Contracts\Core\Persistence\Handler;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation;
use Ibexa\Contracts\Core\Repository\Values\User\UserReference;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Base\Exceptions\InvalidArgumentType;
use Ibexa\Taxonomy\Persistence\Entity\TaxonomyEntry as PersistenceTaxonomyEntry;
use Ibexa\Taxonomy\Security\Limitation\TaxonomyLimitationType;
use Ibexa\Taxonomy\Security\Limitation\Value\TaxonomyLimitation;
use Ibexa\Taxonomy\Security\ValueObject\TaxonomyValue;
use Ibexa\Taxonomy\Service\TaxonomyConfiguration;
use PHPUnit\Framework\TestCase;
use stdClass;

final class TaxonomyLimitationTypeTest extends TestCase
{
    private TaxonomyLimitationType $limitationType;

    /** @var \Ibexa\Contracts\Core\Persistence\Handler|\PHPUnit\Framework\MockObject\MockObject */
    private Handler $handler;

    /** @var \Ibexa\Taxonomy\Service\TaxonomyConfiguration|\PHPUnit\Framework\MockObject\MockObject */
    private TaxonomyConfiguration $taxonomyConfiguration;

    protected function setUp(): void
    {
        $this->handler = $this->createMock(Handler::class);
        $this->taxonomyConfiguration = $this->createMock(TaxonomyConfiguration::class);

        $this->limitationType = new TaxonomyLimitationType(
            $this->handler,
            $this->taxonomyConfiguration,
        );
    }

    /**
     * @dataProvider dataProviderForTestAcceptValue
     */
    public function testAcceptValue(Limitation $limitation, InvalidArgumentType $expectedException): void
    {
        $this->expectExceptionObject($expectedException);

        $this->limitationType->acceptValue($limitation);
    }

    /**
     * @return iterable<array{
     *     \Ibexa\Contracts\Core\Repository\Values\User\Limitation,
     *     \Ibexa\Core\Base\Exceptions\InvalidArgumentType
     * }>
     */
    public function dataProviderForTestAcceptValue(): iterable
    {
        $wrongTypeLimitation = $this->createMock(Limitation::class);

        yield 'throws on TaxonomyLimitation limitation' => [
            $wrongTypeLimitation,
            new InvalidArgumentType(
                '$limitationValue',
                'TaxonomyLimitation',
                $wrongTypeLimitation,
            ),
        ];

        $wrongPropLimitation = new TaxonomyLimitation([
            'limitationValues' => 'foobar',
        ]);
        yield 'throws when limitation values prop is not an array' => [
            $wrongPropLimitation,
            new InvalidArgumentType(
                '$limitationValue->limitationValues',
                'array',
                $wrongPropLimitation->limitationValues,
            ),
        ];

        $wrongValueTypeLimitation = new TaxonomyLimitation([
            'limitationValues' => ['foobar', new stdClass()],
        ]);
        yield 'throws when one of the values is invalid type' => [
            $wrongValueTypeLimitation,
            new InvalidArgumentType(
                '$limitationValue->limitationValues[1]',
                'string|int',
                new stdClass()
            ),
        ];
    }

    public function testValidate(): void
    {
        $this->taxonomyConfiguration
            ->method('getTaxonomies')
            ->willReturn(['foo']);

        $validationErrors = $this->limitationType->validate(
            new TaxonomyLimitation([
                'limitationValues' => ['foo', 'bar'],
            ])
        );

        /** @var \Ibexa\Contracts\Core\Repository\Values\Translation\Message $translatableMessage */
        $translatableMessage = $validationErrors[0]->getTranslatableMessage();

        self::assertCount(1, $validationErrors);
        self::assertEquals(
            "limitationValues[1] => 'bar' does not exist in the backend",
            (string) $translatableMessage,
        );
    }

    public function testBuildValue(): void
    {
        self::assertEquals(
            new TaxonomyLimitation([
                'limitationValues' => ['foo', 'bar'],
            ]),
            $this->limitationType->buildValue(['foo', 'bar']),
        );
    }

    /**
     * @dataProvider dataProviderForTestEvaluate
     *
     * @param array<mixed> $targets
     * @param \Ibexa\Contracts\Core\Limitation\Type::ACCESS_* $expectedResult
     */
    public function testEvaluate(
        Limitation $limitation,
        UserReference $userReference,
        ValueObject $object,
        ?array $targets,
        ?bool $expectedResult
    ): void {
        $result = $this->limitationType->evaluate(
            $limitation,
            $userReference,
            $object,
            $targets,
        );

        self::assertEquals($expectedResult, $result);
    }

    /**
     * @return iterable<array{
     *     \Ibexa\Contracts\Core\Repository\Values\User\Limitation,
     *     \Ibexa\Contracts\Core\Repository\Values\User\UserReference,
     *     \Ibexa\Contracts\Core\Repository\Values\ValueObject,
     *     array<mixed>|null,
     *     \Ibexa\Contracts\Core\Limitation\Type::ACCESS_*
     *
     * }>
     */
    public function dataProviderForTestEvaluate(): iterable
    {
        yield 'no limitation values' => [
            new TaxonomyLimitation(['limitationValues' => []]),
            $this->createMock(UserReference::class),
            $this->createMock(ValueObject::class),
            [],
            Type::ACCESS_DENIED,
        ];

        $taxonomyEntry = new TaxonomyEntry(
            1,
            'taxonomy_item',
            'Taxonomy item',
            'eng-GB',
            [],
            null,
            $this->createMock(Content::class),
            'foo'
        );

        yield 'API TaxonomyEntry object, granted' => [
            new TaxonomyLimitation(['limitationValues' => ['foo']]),
            $this->createMock(UserReference::class),
            $taxonomyEntry,
            [],
            Type::ACCESS_GRANTED,
        ];

        yield 'API TaxonomyEntry object, denied' => [
            new TaxonomyLimitation(['limitationValues' => ['bar']]),
            $this->createMock(UserReference::class),
            $taxonomyEntry,
            [],
            Type::ACCESS_DENIED,
        ];

        $persistenceTaxonomyEntry = new PersistenceTaxonomyEntry();
        $persistenceTaxonomyEntry->setTaxonomy('foo');

        yield 'Persistence TaxonomyEntry object, granted' => [
            new TaxonomyLimitation(['limitationValues' => ['foo']]),
            $this->createMock(UserReference::class),
            $persistenceTaxonomyEntry,
            [],
            Type::ACCESS_GRANTED,
        ];

        yield 'Persistence TaxonomyEntry object, denied' => [
            new TaxonomyLimitation(['limitationValues' => ['bar']]),
            $this->createMock(UserReference::class),
            $persistenceTaxonomyEntry,
            [],
            Type::ACCESS_DENIED,
        ];

        $taxonomyValue = new TaxonomyValue(['taxonomy' => 'foo']);

        yield 'TaxonomyValue object, denied' => [
            new TaxonomyLimitation(['limitationValues' => ['bar']]),
            $this->createMock(UserReference::class),
            $taxonomyValue,
            [],
            Type::ACCESS_DENIED,
        ];
    }

    public function testEvaluateThrowOnInvalidLimitationType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Argument '\$value' is invalid: Must be of type: TaxonomyLimitation");

        $this->limitationType->evaluate(
            $this->createMock(Limitation::class),
            $this->createMock(UserReference::class),
            $this->createMock(ValueObject::class),
            [],
        );
    }
}
