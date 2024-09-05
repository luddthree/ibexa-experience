<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\REST\Input\Parser\Criterion;

use Ibexa\ActivityLog\REST\Input\Parser\AbstractParser;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\UserCriterion;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;

final class User extends AbstractParser
{
    protected function normalize(array $data): array
    {
        if (isset($data['#text']) && !isset($data['value'])) {
            $data['value'] = $data['#text'];
        }

        if (isset($data['value'])) {
            if (!is_array($data['value'])) {
                $data['value'] = [$data['value']];
            }
        }

        return parent::normalize($data);
    }

    public function innerParse(array $data, ParsingDispatcher $parsingDispatcher): UserCriterion
    {
        $userIds = $data['value'];

        return new UserCriterion($userIds);
    }

    protected function getMandatoryKeys(): array
    {
        return ['value'];
    }

    protected function getName(): string
    {
        return 'user';
    }
}
