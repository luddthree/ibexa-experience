<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Content\Image;

/**
 * @internal
 */
interface ImagePathResolverInterface
{
    public function imageExists(string $recommendedImage): bool;

    public function resolveImagePathByContentId(int $customerId, string $recommendedImage, int $contentId): ?string;

    public function resolveImagePathByContentRemoteId(int $customerId, string $recommendedImage, string $remoteId): ?string;
}
