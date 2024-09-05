<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\REST\Input\Parser\Criterion;

use Ibexa\ActivityLog\REST\Input\Parser\AbstractParser;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\CriterionInterface;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use LogicException;

final class Criterion extends AbstractParser
{
    public const MEDIA_TYPE = 'application/vnd.ibexa.api.internal.activity_log.criterion';

    protected function normalize(array $data): array
    {
        if (isset($data['_type']) && !isset($data['type'])) {
            $data['type'] = $data['_type'];
            unset($data['_type']);
        }

        return parent::normalize($data);
    }

    public function innerParse(array $data, ParsingDispatcher $parsingDispatcher): CriterionInterface
    {
        $type = $data['type'];
        $mediaType = sprintf('%s.%s', Criterion::MEDIA_TYPE, $type);

        unset($data['type']);
        $result = $parsingDispatcher->parse($data, $mediaType);

        if (!$result instanceof CriterionInterface) {
            throw new LogicException(sprintf(
                'Invalid result from parser. Expected %s, received %s. Check parser handling %s media type.',
                CriterionInterface::class,
                get_debug_type($result),
                $mediaType,
            ));
        }

        return $result;
    }

    protected function getMandatoryKeys(): array
    {
        return ['type'];
    }

    protected function getName(): string
    {
        return 'criterion';
    }
}
