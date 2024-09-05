<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\FormBuilder\FieldType\Field\Mapper;

use Ibexa\Contracts\FormBuilder\FieldType\Field\FieldMapperInterface;
use Ibexa\FormBuilder\FieldType\Field\Mapper\EmailFieldMapper;
use Ibexa\FormBuilder\Form\Type\Field\EmailFieldType;
use Symfony\Component\Validator\Constraints as Assert;

class EmailFieldMapperTest extends AbstractFieldMapperTest
{
    private const FIELD_NAME = 'The checkbox list field';
    private const ATTR_HELP_VALUE = 'The help';
    private const ATTR_PLACEHOLDER_VALUE = 'The placeholder';

    /**
     * {@inheritdoc}
     */
    public function dataProviderForMapForm(): array
    {
        $withDefaultAttributes = $this->createField(self::FIELD_NAME, [
            'help' => null,
            'placeholder' => null,
        ]);

        $withAllAttributes = $this->createField(self::FIELD_NAME, [
            'help' => self::ATTR_HELP_VALUE,
            'placeholder' => self::ATTR_PLACEHOLDER_VALUE,
        ]);

        return [
            [
                $withDefaultAttributes,
                [],
                EmailFieldType::class,
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
                EmailFieldType::class,
                [
                    'label' => self::FIELD_NAME,
                    'help' => self::ATTR_HELP_VALUE,
                    'attr' => [
                        'placeholder' => self::ATTR_PLACEHOLDER_VALUE,
                    ],
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
        return new EmailFieldMapper('email', EmailFieldType::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedSupportedField(): string
    {
        return 'email';
    }
}

class_alias(EmailFieldMapperTest::class, 'EzSystems\EzPlatformFormBuilder\Tests\FieldType\Field\Mapper\EmailFieldMapperTest');
