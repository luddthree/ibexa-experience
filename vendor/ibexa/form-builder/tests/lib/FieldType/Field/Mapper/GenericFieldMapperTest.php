<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\FormBuilder\FieldType\Field\Mapper;

use Ibexa\Contracts\FormBuilder\FieldType\Field\FieldMapperInterface;
use Ibexa\FormBuilder\FieldType\Field\Mapper\GenericFieldMapper;
use Ibexa\FormBuilder\Form\Validator\Constraints\Required;
use Symfony\Component\Validator\Constraints\NotBlank;

class GenericFieldMapperTest extends AbstractFieldMapperTest
{
    private const FIELD_NAME = 'Some Generic Field';

    /**
     * {@inheritdoc}
     */
    public function dataProviderForMapForm(): array
    {
        $field = $this->createField(self::FIELD_NAME, []);

        return [
            [
                $field,
                [
                    new Required(true),
                ],
                'SomeGenericFieldType',
                [
                    'required' => true,
                    'constraints' => [
                        new Required(true),
                    ],
                    'translation_domain' => false,
                ],
            ],
            [
                $field,
                [
                    new NotBlank(),
                ],
                'SomeGenericFieldType',
                [
                    'required' => true,
                    'constraints' => [
                        new NotBlank(),
                    ],
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
        return new GenericFieldMapper('generic_identifier', 'SomeGenericFieldType');
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedSupportedField(): string
    {
        return 'generic_identifier';
    }
}

class_alias(GenericFieldMapperTest::class, 'EzSystems\EzPlatformFormBuilder\Tests\FieldType\Field\Mapper\GenericFieldMapperTest');
