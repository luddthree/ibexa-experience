<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Scheduler\REST\Server\Output\ValueObjectVisitor;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\ValueObjectVisitor;
use Ibexa\Contracts\Rest\Output\Visitor;

class ScheduledVersion extends ValueObjectVisitor
{
    protected $contentTypeService;

    public function __construct(ContentTypeService $contentTypeService)
    {
        $this->contentTypeService = $contentTypeService;
    }

    /**
     * Visit struct returned by controllers.
     *
     * @param \Ibexa\Contracts\Rest\Output\Visitor $visitor
     * @param \Ibexa\Contracts\Rest\Output\Generator $generator
     * @param \Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data)
    {
        $versionInfo = $data->versionInfo;
        $contentInfo = $versionInfo->contentInfo;
        $contentType = $this->contentTypeService->loadContentType($contentInfo->contentTypeId);

        $visitor->setHeader('Content-Type', $generator->getMediaType('ScheduledVersion'));
        $visitor->setHeader('Accept-Patch', false);

        // Scheduled Version
        $generator->startObjectElement('ScheduledVersion');

        $generator->startAttribute('id', $data->id);
        $generator->endAttribute('id');

        $generator->startAttribute(
            'href',
            $this->router->generate(
                'ibexa.scheduler.rest.get_scheduled_version',
                [
                    'contentId' => $contentInfo->id,
                    'versionNumber' => $versionInfo->versionNo,
                ]
            )
        );
        $generator->endAttribute('href');

        $generator->startValueElement('publicationDate', $data->date->getTimestamp());
        $generator->endValueElement('publicationDate');

        // Version Info
        $generator->startHashElement('version');

        $generator->startAttribute(
            'href',
            $this->router->generate(
                'ibexa.rest.load_content_in_version',
                [
                    'contentId' => $contentInfo->id,
                    'versionNumber' => $versionInfo->versionNo,
                ]
            )
        );
        $generator->endAttribute('href');

        $generator->startAttribute('id', $versionInfo->id);
        $generator->endAttribute('id');

        $generator->startValueElement('name', $versionInfo->getName());
        $generator->endValueElement('name');

        $generator->startValueElement('number', $versionInfo->versionNo);
        $generator->endValueElement('number');

        $generator->startValueElement('modificationDate', $versionInfo->modificationDate->format('U'));
        $generator->endValueElement('modificationDate');

        $generator->startValueElement('initialLanguageCode', $versionInfo->initialLanguageCode);
        $generator->endValueElement('initialLanguageCode');

        $generator->endHashElement('version');

        // Content
        $generator->startHashElement('content');

        $generator->startAttribute('id', $contentInfo->id);
        $generator->endAttribute('id');

        $generator->startAttribute(
            'href',
            $this->router->generate(
                'ibexa.rest.load_content',
                [
                    'contentId' => $contentInfo->id,
                ]
            )
        );
        $generator->endAttribute('href');

        $generator->endHashElement('content');

        // Content type
        $generator->startHashElement('contentType');

        $generator->startAttribute(
            'href',
            $this->router->generate(
                'ibexa.rest.load_content_type',
                [
                    'contentTypeId' => $contentType->id,
                ]
            )
        );
        $generator->endAttribute('href');

        $generator->startAttribute('id', $contentType->id);
        $generator->endAttribute('id');

        $generator->startAttribute('urlRoot', $data->urlRoot);
        $generator->endAttribute('urlRoot');

        $generator->startValueElement('name', $contentType->getName($contentType->mainLanguageCode));
        $generator->endValueElement('name');

        $generator->startValueElement('identifier', $contentType->identifier);
        $generator->endValueElement('identifier');

        $generator->endHashElement('contentType');

        // Publisher
        $generator->startHashElement('publisher');

        $generator->startAttribute('id', $data->user->id);
        $generator->endAttribute('id');

        $generator->startAttribute(
            'href',
            $this->router->generate(
                'ibexa.rest.load_user',
                [
                    'userId' => $data->user->id,
                ]
            )
        );
        $generator->endAttribute('href');

        $generator->startValueElement('login', $data->user->login);
        $generator->endValueElement('login');

        $generator->startValueElement('email', $data->user->email);
        $generator->endValueElement('email');

        $generator->startValueElement('firstName', (string)$data->user->getField('first_name')->value);
        $generator->endValueElement('firstName');

        $generator->startValueElement('lastName', (string)$data->user->getField('last_name')->value);
        $generator->endValueElement('lastName');

        $generator->startValueElement('fullName', sprintf(
            '%s %s',
            $data->user->getField('first_name')->value,
            $data->user->getField('last_name')->value
        ));
        $generator->endValueElement('fullName');

        $generator->endHashElement('publisher');

        $generator->endObjectElement('ScheduledVersion');
    }
}

class_alias(ScheduledVersion::class, 'EzSystems\DateBasedPublisher\Core\REST\Server\Output\ValueObjectVisitor\ScheduledVersion');
