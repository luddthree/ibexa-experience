<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Form;

use Ibexa\ContentForms\Form\Type\Content\ContentEditType;
use Ibexa\Workflow\Form\Type\EditorialWorkflowType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;

/**
 * Extends Content Edit form with additional fields.
 */
class ContentEditTypeExtension extends AbstractTypeExtension
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('workflow', EditorialWorkflowType::class, [
            'mapped' => false,
            'attr' => [
                'formnovalidate' => 'formnovalidate',
            ],
        ]);

        $builder->addEventListener(FormEvents::POST_SUBMIT, static function (PostSubmitEvent $event): void {
            $form = $event->getForm();

            if ($form->get('workflow')->get('apply')->isClicked()) {
                $event->stopPropagation();
            }
        }, 900);
    }

    public static function getExtendedTypes(): iterable
    {
        return [ContentEditType::class];
    }
}

class_alias(ContentEditTypeExtension::class, 'EzSystems\EzPlatformWorkflow\Form\ContentEditTypeExtension');
