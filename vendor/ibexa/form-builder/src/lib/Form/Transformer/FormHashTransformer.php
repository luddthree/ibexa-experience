<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Transformer;

use Ibexa\FormBuilder\FieldType\Converter\FormConverter;
use Symfony\Component\Form\DataTransformerInterface;

class FormHashTransformer implements DataTransformerInterface
{
    /** @var \Ibexa\FormBuilder\FieldType\Converter\FormConverter */
    private $converter;

    /**
     * @param \Ibexa\FormBuilder\FieldType\Converter\FormConverter $converter
     */
    public function __construct(FormConverter $converter)
    {
        $this->converter = $converter;
    }

    /**
     * @param mixed $value
     *
     * @return mixed|string|null
     */
    public function transform($value)
    {
        if (null === $value) {
            return null;
        }

        return $this->converter->encode($value);
    }

    /**
     * @param $value
     *
     * @return \Ibexa\Contracts\FormBuilder\FieldType\Model\Form
     */
    public function reverseTransform($value)
    {
        return $this->converter->decode($value);
    }
}

class_alias(FormHashTransformer::class, 'EzSystems\EzPlatformFormBuilder\Form\Transformer\FormHashTransformer');
