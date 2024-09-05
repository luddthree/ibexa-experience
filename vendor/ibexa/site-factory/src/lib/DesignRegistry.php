<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\SiteFactory;

use Ibexa\Contracts\SiteFactory\Values\Design\Template;

class DesignRegistry
{
    /** @var \Ibexa\Contracts\SiteFactory\Values\Design\Template[] */
    private $templates;

    public function __construct(array $templates)
    {
        $this->templates = $templates;
    }

    public function getTemplateByIdentifier(string $identifier): ?Template
    {
        $templates = array_values(
            array_filter(
                $this->templates,
                static function (Template $template) use ($identifier) {
                    return $template->getIdentifier() === $identifier;
                }
            )
        );

        return $templates[0] ?? null;
    }

    public function getTemplate(string $design, string $saGroup): ?Template
    {
        $templates = array_values(
            array_filter(
                $this->templates,
                static function (Template $template) use ($design, $saGroup): bool {
                    return $template->getDesign() === $design
                        && $template->getSiteAccessGroup() === $saGroup;
                }
            )
        );

        return $templates[0] ?? null;
    }

    /**
     * @return \Ibexa\Contracts\SiteFactory\Values\Design\Template[]
     */
    public function getTemplates(): array
    {
        return $this->templates;
    }
}

class_alias(DesignRegistry::class, 'EzSystems\EzPlatformSiteFactory\DesignRegistry');
