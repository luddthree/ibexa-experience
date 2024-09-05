<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\FormBuilder\FieldType\Field\Mapper;

use Ibexa\Contracts\FormBuilder\FieldType\Field\FieldMapperInterface;
use Ibexa\FormBuilder\FieldType\Field\Mapper\CheckboxFieldMapper;
use Ibexa\FormBuilder\Form\Type\Field\CheckboxFieldType;
use Symfony\Component\Validator\Constraints as Assert;

class CheckboxFieldMapperTest extends AbstractFieldMapperTest
{
    private const FIELD_NAME = 'The checkbox field';
    private const ATTR_HELP_VALUE = 'The help';

    /**
     * {@inheritdoc}
     */
    public function dataProviderForMapForm(): array
    {
        $withDefaultAttributes = $this->createField(self::FIELD_NAME, [
            'help' => null,
        ]);

        $withAllAttributes = $this->createField(self::FIELD_NAME, [
            'help' => self::ATTR_HELP_VALUE,
        ]);

        return [
            [
                $withDefaultAttributes,
                [],
                CheckboxFieldType::class,
                [
                    'label' => self::FIELD_NAME,
                    'help' => null,
                    'required' => false,
                    'constraints' => [],
                    'field' => $withDefaultAttributes,
                    'translation_domain' => false,
                ],
            ],
            [
                $withAllAttributes,
                [
                    new Assert\NotBlank(),
                ],
                CheckboxFieldType::class,
                [
                    'label' => self::FIELD_NAME,
                    'help' => self::ATTR_HELP_VALUE,
                    'required' => true,
                    'constraints' => [
                        new Assert\NotBlank(),
                    ],
                    'field' => $withAllAttributes,
                    'translation_domain' => false,
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function createMapper(): FieldMapperInterface
    {
        return new CheckboxFieldMapper('checkbox', CheckboxFieldType::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedSupportedField(): string
    {
        return 'checkbox';
    }
}

class_alias(CheckboxFieldMapperTest::class, 'EzSystems\EzPlatformFormBuilder\Tests\FieldType\Field\Mapper\CheckboxFieldMapperTest');
