<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Connector\Dam\Event;

use Ibexa\Contracts\Connector\Dam\AssetIdentifier;
use Ibexa\Contracts\Connector\Dam\AssetSource;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Symfony\Contracts\EventDispatcher\Event;

class PublishAssetEvent extends Event
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content */
    private $content;

    /** @var \Ibexa\Contracts\Connector\Dam\AssetIdentifier */
    private $assetIdentifier;

    /** @var \Ibexa\Contracts\Connector\Dam\AssetSource */
    private $assetSource;

    public function __construct(
        Content $content,
        AssetIdentifier $assetIdentifier,
        AssetSource $assetSource
    ) {
        $this->content = $content;
        $this->assetIdentifier = $assetIdentifier;
        $this->assetSource = $assetSource;
    }

    public function getContent(): Content
    {
        return $this->content;
    }

    public function getAssetIdentifier(): AssetIdentifier
    {
        return $this->assetIdentifier;
    }

    public function getAssetSource(): AssetSource
    {
        return $this->assetSource;
    }
}

class_alias(PublishAssetEvent::class, 'Ibexa\Platform\Connector\Dam\Event\PublishAssetEvent');
