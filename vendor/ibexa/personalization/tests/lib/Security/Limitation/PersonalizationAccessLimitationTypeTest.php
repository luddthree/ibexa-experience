<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Security\Limitation;

use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\ObjectStateLimitation;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Personalization\Security\Limitation\Loader\PersonalizationLimitationListLoaderInterface;
use Ibexa\Personalization\Security\Limitation\PersonalizationAccessLimitationType;
use Ibexa\Personalization\Value\Limitation\PersonalizationAccessLimitation;
use Ibexa\Personalization\Value\Security\PersonalizationSecurityContext;
use Ibexa\Tests\Core\Limitation\Base;

final class PersonalizationAccessLimitationTypeTest extends Base
{
    private PersonalizationLimitationListLoaderInterface $limitationListLoader;

    public function setUp(): void
    {
        parent::setUp();

        $this->limitationListLoader = $this->createMock(PersonalizationLimitationListLoaderInterface::class);
    }

    public function testConstruct(): PersonalizationAccessLimitationType
    {
        $this->expectNotToPerformAssertions();

        return new PersonalizationAccessLimitationType($this->limitationListLoader);
    }

    /**
     * @depends testConstruct
     * @dataProvider providerForTestAcceptValue
     */
    public function testAcceptValue(
        PersonalizationAccessLimitation $limitation,
        PersonalizationAccessLimitationType $limitationType
    ): void {
        $this->expectNotToPerformAssertions();

        $limitationType->acceptValue($limitation);
    }

    /**
     * @depends testConstruct
     * @dataProvider providerForTestAcceptValueException
     */
    public function testAcceptValueException(
        Limitation $limitation,
        PersonalizationAccessLimitationType $limitationType
    ): void {
        $this->expectException(InvalidArgumentException::class);

        $limitationType->acceptValue($limitation);
    }

    /**
     * @dataProvider providerForTestValidateError
     * @depends testConstruct
     */
    public function testValidateError(
        PersonalizationAccessLimitation $limitation,
        int $errorCount,
        PersonalizationAccessLimitationType $limitationType
    ): void {
        $validationErrors = $limitationType->validate($limitation);

        self::assertCount($errorCount, $validationErrors);
    }

    /**
     * @depends testConstruct
     */
    public function testBuildValue(PersonalizationAccessLimitationType $limitationType): void
    {
        $expected = ['test', 'test' => 9];
        $value = $limitationType->buildValue($expected);

        self::assertInstanceOf(PersonalizationAccessLimitation::class, $value);
        self::assertIsArray($value->limitationValues);
        self::assertEquals($expected, $value->limitationValues);
    }

    /**
     * @depends testConstruct
     * @dataProvider providerForTestEvaluate
     */
    public function testEvaluate(
        PersonalizationAccessLimitation $limitation,
        ValueObject $object,
        bool $expected,
        PersonalizationAccessLimitationType $limitationType
    ): void {
        $userMock = $this->getUserMock();
        $userMock->expects(self::never())->method(self::anything());

        $value = $limitationType->evaluate(
            $limitation,
            $userMock,
            $object
        );

        self::assertIsBool($value);
        self::assertEquals($expected, $value);
    }

    /**
     * @depends testConstruct
     * @dataProvider providerForTestEvaluateInvalidArgument
     */
    public function testEvaluateInvalidArgument(
        Limitation $limitation,
        ValueObject $object,
        PersonalizationAccessLimitationType $limitationType
    ): void {
        $this->expectException(InvalidArgumentException::class);

        $userMock = $this->getUserMock();
        $userMock->expects(self::never())->method(self::anything());

        $limitationType->evaluate(
            $limitation,
            $userMock,
            $object
        );
    }

    /**
     * @depends testConstruct
     */
    public function testGetCriterion(PersonalizationAccessLimitationType $limitationType): void
    {
        $this->expectException(NotImplementedException::class);

        $limitationType->getCriterion(new PersonalizationAccessLimitation(), $this->getUserMock());
    }

    /**
     * @depends testConstruct
     */
    public function testValueSchema(PersonalizationAccessLimitationType $limitationType): void
    {
        $this->expectException(NotImplementedException::class);

        $limitationType->valueSchema();
    }

    /**
     * @return iterable<array{\Ibexa\Personalization\Value\Limitation\PersonalizationAccessLimitation}>
     */
    public function providerForTestAcceptValue(): iterable
    {
        yield [new PersonalizationAccessLimitation()];
        yield [new PersonalizationAccessLimitation([])];
        yield [
            new PersonalizationAccessLimitation(
                [
                    'limitationValues' => [
                        1234 => '1234 First Test Account',
                    ],
                ]
            ),
        ];
    }

    /**
     * @return iterable<array{mixed}>
     */
    public function providerForTestAcceptValueException(): iterable
    {
        yield [new ObjectStateLimitation()];
        yield [new PersonalizationAccessLimitation(['limitationValues' => [true]])];
    }

    /**
     * @return iterable<array{\Ibexa\Personalization\Value\Limitation\PersonalizationAccessLimitation}>
     */
    public function providerForTestValidateError(): iterable
    {
        yield [new PersonalizationAccessLimitation(), 0];
        yield [new PersonalizationAccessLimitation([]), 0];
        yield [
            new PersonalizationAccessLimitation(
                [
                    'limitationValues' => [1234],
                ]
            ),
            1,
        ];
        yield [new PersonalizationAccessLimitation(['limitationValues' => [true]]), 1];
        yield [
            new PersonalizationAccessLimitation(
                [
                    'limitationValues' => [
                        '4567',
                        false,
                    ],
                ]
            ),
            2,
        ];
    }

    /**
     * @return iterable<array{
     *      'limitation': \Ibexa\Personalization\Value\Limitation\PersonalizationAccessLimitation,
     *      'object': \Ibexa\Personalization\Value\Security\PersonalizationSecurityContext,
     *      'expected': bool
     * }>
     */
    public function providerForTestEvaluate(): iterable
    {
        yield [
            'limitation' => new PersonalizationAccessLimitation(),
            'object' => new PersonalizationSecurityContext(['customerId' => 1234]),
            'expected' => false,
        ];
        yield [
            'limitation' => new PersonalizationAccessLimitation(['limitationValues' => ['5678']]),
            'object' => new PersonalizationSecurityContext(['customerId' => 1234]),
            'expected' => false,
        ];
        yield [
            'limitation' => new PersonalizationAccessLimitation(['limitationValues' => ['1234', '5678']]),
            'object' => new PersonalizationSecurityContext(['customerId' => 1234]),
            'expected' => true,
        ];
    }

    /**
     * @return iterable<array{
     *      'limitation': mixed,
     *      'object': mixed
     * }>
     */
    public function providerForTestEvaluateInvalidArgument(): iterable
    {
        yield [
            'limitation' => new ObjectStateLimitation(),
            'object' => new PersonalizationSecurityContext(['customerId' => 1234]),
        ];
        yield [
            'limitation' => new PersonalizationAccessLimitation(),
            'object' => new ObjectStateLimitation(),
        ];
    }
}

class_alias(PersonalizationAccessLimitationTypeTest::class, 'Ibexa\Platform\Tests\Personalization\Security\Limitation\PersonalizationAccessLimitationTypeTest');
