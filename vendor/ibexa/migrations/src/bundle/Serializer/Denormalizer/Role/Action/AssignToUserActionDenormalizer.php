<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Denormalizer\Role\Action;

use Ibexa\Bundle\Migration\Serializer\Denormalizer\Action\MatchingAssertionTrait;
use Ibexa\Bundle\Migration\Serializer\Denormalizer\Action\UnpackActionValueTrait;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\RoleLimitation;
use Ibexa\Contracts\Migration\Serializer\Denormalizer\AbstractActionDenormalizer;
use Ibexa\Migration\ValueObject\Step\Action\Role\AssignToUser;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Webmozart\Assert\Assert;

final class AssignToUserActionDenormalizer extends AbstractActionDenormalizer implements DenormalizerAwareInterface
{
    use UnpackActionValueTrait;

    use MatchingAssertionTrait;

    use DenormalizerAwareTrait;

    protected function supportsActionName(string $actionName, string $format = null): bool
    {
        return $actionName === AssignToUser::TYPE;
    }

    /**
     * @param mixed $data
     * @param string $type
     * @param string|null $format
     * @param array<mixed> $context
     *
     * @return \Ibexa\Migration\ValueObject\Step\Action\Role\AssignToUser
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        Assert::isArray($data);
        $data = $this->unpackValue($data);

        $matchingMethods = [];

        $id = null;
        if (isset($data['id'])) {
            $matchingMethods[] = 'id';
            $id = (int) $data['id'];
        }

        $email = null;
        if (isset($data['email'])) {
            $matchingMethods[] = 'email';
            $email = (string) $data['email'];
        }

        $login = null;
        if (isset($data['login'])) {
            $matchingMethods[] = 'login';
            $login = (string) $data['login'];
        }

        $this->throwIfNoMatchingMethod(AssignToUser::TYPE, $matchingMethods, ['id', 'login', 'email']);
        $this->throwIfMoreThanOneMatchingMethod(AssignToUser::TYPE, $matchingMethods);

        $action = new AssignToUser($id, $email, $login);

        if (isset($data['limitation'])) {
            $limitation = $this->denormalizer->denormalize($data['limitation'], Limitation::class, $format, $context);
            Assert::isInstanceOf($limitation, RoleLimitation::class);
            $action->setLimitation($limitation);
        }

        return $action;
    }
}

class_alias(AssignToUserActionDenormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Denormalizer\Role\Action\AssignToUserActionDenormalizer');
