<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ImageEditor\Optimizer;

use Spatie\ImageOptimizer\Image;
use Spatie\ImageOptimizer\Optimizers\BaseOptimizer;

final class ImagemagickJpgOptimizer extends BaseOptimizer
{
    protected $binaryName = 'convert';

    public function canHandle(Image $image): bool
    {
        return $image->mime() === 'image/jpg' || $image->mime() === 'image/jpeg';
    }

    public function getCommand(): string
    {
        $optionString = implode(' ', $this->options);

        return "\"{$this->binaryPath}{$this->binaryName}\" {$optionString} " . escapeshellarg($this->imagePath) . ' ' . escapeshellarg($this->imagePath);
    }
}
