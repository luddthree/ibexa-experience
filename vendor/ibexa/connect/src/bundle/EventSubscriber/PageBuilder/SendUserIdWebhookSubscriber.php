<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Connect\EventSubscriber\PageBuilder;

use Ibexa\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeFactoryInterface;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

final class SendUserIdWebhookSubscriber extends AbstractEnabledQueryParameterWebhookSubscriber implements TranslationContainerInterface
{
    private Security $security;

    private TranslatorInterface $translator;

    public function __construct(
        BlockAttributeFactoryInterface $blockAttributeFactory,
        Security $security,
        TranslatorInterface $translator
    ) {
        parent::__construct($blockAttributeFactory);
        $this->security = $security;
        $this->translator = $translator;
    }

    public static function getTranslationMessages(): array
    {
        return [
            (new Message('page_builder.send_user_id', 'ibexa_connect'))
                ->setDesc('Send User ID'),
        ];
    }

    protected function resolveQueryParameter(): ?string
    {
        $user = $this->security->getUser();
        if ($user === null) {
            return null;
        }

        return $user->getUsername();
    }

    protected function getAttributeIdentifier(): string
    {
        return 'send_user_id';
    }

    protected function getQueryParameterName(): string
    {
        return 'user_id';
    }

    protected function getAttributeName(): string
    {
        return $this->translator->trans('page_builder.send_user_id', [], 'ibexa_connect');
    }
}
