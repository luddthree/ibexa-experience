<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Connector\Dam\ParamConverter;

use Ibexa\Connector\Dam\Search\QueryFactoryRegistry;
use Ibexa\Contracts\Connector\Dam\AssetSource;
use Ibexa\Contracts\Connector\Dam\Search\Query;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

final class QueryParamConverter implements ParamConverterInterface
{
    /** @var \Ibexa\Connector\Dam\Search\QueryFactoryRegistry */
    private $queryFactoryRegistry;

    /** @var \Ibexa\Bundle\Connector\Dam\ParamConverter\AssetSourceParamConverter */
    private $assetSourceParamConverter;

    public function __construct(
        QueryFactoryRegistry $queryFactoryRegistry,
        AssetSourceParamConverter $assetSourceParamConverter
    ) {
        $this->queryFactoryRegistry = $queryFactoryRegistry;
        $this->assetSourceParamConverter = $assetSourceParamConverter;
    }

    public function apply(Request $request, ParamConverter $configuration)
    {
        $source = $request->get(AssetSourceParamConverter::SOURCE_ASSET_NAME) ?? $this->createAssetSource($request);

        $query = $this->queryFactoryRegistry->getFactory($source)->buildFromRequest($request);

        $request->attributes->set($configuration->getName(), $query);

        return true;
    }

    public function supports(ParamConverter $configuration)
    {
        return Query::class === $configuration->getClass();
    }

    private function createAssetSource(Request $request): AssetSource
    {
        $configuration = new ParamConverter([
            'name' => AssetSourceParamConverter::SOURCE_ASSET_NAME,
            'class' => AssetSource::class,
        ]);

        $this->assetSourceParamConverter->apply($request, $configuration);

        return $request->get(AssetSourceParamConverter::SOURCE_ASSET_NAME);
    }
}

class_alias(QueryParamConverter::class, 'Ibexa\Platform\Bundle\Connector\Dam\ParamConverter\QueryParamConverter');
