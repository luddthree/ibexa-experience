<?php

namespace Overblog\GraphQLBundle\__DEFINITIONS__;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Overblog\GraphQLBundle\Definition\ConfigProcessor;
use Overblog\GraphQLBundle\Definition\GraphQLServices;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Type\GeneratedTypeInterface;

/**
 * THIS FILE WAS GENERATED AND SHOULD NOT BE EDITED MANUALLY.
 */
final class PlatformMutationType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'PlatformMutation';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'fields' => fn() => [
                'deleteContent' => [
                    'type' => fn() => $services->getType('DeleteContentPayload'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->mutation("DeleteDomainContent", $args);
                    },
                    'args' => [
                        [
                            'name' => 'id',
                            'type' => Type::id(),
                            'description' => 'Global ID of the content item to delete',
                        ],
                        [
                            'name' => 'contentId',
                            'type' => Type::int(),
                            'description' => 'ID of the content item to delete',
                        ],
                    ],
                ],
                'uploadFiles' => [
                    'type' => fn() => $services->getType('UploadedFilesPayload'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->mutation("UploadFiles", $args);
                    },
                    'args' => [
                        [
                            'name' => 'locationId',
                            'type' => Type::nonNull(Type::int()),
                            'description' => 'The location ID of a container to upload the files to',
                        ],
                        [
                            'name' => 'files',
                            'type' => fn() => Type::nonNull(Type::listOf($services->getType('FileUpload'))),
                        ],
                        [
                            'name' => 'language',
                            'type' => fn() => Type::nonNull($services->getType('RepositoryLanguage')),
                            'description' => 'The language the content items must be created in',
                        ],
                    ],
                ],
                'createToken' => [
                    'type' => fn() => $services->getType('CreatedTokenPayload'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->mutation("CreateToken", $args);
                    },
                    'args' => [
                        [
                            'name' => 'username',
                            'type' => Type::nonNull(Type::string()),
                        ],
                        [
                            'name' => 'password',
                            'type' => Type::nonNull(Type::string()),
                        ],
                    ],
                ],
            ],
        ];
        
        parent::__construct($configProcessor->process($config));
    }
    
    /**
     * {@inheritdoc}
     */
    public static function getAliases(): array
    {
        return [self::NAME];
    }
}