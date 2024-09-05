<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Fastly\DependencyInjection\Configuration\Parser;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\AbstractParser;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

final class FastlyVariationsParser extends AbstractParser
{
    public function addSemanticConfig(NodeBuilder $nodeBuilder): void
    {
        $nodeBuilder
            ->arrayNode('fastly_variations')
                ->info('Configuration for your Fastly IO image variations')
                ->defaultValue([])
                ->example([
                    'my_image_variation' => [
                        'reference' => '~',
                        'configuration' => [
                            'width' => 300,
                            'height' => 400,
                        ],
                    ],
                    'my_cropped_variation' => [
                        'reference' => 'some_reference_variation',
                        'configuration' => [
                            'crop' => '300,300,x0,y0',
                        ],
                    ],
                ])
                ->useAttributeAsKey('variation_name')
                ->normalizeKeys(false)
                ->arrayPrototype()
                    ->children()
                        ->arrayNode('mime_types')
                            ->scalarPrototype()->end()
                            ->info('Whitelist these mime type resources only (fallback to reference handler).')
                            ->example(['image/gif', 'image/jpeg'])
                            ->defaultValue([])
                        ->end()
                        ->scalarNode('reference')
                            ->info('Tells the system which original variation to use as reference image.')
                            ->example('large')
                        ->end()
                        ->arrayNode('configuration')
                            ->info('Fastly IO variation configuration, refer to Fastly Image Optimizer developer documentation.')
                            ->normalizeKeys(false)
                            ->children()
                                ->scalarNode('auto')
                                    ->info('Enables optimizations based on content negotiation.')
                                    ->example('webp')
                                ->end()
                                ->scalarNode('bg-color')
                                    ->info('Sets the background color of the image.')
                                    ->example('c5c5c5')
                                ->end()
                                ->scalarNode('blur')
                                    ->info('Applies a Gaussian blur filter to the image.')
                                    ->example('50')
                                ->end()
                                ->scalarNode('brightness')
                                    ->info('The brightness parameter increases or decreases the amount of perceived light an image radiates or reflects.')
                                    ->example('50')
                                ->end()
                                ->scalarNode('canvas')
                                    ->info('Change the size of the image canvas.')
                                    ->example('300,200,x50,y25')
                                ->end()
                                ->scalarNode('contrast')
                                    ->info('Increases or decreases the difference between the darkest and lightest tones in an image.')
                                    ->example('25')
                                ->end()
                                ->scalarNode('crop')
                                    ->info('Removes pixels from an image.')
                                    ->example('1000,500,x400,y50')
                                ->end()
                                ->scalarNode('dpr')
                                    ->info('Device pixel ratio.')
                                    ->example('2')
                                ->end()
                                ->scalarNode('enable')
                                    ->info('Enables features that are disabled by default.')
                                    ->example('upscale')
                                ->end()
                                ->scalarNode('fit')
                                    ->info('Controls how the image will be constrained within the provided size.')
                                    ->example('cover')
                                ->end()
                                ->scalarNode('format')
                                    ->info('Specifies the desired output encoding for the image.')
                                    ->example('jpg')
                                ->end()
                                ->scalarNode('frame')
                                    ->info('Extracts the first frame from an animated image sequence.')
                                    ->example('1')
                                ->end()
                                ->scalarNode('height')
                                    ->info('The desired height of the output image.')
                                    ->example('300')
                                ->end()
                                ->scalarNode('level')
                                    ->info('Specifies a set of constraints indicating a degree of required decoder performance for a profile.')
                                    ->example('5.2')
                                ->end()
                                ->scalarNode('optimize')
                                    ->info('Applies optimal quality compression to produce an output image with as much visual fidelity as possible.')
                                    ->example('medium')
                                ->end()
                                ->scalarNode('orient')
                                    ->info('How the image will be orientated.')
                                    ->example('r')
                                ->end()
                                ->scalarNode('pad')
                                    ->info('Add pixels to the edge of an image.')
                                    ->example('25,50,75,100')
                                ->end()
                                ->scalarNode('precrop')
                                    ->info('Removes pixels from an image before any other transformations occur.')
                                    ->example('1000,500,x400,y50')
                                ->end()
                                ->scalarNode('profile')
                                    ->info('Controls which features the video encoder can use based on a target class of application for decoding the specific video bitstream.')
                                    ->example('high')
                                ->end()
                                ->scalarNode('quality')
                                    ->info('Output image quality for lossy file formats.')
                                    ->example('85')
                                ->end()
                                ->scalarNode('resize-filter')
                                    ->info('enables control over the resizing filter used to generate a new image with a higher or lower number of pixels.')
                                    ->example('bilinear')
                                ->end()
                                ->scalarNode('saturation')
                                    ->info('Increases or decreases the intensity of the colors in an image.')
                                    ->example('25')
                                ->end()
                                ->scalarNode('sharpen')
                                    ->info('Increases the definition of the edges of objects in an image.')
                                    ->example('a5,r2,t0')
                                ->end()
                                ->scalarNode('trim')
                                    ->info('Removes pixels from the edge of an image.')
                                    ->example('25,50,75,100')
                                ->end()
                                ->scalarNode('trim-color')
                                    ->info('Identify a rectangular border based on specified or auto-detected color and remove this border from the edges of an image.')
                                    ->example('ffffff,t0.3')
                                ->end()
                                ->scalarNode('width')
                                    ->info('The desired width of the output image.')
                                    ->example('200')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    public function preMap(array $config, ContextualizerInterface $contextualizer)
    {
        $contextualizer->mapConfigArray('fastly_variations', $config);
    }

    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer)
    {
        // Handled by mapConfigArray in preMap
    }
}
