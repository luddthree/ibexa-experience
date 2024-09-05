<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\FieldType\Field\Mapper;

use Ibexa\Contracts\FormBuilder\FieldType\Field\FieldMapperInterface;
use Ibexa\Contracts\FormBuilder\FieldType\Model\Field;
use Ibexa\FormBuilder\Form\Validator\Constraints\Required;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class GenericFieldMapper implements FieldMapperInterface
{
    private string $fieldIdentifier;

    private string $formType;

    /**
     * @param string $fieldIdentifier
     * @param string $formType
     */
    public function __construct(string $fieldIdentifier, string $formType)
    {
        $this->fieldIdentifier = $fieldIdentifier;
        $this->formType = $formType;
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedField(): string
    {
        return $this->fieldIdentifier;
    }

    /**
     * {@inheritdoc}
     */
    public function mapField(FormBuilderInterface $builder, Field $field, array $constraints = []): void
    {
        $builder->add($field->getId(), $this->formType, $this->mapFormOptions($field, $constraints));

        $this->buildView($builder, $field);
    }

    /**
     * Returns target Form Type options.
     *
     * @param \Symfony\Component\Validator\Constraint[] $constraints
     *
     * @return array
     */
    protected function mapFormOptions(Field $field, array $constraints): array
    {
        $options = [
            'constraints' => $constraints,
            'required' => false,
            'translation_domain' => false,
        ];

        foreach ($constraints as $constraint) {
            if ($constraint instanceof Assert\NotBlank || ($constraint instanceof Required && $constraint->required)) {
                $options['required'] = true;
                break;
            }
        }

        return $options;
    }

    protected function buildView(FormBuilderInterface $builder, Field $field)
    {
    }
}

class_alias(GenericFieldMapper::class, 'EzSystems\EzPlatformFormBuilder\FieldType\Field\Mapper\GenericFieldMapper');
