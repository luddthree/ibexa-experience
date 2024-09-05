<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\REST\Input\Parser\Criterion;

use Ibexa\ActivityLog\REST\Input\Parser\AbstractParser;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\CriterionInterface;
use Ibexa\Contracts\Rest\Exceptions\Parser;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use LogicException;

final class Criteria extends AbstractParser
{
    public const MEDIA_TYPE = 'application/vnd.ibexa.api.internal.activity_log.criteria';

    protected function normalize(array $data): array
    {
        if (isset($data['criterion'])) {
            if (array_is_list($data['criterion'])) {
                $data = $data['criterion'];
            } else {
                $data = [$data['criterion']];
            }
        }

        return parent::normalize($data);
    }

    /**
     * @return array<\Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\CriterionInterface>
     */
    public function innerParse(array $data, ParsingDispatcher $parsingDispatcher): array
    {
        $criteria = [];
        foreach ($data as $key => $criterion) {
            if (!is_array($criterion)) {
                throw new Parser(sprintf('The "%s" criterion must be an array.', $key));
            }

            $result = $parsingDispatcher->parse($criterion, Criterion::MEDIA_TYPE);

            if (!$result instanceof CriterionInterface) {
                throw new LogicException(sprintf(
                    'Invalid result from parser. Expected %s, received %s. Check parser handling %s media type.',
                    CriterionInterface::class,
                    get_debug_type($result),
                    Criterion::MEDIA_TYPE,
                ));
            }

            $criteria[] = $result;
        }

        return $criteria;
    }

    protected function getName(): string
    {
        return 'criteria';
    }
}
