<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type\Catalog;

use Ibexa\Bundle\ProductCatalog\Form\Data\Catalog\CatalogCopyData;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\ProductCatalog\CatalogServiceInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CatalogCopyType extends AbstractType
{
    private const IDENTIFIER_PREFIX = 'copy_';

    private CatalogServiceInterface $catalogService;

    public function __construct(CatalogServiceInterface $catalogService)
    {
        $this->catalogService = $catalogService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('catalog', CatalogReferenceType::class);
        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event): void {
            $data = $event->getData();
            if (!$data->getCatalog()) {
                return;
            }

            $newIdentifier = self::IDENTIFIER_PREFIX . $data->getCatalog()->getIdentifier();

            if ($this->identifierExists($newIdentifier)) {
                $newIdentifier .= date('_Y_m_d_H_i_s');
            }

            $data->setIdentifier($newIdentifier);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CatalogCopyData::class,
            'translation_domain' => 'ibexa_product_catalog',
        ]);
    }

    private function identifierExists(string $identifier): bool
    {
        try {
            $this->catalogService->getCatalogByIdentifier($identifier);

            return true;
        } catch (NotFoundException $e) {
            return false;
        }
    }
}
