<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\SiteFactory\Values\Site;

final class SiteConfiguration
{
    public const LANGUAGES = 'ibexa.site_access.config.languages';
    public const DESIGN = 'ibexa.site_access.config.design';
    public const TREE_ROOT_LOCATION_ID = 'ibexa.site_access.config.content.tree_root.location_id';

    /** @var array */
    private $values;

    public function __construct(array $values)
    {
        $this->values = $values;
    }

    public function getValues()
    {
        return $this->values;
    }

    public function getMainLanguage()
    {
        return reset($this->values[self::LANGUAGES]);
    }

    public function getLanguages(): array
    {
        return $this->values[self::LANGUAGES];
    }

    public function getDesign(): string
    {
        return $this->values[self::DESIGN];
    }

    public function getTreeRootLocationId(): int
    {
        return $this->values[self::TREE_ROOT_LOCATION_ID];
    }

    public function __toString(): string
    {
        return json_encode($this->values);
    }

    public function setTreeRootLocationId(int $treeRootLocationId)
    {
        $this->values[self::TREE_ROOT_LOCATION_ID] = $treeRootLocationId;
    }
}

class_alias(SiteConfiguration::class, 'EzSystems\EzPlatformSiteFactory\Values\Site\SiteConfiguration');
