<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Connect\Controller\REST;

use Ibexa\Bundle\Connect\REST\Value\TemplateList;
use Ibexa\Connect\PageBuilder\TemplateRegistry;
use Ibexa\Rest\Server\Controller;
use Traversable;

final class TemplateController extends Controller
{
    private TemplateRegistry $registry;

    public function __construct(TemplateRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function listView(): TemplateList
    {
        $templates = $this->registry->getTemplates();

        if ($templates instanceof Traversable) {
            $templates = iterator_to_array($templates);
        }

        return new TemplateList($templates);
    }
}
