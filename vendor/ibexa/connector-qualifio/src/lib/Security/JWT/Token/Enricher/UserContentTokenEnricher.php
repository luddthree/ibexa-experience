<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ConnectorQualifio\Security\JWT\Token\Enricher;

use Ibexa\ConnectorQualifio\Security\JWT\Token\AbstractTokenEnricher;
use Ibexa\ConnectorQualifio\Service\QualifioFieldMapResolver;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\User\User;

final class UserContentTokenEnricher extends AbstractTokenEnricher
{
    private const CONTEXT = 'form';
    private const IDENTIFIER = 'content';

    private Repository $repository;

    public function __construct(
        QualifioFieldMapResolver $fieldMapResolver,
        Repository $repository
    ) {
        parent::__construct($fieldMapResolver);
        $this->repository = $repository;
    }

    public function getIdentifier(): string
    {
        return self::IDENTIFIER;
    }

    public function getContext(): string
    {
        return self::CONTEXT;
    }

    public function getPayload(int $userId): array
    {
        $payload = [];
        $user = $this->repository->sudo(
            static fn (Repository $repository): User => $repository->getUserService()->loadUser($userId)
        );

        $fieldMap = [];
        foreach ($user->getFields() as $field) {
            switch ($field->fieldTypeIdentifier) {
                case 'ezdatetime':
                case 'ezdate':
                case 'eztime':
                    /** @var \DateTime $dateTime */
                    $dateTime = $field->getValue()->date;
                    $fieldMap[$field->fieldDefIdentifier] = $dateTime->getTimestamp();
                    break;
                default:
                    // using toString() conversion of FieldTypes
                    $fieldMap[$field->fieldDefIdentifier] = (string)$field->getValue();
            }
        }

        $this->resolveFieldMap($fieldMap, $payload);

        return $payload;
    }
}
