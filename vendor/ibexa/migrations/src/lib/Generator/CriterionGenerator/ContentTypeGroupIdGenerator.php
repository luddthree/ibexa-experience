<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\CriterionGenerator;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;

final class ContentTypeGroupIdGenerator implements GeneratorInterface
{
    public static function getMatchProperty(): string
    {
        return 'content_type_group_id';
    }

    public function generate($value): Criterion
    {
        return new Criterion\ContentTypeGroupId($value);
    }
}

class_alias(ContentTypeGroupIdGenerator::class, 'Ibexa\Platform\Migration\Generator\CriterionGenerator\ContentTypeGroupIdGenerator');
