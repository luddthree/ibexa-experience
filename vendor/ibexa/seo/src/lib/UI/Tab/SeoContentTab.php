<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Seo\UI\Tab;

use Ibexa\Contracts\AdminUi\Tab\AbstractEventDispatchingTab;
use Ibexa\Contracts\AdminUi\Tab\ConditionalTabInterface;
use Ibexa\Contracts\AdminUi\Tab\OrderedTabInterface;
use Ibexa\Seo\Content\SeoFieldResolverInterface;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

final class SeoContentTab extends AbstractEventDispatchingTab implements OrderedTabInterface, ConditionalTabInterface
{
    private SeoFieldResolverInterface $seoFieldResolver;

    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        EventDispatcherInterface $eventDispatcher,
        SeoFieldResolverInterface $seoFieldResolver
    ) {
        parent::__construct($twig, $translator, $eventDispatcher);

        $this->seoFieldResolver = $seoFieldResolver;
    }

    public function getIdentifier(): string
    {
        return 'seo-content';
    }

    public function getName(): string
    {
        /** @Desc("SEO") */
        return $this->translator->trans('tab.name.seo_content', [], 'ibexa_seo_content_view');
    }

    public function getOrder(): int
    {
        return 1000;
    }

    public function getTemplate(): string
    {
        return '@ibexadesign/ibexa/seo/tab/seo.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateParameters(array $contextParameters = []): array
    {
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $content */
        $content = $contextParameters['content'];
        $field = $this->seoFieldResolver->getSeoField($content);

        return [
            'content' => $content,
            'field' => $field,
        ];
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function evaluate(array $parameters): bool
    {
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $content */
        $content = $parameters['content'];

        return null !== $this->seoFieldResolver->getSeoField($content);
    }
}
