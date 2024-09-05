<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\SiteFactory\Form\DataTransformer;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\SiteFactory\Service\SiteServiceInterface;
use Ibexa\Contracts\SiteFactory\Values\Site\Site;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Transforms between a Site's ID and a domain specific object.
 */
final class SiteTransformer implements DataTransformerInterface
{
    /** @var \Ibexa\Contracts\SiteFactory\Service\SiteServiceInterface */
    private $siteService;

    public function __construct(SiteServiceInterface $siteService)
    {
        $this->siteService = $siteService;
    }

    public function transform($value): ?int
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof Site) {
            throw new TransformationFailedException('Expected a ' . Site::class . ' object.');
        }

        return $value->id;
    }

    public function reverseTransform($value): ?Site
    {
        if (empty($value)) {
            return null;
        }

        if (!is_int($value) && !ctype_digit($value)) {
            throw new TransformationFailedException('Expected a numeric string.');
        }

        try {
            return $this->siteService->loadSite((int)$value);
        } catch (NotFoundException $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }
}

class_alias(SiteTransformer::class, 'EzSystems\EzPlatformSiteFactoryBundle\Form\DataTransformer\SiteTransformer');
