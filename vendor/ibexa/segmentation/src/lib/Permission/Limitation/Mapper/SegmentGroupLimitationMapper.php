<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\Permission\Limitation\Mapper;

use Ibexa\AdminUi\Limitation\LimitationFormMapperInterface;
use Ibexa\AdminUi\Limitation\LimitationValueMapperInterface;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation;
use Ibexa\Core\Limitation\LimitationIdentifierToLabelConverter;
use Ibexa\Segmentation\Exception\Persistence\SegmentGroupNotFoundException;
use Ibexa\Segmentation\Form\Type\SegmentGroupChoiceType;
use Ibexa\Segmentation\Persistence\Handler\HandlerInterface;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Form\FormInterface;

final class SegmentGroupLimitationMapper implements LimitationValueMapperInterface, LimitationFormMapperInterface, TranslationContainerInterface
{
    /** @var \Ibexa\Segmentation\Persistence\Handler\HandlerInterface */
    private $segmentationHandler;

    /** @var string */
    private $template;

    public function __construct(HandlerInterface $segmentationHandler)
    {
        $this->segmentationHandler = $segmentationHandler;
    }

    public function mapLimitationForm(FormInterface $form, Limitation $data): void
    {
        $form->add(
            'limitationValues',
            SegmentGroupChoiceType::class,
            ['label' => $data->getIdentifier()]
        );
    }

    /**
     * @param string $template
     */
    public function setFormTemplate(string $template): void
    {
        $this->template = $template;
    }

    public function getFormTemplate(): string
    {
        return $this->template;
    }

    public function filterLimitationValues(Limitation $limitation): void
    {
    }

    public function mapLimitationValue(Limitation $limitation): array
    {
        $segmentGroups = [];
        foreach ($limitation->limitationValues as $segmentGroupId) {
            try {
                $segmentGroups[] = $this->segmentationHandler->loadSegmentGroupById((int) $segmentGroupId);
            } catch (SegmentGroupNotFoundException $exception) {
                continue;
            }
        }

        return $segmentGroups;
    }

    public static function getTranslationMessages(): array
    {
        return [
            Message::create(
                LimitationIdentifierToLabelConverter::convert('segmentgroup'),
                'ibexa_content_forms_policies'
            )->setDesc('Segment Group'),
        ];
    }
}

class_alias(SegmentGroupLimitationMapper::class, 'Ibexa\Platform\Segmentation\Permission\Limitation\Mapper\SegmentGroupLimitationMapper');
