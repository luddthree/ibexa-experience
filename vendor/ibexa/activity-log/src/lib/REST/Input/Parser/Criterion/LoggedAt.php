<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\REST\Input\Parser\Criterion;

use DateTimeImmutable;
use Exception;
use Ibexa\ActivityLog\REST\Input\Parser\AbstractParser;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\LoggedAtCriterion;
use Ibexa\Contracts\Rest\Exceptions\Parser;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;

final class LoggedAt extends AbstractParser
{
    public function innerParse(array $data, ParsingDispatcher $parsingDispatcher): LoggedAtCriterion
    {
        try {
            $dateTime = new DateTimeImmutable($data['value']);
        } catch (Exception $e) {
            throw new Parser(
                sprintf(
                    'Failed to create %s criterion: %s',
                    $this->getName(),
                    $e->getMessage(),
                ),
                0,
                $e,
            );
        }

        return new LoggedAtCriterion(
            $dateTime,
            $data['operator'] ?? LoggedAtCriterion::EQ,
        );
    }

    protected function getMandatoryKeys(): array
    {
        return ['value'];
    }

    protected function getOptionalKeys(): ?array
    {
        return ['operator'];
    }

    protected function getName(): string
    {
        return 'logged_at';
    }
}
