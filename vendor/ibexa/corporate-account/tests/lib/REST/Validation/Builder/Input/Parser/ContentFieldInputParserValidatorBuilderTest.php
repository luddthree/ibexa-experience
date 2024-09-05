<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\CorporateAccount\REST\Validation\Builder\Input\Parser;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\CorporateAccount\REST\Validation\Builder\Input\Parser\ContentFieldInputParserValidatorBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ValidatorBuilder;

/**
 * @covers \Ibexa\CorporateAccount\REST\Validation\Builder\Input\Parser\ContentFieldInputParserValidatorBuilder
 *
 * @phpstan-import-type RESTContentFieldsInputArray from \Ibexa\CorporateAccount\REST\Input\Parser\BaseContentParser
 */
final class ContentFieldInputParserValidatorBuilderTest extends TestCase
{
    private ContentFieldInputParserValidatorBuilder $builder;

    /**
     * @phpstan-return iterable<string, array{mixed, array<string>}>
     */
    public function getDataForTestValidateInputArray(): iterable
    {
        yield 'No fields' => [
            [],
            // no validation messages expected
            [],
        ];

        yield 'Malformed Field Value structure' => [
            [
                'I am not an array',
            ],
            [
                'Field value structure expects "fieldDefinitionIdentifier" and "fieldValue" keys for Field at index 0',
            ],
        ];

        yield 'Malformed Field Value structure array' => [
            [
                0 => null,
            ],
            [
                'Field value structure expects "fieldDefinitionIdentifier" and "fieldValue" keys for Field at index 0',
            ],
        ];

        yield 'Missing Field Definition Identifier' => [
            [
                [
                    'fieldValue' => 'foo',
                ],
            ],
            [
                'Missing "fieldDefinitionIdentifier" element in Field value structure at index 0',
            ],
        ];

        yield 'Invalid Field Definition Identifier' => [
            [
                [
                    'fieldDefinitionIdentifier' => 'bar',
                    'fieldValue' => 'foo',
                ],
            ],
            [
                '"bar" is an invalid Field Definition identifier for "my_content_type" content type in Field Value structure',
            ],
        ];

        yield 'Missing Field Value' => [
            [
                [
                    'fieldDefinitionIdentifier' => 'name',
                ],
            ],
            [
                'Missing "fieldValue" element in Field value structure at index 0',
            ],
        ];

        yield 'All fields correct' => [
            [
                [
                    'fieldDefinitionIdentifier' => 'name',
                    'fieldValue' => 'Acme ltd.',
                ],
                [
                    'fieldDefinitionIdentifier' => 'tax_id',
                    'fieldValue' => '123 456 789',
                ],
            ],
            [],
        ];

        // combines all previous data sets to test if it works on a mix of correct and incorrect data
        yield 'Some fields incorrect' => [
            [
                [
                    'fieldDefinitionIdentifier' => 'name',
                    'fieldValue' => 'Acme ltd.',
                ],
                [
                    'fieldDefinitionIdentifier' => 'invalid_tax_id_field',
                    'fieldValue' => '123 456 789',
                ],
                [
                    // missing fieldValue
                    'fieldDefinitionIdentifier' => 'tax_id',
                ],
                [
                    'fieldValue' => 'missing Field Def Value',
                ],
                [
                    'fieldDefinitionIdentifier' => 'name',
                    'fieldValue' => 'Acme ltd.',
                    'unexpectedKey' => 'unexpected value',
                ],
            ],
            [
                '"invalid_tax_id_field" is an invalid Field Definition identifier for "my_content_type" content type in Field Value structure',
                'Missing "fieldDefinitionIdentifier" element in Field value structure at index 3',
                'Missing "fieldValue" element in Field value structure at index 2',
                'The field "unexpectedKey" was not expected in Field value structure at index 4',
            ],
        ];
    }

    protected function setUp(): void
    {
        $this->builder = new ContentFieldInputParserValidatorBuilder(
            (new ValidatorBuilder())->getValidator(),
            $this->createContentTypeMock()
        );
    }

    /**
     * @dataProvider getDataForTestValidateInputArray
     *
     * @phpstan-param RESTContentFieldsInputArray $data
     *
     * @param array<string> $expectedViolationMessages
     */
    public function testValidateInputArray(array $data, array $expectedViolationMessages): void
    {
        $this->builder->validateInputArray($data);
        $actualViolationMessages = iterator_to_array($this->builder->build()->getViolations());

        self::assertEqualsCanonicalizing(
            $expectedViolationMessages,
            array_map(
                static fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                $actualViolationMessages
            )
        );
    }

    private function createContentTypeMock(): ContentType
    {
        $contentTypeMock = $this->createMock(ContentType::class);
        $contentTypeMock->method('__get')->with('identifier')->willReturn('my_content_type');
        $contentTypeMock->method('getFieldDefinition')->willReturnMap(
            [
                ['name', $this->createMock(FieldDefinition::class)],
                ['tax_id', $this->createMock(FieldDefinition::class)],
            ]
        );

        return $contentTypeMock;
    }
}
