<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Dashboard\EventSubscriber;

use Ibexa\Contracts\Core\Repository\Events\Trash\BeforeTrashEvent;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\Base\Exceptions\ForbiddenException;
use Ibexa\Dashboard\Specification\IsDefaultDashboard;
use JMS\TranslationBundle\Annotation\Desc;
use JMS\TranslationBundle\Annotation\Ignore;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class DeleteDashboardSubscriber implements EventSubscriberInterface
{
    private ConfigResolverInterface $configResolver;

    private TranslatorInterface $translator;

    public function __construct(
        ConfigResolverInterface $configResolver,
        TranslatorInterface $translator
    ) {
        $this->configResolver = $configResolver;
        $this->translator = $translator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeTrashEvent::class => ['onBeforeTrashEvent'],
        ];
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ForbiddenException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function onBeforeTrashEvent(BeforeTrashEvent $event): void
    {
        if (!(new IsDefaultDashboard($this->configResolver))->isSatisfiedBy($event->getLocation())) {
            return;
        }

        $message = $this->translator->trans(
            /** @Desc("The default dashboard cannot be sent to the trash") */
            'dashboard.sent_to_trash.info',
            [],
            'ibexa_dashboard'
        );

        throw new ForbiddenException(
            /** @Ignore */
            $message
        );
    }
}
