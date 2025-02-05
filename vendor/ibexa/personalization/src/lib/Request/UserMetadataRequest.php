<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Request;

use Ibexa\Personalization\SPI\UserAPIRequest;

final class UserMetadataRequest extends UserAPIRequest
{
    /** @var bool */
    public $allSources = false;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $parameters = [])
    {
        parent::__construct($this, $parameters);
    }

    public function getRequestAttributes(): array
    {
        return [
            'allSources' => $this->allSources,
        ];
    }
}

class_alias(UserMetadataRequest::class, 'EzSystems\EzRecommendationClient\Request\UserMetadataRequest');
