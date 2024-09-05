<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ImageEditor\Optimizer;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\ImageEditor\Optimizer\OptimizerInterface;
use Spatie\ImageOptimizer\OptimizerChainFactory;

final class SpatieChainOptimizer implements OptimizerInterface
{
    private ConfigResolverInterface $configResolver;

    public function __construct(ConfigResolverInterface $configResolver)
    {
        $this->configResolver = $configResolver;
    }

    public function optimize(string $filePath): void
    {
        $quality = $this->configResolver->getParameter('image_editor.image_quality');
        $optimizer = OptimizerChainFactory::create(['quality' => $quality * 100]);
        $optimizer->addOptimizer(
            new ImagemagickJpgOptimizer([
                '-quality ' . $quality * 100 . '%',
                '-interlace Plane',
                '-gaussian-blur 0.05',
            ])
        );

        $optimizer->optimize($filePath);
    }
}
