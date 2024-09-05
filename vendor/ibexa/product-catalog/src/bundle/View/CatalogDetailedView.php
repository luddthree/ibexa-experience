<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\View;

use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;
use Ibexa\Core\MVC\Symfony\View\BaseView;

final class CatalogDetailedView extends BaseView
{
    private CatalogInterface $catalog;

    public function __construct(
        string $templateIdentifier,
        CatalogInterface $catalog
    ) {
        parent::__construct($templateIdentifier);

        $this->catalog = $catalog;
    }

    public function getCatalog(): CatalogInterface
    {
        return $this->catalog;
    }

    public function setCatalog(CatalogInterface $catalog): void
    {
        $this->catalog = $catalog;
    }

    /**
     * @return array<string,mixed>
     */
    protected function getInternalParameters(): array
    {
        return [
            'catalog' => $this->catalog,
            'is_editable' => true,
        ];
    }
}
