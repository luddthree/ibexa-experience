<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Connector\Dam\Rest\ValueObjectVisitor;

use Ibexa\Contracts\Connector\Dam\Variation\AssetVariation;
use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\ValueObjectVisitor;
use Ibexa\Contracts\Rest\Output\Visitor;

class ImageAssetVariation extends ValueObjectVisitor
{
    /**
     * @param \Ibexa\Contracts\Connector\Dam\Variation\AssetVariation $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data)
    {
        $generator->startObjectElement('ImageAssetVariation');
        $this->visitImageVariationAttributes($visitor, $generator, $data);
        $generator->endObjectElement('ImageAssetVariation');
    }

    protected function visitImageVariationAttributes(Visitor $visitor, Generator $generator, AssetVariation $data)
    {
        $generator->startAttribute(
            'href',
            $this->router->generate(
                'ibexa.connector.dam.asset_variation',
                [
                    'assetId' => $data->getIdentifier()->getId(),
                    'assetSource' => $data->getSource()->getSourceIdentifier(),
                    'transformationName' => $data->getTransformation()->getName(),
                ]
            )
        );
        $generator->endAttribute('href');

        $generator->startValueElement('uri', $data->getAssetUri()->getPath());
        $generator->endValueElement('uri');
    }
}

class_alias(ImageAssetVariation::class, 'Ibexa\Platform\Connector\Dam\Rest\ValueObjectVisitor\ImageAssetVariation');
