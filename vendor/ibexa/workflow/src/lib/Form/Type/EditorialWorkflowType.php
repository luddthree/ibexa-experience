<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;

class EditorialWorkflowType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class);
        $builder->add('transition', TextType::class);
        $builder->add('comment', TextareaType::class);
        $builder->add('reviewer', IntegerType::class);
        $builder->add('apply', SubmitType::class, [
            'attr' => [
                'formnovalidate' => 'formnovalidate',
            ],
        ]);

        $builder->addEventListener(FormEvents::POST_SUBMIT, static function (PostSubmitEvent $event): void {
            $form = $event->getForm();

            if ($form->get('apply')->isClicked()) {
                $event->stopPropagation();
            }
        }, 900);
    }
}

class_alias(EditorialWorkflowType::class, 'EzSystems\EzPlatformWorkflow\Form\Type\EditorialWorkflowType');
