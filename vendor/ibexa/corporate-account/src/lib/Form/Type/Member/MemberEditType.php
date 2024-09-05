<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Form\Type\Member;

use Ibexa\ContentForms\Form\Type\User\UserUpdateType;
use Ibexa\CorporateAccount\Form\Data\Member\MemberUpdateData;
use Ibexa\CorporateAccount\Form\Type\CorporateContentEditType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MemberEditType extends CorporateContentEditType
{
    public function getParent(): string
    {
        return UserUpdateType::class;
    }

    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder->add(
            'role',
            MemberRoleChoiceType::class
        );
        $builder->addEventListener(FormEvents::POST_SET_DATA, static function (FormEvent $event) use ($options) {
            $fields = $event->getForm()->get('fieldsData');

            /**
             * @var string $identifier
             * @var \Ibexa\Contracts\ContentForms\Data\Content\FieldData $fieldData
             */
            foreach ($fields->getData() as $identifier => $fieldData) {
                if ($fieldData->fieldDefinition->identifier === 'user') {
                    $fields->remove($identifier);
                }

                if ($fieldData->fieldDefinition->identifier === 'customer_group' && $options['is_corporate']) {
                    $fields->remove($identifier);
                }
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefined('is_corporate');
        $resolver->setDefault('is_corporate', true);
        $resolver->setDefault('data_class', MemberUpdateData::class);
        $resolver->setAllowedTypes('is_corporate', ['bool']);
    }
}
