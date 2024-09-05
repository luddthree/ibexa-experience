<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value;

use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\ValueObjectVisitor;
use Ibexa\Contracts\Rest\Output\Visitor;
use Ibexa\Personalization\Exception\ResponseClassNotImplementedException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * ContentDataVisitor converter for REST output.
 */
final class ContentDataVisitor extends ValueObjectVisitor
{
    /** @var array<\Ibexa\Personalization\Response\ResponseInterface> */
    private array $responseRenderers = [];

    /**
     * @param array<\Ibexa\Personalization\Response\ResponseInterface> $responseRenderers
     */
    public function setResponseRenderers(array $responseRenderers): void
    {
        $this->responseRenderers = $responseRenderers;
    }

    /**
     * @param \Ibexa\Personalization\Value\ContentData $data
     *
     * @throws \Ibexa\Personalization\Exception\ResponseClassNotImplementedException
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'responseType' => 'http',
        ]);

        $options = $resolver->resolve($data->getOptions());
        $itemList = $data->getItemList();
        $visitor->setHeader('Content-Type', $generator->getMediaType('ContentList'));
        if (0 === $itemList->count()) {
            $visitor->setStatus(204);

            return;
        }

        if (!isset($this->responseRenderers[$options['responseType']])) {
            throw new ResponseClassNotImplementedException(sprintf('Renderer for %s response not implemented.', $options['responseType']));
        }

        $this->responseRenderers[$options['responseType']]->render($generator, $itemList);
    }
}

class_alias(ContentDataVisitor::class, 'EzSystems\EzRecommendationClient\Value\ContentDataVisitor');
