<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteFactory\Form\Data;

use Ibexa\Contracts\SiteFactory\Values\Site\PublicAccess;

class PublicAccessData
{
    /** @var \Ibexa\Bundle\SiteFactory\Form\Data\SiteMatcherConfigurationData|null */
    private $matcherConfiguration;

    /** @var string|null */
    private $identifier;

    /** @var array */
    private $config = [];

    /** @var int */
    private $status = PublicAccess::STATUS_ONLINE;

    public function __construct()
    {
        $this->matcherConfiguration = new SiteMatcherConfigurationData();
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    public function getMatcherConfiguration(): ?SiteMatcherConfigurationData
    {
        return $this->matcherConfiguration;
    }

    public function setMatcherConfiguration(SiteMatcherConfigurationData $matcherConfiguration): void
    {
        $this->matcherConfiguration = $matcherConfiguration;
    }

    public function getConfig(): array
    {
        return $this->config ?? [];
    }

    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(?string $identifier): void
    {
        $this->identifier = $identifier;
    }
}

class_alias(PublicAccessData::class, 'EzSystems\EzPlatformSiteFactoryBundle\Form\Data\PublicAccessData');
