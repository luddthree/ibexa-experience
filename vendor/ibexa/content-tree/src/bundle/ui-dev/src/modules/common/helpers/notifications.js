import { getTranslator } from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/context.helper';

export const getNotDeletedItemsData = (notDeletedItems, deletedItems, isUser) => {
    const Translator = getTranslator();
    const output = { message: null };
    const hadUserContentItemFailed = notDeletedItems.some(isUser);
    const hadNonUserContentItemFailed = notDeletedItems.some((item) => !isUser(item));

    if (hadUserContentItemFailed && hadNonUserContentItemFailed) {
        output.message = Translator.trans(
            /*@Desc("%notDeletedCount% of the %totalCount% selected item(s) could not be deleted or sent to Trash because you do not have proper user permissions. Contact your Administrator.")*/ 'bulk_delete.error.message.users_with_nonusers',
            {
                notDeletedCount: notDeletedItems.length,
                totalCount: deletedItems.length + notDeletedItems.length,
            },
            'ibexa_content_tree_ui',
        );
    } else if (hadUserContentItemFailed) {
        output.message = Translator.trans(
            /*@Desc("%notDeletedCount% of the %totalCount% selected item(s) could not be deleted because you do not have proper user permissions. Contact your Administrator.")*/ 'bulk_delete.error.message.users',
            {
                notDeletedCount: notDeletedItems.length,
                totalCount: deletedItems.length + notDeletedItems.length,
            },
            'ibexa_content_tree_ui',
        );
    } else {
        output.message = Translator.trans(
            /*@Desc("%notDeletedCount% of the %totalCount% selected item(s) could not be sent to Trash because you do not have proper user permissions. Contact your Administrator.")*/ 'bulk_delete.error.message.nonusers',
            {
                notDeletedCount: notDeletedItems.length,
                totalCount: deletedItems.length + notDeletedItems.length,
            },
            'ibexa_content_tree_ui',
        );
    }

    return output;
};
