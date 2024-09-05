<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Validator\Constraints;

use Ibexa\Bundle\ProductCatalog\Form\Data\Currency\CurrencyCreateData;
use Ibexa\Bundle\ProductCatalog\Form\Data\Currency\CurrencyUpdateData;
use Ibexa\Bundle\ProductCatalog\Validator\Constraints\UniqueCurrencyCode;
use Ibexa\Bundle\ProductCatalog\Validator\Constraints\UniqueCurrencyCodeValidator;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\ProductCatalog\CurrencyServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

/**
 * @covers \Ibexa\Bundle\ProductCatalog\Validator\Constraints\UniqueCurrencyCodeValidator
 */
final class UniqueCurrencyCodeValidatorTest extends TestCase
{
    /** @var \Ibexa\Contracts\ProductCatalog\CurrencyServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private CurrencyServiceInterface $currencyService;

    /** @var \Symfony\Component\Validator\Context\ExecutionContextInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ExecutionContextInterface $executionContext;

    private UniqueCurrencyCodeValidator $validator;

    protected function setUp(): void
    {
        $this->currencyService = $this->createMock(CurrencyServiceInterface::class);
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);

        $this->validator = new UniqueCurrencyCodeValidator($this->currencyService);
        $this->validator->initialize($this->executionContext);
    }

    /**
     * @dataProvider dataProviderForTestSkipValidation
     */
    public function testSkipValidation(object $value): void
    {
        $this->currencyService
            ->expects(self::never())
            ->method('getCurrencyByCode');

        $this->executionContext
            ->expects(self::never())
            ->method('buildViolation');

        $this->validator->validate($value, new UniqueCurrencyCode());
    }

    /**
     * @return iterable<string,array{object}>
     */
    public function dataProviderForTestSkipValidation(): iterable
    {
        yield 'unsupported data class' => [
            new stdClass(),
        ];

        yield 'missing code (CurrencyCreateData)' => [
            new CurrencyCreateData(),
        ];

        yield 'missing code (CurrencyUpdateData)' => [
            new CurrencyUpdateData(1),
        ];
    }

    public function testValidCurrencyCreateData(): void
    {
        $code = 'foo';

        $this->currencyService
            ->method('getCurrencyByCode')
            ->with($code)
            ->willThrowException($this->createMock(NotFoundException::class));

        $this->executionContext
            ->expects(self::never())
            ->method('buildViolation');

        $value = new CurrencyCreateData();
        $value->setCode($code);

        $this->validator->validate($value, new UniqueCurrencyCode());
    }

    public function testValidCurrencyUpdateData(): void
    {
        $updatedCurrencyId = 42;
        $code = 'foo';

        $currency = $this->createMock(CurrencyInterface::class);
        $currency->method('getId')->willReturn($updatedCurrencyId);

        $this->currencyService
            ->method('getCurrencyByCode')
            ->with($code)
            ->willReturn($currency);

        $this->executionContext
            ->expects(self::never())
            ->method('buildViolation');

        $value = new CurrencyUpdateData($updatedCurrencyId);
        $value->setCode($code);

        $this->validator->validate($value, new UniqueCurrencyCode());
    }

    public function testInvalidCurrencyCreateData(): void
    {
        $code = 'foo';

        $currency = $this->createMock(CurrencyInterface::class);
        $currency->method('getId')->willReturn(43);

        $this->currencyService
            ->method('getCurrencyByCode')
            ->with($code)
            ->willReturn($currency);

        $constraint = new UniqueCurrencyCode();

        $this->executionContext
            ->expects(self::once())
            ->method('buildViolation')
            ->with($constraint->message)
            ->willReturn($this->getConstraintViolationBuilderMock($code));

        $value = new CurrencyCreateData();
        $value->setCode($code);

        $this->validator->validate($value, $constraint);
    }

    public function testInvalidCurrencyUpdateData(): void
    {
        $updatedCurrencyId = 42;
        $conflictingCurrencyId = 43;
        $code = 'foo';

        $currency = $this->createMock(CurrencyInterface::class);
        $currency->method('getId')->willReturn($conflictingCurrencyId);

        $this->currencyService
            ->method('getCurrencyByCode')
            ->with($code)
            ->willReturn($currency);

        $constraint = new UniqueCurrencyCode();

        $this->executionContext
            ->expects(self::once())
            ->method('buildViolation')
            ->with($constraint->message)
            ->willReturn($this->getConstraintViolationBuilderMock($code));

        $value = new CurrencyUpdateData($updatedCurrencyId);
        $value->setCode($code);

        $this->validator->validate($value, $constraint);
    }

    private function getConstraintViolationBuilderMock(string $code): ConstraintViolationBuilderInterface
    {
        $constraintViolationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $constraintViolationBuilder
            ->expects(self::once())
            ->method('atPath')
            ->with('code')
            ->willReturn($constraintViolationBuilder);

        $constraintViolationBuilder
            ->expects(self::once())
            ->method('setParameter')
            ->with('%code%', $code)
            ->willReturn($constraintViolationBuilder);

        $constraintViolationBuilder->expects(self::once())->method('addViolation');

        return $constraintViolationBuilder;
    }
}
