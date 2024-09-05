<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\SiteFactory\Values\Site;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

/**
 * @property-read string $identifier
 * @property-read string $saGroup
 * @property-read \Ibexa\Contracts\SiteFactory\Values\Site\SiteAccessMatcherConfiguration $matcherConfiguration
 */
final class PublicAccess extends ValueObject
{
    public const STATUS_OFFLINE = 0;
    public const STATUS_ONLINE = 1;

    /** @var string|null */
    protected $identifier;

    /** @var string */
    protected $saGroup;

    /** @var \Ibexa\Contracts\SiteFactory\Values\Site\SiteAccessMatcherConfiguration */
    protected $matcherConfiguration;

    /** @var \Ibexa\Contracts\SiteFactory\Values\Site\SiteConfiguration */
    protected $config;

    /** @var int */
    protected $status;

    /** @var int */
    protected $siteId;

    public function __construct(
        ?string $identifier,
        ?int $siteId,
        string $saGroup,
        SiteAccessMatcherConfiguration $matcherConfiguration = null,
        SiteConfiguration $config = null,
        int $status = self::STATUS_ONLINE
    ) {
        parent::__construct();

        $this->identifier = $identifier ?? bin2hex(random_bytes(20));
        $this->saGroup = $saGroup;
        $this->status = $status;
        $this->config = $config;
        $this->matcherConfiguration = $matcherConfiguration ?? new SiteAccessMatcherConfiguration();
        $this->siteId = $siteId;
    }

    public function getMainLanguage(): string
    {
        return $this->config->getMainLanguage();
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    public function getDesign(): string
    {
        return $this->config->getDesign();
    }

    public function getSiteAccessGroup(): string
    {
        return $this->saGroup;
    }

    public function getTreeRootLocationId(): int
    {
        return $this->config->getTreeRootLocationId();
    }

    public function getSiteConfiguration(): SiteConfiguration
    {
        return $this->config;
    }

    public function getConfigParameter(string $namespace, string $name)
    {
        return $this->config->getValues()[$namespace . '.' . $name];
    }

    public function hasConfigParameter(string $namespace, string $name)
    {
        return isset($this->config->getValues()[$namespace . '.' . $name]);
    }

    public function setTreeRootLocationId(int $treeRootLocationId)
    {
        $this->config->setTreeRootLocationId($treeRootLocationId);
    }

    public function getSiteId(): int
    {
        return $this->siteId;
    }

    public function getMatcherConfiguration(): SiteAccessMatcherConfiguration
    {
        return $this->matcherConfiguration;
    }
}

class_alias(PublicAccess::class, 'EzSystems\EzPlatformSiteFactory\Values\Site\PublicAccess');
