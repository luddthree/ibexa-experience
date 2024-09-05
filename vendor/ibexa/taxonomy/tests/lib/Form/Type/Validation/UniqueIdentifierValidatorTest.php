<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Taxonomy\Form\Type\Validation;

use Ibexa\Contracts\ContentForms\Data\Content\FieldData;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Core\FieldType\TextLine\Value;
use Ibexa\Taxonomy\Exception\TaxonomyEntryNotFoundException;
use Ibexa\Taxonomy\Form\Type\Validation\Constraint\UniqueIdentifier;
use Ibexa\Taxonomy\Form\Type\Validation\UniqueIdentifierValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class UniqueIdentifierValidatorTest extends TestCase
{
    /** @var \Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private TaxonomyServiceInterface $taxonomyService;

    private UniqueIdentifierValidator $validator;

    protected function setUp(): void
    {
        $this->taxonomyService = $this->createMock(TaxonomyServiceInterface::class);
        $this->validator = new UniqueIdentifierValidator($this->taxonomyService);
    }

    /**
     * @dataProvider dataProviderForSkipValidation
     */
    public function testSkipValidation(?FieldData $value): void
    {
        $this->taxonomyService
            ->expects(self::never())
            ->method('loadEntryByIdentifier');

        $this->validator->validate($value, new UniqueIdentifier(['taxonomy' => 'taxonomy_foo']));
    }

    /**
     * @return iterable<array{\Ibexa\Contracts\ContentForms\Data\Content\FieldData|null}>
     */
    public function dataProviderForSkipValidation(): iterable
    {
        yield 'null' => [
            null,
        ];

        yield 'empty Value' => [
            new FieldData(['value' => new Value()]),
        ];

        yield 'identifier matching currently edited entry' => [
            new FieldData([
                'value' => new Value('foo'),
                'field' => new Field([
                    'value' => new Value('foo'),
                ]),
            ]),
        ];
    }

    public function testSkipValidationWhenEntryDoesNotExist(): void
    {
        $this->taxonomyService
            ->expects(self::once())
            ->method('loadEntryByIdentifier')
            ->willThrowException(new TaxonomyEntryNotFoundException());

        $value = new FieldData([
            'value' => new Value('foo'),
            'field' => new Field([
                'value' => new Value('bar'),
            ]),
        ]);

        $this->validator->validate($value, new UniqueIdentifier(['taxonomy' => 'taxonomy_foo']));
    }

    public function testThrowOnInvalidConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->validator->validate(null, $this->createMock(Constraint::class));
    }

    public function testValidate(): void
    {
        $executionContext = $this->createMock(ExecutionContextInterface::class);

        $executionContext
            ->expects(self::once())
            ->method('buildViolation')
            ->with(
                'Taxonomy Entry with identifier "{{ identifier }}" already exists in "{{ taxonomy }}" '
                    . 'taxonomy tree. Please use unique identifier.'
            )
            ->willReturn(
                $this->getConstraintViolationBuilderMock(
                    'c1051bb5-d103-4f74-8988-acbcafc7fdc3',
                    'identifier_foo',
                    'taxonomy_foo',
                )
            );

        $data = new FieldData([
            'value' => new Value('identifier_foo'),
            'field' => new Field([
                'value' => new Value('bar'),
            ]),
        ]);
        $taxonomyEntry = $this->createMock(TaxonomyEntry::class);
        $taxonomyEntry->method('getIdentifier')->willReturn('identifier_foo');
        $taxonomyEntry->method('getTaxonomy')->willReturn('taxonomy_foo');

        $this->taxonomyService
            ->expects(self::once())
            ->method('loadEntryByIdentifier')
            ->with('identifier_foo')
            ->willReturn($taxonomyEntry);

        $this->validator->initialize($executionContext);

        $this->validator->validate($data, new UniqueIdentifier(['taxonomy' => 'taxonomy_foo']));
    }

    private function getConstraintViolationBuilderMock(
        string $code,
        string $identifier,
        string $taxonomy
    ): ConstraintViolationBuilderInterface {
        $constraintViolationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $constraintViolationBuilder
            ->expects(self::once())
            ->method('setCode')
            ->with($code)
            ->willReturnSelf();

        $constraintViolationBuilder
            ->expects(self::exactly(2))
            ->method('setParameter')
            ->withConsecutive(['{{ identifier }}', $identifier], ['{{ taxonomy }}', $taxonomy])
            ->willReturnSelf();

        $constraintViolationBuilder
            ->expects(self::once())
            ->method('addViolation');

        return $constraintViolationBuilder;
    }
}
