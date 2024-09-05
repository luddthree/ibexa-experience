import React, { useState, useEffect, useRef, createContext } from 'react';

import { getData, saveData } from '@ibexa-tree-builder/src/bundle/ui-dev/src/modules/tree-builder/helpers/localStorage';
import Move from '@ibexa-tree-builder/src/bundle/ui-dev/src/modules/tree-builder/actions/move/move';
import Delete from '@ibexa-tree-builder/src/bundle/ui-dev/src/modules/tree-builder/actions/delete/delete';
import CollapseAll from '@ibexa-tree-builder/src/bundle/ui-dev/src/modules/tree-builder/actions/collapse-all/collapse.all';
import SelectAll from '@ibexa-tree-builder/src/bundle/ui-dev/src/modules/tree-builder/actions/select-all/select.all';
import UnselectAll from '@ibexa-tree-builder/src/bundle/ui-dev/src/modules/tree-builder/actions/unselect-all/unselect.all';
import { ACTION_TYPE, ACTION_PARENT } from '@ibexa-tree-builder/src/bundle/ui-dev/src/modules/tree-builder/tree.builder.module';

import { loadNode, moveElements, deleteElements } from '../common/services/taxonomy.tree.service';
import { getContentLink } from '../common/helpers/getters';
import { checkIsDisabledDelete, checkIsDisabledMove } from '../common/helpers/actions';
import { getTranslatedName } from '../common/helpers/languages';
import AddTag from './components/add-tag/add.tag';
import HeaderContent from '../common/components/header-content/header.content';
import AssignContent from './components/assign-content/assign.content';
import NameContent from '../common/components/name-content/name-content';
import TaxonomyTreeBase from './taxonomy.tree.base';

export const RestInfoContext = createContext();
export const LanguageCodeContext = createContext();
const { ibexa, Translator } = window;
const languages = Object.values(ibexa.adminUiConfig.languages.mappings).filter(({ enabled }) => enabled);
const MODULE_ID = 'ibexa-taxonomy-tree';
const SUBTREE_ID = 'subtree';
const LANGUAGE_ID = 'language';

const TaxonomyTree = (props) => {
    const { userId, taxonomyName, moduleName, currentPath, rootSelectionDisabled } = props;
    const treeBaseRef = useRef(null);
    const getLanguageCode = () => {
        const savedLanguageCode = getData({ moduleId: MODULE_ID, userId, subId: taxonomyName, dataId: LANGUAGE_ID }) ?? props.languageCode;

        if (savedLanguageCode in languages) {
            return savedLanguageCode;
        }

        return ibexa.adminUiConfig.languages.priority[0];
    };
    const [languageCode, setLanguageCode] = useState(getLanguageCode());
    const renderLabel = (item, { searchActive, searchValue }) => {
        const name = getTranslatedName(item.internalItem, languageCode);

        return (
            <span className="c-tt-list-item__link">
                <NameContent searchActive={searchActive} searchValue={searchValue} name={name} />
            </span>
        );
    };
    const callbackDeleteElements = ({ selectedData }) => {
        const entries = selectedData.map((element) => element.id);

        return deleteElements(entries, { restInfo: props.restInfo, taxonomyName })
            .then(treeBaseRef.current.onItemsMoved)
            .then(() => {
                let message;

                if (selectedData.length === 1) {
                    message = Translator.trans(
                        /*@Desc("Tag %name% has been removed")*/ 'taxonomy.tag_removed',
                        { name: selectedData[0].internalItem.name },
                        'ibexa_taxonomy_ui',
                    );
                } else {
                    const names = selectedData.map((element) => element.internalItem.name).join(', ');

                    message = Translator.trans(
                        /*@Desc("Tags %names% has been removed")*/ 'taxonomy.tags_removed',
                        { names },
                        'ibexa_taxonomy_ui',
                    );
                }

                ibexa.helpers.notification.showSuccessNotification(message);
            })
            .then(() => {
                let pathIds = currentPath.split('/').map((id) => parseInt(id, 10));
                const pathLength = pathIds.length;

                entries.forEach((entryId) => {
                    const entryIdIndex = pathIds.findIndex((id) => id === entryId);

                    if (entryIdIndex !== -1) {
                        pathIds = pathIds.slice(0, entryIdIndex);
                    }
                });

                if (pathIds.length !== pathLength) {
                    const [entryId] = pathIds.slice(-1); // eslint-disable-line

                    loadNode({ taxonomyName, entryId }).then((nodeResponse) => {
                        window.location.href = getContentLink(nodeResponse);
                    });
                }
            })
            .catch(window.ibexa.helpers.notification.showErrorNotification);
    };
    const callbackMoveElements = ({ nextIndex, nextParent, sibling, siblingPosition }, { selectedData }) => {
        return new Promise((resolve) => {
            if (nextIndex === -1) {
                loadNode({ taxonomyName, entryId: nextParent.id }).then((nodeResponse) => {
                    const lastChildId = nodeResponse.__children[nodeResponse.__children.length - 1].id;
                    const entries = selectedData.map((element) => ({
                        entry: element.id,
                        sibling: lastChildId,
                        position: 'next',
                    }));

                    resolve(entries);
                });
            } else {
                const entries = selectedData.map((element) => ({
                    entry: element.id,
                    sibling: sibling.id,
                    position: siblingPosition,
                }));

                resolve(entries);
            }
        })
            .then((entries) => moveElements(entries, { restInfo: props.restInfo, taxonomyName }))
            .then(treeBaseRef.current.onItemsMoved)
            .then(() => {
                const shouldReload = !!selectedData.find(({ path }) => path === currentPath);
                let message;

                if (selectedData.length === 1) {
                    message = Translator.trans(
                        /*@Desc("Tag %name% has been moved")*/ 'taxonomy.tag_moved',
                        { name: selectedData[0].internalItem.name },
                        'ibexa_taxonomy_ui',
                    );
                } else {
                    const names = selectedData.map((element) => element.internalItem.name).join(', ');

                    message = Translator.trans(
                        /*@Desc("Tags %names% has been moved")*/ 'taxonomy.tags_moved',
                        { names },
                        'ibexa_taxonomy_ui',
                    );
                }

                ibexa.helpers.notification.showSuccessNotification(message);

                if (shouldReload) {
                    window.location.reload();
                }
            })
            .catch(window.ibexa.helpers.notification.showErrorNotification);
    };
    const renderHeaderContent = () => {
        return <HeaderContent name={moduleName} languageCode={languageCode} setLanguageCode={setLanguageCode} />;
    };
    const isActive = (item) => currentPath === item.path;

    useEffect(() => {
        saveData({ moduleId: MODULE_ID, userId, subId: taxonomyName, dataId: LANGUAGE_ID, data: languageCode });
    }, [languageCode]);

    return (
        <RestInfoContext.Provider value={props.restInfo}>
            <LanguageCodeContext.Provider value={languageCode}>
                <TaxonomyTreeBase
                    ref={treeBaseRef}
                    taxonomyName={taxonomyName}
                    userId={userId}
                    moduleId={MODULE_ID}
                    subId={taxonomyName}
                    subtreeId={SUBTREE_ID}
                    currentPath={currentPath}
                    languageCode={languageCode}
                    renderLabel={renderLabel}
                    getItemLink={getContentLink}
                    useTheme={true}
                    treeBuilderExtraProps={{
                        moduleName,
                        rootSelectionDisabled,
                        searchVisible: true,
                        dragDisabled: false,
                        renderHeaderContent,
                        callbackMoveElements,
                        callbackDeleteElements,
                        isActive,
                        actionsType: ACTION_TYPE.CONTEXTUAL_MENU,
                    }}
                />
            </LanguageCodeContext.Provider>
        </RestInfoContext.Provider>
    );
};

TaxonomyTree.propTypes = {
    userId: PropTypes.number.isRequired,
    taxonomyName: PropTypes.string.isRequired,
    restInfo: PropTypes.shape({
        token: PropTypes.string.isRequired,
        siteaccess: PropTypes.string.isRequired,
    }).isRequired,
    currentPath: PropTypes.string.isRequired,
    languageCode: PropTypes.string,
    moduleName: PropTypes.string,
    rootSelectionDisabled: PropTypes.bool,
};

TaxonomyTree.defaultProps = {
    languageCode: ibexa.adminUiConfig.languages.priority[0],
    moduleName: Translator.trans(/*@Desc("Taxonomy tree")*/ 'taxonomy.tree_name', {}, 'ibexa_taxonomy_ui'),
    rootSelectionDisabled: false,
};

const treeBuilderConfig = {
    [MODULE_ID]: {
        menuActions: [
            {
                priority: 20,
                id: 'modifying',
                subitems: [
                    {
                        priority: 10,
                        id: 'add',
                        component: AddTag,
                        visibleIn: [ACTION_PARENT.SINGLE_ITEM],
                    },
                    {
                        priority: 30,
                        id: 'move',
                        component: Move,
                        visibleIn: [ACTION_PARENT.TOP_MENU, ACTION_PARENT.SINGLE_ITEM],
                        checkIsDisabled: checkIsDisabledMove,
                    },
                    {
                        priority: 40,
                        id: 'delete',
                        component: Delete,
                        visibleIn: [ACTION_PARENT.TOP_MENU, ACTION_PARENT.SINGLE_ITEM],
                        checkIsDisabled: checkIsDisabledDelete,
                        modalConfirmationBody: Translator.trans(
                            /*@Desc("Are you sure you want to delete selected tree item(s)?")*/ 'taxonomy.delete.confirm',
                            {},
                            'ibexa_taxonomy_ui',
                        ),
                    },
                    {
                        priority: 40,
                        id: 'assignContent',
                        component: AssignContent,
                        visibleIn: [ACTION_PARENT.TOP_MENU, ACTION_PARENT.SINGLE_ITEM],
                    },
                ],
            },
            {
                priority: 30,
                id: 'collapsible',
                subitems: [
                    {
                        priority: 20,
                        id: 'collapse',
                        component: CollapseAll,
                        visibleIn: [ACTION_PARENT.TOP_MENU, ACTION_PARENT.SINGLE_ITEM],
                    },
                ],
            },
            {
                priority: 40,
                id: 'other',
                subitems: [
                    {
                        priority: 10,
                        id: 'select',
                        component: SelectAll,
                        visibleIn: [ACTION_PARENT.TOP_MENU, ACTION_PARENT.SINGLE_ITEM],
                    },
                    {
                        priority: 20,
                        id: 'unselect',
                        component: UnselectAll,
                        visibleIn: [ACTION_PARENT.TOP_MENU, ACTION_PARENT.SINGLE_ITEM],
                    },
                ],
            },
        ],
    },
};

window.ibexa.addConfig('treeBuilder', treeBuilderConfig);
window.ibexa.addConfig('modules.TaxonomyTree', TaxonomyTree);

export default TaxonomyTree;
