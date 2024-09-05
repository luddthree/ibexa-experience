<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteFactory\Form\DataMapper;

use Ibexa\AdminUi\Limitation\LimitationValueMapperInterface;
use Ibexa\AdminUi\Limitation\Mapper\MultipleSelectionBasedMapper;
use Ibexa\AdminUi\Siteaccess\SiteAccessKeyGeneratorInterface;
use Ibexa\AdminUi\Siteaccess\SiteAccessNameGeneratorInterface;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;

class SiteFactorySiteAccessLimitationMapper extends MultipleSelectionBasedMapper implements LimitationValueMapperInterface
{
    /** @var \Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface */
    private $siteAccessService;

    /** @var \Ibexa\AdminUi\Siteaccess\SiteAccessKeyGeneratorInterface */
    private $siteAccessKeyGenerator;

    /** @var \Ibexa\AdminUi\Siteaccess\SiteAccessNameGeneratorInterface */
    private $siteAccessNameGenerator;

    public function __construct(
        SiteAccessServiceInterface $siteAccessService,
        SiteAccessKeyGeneratorInterface $siteAccessKeyGenerator,
        SiteAccessNameGeneratorInterface $siteAccessNameGenerator
    ) {
        $this->siteAccessService = $siteAccessService;
        $this->siteAccessKeyGenerator = $siteAccessKeyGenerator;
        $this->siteAccessNameGenerator = $siteAccessNameGenerator;
    }

    protected function getSelectionChoices(): array
    {
        $siteAccesses = [];
        foreach ($this->siteAccessService->getAll() as $siteAccess) {
            $key = $this->siteAccessKeyGenerator->generate($siteAccess->name);
            $name = $this->siteAccessNameGenerator->generate($siteAccess);
            $siteAccesses[$key] = $name;
        }

        return $siteAccesses;
    }

    public function mapLimitationValue(Limitation $limitation): array
    {
        $values = [];
        foreach ($this->siteAccessService->getAll() as $siteAccess) {
            if (in_array($this->siteAccessKeyGenerator->generate($siteAccess->name), $limitation->limitationValues)) {
                $values[] = $this->siteAccessNameGenerator->generate($siteAccess);
            }
        }

        return $values;
    }
}

class_alias(SiteFactorySiteAccessLimitationMapper::class, 'EzSystems\EzPlatformSiteFactoryBundle\Form\DataMapper\SiteFactorySiteAccessLimitationMapper');
