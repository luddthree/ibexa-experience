<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Validator\Constraints;

use Ibexa\Bundle\ProductCatalog\Form\Data\Catalog\CatalogCreateData;
use Ibexa\Bundle\ProductCatalog\Form\Data\Catalog\CatalogUpdateData;
use Ibexa\Bundle\ProductCatalog\Validator\Constraints\UniqueCatalogIdentifier;
use Ibexa\Bundle\ProductCatalog\Validator\Constraints\UniqueCatalogIdentifierValidator;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\CatalogServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

/**
 * @covers \Ibexa\Bundle\ProductCatalog\Validator\Constraints\UniqueCatalogIdentifierValidator
 */
final class UniqueCatalogIdentifierValidatorTest extends TestCase
{
    private const CATALOG_ID = 14;
    private const CATALOG_IDENTIFIER = 'foo';

    /** @var \Ibexa\Contracts\ProductCatalog\CatalogServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private CatalogServiceInterface $catalogService;

    /** @var \Symfony\Component\Validator\Context\ExecutionContextInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ExecutionContextInterface $executionContext;

    private UniqueCatalogIdentifierValidator $validator;

    protected function setUp(): void
    {
        $this->catalogService = $this->createMock(CatalogServiceInterface::class);
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);

        $this->validator = new UniqueCatalogIdentifierValidator($this->catalogService);
        $this->validator->initialize($this->executionContext);
    }

    /**
     * @dataProvider dataProviderForTestSkipValidation
     *
     * @param mixed $value
     */
    public function testSkipValidation($value): void
    {
        $this->catalogService
            ->expects(self::never())
            ->method('getCatalog');

        $this->executionContext
            ->expects(self::never())
            ->method('buildViolation');

        $this->validator->validate($value, new UniqueCatalogIdentifier());
    }

    /**
     * @return iterable<string, array{object}>
     */
    public function dataProviderForTestSkipValidation(): iterable
    {
        yield 'unsupported data class' => [
            new stdClass(),
        ];

        yield 'missing identifier (CatalogCreateData)' => [
            new CatalogCreateData(),
        ];

        yield 'missing identifier (CatalogUpdateData)' => [
            $this->getCatalogUpdateData(),
        ];
    }

    public function testValidCatalogCreateData(): void
    {
        $this->catalogService
            ->method('getCatalogByIdentifier')
            ->with(self::CATALOG_IDENTIFIER)
            ->willThrowException($this->createMock(NotFoundException::class));

        $this->executionContext
            ->expects(self::never())
            ->method('buildViolation');

        $value = new CatalogCreateData();
        $value->setIdentifier(self::CATALOG_IDENTIFIER);

        $this->validator->validate($value, new UniqueCatalogIdentifier());
    }

    public function testValidCatalogUpdateData(): void
    {
        $catalog = $this->createMock(CatalogInterface::class);
        $catalog->method('getId')->willReturn(self::CATALOG_ID);

        $this->catalogService
            ->method('getCatalogByIdentifier')
            ->with(self::CATALOG_IDENTIFIER)
            ->willReturn($catalog);

        $this->executionContext
            ->expects(self::never())
            ->method('buildViolation');

        $value = $this->getCatalogUpdateData();

        $this->validator->validate($value, new UniqueCatalogIdentifier());
    }

    public function testInvalidCatalogCreateData(): void
    {
        $catalog = $this->createMock(CatalogInterface::class);
        $catalog->method('getIdentifier')->willReturn(self::CATALOG_IDENTIFIER);

        $this->catalogService
            ->method('getCatalogByIdentifier')
            ->with(self::CATALOG_IDENTIFIER)
            ->willReturn($catalog);

        $constraint = new UniqueCatalogIdentifier();

        $this->executionContext
            ->expects(self::once())
            ->method('buildViolation')
            ->with($constraint->message)
            ->willReturn($this->getConstraintViolationBuilderMock(self::CATALOG_IDENTIFIER));

        $value = new CatalogCreateData();
        $value->setIdentifier(self::CATALOG_IDENTIFIER);

        $this->validator->validate($value, new UniqueCatalogIdentifier());
    }

    public function testInvalidCatalogUpdateData(): void
    {
        $catalog = $this->createMock(CatalogInterface::class);
        $catalog->method('getIdentifier')->willReturn(self::CATALOG_IDENTIFIER);

        $this->catalogService
            ->method('getCatalogByIdentifier')
            ->with(self::CATALOG_IDENTIFIER)
            ->willReturn($catalog);

        $constraint = new UniqueCatalogIdentifier();

        $this->executionContext
            ->expects(self::once())
            ->method('buildViolation')
            ->with($constraint->message)
            ->willReturn($this->getConstraintViolationBuilderMock(self::CATALOG_IDENTIFIER));

        $value = $this->getCatalogUpdateData();
        $value->setIdentifier(self::CATALOG_IDENTIFIER);

        $this->validator->validate($value, new UniqueCatalogIdentifier());
    }

    private function getConstraintViolationBuilderMock(string $identifier): ConstraintViolationBuilderInterface
    {
        $constraintViolationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $constraintViolationBuilder
            ->expects(self::once())
            ->method('atPath')
            ->with('identifier')
            ->willReturn($constraintViolationBuilder);

        $constraintViolationBuilder
            ->expects(self::once())
            ->method('setParameter')
            ->with('%identifier%', $identifier)
            ->willReturn($constraintViolationBuilder);

        $constraintViolationBuilder
            ->expects(self::once())
            ->method('addViolation');

        return $constraintViolationBuilder;
    }

    private function getCatalogUpdateData(): CatalogUpdateData
    {
        return new CatalogUpdateData(
            self::CATALOG_ID,
            new Language(['languageCode' => 'eng-GB']),
        );
    }
}
