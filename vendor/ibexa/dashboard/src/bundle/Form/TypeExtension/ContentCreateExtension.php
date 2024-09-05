<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Dashboard\Form\TypeExtension;

use Ibexa\AdminUi\Form\Type\ChoiceList\Loader\ContentCreateContentTypeChoiceLoader;
use Ibexa\AdminUi\Form\Type\Content\Draft\ContentCreateType;
use Ibexa\AdminUi\Form\Type\ContentType\ContentTypeChoiceType;
use Ibexa\Contracts\Core\Persistence\Content\Location\Handler;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Dashboard\Specification\IsInDashboardTreeRoot;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;

/**
 * @internal
 */
final class ContentCreateExtension extends AbstractTypeExtension
{
    private ContentCreateContentTypeChoiceLoader $contentCreateContentTypeChoiceLoader;

    private LocationService $locationService;

    private ConfigResolverInterface $configResolver;

    private Handler $locationHandler;

    public function __construct(
        ContentCreateContentTypeChoiceLoader $contentCreateContentTypeChoiceLoader,
        LocationService $locationService,
        ConfigResolverInterface $configResolver,
        Handler $locationHandler
    ) {
        $this->contentCreateContentTypeChoiceLoader = $contentCreateContentTypeChoiceLoader;
        $this->locationService = $locationService;
        $this->configResolver = $configResolver;
        $this->locationHandler = $locationHandler;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (PreSubmitEvent $event): void {
                $location = $this->locationService->loadLocation(
                    (int)$event->getData()['parent_location']
                );

                if (!$this->isInDashboardTreeRoot($location)) {
                    return;
                }

                $form = $event->getForm();
                $opts = $form->get('content_type')->getConfig()->getOptions();

                $opts['choice_loader'] = $this->contentCreateContentTypeChoiceLoader->setTargetLocation($location);

                $form->remove('content_type');
                $form->add('content_type', ContentTypeChoiceType::class, $opts);
            },
        );
    }

    public static function getExtendedTypes(): iterable
    {
        return [ContentCreateType::class];
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     */
    private function isInDashboardTreeRoot(Location $location): bool
    {
        return (new IsInDashboardTreeRoot(
            $this->configResolver,
            $this->locationHandler
        ))->isSatisfiedBy($location);
    }
}
