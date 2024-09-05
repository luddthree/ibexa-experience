<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Form\Type\Extension\EventSubscriber;

use Ibexa\ContentForms\Data\Content\ContentCreateData;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Taxonomy\Service\TaxonomyConfiguration;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @internal
 */
final class TaxonomyParentSubscriber implements EventSubscriberInterface
{
    private TaxonomyConfiguration $taxonomyConfiguration;

    private TaxonomyServiceInterface $taxonomyService;

    private RequestStack $requestStack;

    public function __construct(
        TaxonomyConfiguration $taxonomyConfiguration,
        TaxonomyServiceInterface $taxonomyService,
        RequestStack $requestStack
    ) {
        $this->taxonomyConfiguration = $taxonomyConfiguration;
        $this->taxonomyService = $taxonomyService;
        $this->requestStack = $requestStack;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'onPreSetData',
        ];
    }

    /**
     * Injects Taxonomy Entry to Parent field using Request query param.
     */
    public function onPreSetData(FormEvent $event): void
    {
        $data = $event->getData();

        if (!$data instanceof ContentCreateData) {
            return;
        }

        $taxonomy = $this->taxonomyConfiguration->getTaxonomyForContentType($data->contentType);
        $fieldMappings = $this->taxonomyConfiguration->getFieldMappings($taxonomy);

        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            return;
        }

        $taxonomyParentId = $request->query->getInt('taxonomyParent');
        if (0 === $taxonomyParentId) {
            return;
        }

        $taxonomyParent = $this->taxonomyService->loadEntryById($taxonomyParentId);

        /** @var \Ibexa\Contracts\ContentForms\Data\Content\FieldData[] $fieldsData */
        $fieldsData = $data->fieldsData;

        /** @var \Ibexa\Taxonomy\FieldType\TaxonomyEntry\Value $value */
        $value = $fieldsData[$fieldMappings['parent']]->value;
        $value->setTaxonomyEntry($taxonomyParent);
    }
}
