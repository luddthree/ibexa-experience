<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Form\Type\Page;

use Ibexa\FieldTypePage\FieldType\LandingPage\Converter\PageConverter;
use Ibexa\PageBuilder\Form\Transformer\PageHashTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class PageType extends AbstractType
{
    /** @var \Ibexa\FieldTypePage\FieldType\LandingPage\Converter\PageConverter */
    private $converter;

    /**
     * @param \Ibexa\FieldTypePage\FieldType\LandingPage\Converter\PageConverter $converter
     */
    public function __construct(PageConverter $converter)
    {
        $this->converter = $converter;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(new PageHashTransformer($this->converter));
    }

    /**
     * @return string|null
     */
    public function getParent()
    {
        return TextareaType::class;
    }
}

class_alias(PageType::class, 'EzSystems\EzPlatformPageBuilder\Form\Type\Page\PageType');
