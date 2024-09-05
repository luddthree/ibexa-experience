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

final class UserBillingAddressTokenEnricher extends AbstractTokenEnricher
{
    private const CONTEXT = 'form';
    private const IDENTIFIER = 'billing_address';

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

        $field = $user->getField('billing_address');
        if ($field !== null) {
            /** @var \Ibexa\FieldTypeAddress\FieldType\Value $address */
            $address = $field->getValue();
            $this->resolveFieldMap($address->fields, $payload);
        }

        return $payload;
    }
}
