<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Fastly\ImageOptimizer;

use Ibexa\Bundle\Core\Imagine\Filter\Loader\BorderFilterLoader;
use Ibexa\Bundle\Core\Imagine\Filter\Loader\CropFilterLoader;
use Ibexa\Bundle\Core\Imagine\Filter\Loader\GrayscaleFilterLoader;
use Ibexa\Bundle\Core\Imagine\Filter\Loader\ScaleDownOnlyFilterLoader;
use Ibexa\Bundle\Core\Imagine\Filter\Loader\ScaleExactFilterLoader;
use Ibexa\Bundle\Core\Imagine\Filter\Loader\ScaleFilterLoader;
use Ibexa\Bundle\Core\Imagine\Filter\Loader\ScaleHeightDownOnlyFilterLoader;
use Ibexa\Bundle\Core\Imagine\Filter\Loader\ScaleHeightFilterLoader;
use Ibexa\Bundle\Core\Imagine\Filter\Loader\ScalePercentFilterLoader;
use Ibexa\Bundle\Core\Imagine\Filter\Loader\ScaleWidthDownOnlyFilterLoader;
use Ibexa\Bundle\Core\Imagine\Filter\Loader\ScaleWidthFilterLoader;
use Ibexa\Bundle\Core\Imagine\IORepositoryResolver;
use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;

/**
 * @phpstan-type TAliasConfig array{
 *     reference: string|null,
 *     filters: array<string, array<int, mixed>>,
 * }
 */
final class AliasConfigurationMapper
{
    private ConfigResolverInterface $configResolver;

    public function __construct(
        ConfigResolverInterface $configResolver
    ) {
        $this->configResolver = $configResolver;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     *
     * @return array<string, string|int>
     */
    public function map(string $variation): array
    {
        if ($variation === IORepositoryResolver::VARIATION_ORIGINAL) {
            return [];
        }

        $alias = $this->configResolver->getParameter('image_variations');

        if (!isset($alias[$variation])) {
            throw new InvalidArgumentException(
                'variation',
                sprintf(
                    'Variation "%s" alias does not exist.',
                    $variation
                )
            );
        }

        return $this->mapConfiguration($alias[$variation] ?? []);
    }

    /**
     * @phpstan-param TAliasConfig $aliasConfiguration
     *
     * @return array{
     *     reference: string,
     *     configuration: array<string, mixed>
     * }
     *
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     */
    public function mapConfiguration(array $aliasConfiguration): array
    {
        $configuration = [
            'reference' => $aliasConfiguration['reference'] ?? IORepositoryResolver::VARIATION_ORIGINAL,
            'configuration' => [],
        ];
        foreach ($aliasConfiguration['filters'] ?? [] as $filter => $parameters) {
            $configuration['configuration'] = array_merge(
                $configuration['configuration'],
                $this->mapFilter($filter, $parameters)
            );
        }

        return $configuration;
    }

    /**
     * @param array<int, mixed> $parameters
     *
     * @return array<string, mixed>
     *
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     */
    public function mapFilter(string $filter, array $parameters = []): array
    {
        $configuration = [];
        switch ($filter) {
            case ScaleDownOnlyFilterLoader::IDENTIFIER:
                $configuration['width'] = $parameters[0];
                $configuration['height'] = $parameters[1];
                $configuration['fit'] = 'bounds';
                break;
            case ScaleWidthDownOnlyFilterLoader::IDENTIFIER:
                $configuration['width'] = $parameters[0];
                $configuration['height'] = '100p';
                $configuration['fit'] = 'bounds';
                break;
            case ScaleHeightDownOnlyFilterLoader::IDENTIFIER:
                $configuration['width'] = '100p';
                $configuration['height'] = $parameters[0];
                $configuration['fit'] = 'bounds';
                break;
            case ScaleWidthFilterLoader::IDENTIFIER:
                $configuration['width'] = $parameters[0];
                $configuration['enable'] = 'upscale';
                break;
            case ScaleHeightFilterLoader::IDENTIFIER:
                $configuration['height'] = $parameters[0];
                $configuration['enable'] = 'upscale';
                break;
            case ScaleFilterLoader::IDENTIFIER:
                $configuration['width'] = $parameters[0];
                $configuration['height'] = $parameters[1];
                $configuration['fit'] = 'cover';
                $configuration['enable'] = 'upscale';
                break;
            case ScaleExactFilterLoader::IDENTIFIER:
            case 'resize':
                $configuration['width'] = $parameters[0];
                $configuration['height'] = $parameters[1];
                $configuration['enable'] = 'upscale';
                break;
            case ScalePercentFilterLoader::IDENTIFIER:
                $configuration['width'] = $parameters[0] . 'p';
                $configuration['height'] = $parameters[1] . 'p';
                $configuration['enable'] = 'upscale';
                break;
            case CropFilterLoader::IDENTIFIER:
                $configuration['crop'] = sprintf(
                    '%d,%d,x%d,y%d',
                    $parameters[0],
                    $parameters[1],
                    $parameters[2],
                    $parameters[3],
                );
                break;
            case BorderFilterLoader::IDENTIFIER:
                $configuration['pad'] = sprintf(
                    '%d,%d',
                    $parameters[0],
                    $parameters[1]
                );
                $configuration['bg-color'] = ltrim(
                    $parameters[2] ?? BorderFilterLoader::DEFAULT_BORDER_COLOR,
                    '#'
                );
                break;
            case GrayscaleFilterLoader::IDENTIFIER:
                $configuration['saturation'] = -100;
                break;
            default:
                throw new InvalidArgumentException(
                    'filter',
                    sprintf(
                        'Filter "%s" is not compatible with Fastly Image Optimizer.',
                        $filter
                    )
                );
        }

        return $configuration;
    }
}
