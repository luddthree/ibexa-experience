<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteFactory\ParamConverter;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\SiteFactory\Service\SiteServiceInterface;
use Ibexa\Contracts\SiteFactory\Values\Site\Site;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SiteParamConverter implements ParamConverterInterface
{
    public const PARAMETER_SITE_ID = 'siteId';

    /**
     * @var \Ibexa\Contracts\SiteFactory\Service\SiteServiceInterface
     */
    private $siteService;

    /**
     * @param \Ibexa\Contracts\SiteFactory\Service\SiteServiceInterface $siteService
     */
    public function __construct(SiteServiceInterface $siteService)
    {
        $this->siteService = $siteService;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function apply(Request $request, ParamConverter $configuration): bool
    {
        if (!$request->get(self::PARAMETER_SITE_ID)) {
            return false;
        }

        $siteId = (int)$request->get(self::PARAMETER_SITE_ID);

        try {
            $site = $this->siteService->loadSite($siteId);
        } catch (NotFoundException $e) {
            throw new NotFoundHttpException("Site $siteId not found.");
        }

        $request->attributes->set($configuration->getName(), $site);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ParamConverter $configuration): bool
    {
        return Site::class === $configuration->getClass();
    }
}

class_alias(SiteParamConverter::class, 'EzSystems\EzPlatformSiteFactoryBundle\ParamConverter\SiteParamConverter');
