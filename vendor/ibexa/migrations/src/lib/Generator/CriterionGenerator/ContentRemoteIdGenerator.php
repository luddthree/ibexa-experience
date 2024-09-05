<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\CriterionGenerator;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;

final class ContentRemoteIdGenerator implements GeneratorInterface
{
    public static function getMatchProperty(): string
    {
        return 'content_remote_id';
    }

    public function generate($value): Criterion
    {
        return new Criterion\RemoteId($value);
    }
}

class_alias(ContentRemoteIdGenerator::class, 'Ibexa\Platform\Migration\Generator\CriterionGenerator\ContentRemoteIdGenerator');
