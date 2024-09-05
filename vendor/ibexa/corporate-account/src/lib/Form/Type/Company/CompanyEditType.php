<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Form\Type\Company;

use Ibexa\ContentForms\Form\Type\Content\ContentEditType;
use Ibexa\CorporateAccount\Form\Type\CorporateContentEditType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class CompanyEditType extends CorporateContentEditType
{
    public function getParent(): string
    {
        return ContentEditType::class;
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
                if ($fieldData->fieldDefinition->fieldGroup === 'internal') {
                    $fields->remove($identifier);
                }
            }
        });
    }
}
