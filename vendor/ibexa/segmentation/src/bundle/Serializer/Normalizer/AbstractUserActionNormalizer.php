<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Segmentation\Serializer\Normalizer;

use Ibexa\Contracts\Migration\Serializer\Normalizer\AbstractActionNormalizer;
use Ibexa\Segmentation\Value\Step\Action\AbstractAssignUser;

abstract class AbstractUserActionNormalizer extends AbstractActionNormalizer
{
    /**
     * @return array{
     *     "id"?: int,
     *     "email"?: string,
     *     "login"?: string,
     * }
     */
    final protected function getUserData(AbstractAssignUser $object): array
    {
        $data = [];

        if ($object->getId() !== null) {
            $data['id'] = $object->getId();
        }

        if ($object->getEmail() !== null) {
            $data['email'] = $object->getEmail();
        }

        if ($object->getLogin() !== null) {
            $data['login'] = $object->getLogin();
        }

        return $data;
    }
}
