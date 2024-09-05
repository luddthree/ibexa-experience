<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Exception;

use DateTimeInterface;
use Exception;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Core\Base\Exceptions\Httpable;
use Ibexa\Core\Base\Translatable;
use Ibexa\Core\Base\TranslatableBase;
use Ibexa\Workflow\Value\VersionLock;

final class VersionLockedException extends UnauthorizedException implements Httpable, Translatable
{
    use TranslatableBase;

    /** @var \Ibexa\Workflow\Value\VersionLock */
    private $versionLock;

    /** @var \Ibexa\Contracts\Core\Repository\Values\User\User */
    private $user;

    public function __construct(
        VersionLock $versionLock,
        User $user,
        Exception $previous = null
    ) {
        $this->setMessageTemplate('Version is assigned to another user (%name%) since %modified%.');
        $this->setParameters([
            '%name%' => $user->getName(),
            '%modified%' => $versionLock->modified->format(DateTimeInterface::ISO8601),
        ]);

        parent::__construct($this->getBaseTranslation(), self::UNAUTHORIZED, $previous);
        $this->versionLock = $versionLock;
        $this->user = $user;
    }

    public function getVersionLock(): VersionLock
    {
        return $this->versionLock;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
