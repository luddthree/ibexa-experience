<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Connector\Dam\ParamConverter;

use Ibexa\Contracts\Connector\Dam\AssetSource;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class AssetSourceParamConverter implements ParamConverterInterface
{
    public const SOURCE_PARAM_NAME = 'source';
    public const SOURCE_ASSET_NAME = 'sourceAsset';

    public function apply(Request $request, ParamConverter $configuration)
    {
        if (null === $request->get(self::SOURCE_PARAM_NAME)) {
            throw new BadRequestHttpException('Request do not contain all required arguments');
        }

        $source = new AssetSource($request->get(self::SOURCE_PARAM_NAME));

        $request->attributes->set($configuration->getName(), $source);
        $request->attributes->set(self::SOURCE_ASSET_NAME, $source);

        return true;
    }

    public function supports(ParamConverter $configuration)
    {
        return AssetSource::class === $configuration->getClass();
    }
}

class_alias(AssetSourceParamConverter::class, 'Ibexa\Platform\Bundle\Connector\Dam\ParamConverter\AssetSourceParamConverter');
