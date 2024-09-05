<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Mapper\Matcher;

use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;

class ContentStatusMatcherMapper implements MatcherValueMapperInterface, TranslationContainerInterface
{
    /** @var string */
    private $identifier;

    /**
     * @param string $identifier
     */
    public function __construct(string $identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function mapMatcherValue(array $matcherValues): array
    {
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
            (new Message('matcher.content_status', 'ibexa_workflow'))->setDesc('Content status'),
        ];
    }
}

class_alias(ContentStatusMatcherMapper::class, 'EzSystems\EzPlatformWorkflow\Mapper\Matcher\ContentStatusMatcherMapper');
