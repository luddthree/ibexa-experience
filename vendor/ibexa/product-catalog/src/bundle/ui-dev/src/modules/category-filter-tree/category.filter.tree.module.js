import React, { useRef } from 'react';

import { getTranslatedName } from '@ibexa-taxonomy/src/bundle/ui-dev/src/modules/common/helpers/languages';
import NameContent from '@ibexa-taxonomy/src/bundle/ui-dev/src/modules/common/components/name-content/name-content';
import TaxonomyTreeBase from '@ibexa-taxonomy/src/bundle/ui-dev/src/modules/taxonomy-tree/taxonomy.tree.base';

const { ibexa, Translator } = window;
const MODULE_ID = 'ibexa-category-filter-tree';
const SUBTREE_ID = 'subtree';
const UNCATEGORISED_FAKE_ITEM_ID = -1;

const CategoryFilterTreeModule = (props) => {
    const { userId, taxonomyName, currentPath, activeItemId, languageCode, categoryWithFormDataUrlTemplate } = props;
    const treeBaseRef = useRef(null);
    const [href, paramsString] = categoryWithFormDataUrlTemplate.split('?');
    const searchParams = new URLSearchParams(paramsString);
    const uncategorizedItemLabel = Translator.trans(
        /*@Desc("Uncategorized products")*/ 'category_filter.tree.uncategorized.label',
        {},
        'ibexa_product_catalog',
    );
    const renderLabel = (item, { searchActive, searchValue }) => {
        const rootPathRegex = /^[^/]+(\/)?$/;
        let name = null;

        if (item.id === UNCATEGORISED_FAKE_ITEM_ID) {
            name = uncategorizedItemLabel;
        } else if (rootPathRegex.test(item.path)) {
            name = Translator.trans(/*@Desc("All categories")*/ 'category_filter.tree.root_category.label', {}, 'ibexa_product_catalog');
        } else {
            name = getTranslatedName(item.internalItem, languageCode);
        }

        return (
            <span className="c-tt-list-item__link">
                <NameContent searchActive={searchActive} searchValue={searchValue} name={name} />
            </span>
        );
    };
    const getSearchLink = (item) => {
        searchParams.set('page', 1);
        searchParams.set('product_search[category]', item.id === UNCATEGORISED_FAKE_ITEM_ID ? '' : item.id);

        return `${href}?${searchParams.toString()}`;
    };
    const isActive = (item) => {
        const isUncategorisedActive = activeItemId === null;
        const isUncategorisedItem = item.id === UNCATEGORISED_FAKE_ITEM_ID;

        if (isUncategorisedActive && isUncategorisedItem) {
            return true;
        }

        return item.id === activeItemId;
    };
    const generateExtraBottomItems = ({ searchActive, searchValue }) => {
        if (searchActive) {
            const searchRegex = new RegExp(`.*${searchValue}.*`);
            const isMatchingSearch = searchRegex.test(uncategorizedItemLabel);

            if (!isMatchingSearch) {
                return [];
            }
        }

        return [
            {
                internalItem: null,
                id: UNCATEGORISED_FAKE_ITEM_ID,
                href: getSearchLink({ id: UNCATEGORISED_FAKE_ITEM_ID }),
                path: UNCATEGORISED_FAKE_ITEM_ID.toString(),
                renderLabel,
                customItemClass: 'c-tt-list-item',
            },
        ];
    };

    return (
        <TaxonomyTreeBase
            ref={treeBaseRef}
            taxonomyName={taxonomyName}
            userId={userId}
            moduleId={MODULE_ID}
            subId={`category-filter-${taxonomyName}`}
            subtreeId={SUBTREE_ID}
            currentPath={currentPath}
            renderLabel={renderLabel}
            getItemLink={getSearchLink}
            languageCode={languageCode}
            generateExtraBottomItems={generateExtraBottomItems}
            useTheme={true}
            treeBuilderExtraProps={{
                moduleName: Translator.trans(/*@Desc("Category filter")*/ 'category_filter.tree.name', {}, 'ibexa_product_catalog'),
                selectionDisabled: true,
                dragDisabled: true,
                actionsVisible: false,
                isActive,
            }}
        />
    );
};

CategoryFilterTreeModule.propTypes = {
    userId: PropTypes.number.isRequired,
    taxonomyName: PropTypes.string.isRequired,
    currentPath: PropTypes.string.isRequired,
    languageCode: PropTypes.string,
    activeItemId: PropTypes.number.isRequired,
    categoryWithFormDataUrlTemplate: PropTypes.string.isRequired,
};

CategoryFilterTreeModule.defaultProps = {
    languageCode: ibexa.adminUiConfig.languages.priority[0],
};

export default CategoryFilterTreeModule;
