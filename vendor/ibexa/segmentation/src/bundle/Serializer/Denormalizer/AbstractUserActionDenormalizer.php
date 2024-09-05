<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Segmentation\Serializer\Denormalizer;

use Ibexa\Bundle\Migration\Serializer\Denormalizer\Action\MatchingAssertionTrait;
use Ibexa\Bundle\Migration\Serializer\Denormalizer\Action\UnpackActionValueTrait;
use Ibexa\Contracts\Migration\Serializer\Denormalizer\AbstractActionDenormalizer;
use Ibexa\Segmentation\Value\Step\Action\AbstractAssignUser;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;

/**
 * @template T of \Ibexa\Segmentation\Value\Step\Action\AbstractAssignUser
 */
abstract class AbstractUserActionDenormalizer extends AbstractActionDenormalizer implements DenormalizerAwareInterface
{
    use UnpackActionValueTrait;

    use MatchingAssertionTrait;

    use DenormalizerAwareTrait;

    /**
     * @param array<mixed> $data
     * @param string $actionClass
     *
     * @phpstan-param class-string<T> $actionClass
     *
     * @phpstan-return T
     */
    final protected function getActionStep(array $data, string $actionClass): AbstractAssignUser
    {
        $data = $this->unpackValue($data);

        $matchingMethods = [];

        $id = null;
        if (isset($data['id']) && is_int($data['id'])) {
            $matchingMethods[] = 'id';
            $id = $data['id'];
        }

        $email = null;
        if (isset($data['email']) && is_string($data['email'])) {
            $matchingMethods[] = 'email';
            $email = $data['email'];
        }

        $login = null;
        if (isset($data['login']) && is_string($data['login'])) {
            $matchingMethods[] = 'login';
            $login = $data['login'];
        }

        $this->throwIfNoMatchingMethod($actionClass::TYPE, $matchingMethods, ['id', 'login', 'email']);
        $this->throwIfMoreThanOneMatchingMethod($actionClass::TYPE, $matchingMethods);

        return new $actionClass($id, $email, $login);
    }
}
