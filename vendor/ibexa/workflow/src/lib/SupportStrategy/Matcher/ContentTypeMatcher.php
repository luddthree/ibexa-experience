<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\SupportStrategy\Matcher;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\Workflow\SupportStrategy\Matcher\MatcherInterface;

class ContentTypeMatcher implements MatcherInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var string */
    private $identifier;

    /**
     * @param string $identifier
     * @param \Ibexa\Contracts\Core\Repository\ContentTypeService $contentTypeService
     */
    public function __construct(string $identifier, ContentTypeService $contentTypeService)
    {
        $this->contentTypeService = $contentTypeService;
        $this->identifier = $identifier;
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
     * @param array $conditions
     *
     * @return bool
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function match(ValueObject $subject, array $conditions): bool
    {
        if ($subject instanceof Content) {
            $contentTypeId = $subject->getVersionInfo()->getContentInfo()->contentTypeId;
            $contentType = $this->contentTypeService->loadContentType($contentTypeId);
        } elseif ($subject instanceof VersionInfo) {
            $contentType = $subject->getContentInfo()->getContentType();
        } elseif ($subject instanceof ContentCreateStruct) {
            $contentType = $subject->contentType;
        } else {
            return false;
        }

        return \in_array($contentType->identifier, $conditions);
    }
}

class_alias(ContentTypeMatcher::class, 'EzSystems\EzPlatformWorkflow\SupportStrategy\Matcher\ContentTypeMatcher');
