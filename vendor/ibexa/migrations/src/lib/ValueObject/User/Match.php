<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\User;

class_exists(Matcher::class);

class_alias(Matcher::class, 'Ibexa\Platform\Migration\ValueObject\User\Match');
