<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\REST\Input\Parser\Criterion;

use Ibexa\ActivityLog\REST\Input\Parser\AbstractParser;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\ObjectCriterion;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;

final class ObjectClass extends AbstractParser
{
    protected function normalize(array $data): array
    {
        if (isset($data['ids'])) {
            if (isset($data['ids']['id'])) {
                $data['ids'] = $data['ids']['id'];
            }

            if (!is_array($data['ids'])) {
                $data['ids'] = [$data['ids']];
            }
        }

        return parent::normalize($data);
    }

    protected function getName(): string
    {
        return 'object_class';
    }

    public function innerParse(array $data, ParsingDispatcher $parsingDispatcher): ObjectCriterion
    {
        return new ObjectCriterion($data['class'], $data['ids'] ?? null);
    }

    protected function getMandatoryKeys(): array
    {
        return ['class'];
    }

    protected function getOptionalKeys(): ?array
    {
        return ['ids'];
    }
}
