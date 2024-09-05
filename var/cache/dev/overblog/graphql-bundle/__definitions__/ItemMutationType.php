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
final class ItemMutationType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'ItemMutation';
    
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
                'createArticle' => [
                    'type' => fn() => Type::nonNull($services->getType('ArticleItem')),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->mutation("CreateDomainContent", $args["input"], "article", $args["parentLocationId"], $args["language"]);
                    },
                    'args' => [
                        [
                            'name' => 'input',
                            'type' => fn() => Type::nonNull($services->getType('ArticleItemCreateInput')),
                        ],
                        [
                            'name' => 'language',
                            'type' => fn() => Type::nonNull($services->getType('RepositoryLanguage')),
                            'description' => 'The language the content should be created/updated in.',
                        ],
                        [
                            'name' => 'parentLocationId',
                            'type' => Type::nonNull(Type::int()),
                        ],
                    ],
                ],
                'updateArticle' => [
                    'type' => fn() => Type::nonNull($services->getType('ArticleItem')),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->mutation("UpdateDomainContent", $args["input"], $args, $args["versionNo"], $args["language"]);
                    },
                    'args' => [
                        [
                            'name' => 'input',
                            'type' => fn() => Type::nonNull($services->getType('ArticleItemUpdateInput')),
                        ],
                        [
                            'name' => 'language',
                            'type' => fn() => Type::nonNull($services->getType('RepositoryLanguage')),
                            'description' => 'The language the content should be created/updated in.',
                        ],
                        [
                            'name' => 'id',
                            'type' => Type::id(),
                            'description' => 'ID of the content item to update',
                        ],
                        [
                            'name' => 'contentId',
                            'type' => Type::int(),
                            'description' => 'Repository content ID of the content item to update',
                        ],
                        [
                            'name' => 'versionNo',
                            'type' => Type::int(),
                            'description' => 'Optional version number to update. If it is a draft, it is saved, not published. If it is archived, it is used as the source version for the update, to complete missing fields.',
                        ],
                    ],
                ],
                'createFolder' => [
                    'type' => fn() => Type::nonNull($services->getType('FolderItem')),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->mutation("CreateDomainContent", $args["input"], "folder", $args["parentLocationId"], $args["language"]);
                    },
                    'args' => [
                        [
                            'name' => 'input',
                            'type' => fn() => Type::nonNull($services->getType('FolderItemCreateInput')),
                        ],
                        [
                            'name' => 'language',
                            'type' => fn() => Type::nonNull($services->getType('RepositoryLanguage')),
                            'description' => 'The language the content should be created/updated in.',
                        ],
                        [
                            'name' => 'parentLocationId',
                            'type' => Type::nonNull(Type::int()),
                        ],
                    ],
                ],
                'updateFolder' => [
                    'type' => fn() => Type::nonNull($services->getType('FolderItem')),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->mutation("UpdateDomainContent", $args["input"], $args, $args["versionNo"], $args["language"]);
                    },
                    'args' => [
                        [
                            'name' => 'input',
                            'type' => fn() => Type::nonNull($services->getType('FolderItemUpdateInput')),
                        ],
                        [
                            'name' => 'language',
                            'type' => fn() => Type::nonNull($services->getType('RepositoryLanguage')),
                            'description' => 'The language the content should be created/updated in.',
                        ],
                        [
                            'name' => 'id',
                            'type' => Type::id(),
                            'description' => 'ID of the content item to update',
                        ],
                        [
                            'name' => 'contentId',
                            'type' => Type::int(),
                            'description' => 'Repository content ID of the content item to update',
                        ],
                        [
                            'name' => 'versionNo',
                            'type' => Type::int(),
                            'description' => 'Optional version number to update. If it is a draft, it is saved, not published. If it is archived, it is used as the source version for the update, to complete missing fields.',
                        ],
                    ],
                ],
                'createForm' => [
                    'type' => fn() => Type::nonNull($services->getType('FormItem')),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->mutation("CreateDomainContent", $args["input"], "form", $args["parentLocationId"], $args["language"]);
                    },
                    'args' => [
                        [
                            'name' => 'input',
                            'type' => fn() => Type::nonNull($services->getType('FormItemCreateInput')),
                        ],
                        [
                            'name' => 'language',
                            'type' => fn() => Type::nonNull($services->getType('RepositoryLanguage')),
                            'description' => 'The language the content should be created/updated in.',
                        ],
                        [
                            'name' => 'parentLocationId',
                            'type' => Type::nonNull(Type::int()),
                        ],
                    ],
                ],
                'updateForm' => [
                    'type' => fn() => Type::nonNull($services->getType('FormItem')),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->mutation("UpdateDomainContent", $args["input"], $args, $args["versionNo"], $args["language"]);
                    },
                    'args' => [
                        [
                            'name' => 'input',
                            'type' => fn() => Type::nonNull($services->getType('FormItemUpdateInput')),
                        ],
                        [
                            'name' => 'language',
                            'type' => fn() => Type::nonNull($services->getType('RepositoryLanguage')),
                            'description' => 'The language the content should be created/updated in.',
                        ],
                        [
                            'name' => 'id',
                            'type' => Type::id(),
                            'description' => 'ID of the content item to update',
                        ],
                        [
                            'name' => 'contentId',
                            'type' => Type::int(),
                            'description' => 'Repository content ID of the content item to update',
                        ],
                        [
                            'name' => 'versionNo',
                            'type' => Type::int(),
                            'description' => 'Optional version number to update. If it is a draft, it is saved, not published. If it is archived, it is used as the source version for the update, to complete missing fields.',
                        ],
                    ],
                ],
                'createTag' => [
                    'type' => fn() => Type::nonNull($services->getType('TagItem')),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->mutation("CreateDomainContent", $args["input"], "tag", $args["parentLocationId"], $args["language"]);
                    },
                    'args' => [
                        [
                            'name' => 'input',
                            'type' => fn() => Type::nonNull($services->getType('TagItemCreateInput')),
                        ],
                        [
                            'name' => 'language',
                            'type' => fn() => Type::nonNull($services->getType('RepositoryLanguage')),
                            'description' => 'The language the content should be created/updated in.',
                        ],
                        [
                            'name' => 'parentLocationId',
                            'type' => Type::nonNull(Type::int()),
                        ],
                    ],
                ],
                'updateTag' => [
                    'type' => fn() => Type::nonNull($services->getType('TagItem')),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->mutation("UpdateDomainContent", $args["input"], $args, $args["versionNo"], $args["language"]);
                    },
                    'args' => [
                        [
                            'name' => 'input',
                            'type' => fn() => Type::nonNull($services->getType('TagItemUpdateInput')),
                        ],
                        [
                            'name' => 'language',
                            'type' => fn() => Type::nonNull($services->getType('RepositoryLanguage')),
                            'description' => 'The language the content should be created/updated in.',
                        ],
                        [
                            'name' => 'id',
                            'type' => Type::id(),
                            'description' => 'ID of the content item to update',
                        ],
                        [
                            'name' => 'contentId',
                            'type' => Type::int(),
                            'description' => 'Repository content ID of the content item to update',
                        ],
                        [
                            'name' => 'versionNo',
                            'type' => Type::int(),
                            'description' => 'Optional version number to update. If it is a draft, it is saved, not published. If it is archived, it is used as the source version for the update, to complete missing fields.',
                        ],
                    ],
                ],
                'createLandingPage' => [
                    'type' => fn() => Type::nonNull($services->getType('LandingPageItem')),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->mutation("CreateDomainContent", $args["input"], "landing_page", $args["parentLocationId"], $args["language"]);
                    },
                    'args' => [
                        [
                            'name' => 'input',
                            'type' => fn() => Type::nonNull($services->getType('LandingPageItemCreateInput')),
                        ],
                        [
                            'name' => 'language',
                            'type' => fn() => Type::nonNull($services->getType('RepositoryLanguage')),
                            'description' => 'The language the content should be created/updated in.',
                        ],
                        [
                            'name' => 'parentLocationId',
                            'type' => Type::nonNull(Type::int()),
                        ],
                    ],
                ],
                'updateLandingPage' => [
                    'type' => fn() => Type::nonNull($services->getType('LandingPageItem')),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->mutation("UpdateDomainContent", $args["input"], $args, $args["versionNo"], $args["language"]);
                    },
                    'args' => [
                        [
                            'name' => 'input',
                            'type' => fn() => Type::nonNull($services->getType('LandingPageItemUpdateInput')),
                        ],
                        [
                            'name' => 'language',
                            'type' => fn() => Type::nonNull($services->getType('RepositoryLanguage')),
                            'description' => 'The language the content should be created/updated in.',
                        ],
                        [
                            'name' => 'id',
                            'type' => Type::id(),
                            'description' => 'ID of the content item to update',
                        ],
                        [
                            'name' => 'contentId',
                            'type' => Type::int(),
                            'description' => 'Repository content ID of the content item to update',
                        ],
                        [
                            'name' => 'versionNo',
                            'type' => Type::int(),
                            'description' => 'Optional version number to update. If it is a draft, it is saved, not published. If it is archived, it is used as the source version for the update, to complete missing fields.',
                        ],
                    ],
                ],
                'createProductCategoryTag' => [
                    'type' => fn() => Type::nonNull($services->getType('ProductCategoryTagItem')),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->mutation("CreateDomainContent", $args["input"], "product_category_tag", $args["parentLocationId"], $args["language"]);
                    },
                    'args' => [
                        [
                            'name' => 'input',
                            'type' => fn() => Type::nonNull($services->getType('ProductCategoryTagItemCreateInput')),
                        ],
                        [
                            'name' => 'language',
                            'type' => fn() => Type::nonNull($services->getType('RepositoryLanguage')),
                            'description' => 'The language the content should be created/updated in.',
                        ],
                        [
                            'name' => 'parentLocationId',
                            'type' => Type::nonNull(Type::int()),
                        ],
                    ],
                ],
                'updateProductCategoryTag' => [
                    'type' => fn() => Type::nonNull($services->getType('ProductCategoryTagItem')),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->mutation("UpdateDomainContent", $args["input"], $args, $args["versionNo"], $args["language"]);
                    },
                    'args' => [
                        [
                            'name' => 'input',
                            'type' => fn() => Type::nonNull($services->getType('ProductCategoryTagItemUpdateInput')),
                        ],
                        [
                            'name' => 'language',
                            'type' => fn() => Type::nonNull($services->getType('RepositoryLanguage')),
                            'description' => 'The language the content should be created/updated in.',
                        ],
                        [
                            'name' => 'id',
                            'type' => Type::id(),
                            'description' => 'ID of the content item to update',
                        ],
                        [
                            'name' => 'contentId',
                            'type' => Type::int(),
                            'description' => 'Repository content ID of the content item to update',
                        ],
                        [
                            'name' => 'versionNo',
                            'type' => Type::int(),
                            'description' => 'Optional version number to update. If it is a draft, it is saved, not published. If it is archived, it is used as the source version for the update, to complete missing fields.',
                        ],
                    ],
                ],
                'createEditor' => [
                    'type' => fn() => Type::nonNull($services->getType('EditorItem')),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->mutation("CreateDomainContent", $args["input"], "editor", $args["parentLocationId"], $args["language"]);
                    },
                    'args' => [
                        [
                            'name' => 'input',
                            'type' => fn() => Type::nonNull($services->getType('EditorItemCreateInput')),
                        ],
                        [
                            'name' => 'language',
                            'type' => fn() => Type::nonNull($services->getType('RepositoryLanguage')),
                            'description' => 'The language the content should be created/updated in.',
                        ],
                        [
                            'name' => 'parentLocationId',
                            'type' => Type::nonNull(Type::int()),
                        ],
                    ],
                ],
                'updateEditor' => [
                    'type' => fn() => Type::nonNull($services->getType('EditorItem')),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->mutation("UpdateDomainContent", $args["input"], $args, $args["versionNo"], $args["language"]);
                    },
                    'args' => [
                        [
                            'name' => 'input',
                            'type' => fn() => Type::nonNull($services->getType('EditorItemUpdateInput')),
                        ],
                        [
                            'name' => 'language',
                            'type' => fn() => Type::nonNull($services->getType('RepositoryLanguage')),
                            'description' => 'The language the content should be created/updated in.',
                        ],
                        [
                            'name' => 'id',
                            'type' => Type::id(),
                            'description' => 'ID of the content item to update',
                        ],
                        [
                            'name' => 'contentId',
                            'type' => Type::int(),
                            'description' => 'Repository content ID of the content item to update',
                        ],
                        [
                            'name' => 'versionNo',
                            'type' => Type::int(),
                            'description' => 'Optional version number to update. If it is a draft, it is saved, not published. If it is archived, it is used as the source version for the update, to complete missing fields.',
                        ],
                    ],
                ],
                'createUser' => [
                    'type' => fn() => Type::nonNull($services->getType('UserItem')),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->mutation("CreateDomainContent", $args["input"], "user", $args["parentLocationId"], $args["language"]);
                    },
                    'args' => [
                        [
                            'name' => 'input',
                            'type' => fn() => Type::nonNull($services->getType('UserItemCreateInput')),
                        ],
                        [
                            'name' => 'language',
                            'type' => fn() => Type::nonNull($services->getType('RepositoryLanguage')),
                            'description' => 'The language the content should be created/updated in.',
                        ],
                        [
                            'name' => 'parentLocationId',
                            'type' => Type::nonNull(Type::int()),
                        ],
                    ],
                ],
                'updateUser' => [
                    'type' => fn() => Type::nonNull($services->getType('UserItem')),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->mutation("UpdateDomainContent", $args["input"], $args, $args["versionNo"], $args["language"]);
                    },
                    'args' => [
                        [
                            'name' => 'input',
                            'type' => fn() => Type::nonNull($services->getType('UserItemUpdateInput')),
                        ],
                        [
                            'name' => 'language',
                            'type' => fn() => Type::nonNull($services->getType('RepositoryLanguage')),
                            'description' => 'The language the content should be created/updated in.',
                        ],
                        [
                            'name' => 'id',
                            'type' => Type::id(),
                            'description' => 'ID of the content item to update',
                        ],
                        [
                            'name' => 'contentId',
                            'type' => Type::int(),
                            'description' => 'Repository content ID of the content item to update',
                        ],
                        [
                            'name' => 'versionNo',
                            'type' => Type::int(),
                            'description' => 'Optional version number to update. If it is a draft, it is saved, not published. If it is archived, it is used as the source version for the update, to complete missing fields.',
                        ],
                    ],
                ],
                'createUserGroup' => [
                    'type' => fn() => Type::nonNull($services->getType('UserGroupItem')),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->mutation("CreateDomainContent", $args["input"], "user_group", $args["parentLocationId"], $args["language"]);
                    },
                    'args' => [
                        [
                            'name' => 'input',
                            'type' => fn() => Type::nonNull($services->getType('UserGroupItemCreateInput')),
                        ],
                        [
                            'name' => 'language',
                            'type' => fn() => Type::nonNull($services->getType('RepositoryLanguage')),
                            'description' => 'The language the content should be created/updated in.',
                        ],
                        [
                            'name' => 'parentLocationId',
                            'type' => Type::nonNull(Type::int()),
                        ],
                    ],
                ],
                'updateUserGroup' => [
                    'type' => fn() => Type::nonNull($services->getType('UserGroupItem')),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->mutation("UpdateDomainContent", $args["input"], $args, $args["versionNo"], $args["language"]);
                    },
                    'args' => [
                        [
                            'name' => 'input',
                            'type' => fn() => Type::nonNull($services->getType('UserGroupItemUpdateInput')),
                        ],
                        [
                            'name' => 'language',
                            'type' => fn() => Type::nonNull($services->getType('RepositoryLanguage')),
                            'description' => 'The language the content should be created/updated in.',
                        ],
                        [
                            'name' => 'id',
                            'type' => Type::id(),
                            'description' => 'ID of the content item to update',
                        ],
                        [
                            'name' => 'contentId',
                            'type' => Type::int(),
                            'description' => 'Repository content ID of the content item to update',
                        ],
                        [
                            'name' => 'versionNo',
                            'type' => Type::int(),
                            'description' => 'Optional version number to update. If it is a draft, it is saved, not published. If it is archived, it is used as the source version for the update, to complete missing fields.',
                        ],
                    ],
                ],
                'createFile' => [
                    'type' => fn() => Type::nonNull($services->getType('FileItem')),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->mutation("CreateDomainContent", $args["input"], "file", $args["parentLocationId"], $args["language"]);
                    },
                    'args' => [
                        [
                            'name' => 'input',
                            'type' => fn() => Type::nonNull($services->getType('FileItemCreateInput')),
                        ],
                        [
                            'name' => 'language',
                            'type' => fn() => Type::nonNull($services->getType('RepositoryLanguage')),
                            'description' => 'The language the content should be created/updated in.',
                        ],
                        [
                            'name' => 'parentLocationId',
                            'type' => Type::nonNull(Type::int()),
                        ],
                    ],
                ],
                'updateFile' => [
                    'type' => fn() => Type::nonNull($services->getType('FileItem')),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->mutation("UpdateDomainContent", $args["input"], $args, $args["versionNo"], $args["language"]);
                    },
                    'args' => [
                        [
                            'name' => 'input',
                            'type' => fn() => Type::nonNull($services->getType('FileItemUpdateInput')),
                        ],
                        [
                            'name' => 'language',
                            'type' => fn() => Type::nonNull($services->getType('RepositoryLanguage')),
                            'description' => 'The language the content should be created/updated in.',
                        ],
                        [
                            'name' => 'id',
                            'type' => Type::id(),
                            'description' => 'ID of the content item to update',
                        ],
                        [
                            'name' => 'contentId',
                            'type' => Type::int(),
                            'description' => 'Repository content ID of the content item to update',
                        ],
                        [
                            'name' => 'versionNo',
                            'type' => Type::int(),
                            'description' => 'Optional version number to update. If it is a draft, it is saved, not published. If it is archived, it is used as the source version for the update, to complete missing fields.',
                        ],
                    ],
                ],
                'createImage' => [
                    'type' => fn() => Type::nonNull($services->getType('ImageItem')),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->mutation("CreateDomainContent", $args["input"], "image", $args["parentLocationId"], $args["language"]);
                    },
                    'args' => [
                        [
                            'name' => 'input',
                            'type' => fn() => Type::nonNull($services->getType('ImageItemCreateInput')),
                        ],
                        [
                            'name' => 'language',
                            'type' => fn() => Type::nonNull($services->getType('RepositoryLanguage')),
                            'description' => 'The language the content should be created/updated in.',
                        ],
                        [
                            'name' => 'parentLocationId',
                            'type' => Type::nonNull(Type::int()),
                        ],
                    ],
                ],
                'updateImage' => [
                    'type' => fn() => Type::nonNull($services->getType('ImageItem')),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->mutation("UpdateDomainContent", $args["input"], $args, $args["versionNo"], $args["language"]);
                    },
                    'args' => [
                        [
                            'name' => 'input',
                            'type' => fn() => Type::nonNull($services->getType('ImageItemUpdateInput')),
                        ],
                        [
                            'name' => 'language',
                            'type' => fn() => Type::nonNull($services->getType('RepositoryLanguage')),
                            'description' => 'The language the content should be created/updated in.',
                        ],
                        [
                            'name' => 'id',
                            'type' => Type::id(),
                            'description' => 'ID of the content item to update',
                        ],
                        [
                            'name' => 'contentId',
                            'type' => Type::int(),
                            'description' => 'Repository content ID of the content item to update',
                        ],
                        [
                            'name' => 'versionNo',
                            'type' => Type::int(),
                            'description' => 'Optional version number to update. If it is a draft, it is saved, not published. If it is archived, it is used as the source version for the update, to complete missing fields.',
                        ],
                    ],
                ],
                'createCustomerPortal' => [
                    'type' => fn() => Type::nonNull($services->getType('CustomerPortalItem')),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->mutation("CreateDomainContent", $args["input"], "customer_portal", $args["parentLocationId"], $args["language"]);
                    },
                    'args' => [
                        [
                            'name' => 'input',
                            'type' => fn() => Type::nonNull($services->getType('CustomerPortalItemCreateInput')),
                        ],
                        [
                            'name' => 'language',
                            'type' => fn() => Type::nonNull($services->getType('RepositoryLanguage')),
                            'description' => 'The language the content should be created/updated in.',
                        ],
                        [
                            'name' => 'parentLocationId',
                            'type' => Type::nonNull(Type::int()),
                        ],
                    ],
                ],
                'updateCustomerPortal' => [
                    'type' => fn() => Type::nonNull($services->getType('CustomerPortalItem')),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->mutation("UpdateDomainContent", $args["input"], $args, $args["versionNo"], $args["language"]);
                    },
                    'args' => [
                        [
                            'name' => 'input',
                            'type' => fn() => Type::nonNull($services->getType('CustomerPortalItemUpdateInput')),
                        ],
                        [
                            'name' => 'language',
                            'type' => fn() => Type::nonNull($services->getType('RepositoryLanguage')),
                            'description' => 'The language the content should be created/updated in.',
                        ],
                        [
                            'name' => 'id',
                            'type' => Type::id(),
                            'description' => 'ID of the content item to update',
                        ],
                        [
                            'name' => 'contentId',
                            'type' => Type::int(),
                            'description' => 'Repository content ID of the content item to update',
                        ],
                        [
                            'name' => 'versionNo',
                            'type' => Type::int(),
                            'description' => 'Optional version number to update. If it is a draft, it is saved, not published. If it is archived, it is used as the source version for the update, to complete missing fields.',
                        ],
                    ],
                ],
                'createCustomerPortalPage' => [
                    'type' => fn() => Type::nonNull($services->getType('CustomerPortalPageItem')),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->mutation("CreateDomainContent", $args["input"], "customer_portal_page", $args["parentLocationId"], $args["language"]);
                    },
                    'args' => [
                        [
                            'name' => 'input',
                            'type' => fn() => Type::nonNull($services->getType('CustomerPortalPageItemCreateInput')),
                        ],
                        [
                            'name' => 'language',
                            'type' => fn() => Type::nonNull($services->getType('RepositoryLanguage')),
                            'description' => 'The language the content should be created/updated in.',
                        ],
                        [
                            'name' => 'parentLocationId',
                            'type' => Type::nonNull(Type::int()),
                        ],
                    ],
                ],
                'updateCustomerPortalPage' => [
                    'type' => fn() => Type::nonNull($services->getType('CustomerPortalPageItem')),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->mutation("UpdateDomainContent", $args["input"], $args, $args["versionNo"], $args["language"]);
                    },
                    'args' => [
                        [
                            'name' => 'input',
                            'type' => fn() => Type::nonNull($services->getType('CustomerPortalPageItemUpdateInput')),
                        ],
                        [
                            'name' => 'language',
                            'type' => fn() => Type::nonNull($services->getType('RepositoryLanguage')),
                            'description' => 'The language the content should be created/updated in.',
                        ],
                        [
                            'name' => 'id',
                            'type' => Type::id(),
                            'description' => 'ID of the content item to update',
                        ],
                        [
                            'name' => 'contentId',
                            'type' => Type::int(),
                            'description' => 'Repository content ID of the content item to update',
                        ],
                        [
                            'name' => 'versionNo',
                            'type' => Type::int(),
                            'description' => 'Optional version number to update. If it is a draft, it is saved, not published. If it is archived, it is used as the source version for the update, to complete missing fields.',
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