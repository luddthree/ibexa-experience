<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\FieldTypePage\FieldType\Page\Block\Attribute\FormTypeMapper;

use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeDefinition;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition;
use Ibexa\FieldTypePage\FieldType\Page\Block\Attribute\FormTypeMapper\GenericFormTypeMapper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;

final class GenericFormTypeMapperTest extends TestCase
{
    public function testOptionMergingDefaults(): void
    {
        /** @var class-string $formTypeClass */
        $formTypeClass = 'form_class';
        $mapper = new GenericFormTypeMapper($formTypeClass);

        $blockDefinition = new BlockDefinition();
        $blockAttributeDefinition = new BlockAttributeDefinition();
        $blockAttributeDefinition->setOptions([
            'foo' => 'bar',
        ]);

        $formBuilder = $this->getFormBuilderMock([
            'constraints' => [],
        ]);

        $mapper->map($formBuilder, $blockDefinition, $blockAttributeDefinition);
    }

    /**
     * @dataProvider provideForOptionsMerging
     *
     * @param array<string>|null $allowList
     * @param array<string, mixed> $expectedOptions
     */
    public function testOptionMerging(bool $mergeOptions, ?array $allowList, array $expectedOptions): void
    {
        /** @var class-string $formTypeClass */
        $formTypeClass = 'form_class';
        $mapper = new GenericFormTypeMapper($formTypeClass, $mergeOptions, $allowList);

        $blockDefinition = new BlockDefinition();
        $blockAttributeDefinition = new BlockAttributeDefinition();
        $blockAttributeDefinition->setOptions([
            'foo' => 'bar',
        ]);

        $formBuilder = $this->getFormBuilderMock($expectedOptions);

        $mapper->map($formBuilder, $blockDefinition, $blockAttributeDefinition);
    }

    /**
     * @return iterable<string, array{bool, array<string>|null, array<string, mixed>}>
     */
    public static function provideForOptionsMerging(): iterable
    {
        $defaultOptions = ['constraints' => []];

        yield '$mergeOptions = false | $allowList = null' => [
            false,
            null,
            $defaultOptions,
        ];

        yield '$mergeOptions = true | $allowList = null' => [
            true,
            null,
            $defaultOptions + ['foo' => 'bar'],
        ];

        yield '$mergeOptions = false | $allowList = []' => [
            false,
            [],
            $defaultOptions,
        ];

        yield '$mergeOptions = true | $allowList = []' => [
            true,
            [],
            $defaultOptions,
        ];

        yield '$mergeOptions = false | $allowList = ["foo"]' => [
            false,
            ['foo'],
            $defaultOptions,
        ];

        yield '$mergeOptions = true | $allowList = ["foo"]' => [
            true,
            ['foo'],
            $defaultOptions + ['foo' => 'bar'],
        ];

        yield '$mergeOptions = false | $allowList = ["bar"]' => [
            false,
            ['bar'],
            $defaultOptions,
        ];

        yield '$mergeOptions = true | $allowList = ["bar"]' => [
            true,
            ['bar'],
            $defaultOptions,
        ];
    }

    /**
     * @param array<mixed> $expectedOptions
     */
    private function getFormBuilderMock(array $expectedOptions): FormBuilderInterface
    {
        $formBuilder = $this->createMock(FormBuilderInterface::class);
        $formBuilder->expects(self::once())
            ->method('create')
            ->with(
                'value',
                'form_class',
                $expectedOptions,
            )
            ->willReturnSelf();

        return $formBuilder;
    }
}
