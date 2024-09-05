<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\REST\Input\Parser\Criterion;

use Ibexa\ActivityLog\REST\Input\Parser\AbstractParser;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\ActionCriterion;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;

final class Action extends AbstractParser
{
    protected function normalize(array $data): array
    {
        if (isset($data['#text']) && !isset($data['value'])) {
            $data['value'] = $data['#text'];
        }

        if (isset($data['value']) && !is_array($data['value'])) {
            $data['value'] = [$data['value']];
        }

        return parent::normalize($data);
    }

    public function innerParse(array $data, ParsingDispatcher $parsingDispatcher): ActionCriterion
    {
        $value = $data['value'];

        return new ActionCriterion($value);
    }

    protected function getName(): string
    {
        return 'action';
    }

    protected function getMandatoryKeys(): array
    {
        return ['value'];
    }
}
