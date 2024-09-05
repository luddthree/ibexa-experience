<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Connector\Dam\View;

use Ibexa\Core\MVC\Symfony\View\BaseView;

final class ForwardView extends BaseView
{
    /** @var string */
    private $forwardTo;

    public function getForwardTo(): string
    {
        return $this->forwardTo;
    }

    public function setForwardTo(string $forwardTo): void
    {
        $this->forwardTo = $forwardTo;
    }
}

class_alias(ForwardView::class, 'Ibexa\Platform\Connector\Dam\View\ForwardView');
