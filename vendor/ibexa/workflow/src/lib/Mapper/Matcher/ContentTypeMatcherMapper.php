<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Mapper\Matcher;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;

class ContentTypeMatcherMapper implements MatcherValueMapperInterface, TranslationContainerInterface
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
     * {@inheritdoc}
     */
    public function mapMatcherValue(array $matcherValues): array
    {
        $matcherValues = array_map(function ($contentTypeIdentifier) {
            try {
                return $this->contentTypeService->loadContentTypeByIdentifier($contentTypeIdentifier);
            } catch (NotFoundException $e) {
            }
        }, $matcherValues);

        return $matcherValues;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @return \JMS\TranslationBundle\Model\Message[]
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message('matcher.content_type', 'ibexa_workflow'))->setDesc('Content type'),
        ];
    }
}

class_alias(ContentTypeMatcherMapper::class, 'EzSystems\EzPlatformWorkflow\Mapper\Matcher\ContentTypeMatcherMapper');
