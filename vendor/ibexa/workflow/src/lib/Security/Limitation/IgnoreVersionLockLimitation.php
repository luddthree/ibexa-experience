<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Security\Limitation;

/**
 * @internal
 *
 * @see \Ibexa\Workflow\Security\Limitation\VersionLockLimitationType
 * @see \Ibexa\Bundle\Workflow\Controller\SuggestReviewerController
 *
 * This is dummy $target used as a marking and consumed by VersionLockLimitationType
 * to indicate that we should not perform limitation lock check during resolving this
 * single permission check.
 *
 * Used by SuggestReviewerController to prevent filtering out users without permissions
 * dues to version being currently locked.
 */
final class IgnoreVersionLockLimitation
{
}
