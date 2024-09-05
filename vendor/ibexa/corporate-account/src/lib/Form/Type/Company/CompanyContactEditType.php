<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Form\Type\Company;

use Ibexa\CorporateAccount\Form\Type\CorporateContentEditType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class CompanyContactEditType extends CorporateContentEditType
{
    public function getParent(): string
    {
        return CompanyEditType::class;
    }

    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder->addEventListener(FormEvents::POST_SET_DATA, static function (FormEvent $event) {
            $fields = $event->getForm()->get('fieldsData');

            /**
             * @var string $identifier
             * @var \Ibexa\Contracts\ContentForms\Data\Content\FieldData $fieldData
             */
            foreach ($fields->getData() as $identifier => $fieldData) {
                if ($fieldData->fieldDefinition->identifier !== 'contact') {
                    $fields->remove($identifier);
                }
            }
        });
    }
}
