<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Elasticsearch\Mapping\Event;

use Ibexa\Contracts\Core\Persistence\Content;
use Ibexa\Contracts\Elasticsearch\Mapping\ContentDocument;
use Symfony\Contracts\EventDispatcher\Event;

final class ContentIndexCreateEvent extends Event
{
    /** @var \Ibexa\Contracts\Core\Persistence\Content */
    private $content;

    /** @var \Ibexa\Contracts\Elasticsearch\Mapping\ContentDocument */
    private $document;

    public function __construct(Content $content, ContentDocument $document)
    {
        $this->content = $content;
        $this->document = $document;
    }

    public function getContent(): Content
    {
        return $this->content;
    }

    public function getDocument(): ContentDocument
    {
        return $this->document;
    }
}

class_alias(ContentIndexCreateEvent::class, 'Ibexa\Platform\Contracts\ElasticSearchEngine\Mapping\Event\ContentIndexCreateEvent');
