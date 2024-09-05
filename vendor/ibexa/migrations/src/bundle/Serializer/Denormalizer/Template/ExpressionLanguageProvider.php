<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Denormalizer\Template;

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Symfony\Contracts\Service\ServiceProviderInterface;

final class ExpressionLanguageProvider implements ExpressionFunctionProviderInterface
{
    public const CONTEXT = 'context';
    public const _FUNCTIONS = '_functions';

    private ServiceProviderInterface $functions;

    public function __construct(ServiceProviderInterface $functions)
    {
        $this->functions = $functions;
    }

    public function getFunctions(): array
    {
        $functions = [];

        foreach ($this->functions->getProvidedServices() as $function => $type) {
            $functions[] = new ExpressionFunction(
                $function,
                static function (...$args) use ($function) {
                    return sprintf(
                        '($%s[\'%s\']->get(%s)(%s))',
                        self::CONTEXT,
                        self::_FUNCTIONS,
                        var_export($function, true),
                        implode(', ', $args),
                    );
                },
                static function ($values, ...$args) use ($function) {
                    $context = $values[self::CONTEXT];

                    return $context[self::_FUNCTIONS]->get($function)(...$args);
                }
            );
        }

        return $functions;
    }

    public function get(string $function): callable
    {
        return $this->functions->get($function);
    }
}
