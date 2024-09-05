<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Security\Limitation\Mapper;

use Ibexa\AdminUi\Limitation\LimitationValueMapperInterface;
use Ibexa\AdminUi\Limitation\Mapper\MultipleSelectionBasedMapper;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation;
use Ibexa\Core\Limitation\LimitationIdentifierToLabelConverter;
use JMS\TranslationBundle\Annotation\Desc;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class VersionLockLimitationMapper extends MultipleSelectionBasedMapper implements LimitationValueMapperInterface, TranslationContainerInterface
{
    private const ASSIGNED_ONLY = 1;

    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return array<int, string>
     */
    protected function getSelectionChoices(): array
    {
        return [
            self::ASSIGNED_ONLY => $this->translator->trans(/** @Desc("Assigned only") */
                'policy.limitation.versionlock.assigned_only',
                [],
                'ibexa_workflow'
            ),
        ];
    }

    /**
     * @return string[]
     */
    public function mapLimitationValue(Limitation $limitation): array
    {
        return [
            $this->translator->trans(/** @Desc("Assigned only") */
                'policy.limitation.versionlock.assigned_only',
                [],
                'ibexa_workflow'
            ),
        ];
    }

    /**
     * @return \JMS\TranslationBundle\Model\Message[]
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message(LimitationIdentifierToLabelConverter::convert('versionlock'), 'ibexa_content_forms_policies'))->setDesc('Version Lock'),
        ];
    }
}
