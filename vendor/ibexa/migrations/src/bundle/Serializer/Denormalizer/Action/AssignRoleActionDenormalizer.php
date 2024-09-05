<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Denormalizer\Action;

use Ibexa\Contracts\Core\Repository\Values\User\Limitation;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\RoleLimitation;
use Ibexa\Contracts\Migration\Serializer\Denormalizer\AbstractActionDenormalizer;
use Ibexa\Migration\ValueObject\Step\Action;
use LogicException;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Webmozart\Assert\Assert;

final class AssignRoleActionDenormalizer extends AbstractActionDenormalizer implements DenormalizerAwareInterface
{
    private const ALLOWED_TYPES = [
        Action\UserGroup\AssignRole::TYPE,
        Action\User\AssignRole::TYPE,
    ];

    use UnpackActionValueTrait;

    use MatchingAssertionTrait;

    use DenormalizerAwareTrait;

    protected function supportsActionName(string $actionName, string $format = null): bool
    {
        return in_array($actionName, self::ALLOWED_TYPES, true);
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function createAssignRole(array $data): Action\AbstractUserAssignRole
    {
        Assert::notEmpty($data['action']);
        $matchingMethods = [];

        $id = null;
        if (isset($data['id'])) {
            $matchingMethods[] = 'id';
            $id = (int) $data['id'];
        }

        $identifier = null;
        if (isset($data['identifier'])) {
            $matchingMethods[] = 'identifier';
            $identifier = (string) $data['identifier'];
        }

        $action = $data['action'];
        $this->throwIfNoMatchingMethod($action, $matchingMethods, ['id', 'identifier']);
        $this->throwIfMoreThanOneMatchingMethod($action, $matchingMethods);

        if ($action === Action\UserGroup\AssignRole::TYPE) {
            return new Action\UserGroup\AssignRole($id, $identifier);
        }

        if ($action === Action\User\AssignRole::TYPE) {
            return new Action\User\AssignRole($id, $identifier);
        }

        throw new LogicException(sprintf(
            '%s works with "%s" objects only, received %s.',
            __CLASS__,
            implode('", "', self::ALLOWED_TYPES),
            $action,
        ));
    }

    /**
     * @param mixed $data
     * @param string $type
     * @param string|null $format
     * @param array<mixed> $context
     *
     * @return \Ibexa\Migration\ValueObject\Step\Action\AbstractUserAssignRole
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        Assert::isArray($data);
        $data = $this->unpackValue($data);

        $action = $this->createAssignRole($data);
        if (isset($data['limitation'])) {
            $limitation = $this->denormalizer->denormalize($data['limitation'], Limitation::class, $format, $context);
            Assert::isInstanceOf($limitation, RoleLimitation::class);
            $action->setLimitation($limitation);
        }

        return $action;
    }
}

class_alias(AssignRoleActionDenormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Denormalizer\Action\AssignRoleActionDenormalizer');
