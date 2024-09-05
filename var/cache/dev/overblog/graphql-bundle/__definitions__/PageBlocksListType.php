<?php

namespace Overblog\GraphQLBundle\__DEFINITIONS__;

use GraphQL\Type\Definition\EnumType;
use Overblog\GraphQLBundle\Definition\ConfigProcessor;
use Overblog\GraphQLBundle\Definition\GraphQLServices;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Type\GeneratedTypeInterface;

/**
 * THIS FILE WAS GENERATED AND SHOULD NOT BE EDITED MANUALLY.
 */
final class PageBlocksListType extends EnumType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'PageBlocksList';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'values' => [
                'qualifio' => [
                    'value' => 'QualifioPageBlock',
                ],
                'ibexa_news' => [
                    'value' => 'IbexaNewsPageBlock',
                ],
                'quick_actions' => [
                    'value' => 'QuickActionsPageBlock',
                ],
                'my_content' => [
                    'value' => 'MyContentPageBlock',
                ],
                'common_content' => [
                    'value' => 'CommonContentPageBlock',
                ],
                'review_queue' => [
                    'value' => 'ReviewQueuePageBlock',
                ],
                'ibexa_activity_log_list' => [
                    'value' => 'IbexaActivityLogListPageBlock',
                ],
                'sales_rep' => [
                    'value' => 'SalesRepPageBlock',
                ],
                'targeting' => [
                    'value' => 'TargetingPageBlock',
                ],
                'form' => [
                    'value' => 'FormPageBlock',
                ],
                'tag' => [
                    'value' => 'TagPageBlock',
                ],
                'contentlist' => [
                    'value' => 'ContentlistPageBlock',
                ],
                'banner' => [
                    'value' => 'BannerPageBlock',
                ],
                'collection' => [
                    'value' => 'CollectionPageBlock',
                ],
                'embed' => [
                    'value' => 'EmbedPageBlock',
                ],
                'gallery' => [
                    'value' => 'GalleryPageBlock',
                ],
                'video' => [
                    'value' => 'VideoPageBlock',
                ],
                'rss' => [
                    'value' => 'RssPageBlock',
                ],
                'schedule' => [
                    'value' => 'SchedulePageBlock',
                ],
                'richtext' => [
                    'value' => 'RichtextPageBlock',
                ],
                'catalog' => [
                    'value' => 'CatalogPageBlock',
                ],
                'product_collection' => [
                    'value' => 'ProductCollectionPageBlock',
                ],
                'products_with_lowest_stock' => [
                    'value' => 'ProductsWithLowestStockPageBlock',
                ],
                'products_by_categories' => [
                    'value' => 'ProductsByCategoriesPageBlock',
                ],
                'personalized' => [
                    'value' => 'PersonalizedPageBlock',
                ],
                'dynamic_targeting' => [
                    'value' => 'DynamicTargetingPageBlock',
                ],
                'last_viewed' => [
                    'value' => 'LastViewedPageBlock',
                ],
                'last_purchased' => [
                    'value' => 'LastPurchasedPageBlock',
                ],
                'bestsellers' => [
                    'value' => 'BestsellersPageBlock',
                ],
                'recently_added' => [
                    'value' => 'RecentlyAddedPageBlock',
                ],
                'top_clicked_items' => [
                    'value' => 'TopClickedItemsPageBlock',
                ],
                'ibexa_connect_block' => [
                    'value' => 'IbexaConnectBlockPageBlock',
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