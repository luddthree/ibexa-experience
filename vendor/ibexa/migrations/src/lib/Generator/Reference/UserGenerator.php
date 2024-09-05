<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\Reference;

use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\StepExecutor\ReferenceDefinition\User\UserIdResolver;
use Webmozart\Assert\Assert;

final class UserGenerator extends AbstractReferenceGenerator
{
    protected function getReferenceMetadata(): array
    {
        return [
            new ReferenceMetadata('ref__user', UserIdResolver::getHandledType()),
        ];
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\User\User $user
     *
     * @return \Ibexa\Migration\Generator\Reference\ReferenceDefinition[]
     */
    public function generate(ValueObject $user): array
    {
        Assert::isInstanceOf($user, User::class);

        return $this->generateReferences((string)$user->id, 'user_id');
    }
}
