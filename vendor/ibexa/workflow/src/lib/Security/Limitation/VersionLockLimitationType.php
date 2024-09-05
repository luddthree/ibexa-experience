<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Security\Limitation;

use Ibexa\Contracts\Core\Limitation\Type as SPILimitationTypeInterface;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation as APILimitationValue;
use Ibexa\Contracts\Core\Repository\Values\User\UserReference as APIUserReference;
use Ibexa\Contracts\Core\Repository\Values\ValueObject as APIValueObject;
use Ibexa\Contracts\Workflow\Persistence\Handler\HandlerInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Base\Exceptions\InvalidArgumentType;

final class VersionLockLimitationType implements SPILimitationTypeInterface
{
    /** @var \Ibexa\Contracts\Workflow\Persistence\Handler\HandlerInterface */
    private $handler;

    public function __construct(
        HandlerInterface $handler
    ) {
        $this->handler = $handler;
    }

    public function acceptValue(APILimitationValue $limitationValue): void
    {
        if (!$limitationValue instanceof VersionLockLimitation) {
            throw new InvalidArgumentType('$limitationValue', 'VersionLockLimitation', $limitationValue);
        } elseif (!is_array($limitationValue->limitationValues)) {
            throw new InvalidArgumentType('$limitationValue->limitationValues', 'array', $limitationValue->limitationValues);
        }

        foreach ($limitationValue->limitationValues as $key => $value) {
            if (!is_int($limitationValue->limitationValues[$key])) {
                throw new InvalidArgumentType("\$limitationValue->limitationValues[{$key}]", 'integer', $limitationValue->limitationValues[$key]);
            }
        }
    }

    public function validate(APILimitationValue $limitationValue): array
    {
        return [];
    }

    public function buildValue(array $limitationValues): VersionLockLimitation
    {
        return new VersionLockLimitation(['limitationValues' => array_map('intval', $limitationValues)]);
    }

    public function evaluate(
        APILimitationValue $value,
        APIUserReference $currentUser,
        APIValueObject $object,
        array $targets = null
    ): bool {
        if (!$value instanceof VersionLockLimitation) {
            throw new InvalidArgumentException('$value', 'Must be of type: VersionLockLimitation');
        }

        if (is_array($targets)) {
            foreach ($targets as $target) {
                if ($target instanceof IgnoreVersionLockLimitation) {
                    return true;
                }
            }
        }

        if ($object instanceof Content) {
            $versionInfo = $object->versionInfo;
        } elseif ($object instanceof VersionInfo) {
            $versionInfo = $object;
        } else {
            return true;
        }

        if (empty($value->limitationValues)) {
            return true;
        }

        $contentId = $versionInfo->contentInfo->id;
        $version = $versionInfo->versionNo;

        try {
            $versionLock = $this->handler->getVersionLock($contentId, $version);
            if ($versionLock->isLocked && $versionLock->userId !== $currentUser->getUserId()) {
                return false;
            }
        } catch (NotFoundException $exception) {
            return true;
        }

        return true;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException
     */
    public function getCriterion(
        APILimitationValue $value,
        APIUserReference $currentUser
    ): void {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException
     */
    public function valueSchema(): void
    {
        throw new NotImplementedException(__METHOD__);
    }
}
