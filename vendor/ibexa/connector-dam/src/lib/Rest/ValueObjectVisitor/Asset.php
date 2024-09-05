<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Connector\Dam\Rest\ValueObjectVisitor;

use Ibexa\Connector\Dam\Rest\Value\Asset as RestAsset;
use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\ValueObjectVisitor;
use Ibexa\Contracts\Rest\Output\Visitor;

class Asset extends ValueObjectVisitor
{
    /**
     * @param \Ibexa\Connector\Dam\Rest\Value\Asset $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data)
    {
        $generator->startObjectElement('Asset');
        $this->visitImageVariationAttributes($visitor, $generator, $data);
        $generator->endObjectElement('Asset');
    }

    protected function visitImageVariationAttributes(Visitor $visitor, Generator $generator, RestAsset $data)
    {
        $asset = $data->getAsset();

        $generator->attribute(
            'href',
            $this->router->generate(
                'ibexa.connector.dam.asset',
                [
                    'assetId' => $asset->getIdentifier()->getId(),
                    'assetSource' => $asset->getSource()->getSourceIdentifier(),
                ]
            )
        );

        $generator->valueElement('uri', $asset->getAssetUri()->getPath());
        $generator->valueElement('assetId', $asset->getIdentifier()->getId());
        $generator->valueElement('assetSource', $asset->getSource()->getSourceIdentifier());

        $generator->startHashElement('assetMetadata');
        foreach ($asset->getAssetMetadata() as $metadataKey => $assetMetadata) {
            $generator->valueElement($metadataKey, $assetMetadata);
        }
        $generator->endHashElement('assetMetadata');
    }
}

class_alias(Asset::class, 'Ibexa\Platform\Connector\Dam\Rest\ValueObjectVisitor\Asset');
