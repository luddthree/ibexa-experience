<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Connector\Dam\Controller;

use Ibexa\Connector\Dam\Variation\TransformationFactoryRegistry;
use Ibexa\Contracts\Connector\Dam\AssetCollection;
use Ibexa\Contracts\Connector\Dam\AssetService;
use Ibexa\Contracts\Connector\Dam\AssetSource;
use Ibexa\Contracts\Connector\Dam\Search\AssetSearchResult;
use Ibexa\Contracts\Connector\Dam\Search\Query;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

final class AssetSearchController extends AbstractController
{
    /** @var \Ibexa\Contracts\Connector\Dam\AssetService */
    private $assetService;

    /** @var \Symfony\Component\Serializer\SerializerInterface */
    private $serializer;

    /** @var \Ibexa\Connector\Dam\Variation\TransformationFactoryRegistry */
    private $factoryRegistry;

    public function __construct(
        AssetService $assetService,
        SerializerInterface $serializer,
        TransformationFactoryRegistry $factoryRegistry
    ) {
        $this->assetService = $assetService;
        $this->serializer = $serializer;
        $this->factoryRegistry = $factoryRegistry;
    }

    public function fetchResultsAction(
        AssetSource $assetSource,
        Query $query,
        Request $request
    ): JsonResponse {
        $result = $this->assetService->search(
            $query,
            $assetSource,
            (int)$request->get('offset', 0),
            (int)$request->get('limit', 20)
        );

        if ($request->get('variation')) {
            $transformationFactory = $this->factoryRegistry->getFactory($assetSource);
            $transformation = $transformationFactory->build($request->get('variation'));

            $variations = [];
            foreach ($result as $inputAsset) {
                $variations[] = $this->assetService->transform($inputAsset, $transformation);
            }
            $result = new AssetSearchResult(
                $result->getTotalCount(),
                new AssetCollection($variations)
            );
        }

        return JsonResponse::fromJsonString(
            $this->serializer->serialize($result, 'json'),
            Response::HTTP_OK,
        );
    }
}

class_alias(AssetSearchController::class, 'Ibexa\Platform\Bundle\Connector\Dam\Controller\AssetSearchController');
