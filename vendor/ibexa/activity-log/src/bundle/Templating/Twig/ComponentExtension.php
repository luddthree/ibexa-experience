<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ActivityLog\Templating\Twig;

use Ibexa\ActivityLog\ObjectClassToShortNameMapper;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActivityLogGroupInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActivityLogInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Twig\Environment;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ComponentExtension extends AbstractExtension implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private ObjectClassToShortNameMapper $classToShortNameMapper;

    public function __construct(
        ObjectClassToShortNameMapper $classToShortNameMapper,
        ?LoggerInterface $logger = null
    ) {
        $this->classToShortNameMapper = $classToShortNameMapper;
        $this->logger = $logger ?? new NullLogger();
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'ibexa_render_activity_log',
                [$this, 'render'],
                [
                    'is_safe' => ['html'],
                    'needs_environment' => true,
                ],
            ),
            new TwigFunction(
                'ibexa_render_activity_log_group',
                [$this, 'renderGroup'],
                [
                    'is_safe' => ['html'],
                    'needs_environment' => true,
                ],
            ),
        ];
    }

    /**
     * @phpstan-param \Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActivityLogInterface<object> $log
     *
     * @param array<mixed> $parameters
     *
     * @throws \Twig\Error\SyntaxError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\LoaderError
     */
    public function render(Environment $twig, ActivityLogInterface $log, array $parameters = []): string
    {
        $objectClassName = $log->getObjectClass();
        $shortName = $this->classToShortNameMapper->getShortNameForObjectClass($objectClassName);
        $context = array_merge(
            [
                'log' => $log,
            ],
            $parameters,
        );

        try {
            $template = $twig->resolveTemplate([
                sprintf('@ibexadesign/activity_log/ui/%s/%s.html.twig', $shortName, $log->getAction()),
                sprintf('@ibexadesign/activity_log/ui/%s.html.twig', $shortName),
                '@ibexadesign/activity_log/ui/default.html.twig',
            ]);

            return $template->render($context);
        } catch (RuntimeError|SyntaxError $e) {
            $this->logger->error($e->getMessage(), [
                'exception' => $e,
            ]);

            return $twig->render('@ibexadesign/activity_log/ui/default.html.twig', $context);
        }
    }

    /**
     * @param array<mixed> $parameters
     *
     * @throws \Twig\Error\SyntaxError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\LoaderError
     */
    public function renderGroup(Environment $twig, ActivityLogGroupInterface $group, array $parameters = []): string
    {
        $context = array_merge(
            [
                'group' => $group,
            ],
            $parameters,
        );

        $templates = [
            '@ibexadesign/activity_log/ui/group.html.twig',
        ];

        if ($group->getSource() !== null) {
            array_unshift(
                $templates,
                sprintf('@ibexadesign/activity_log/ui/%s/group.html.twig', $group->getSource()->getName()),
            );
        }

        $template = $twig->resolveTemplate($templates);

        return $template->render($context);
    }
}
