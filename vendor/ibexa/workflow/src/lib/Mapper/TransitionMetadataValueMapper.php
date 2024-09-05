<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Mapper;

use DateTimeImmutable;
use Ibexa\Contracts\Core\Persistence\ValueObject as PersistenceValueObject;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException as ApiNotFoundException;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Workflow\Value\TransitionMetadata;

class TransitionMetadataValueMapper implements ValueMapper
{
    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    private $userService;

    /**
     * @param \Ibexa\Contracts\Core\Repository\UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * {@inheritdoc}
     *
     * @param \Ibexa\Workflow\Value\TransitionMetadata
     *
     * @return \Ibexa\Workflow\Value\Persistence\TransitionMetadata
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function fromPersistenceValue(PersistenceValueObject $object): ValueObject
    {
        $user = null;
        try {
            $user = $this->userService->loadUser($object->userId);
        } catch (ApiNotFoundException $ex) {
        }

        $apiTransitionMetadata = new TransitionMetadata();
        $apiTransitionMetadata->id = $object->id;
        $apiTransitionMetadata->name = $object->name;
        $apiTransitionMetadata->date = DateTimeImmutable::createFromFormat(
            'U',
            (string)$object->date
        );
        $apiTransitionMetadata->user = $user;
        $apiTransitionMetadata->message = $object->message;

        return $apiTransitionMetadata;
    }
}

class_alias(TransitionMetadataValueMapper::class, 'EzSystems\EzPlatformWorkflow\Mapper\TransitionMetadataValueMapper');
