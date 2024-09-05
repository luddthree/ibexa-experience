<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Taxonomy\Controller;

use Ibexa\AdminUi\Event\Options;
use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\AdminUi\Event\ContentProxyCreateEvent;
use Ibexa\Taxonomy\Form\Data\TaxonomyEntryCreateData;
use Ibexa\Taxonomy\Form\Type\TaxonomyEntryCreateType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class ContentProxyController extends Controller
{
    private FormFactoryInterface $formFactory;

    private SubmitHandler $submitHandler;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        FormFactoryInterface $formFactory,
        SubmitHandler $submitHandler,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function createProxyAction(
        Request $request,
        string $taxonomyName
    ): Response {
        $form = $this->formFactory->create(
            TaxonomyEntryCreateType::class,
            new TaxonomyEntryCreateData(),
            ['taxonomy' => $taxonomyName],
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (TaxonomyEntryCreateData $data): ?Response {
                $contentType = $data->getContentType();
                $language = $data->getLanguage();
                $parentEntry = $data->getParentEntry();
                $parentLocation = $data->getParentLocation();

                if (
                    null === $language
                    || null === $parentLocation
                    || null === $contentType
                    || null === $parentEntry
                ) {
                    return null;
                }

                $event = $this->eventDispatcher->dispatch(
                    new ContentProxyCreateEvent(
                        $contentType,
                        $language->languageCode,
                        $parentLocation->id,
                        new Options(['parent_entry' => $parentEntry])
                    )
                );

                if ($event->hasResponse()) {
                    return $event->getResponse();
                }

                // Fallback to "nodraft"
                return $this->redirectToRoute('ibexa.content.create_no_draft', [
                    'contentTypeIdentifier' => $contentType->identifier,
                    'language' => $language->languageCode,
                    'parentLocationId' => $parentLocation->id,
                    'taxonomyParent' => $parentEntry->id,
                ]);
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirect($this->generateUrl('ibexa.dashboard'));
    }
}
