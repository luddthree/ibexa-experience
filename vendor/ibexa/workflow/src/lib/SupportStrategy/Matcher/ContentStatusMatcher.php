<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\SupportStrategy\Matcher;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\Workflow\SupportStrategy\Matcher\MatcherInterface;

class ContentStatusMatcher implements MatcherInterface
{
    public const CONTENT_STATUS_DRAFT = 'draft';
    public const CONTENT_STATUS_PUBLISHED = 'published';

    /** @var string */
    private $identifier;

    public function __construct(string $identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ValueObject $subject
     * @param array $conditions
     *
     * @return bool
     */
    public function match(ValueObject $subject, array $conditions): bool
    {
        foreach ($conditions as $condition) {
            if ($condition === self::CONTENT_STATUS_DRAFT && $this->isDraft($subject)) {
                return true;
            } elseif ($condition === self::CONTENT_STATUS_PUBLISHED && $this->isPublished($subject)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ValueObject $subject
     *
     * @return bool
     */
    protected function isDraft(ValueObject $subject): bool
    {
        return $subject instanceof ContentCreateStruct
            || ($subject instanceof Content && $subject->getVersionInfo()->getContentInfo()->isDraft())
            || ($subject instanceof VersionInfo && $subject->getContentInfo()->isDraft());
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ValueObject $subject
     *
     * @return bool
     */
    protected function isPublished(ValueObject $subject): bool
    {
        return ($subject instanceof Content && $subject->getVersionInfo()->getContentInfo()->isPublished())
            || ($subject instanceof VersionInfo && $subject->getContentInfo()->isPublished());
    }
}

class_alias(ContentStatusMatcher::class, 'EzSystems\EzPlatformWorkflow\SupportStrategy\Matcher\ContentStatusMatcher');
