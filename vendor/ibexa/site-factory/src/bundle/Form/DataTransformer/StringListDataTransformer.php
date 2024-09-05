<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteFactory\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class StringListDataTransformer implements DataTransformerInterface
{
    private const DEFAULT_SEPARATOR = ',';

    /**
     * @var string
     */
    private $separator;

    public function __construct(string $separator = self::DEFAULT_SEPARATOR)
    {
        $this->separator = trim($separator);
    }

    public function transform($value)
    {
        if (empty($value)) {
            return null;
        }
        if (!\is_array($value)) {
            throw new TransformationFailedException(
                sprintf('Expected a numeric, got %s instead', \gettype($value))
            );
        }

        return implode($this->separator . ' ', array_map('trim', $value));
    }

    public function reverseTransform($value)
    {
        if (empty($value)) {
            return null;
        }

        if (!\is_string($value)) {
            throw new TransformationFailedException(
                sprintf('Expected a string, got %s instead', \gettype($value))
            );
        }

        return array_map('trim', explode($this->separator, $value));
    }
}

class_alias(StringListDataTransformer::class, 'EzSystems\EzPlatformSiteFactoryBundle\Form\DataTransformer\StringListDataTransformer');
