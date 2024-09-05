<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor\ContentType;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Migration\ValueObject\ContentType\Matcher;

interface ContentTypeFinderInterface
{
    /**
     * @param string $field
     */
    public function supports(string $field): bool;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function find(Matcher $matcher): ContentType;
}

class_alias(ContentTypeFinderInterface::class, 'Ibexa\Platform\Migration\StepExecutor\ContentType\ContentTypeFinderInterface');
