<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\OAuth2Client\Twig;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\Node\Expression\ArrayExpression;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Node;
use Twig\TwigFunction;

final class OAuth2Extension extends AbstractExtension
{
    /** @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface */
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'ibexa_oauth2_connect_path',
                [$this, 'getOAuth2ConnectPath'],
                [
                    'is_safe_callback' => [$this, 'isUrlGenerationSafe'],
                ]
            ),
            new TwigFunction(
                'ibexa_oauth2_connect_url',
                [$this, 'getOAuth2ConnectUrl'],
                [
                    'is_safe_callback' => [$this, 'isUrlGenerationSafe'],
                ]
            ),
        ];
    }

    public function getOAuth2ConnectPath(
        string $identifier,
        array $parameters = [],
        bool $relative = false
    ): string {
        $referenceType = $relative ? UrlGeneratorInterface::RELATIVE_PATH : UrlGeneratorInterface::ABSOLUTE_PATH;

        return $this->generate($identifier, $parameters, $referenceType);
    }

    public function getOAuth2ConnectUrl(
        string $identifier,
        array $parameters = [],
        bool $schemeRelative = false
    ): string {
        $referenceType = $schemeRelative ? UrlGeneratorInterface::NETWORK_PATH : UrlGeneratorInterface::ABSOLUTE_URL;

        return $this->generate($identifier, $parameters, $referenceType);
    }

    private function generate(string $identifier, array $parameters, int $referenceType): string
    {
        $parameters += [
            'identifier' => $identifier,
        ];

        return $this->urlGenerator->generate('ibexa.oauth2.connect', $parameters, $referenceType);
    }

    /**
     * Determines at compile time whether the generated URL will be safe and thus
     * saving the unneeded automatic escaping for performance reasons.
     *
     * @see \Symfony\Bridge\Twig\Extension\RoutingExtension::isUrlGenerationSafe
     */
    public function isUrlGenerationSafe(Node $argsNode): array
    {
        // support named arguments
        $paramsNode = $argsNode->hasNode('parameters') ? $argsNode->getNode('parameters') : (
            $argsNode->hasNode('1') ? $argsNode->getNode('1') : null
        );

        if (null === $paramsNode || $paramsNode instanceof ArrayExpression && \count($paramsNode) <= 2 &&
            (!$paramsNode->hasNode('1') || $paramsNode->getNode('1') instanceof ConstantExpression)
        ) {
            return ['html'];
        }

        return [];
    }
}

class_alias(OAuth2Extension::class, 'Ibexa\Platform\Bundle\OAuth2Client\Twig\OAuth2Extension');
