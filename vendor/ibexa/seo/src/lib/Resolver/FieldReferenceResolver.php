<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Seo\Resolver;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Seo\Resolver\FieldReferenceResolverInterface;

final class FieldReferenceResolver implements FieldReferenceResolverInterface
{
    private const TAG_PATTERN = '/(?<container>\<(?<value>.*)\>)/mU';

    public function resolve(Content $content, string $fieldValue): string
    {
        preg_match_all(self::TAG_PATTERN, $fieldValue, $matches, PREG_SET_ORDER, 0);

        foreach ($matches as $match) {
            if (empty($match['container'])) {
                continue;
            }

            if (strpos($match['value'], '|') !== false) {
                $values = explode('|', $match['value']);
            } else {
                $values = [$match['value']];
            }

            foreach ($values as $value) {
                if (null === ($fieldReference = $content->getField($value))) {
                    continue;
                }

                $fieldValue = self::stringReplaceFirst(
                    $match['container'],
                    (string) $fieldReference->value,
                    $fieldValue
                );

                break;
            }

            $fieldValue = trim(str_replace($match['container'], '', $fieldValue));
        }

        return $fieldValue;
    }

    private static function stringReplaceFirst(string $search, string $replace, string $subject): string
    {
        $pos = strpos($subject, $search);

        if ($pos !== false) {
            return substr_replace($subject, $replace, $pos, \strlen($search));
        }

        return $subject;
    }
}
