<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Connector\Dam\Twig\Extension;

use Ibexa\Connector\Dam\Variation\TransformationFactoryRegistry;
use Ibexa\Contracts\Connector\Dam\AssetSource;
use Ibexa\Contracts\Connector\Dam\Variation\Transformation;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class TransformationExtension extends AbstractExtension
{
    /** @var \Ibexa\Connector\Dam\Variation\TransformationFactoryRegistry */
    private $transformationFactoryRegistry;

    public function __construct(TransformationFactoryRegistry $transformationFactoryRegistry)
    {
        $this->transformationFactoryRegistry = $transformationFactoryRegistry;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction(
                'ibexa_platform_dam_image_transformation',
                [$this, 'getTransformation'],
                [
                    'is_safe' => ['html'],
                    'deprecated' => '4.0',
                    'alternative' => 'ibexa_dam_image_transformation',
                ]
            ),
            new TwigFunction(
                'ibexa_dam_image_transformation',
                [$this, 'getTransformation'],
                ['is_safe' => ['html']]
            ),
        ];
    }

    public function getTransformation(
        string $source,
        ?string $variationName = null,
        array $transformationParameters = []
    ): Transformation {
        return $this
            ->transformationFactoryRegistry
            ->getFactory(new AssetSource($source))
            ->build($variationName, $transformationParameters);
    }
}

class_alias(TransformationExtension::class, 'Ibexa\Platform\Connector\Dam\Twig\Extension\TransformationExtension');
