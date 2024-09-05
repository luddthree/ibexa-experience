<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Dashboard\ViewMatcher;

use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Core\MVC\Symfony\Matcher\ContentBased\MatcherInterface;
use Ibexa\Core\MVC\Symfony\View\ContentValueView;
use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\Dashboard\Specification\IsDashboardContentType;
use LogicException;

/**
 * @internal
 */
final class IsDashboardMatcher implements MatcherInterface
{
    private IsDashboardContentType $specification;

    private bool $isDashboard;

    public function __construct(IsDashboardContentType $isDashboardContentTypeSpecification)
    {
        $this->specification = $isDashboardContentTypeSpecification;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function matchLocation(Location $location): bool
    {
        $this->validate();

        $isSatisfiedBy = $this->specification->isSatisfiedBy(
            $location->getContentInfo()->getContentType()
        );

        return $this->isDashboard === $isSatisfiedBy;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function matchContentInfo(ContentInfo $contentInfo): bool
    {
        $this->validate();

        $isSatisfiedBy = $this->specification->isSatisfiedBy($contentInfo->getContentType());

        return $this->isDashboard === $isSatisfiedBy;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function match(View $view): bool
    {
        $this->validate();

        if (!$view instanceof ContentValueView) {
            return false;
        }

        $isSatisfiedBy = $this->specification->isSatisfiedBy(
            $view->getContent()->getContentType()
        );

        return $this->isDashboard === $isSatisfiedBy;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function setMatchingConfig($matchingConfig): void
    {
        if (!is_bool($matchingConfig)) {
            throw new InvalidArgumentException(
                '$matchingConfig',
                'IsDashboard View Matcher config should be true or false'
            );
        }

        $this->isDashboard = $matchingConfig;
    }

    private function validate(): void
    {
        if (!isset($this->isDashboard)) {
            throw new LogicException(
                sprintf('%s::setMatchingConfig needs to be called prior calling any match method', __CLASS__)
            );
        }
    }
}
