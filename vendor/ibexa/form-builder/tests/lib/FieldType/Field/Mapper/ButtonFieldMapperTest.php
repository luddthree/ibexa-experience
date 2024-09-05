<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\FormBuilder\FieldType\Field\Mapper;

use Ibexa\Contracts\FormBuilder\FieldType\Field\FieldMapperInterface;
use Ibexa\FormBuilder\FieldType\Field\Mapper\ButtonFieldMapper;
use Ibexa\FormBuilder\Form\Type\Field\ButtonFieldType;

class ButtonFieldMapperTest extends AbstractFieldMapperTest
{
    private const FIELD_NAME = 'The button';

    /**
     * {@inheritdoc}
     */
    public function dataProviderForMapForm(): array
    {
        $field = $this->createField(self::FIELD_NAME, [
            'action' => null,
            'notification_email' => null,
        ]);

        return [
            [
                $field,
                [],
                ButtonFieldType::class,
                [
                    'label' => self::FIELD_NAME,
                    'field' => $field,
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function createMapper(): FieldMapperInterface
    {
        return new ButtonFieldMapper('button', ButtonFieldType::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedSupportedField(): string
    {
        return 'button';
    }
}

class_alias(ButtonFieldMapperTest::class, 'EzSystems\EzPlatformFormBuilder\Tests\FieldType\Field\Mapper\ButtonFieldMapperTest');
