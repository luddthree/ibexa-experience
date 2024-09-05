<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\FormBuilder\FieldType\Field\Mapper;

use Ibexa\Contracts\FormBuilder\FieldType\Field\FieldMapperInterface;
use Ibexa\FormBuilder\FieldType\Field\Mapper\HiddenFieldMapper;
use Ibexa\FormBuilder\Form\Type\Field\HiddenFieldType;
use Symfony\Component\Validator\Constraints as Assert;

class HiddenFieldMapperTest extends AbstractFieldMapperTest
{
    public const ATTR_VALUE_VALUE = 'The value';

    /**
     * {@inheritdoc}
     */
    public function dataProviderForMapForm(): array
    {
        $field = $this->createField('name', [
            'value' => self::ATTR_VALUE_VALUE,
        ]);

        return [
            [
                $field,
                [],
                HiddenFieldType::class,
                [
                    'data' => self::ATTR_VALUE_VALUE,
                    'required' => false,
                    'constraints' => [],
                    'field' => $field,
                    'translation_domain' => false,
                ],
            ],
            [
                $field,
                [
                    new Assert\NotBlank(),
                ],
                HiddenFieldType::class,
                [
                    'data' => self::ATTR_VALUE_VALUE,
                    'required' => true,
                    'constraints' => [
                        new Assert\NotBlank(),
                    ],
                    'field' => $field,
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
        return new HiddenFieldMapper('hidden', HiddenFieldType::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedSupportedField(): string
    {
        return 'hidden';
    }
}

class_alias(HiddenFieldMapperTest::class, 'EzSystems\EzPlatformFormBuilder\Tests\FieldType\Field\Mapper\HiddenFieldMapperTest');
