<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\CriterionGenerator;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;

final class ContentTypeIdentifierGenerator implements GeneratorInterface
{
    public static function getMatchProperty(): string
    {
        return 'content_type_identifier';
    }

    public function generate($value): Criterion
    {
        return new Criterion\ContentTypeIdentifier($value);
    }
}

class_alias(ContentTypeIdentifierGenerator::class, 'Ibexa\Platform\Migration\Generator\CriterionGenerator\ContentTypeIdentifierGenerator');
