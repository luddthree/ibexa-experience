<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FieldTypePage\Form\Type;

use Ibexa\FieldTypePage\FieldType\LandingPage\Converter\PageConverter;
use Ibexa\FieldTypePage\FieldType\LandingPage\Value;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Form Type representing ezlandingpage field type.
 */
class PageFieldType extends AbstractType
{
    /** @var \Ibexa\FieldTypePage\FieldType\LandingPage\Converter\PageConverter */
    protected $converter;

    /**
     * @param \Ibexa\FieldTypePage\FieldType\LandingPage\Converter\PageConverter $converter
     */
    public function __construct(PageConverter $converter)
    {
        $this->converter = $converter;
    }

    /**
     * @return {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * @return {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'ezplatform_fieldtype_ezlandingpage';
    }

    /**
     * @return {@inheritdoc}
     */
    public function getParent()
    {
        return TextareaType::class;
    }

    /**
     * @return {@inheritdoc}
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(
            $this->getDataTransformer()
        );
    }

    /**
     * @return \Symfony\Component\Form\CallbackTransformer
     */
    private function getDataTransformer()
    {
        return new CallbackTransformer(
            function ($value) {
                return $this->converter->encode($value->getPage());
            },
            function ($value) {
                return new Value($this->converter->decode($value));
            }
        );
    }
}

class_alias(PageFieldType::class, 'EzSystems\EzPlatformPageFieldType\Form\Type\PageFieldType');
