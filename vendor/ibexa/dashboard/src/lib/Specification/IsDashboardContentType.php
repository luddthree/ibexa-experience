<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Dashboard\Specification;

use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\Core\Specification\AbstractSpecification;

/**
 * @internal
 */
final class IsDashboardContentType extends AbstractSpecification
{
    public const CONTENT_TYPE_IDENTIFIER_PARAM_NAME = 'dashboard.content_type_identifier';

    private ConfigResolverInterface $configResolver;

    public function __construct(ConfigResolverInterface $configResolver)
    {
        $this->configResolver = $configResolver;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $item
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function isSatisfiedBy($item): bool
    {
        if (!$item instanceof ContentType) {
            throw new InvalidArgumentException('$item', sprintf('Must be an instance of %s', ContentType::class));
        }

        return $item->identifier === $this->configResolver->getParameter(self::CONTENT_TYPE_IDENTIFIER_PARAM_NAME);
    }
}
