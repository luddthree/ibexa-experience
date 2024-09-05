<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Form\Transformer;

use Ibexa\FieldTypePage\FieldType\LandingPage\Converter\PageConverter;
use Symfony\Component\Form\DataTransformerInterface;

class PageHashTransformer implements DataTransformerInterface
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
     * @return \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page|mixed
     */
    public function reverseTransform($value)
    {
        return $this->converter->decode($value);
    }
}

class_alias(PageHashTransformer::class, 'EzSystems\EzPlatformPageBuilder\Form\Transformer\PageHashTransformer');
