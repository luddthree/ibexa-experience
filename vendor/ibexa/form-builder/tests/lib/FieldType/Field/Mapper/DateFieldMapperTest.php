<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\FormBuilder\FieldType\Field\Mapper;

use DateTime;
use Ibexa\Contracts\FormBuilder\FieldType\Field\FieldMapperInterface;
use Ibexa\FormBuilder\FieldType\Field\Mapper\DateFieldMapper;
use Ibexa\FormBuilder\Form\Type\Field\DateFieldType;
use Symfony\Component\Validator\Constraints as Assert;

class DateFieldMapperTest extends AbstractFieldMapperTest
{
    private const FIELD_NAME = 'The date field';
    private const ATTR_HELP_VALUE = 'The help';
    private const ATTR_PLACEHOLDER_VALUE = 'The placeholder';
    private const ATTR_FORMAT_VALUE = 'mm-dd-YY';

    /**
     * {@inheritdoc}
     */
    public function dataProviderForMapForm(): array
    {
        $withDefaultAttributes = $this->createField(self::FIELD_NAME, [
            'help' => null,
            'placeholder' => null,
            'current_date_as_default_value' => false,
            'format' => self::ATTR_FORMAT_VALUE,
        ]);

        $withAllAttributes = $this->createField(self::FIELD_NAME, [
            'help' => self::ATTR_HELP_VALUE,
            'placeholder' => self::ATTR_PLACEHOLDER_VALUE,
            'current_date_as_default_value' => true,
            'format' => self::ATTR_FORMAT_VALUE,
        ]);

        $today = new DateTime();
        $today->setTime(0, 0, 0, 0);

        return [
            [
                $withDefaultAttributes,
                [],
                DateFieldType::class,
                [
                    'label' => self::FIELD_NAME,
                    'help' => null,
                    'data' => null,
                    'required' => false,
                    'html5' => false,
                    'format' => self::ATTR_FORMAT_VALUE,
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
                DateFieldType::class,
                [
                    'label' => self::FIELD_NAME,
                    'help' => self::ATTR_HELP_VALUE,
                    'attr' => [
                        'placeholder' => self::ATTR_PLACEHOLDER_VALUE,
                    ],
                    'data' => $today,
                    'required' => true,
                    'html5' => false,
                    'format' => self::ATTR_FORMAT_VALUE,
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
        return new DateFieldMapper('date', DateFieldType::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedSupportedField(): string
    {
        return 'date';
    }
}

class_alias(DateFieldMapperTest::class, 'EzSystems\EzPlatformFormBuilder\Tests\FieldType\Field\Mapper\DateFieldMapperTest');
