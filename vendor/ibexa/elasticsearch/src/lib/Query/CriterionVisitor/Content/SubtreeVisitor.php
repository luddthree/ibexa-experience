<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\CriterionVisitor\Content;

use Ibexa\Elasticsearch\Query\CriterionVisitor\AbstractSubtreeVisitor;

final class SubtreeVisitor extends AbstractSubtreeVisitor
{
    protected function getTargetField(): string
    {
        return 'location_path_string_mid';
    }
}

class_alias(SubtreeVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\CriterionVisitor\Content\SubtreeVisitor');
