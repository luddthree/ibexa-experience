<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/graphiql' => [[['_route' => 'overblog_graphiql_endpoint', '_controller' => 'Overblog\\GraphiQLBundle\\Controller\\GraphiQLController::indexAction'], null, null, null, false, false, null]],
        '/graphql' => [[['_route' => 'overblog_graphql_endpoint', '_controller' => 'Overblog\\GraphQLBundle\\Controller\\GraphController::endpointAction', '_format' => 'json'], null, null, null, false, false, null]],
        '/login' => [[['_route' => 'login', '_controller' => 'Ibexa\\Core\\MVC\\Symfony\\Controller\\SecurityController::loginAction'], null, null, null, false, false, null]],
        '/login_check' => [[['_route' => 'login_check'], null, null, null, false, false, null]],
        '/logout' => [[['_route' => 'logout'], null, null, null, false, false, null]],
        '/_fos_user_context_hash' => [[['_route' => 'ibexa.user_hash'], null, null, null, false, false, null]],
        '/activity-log' => [[['_route' => 'ibexa.activity_log.list', '_controller' => 'Ibexa\\Bundle\\ActivityLog\\Controller\\ActivityLogController::renderAction'], null, ['GET' => 0], null, false, false, null]],
        '/current-user/activity-log' => [[['_route' => 'ibexa.activity_log.user_activity_list', '_controller' => 'Ibexa\\Bundle\\ActivityLog\\Controller\\UserActivityLogController::renderAction'], null, ['GET' => 0], null, false, false, null]],
        '/api/ibexa/v2/activity-log-group/list' => [[['_route' => 'ibexa.activity_log.rest.activity_log_group.list', '_controller' => 'Ibexa\\Bundle\\ActivityLog\\Controller\\REST\\ActivityLog\\ListController::list'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
        '/dashboard' => [[['_route' => 'ibexa.dashboard', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\DashboardController::dashboardAction', 'siteaccess_group_whitelist' => 'admin_group'], null, null, null, false, false, null]],
        '/section/list' => [[['_route' => 'ibexa.section.list', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\SectionController::listAction', 'siteaccess_group_whitelist' => 'admin_group'], null, null, null, false, false, null]],
        '/section/create' => [[['_route' => 'ibexa.section.create', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\SectionController::createAction', 'siteaccess_group_whitelist' => 'admin_group'], null, null, null, false, false, null]],
        '/section/bulk-delete' => [[['_route' => 'ibexa.section.bulk_delete', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\SectionController::bulkDeleteAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/language/list' => [[['_route' => 'ibexa.language.list', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\LanguageController::listAction', 'siteaccess_group_whitelist' => 'admin_group'], null, null, null, false, false, null]],
        '/language/create' => [[['_route' => 'ibexa.language.create', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\LanguageController::createAction', 'siteaccess_group_whitelist' => 'admin_group'], null, null, null, false, false, null]],
        '/language/bulk-delete' => [[['_route' => 'ibexa.language.bulk_delete', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\LanguageController::bulkDeleteAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/role/list' => [[['_route' => 'ibexa.role.list', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\RoleController::listAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['GET' => 0], null, false, false, null]],
        '/role/create' => [[['_route' => 'ibexa.role.create', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\RoleController::createAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
        '/role/bulk-delete' => [[['_route' => 'ibexa.role.bulk_delete', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\RoleController::bulkDeleteAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/contenttypegroup/list' => [[['_route' => 'ibexa.content_type_group.list', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\ContentTypeGroupController::listAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['GET' => 0], null, false, false, null]],
        '/contenttypegroup/create' => [[['_route' => 'ibexa.content_type_group.create', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\ContentTypeGroupController::createAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
        '/contenttypegroup/bulk-delete' => [[['_route' => 'ibexa.content_type_group.bulk_delete', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\ContentTypeGroupController::bulkDeleteAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/trash/list' => [[['_route' => 'ibexa.trash.list', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\TrashController::listAction', 'siteaccess_group_whitelist' => 'admin_group'], null, null, null, false, false, null]],
        '/trash/empty' => [[['_route' => 'ibexa.trash.empty', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\TrashController::emptyAction', 'siteaccess_group_whitelist' => 'admin_group'], null, null, null, false, false, null]],
        '/trash/restore' => [[['_route' => 'ibexa.trash.restore', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\TrashController::restoreAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/trash/delete' => [[['_route' => 'ibexa.trash.delete', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\TrashController::deleteAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/content-type/translation/add' => [[['_route' => 'ibexa.content_type.add_translation', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\ContentTypeController::addTranslationAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/content-type/translation/remove' => [[['_route' => 'ibexa.content_type.remove_translation', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\ContentTypeController::removeTranslationAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/location/move' => [[['_route' => 'ibexa.location.move', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\LocationController::moveAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/location/copy' => [[['_route' => 'ibexa.location.copy', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\LocationController::copyAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/location/trash' => [[['_route' => 'ibexa.location.trash', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\LocationController::trashAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/location/update' => [[['_route' => 'ibexa.location.update', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\LocationController::updateAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/location/copy-subtree' => [[['_route' => 'ibexa.location.copy_subtree', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\LocationController::copySubtreeAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/translation/add' => [[['_route' => 'ibexa.translation.add', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\TranslationController::addAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/translation/remove' => [[['_route' => 'ibexa.translation.remove', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\TranslationController::removeAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/content/update-main-translation' => [[['_route' => 'ibexa.content.update_main_translation', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\ContentController::updateMainTranslationAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/content/update-visibility' => [[['_route' => 'ibexa.content.update_visibility', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\ContentController::updateVisibilityAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/version/remove' => [[['_route' => 'ibexa.version.remove', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\VersionController::removeAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/location/add' => [[['_route' => 'ibexa.location.add', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\LocationController::addAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/location/remove' => [[['_route' => 'ibexa.location.remove', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\LocationController::removeAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/location/swap' => [[['_route' => 'ibexa.location.swap', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\LocationController::swapAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/location/update-visibility' => [[['_route' => 'ibexa.location.update_visibility', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\LocationController::updateVisibilityAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/location/assign-section' => [[['_route' => 'ibexa.location.assign_section', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\LocationController::assignSectionAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/content/update-main-location' => [[['_route' => 'ibexa.content.update_main_location', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\ContentController::updateMainLocationAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/content/edit' => [[['_route' => 'ibexa.content.edit', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\ContentController::editAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/content/create' => [[['_route' => 'ibexa.content.create', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\ContentController::createAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/user/delete' => [[['_route' => 'ibexa.user.delete', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\User\\UserDeleteController::userDeleteAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/user/profile/edit' => [[['_route' => 'ibexa.user.profile.edit', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\User\\ProfileEditController::editAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
        '/url-alias/add' => [[['_route' => 'ibexa.custom_url.add', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\UrlAliasController::addAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/url-alias/remove' => [[['_route' => 'ibexa.custom_url.remove', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\UrlAliasController::removeAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/url-wildcard/add' => [[['_route' => 'ibexa.url_wildcard.add', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\URLWildcardController::addAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
        '/url-wildcard/bulk-delete' => [[['_route' => 'ibexa.url_wildcard.bulk_delete', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\URLWildcardController::bulkDeleteAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
        '/url-management' => [[['_route' => 'ibexa.url_management', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\URLManagementController::urlManagementAction', 'siteaccess_group_whitelist' => 'admin_group'], null, null, null, false, false, null]],
        '/state/groups' => [[['_route' => 'ibexa.object_state.groups.list', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\ObjectStateGroupController::listAction', 'siteaccess_group_whitelist' => 'admin_group'], null, null, null, false, false, null]],
        '/state/group/create' => [[['_route' => 'ibexa.object_state.group.add', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\ObjectStateGroupController::addAction', 'siteaccess_group_whitelist' => 'admin_group'], null, null, null, false, false, null]],
        '/state/group/bulk-delete' => [[['_route' => 'ibexa.object_state.group.bulk_delete', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\ObjectStateGroupController::bulkDeleteAction', 'siteaccess_group_whitelist' => 'admin_group'], null, null, null, false, false, null]],
        '/bookmark/list' => [[['_route' => 'ibexa.bookmark.list', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\BookmarkController::listAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['GET' => 0], null, false, false, null]],
        '/bookmark/remove' => [[['_route' => 'ibexa.bookmark.remove', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\BookmarkController::removeAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/contentdraft/list' => [[['_route' => 'ibexa.content_draft.list', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\ContentDraftController::listAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['GET' => 0], null, false, false, null]],
        '/contentdraft/remove' => [[['_route' => 'ibexa.content_draft.remove', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\ContentDraftController::removeAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/notifications/count' => [[['_route' => 'ibexa.notifications.count', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\NotificationController::countNotificationsAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['GET' => 0], null, false, false, null]],
        '/asset/image' => [[['_route' => 'ibexa.asset.upload_image', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\AssetController::uploadImageAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/user/focus-mode' => [[['_route' => 'ibexa.focus_mode.change', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\User\\FocusModeController::changeAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
        '/api/ibexa/v2/bulk' => [[['_route' => 'ibexa.rest.bulk_operation', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\BulkOperation\\BulkOperationController::bulkAction'], null, ['POST' => 0], null, false, false, null]],
        '/api/ibexa/v2/location/tree/load-subtree' => [[['_route' => 'ibexa.rest.location.tree.load_subtree', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\Content\\ContentTreeController::loadSubtreeAction'], null, ['POST' => 0], null, false, false, null]],
        '/api/ibexa/v2/module/universal-discovery/locations' => [[['_route' => 'ibexa.udw.locations.data', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\UniversalDiscoveryController::locationsAction'], null, ['GET' => 0], null, false, false, null]],
        '/api/ibexa/v2/application-config' => [[['_route' => 'ibexa.rest.application_config', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\ApplicationConfigController::loadConfigAction'], null, ['GET' => 0], null, false, false, null]],
        '/calendar' => [[['_route' => 'ibexa.calendar.view', '_controller' => 'Ibexa\\Bundle\\Calendar\\Controller\\CalendarController::viewAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['GET' => 0], null, false, false, null]],
        '/api/ibexa/v2/calendar/event/grouped-by-day' => [[['_route' => 'ibexa.calendar.rest.event.list.group_by_day', '_controller' => 'Ibexa\\Bundle\\Calendar\\Controller\\REST\\EventController::listEventsByDayAction'], null, ['GET' => 0], null, false, false, null]],
        '/api/ibexa/v2/calendar/event' => [[['_route' => 'ibexa.calendar.rest.event.list', '_controller' => 'Ibexa\\Bundle\\Calendar\\Controller\\REST\\EventController::listEventsAction'], null, ['GET' => 0], null, false, false, null]],
        '/connector/asset/search' => [[['_route' => 'ibexa.connector.dam.generic_search', '_controller' => 'Ibexa\\Bundle\\Connector\\Dam\\Controller\\AssetSearchController::fetchResultsAction'], null, ['GET' => 0], null, false, false, null]],
        '/company/create' => [[['_route' => 'ibexa.corporate_account.company.create', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\CompanyCreateController::createAction'], null, null, null, false, false, null]],
        '/company/list' => [[['_route' => 'ibexa.corporate_account.company.list', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\CompanyListController::listAction'], null, null, null, false, false, null]],
        '/company/deactivate/bulk' => [[['_route' => 'ibexa.corporate_account.company.bulk_deactivate', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\CompanyStatusController::bulkDeactivateAction'], null, null, null, false, false, null]],
        '/individual/list' => [[['_route' => 'ibexa.corporate_account.individual.list', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\IndividualListController::listAction'], null, null, null, false, false, null]],
        '/company/member/change_role' => [[['_route' => 'ibexa.corporate_account.company.member.change_role', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\MemberEditController::changeRoleAction'], null, null, null, false, false, null]],
        '/application/list' => [[['_route' => 'ibexa.corporate_account.application.list', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\ApplicationListController::listAction'], null, null, null, false, false, null]],
        '/application/delete/bulk' => [[['_route' => 'ibexa.corporate_account.application.delete.bulk', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\ApplicationDeleteController::bulkDeleteAction'], null, null, null, false, false, null]],
        '/customer-portal/customer-center' => [[['_route' => 'ibexa.corporate_account.customer_portal.customer_center', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\CorporatePortal\\CustomerCenterController::showAction', 'siteaccess_group_whitelist' => 'corporate_group'], null, null, null, false, false, null]],
        '/customer-portal/pending-orders' => [[['_route' => 'ibexa.corporate_account.customer_portal.pending_orders', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\CorporatePortal\\OrdersController::showPendingOrdersAction', 'siteaccess_group_whitelist' => 'corporate_group'], null, null, null, false, false, null]],
        '/customer-portal/past-orders' => [[['_route' => 'ibexa.corporate_account.customer_portal.past_orders', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\CorporatePortal\\OrdersController::showPastOrdersAction', 'siteaccess_group_whitelist' => 'corporate_group'], null, null, null, false, false, null]],
        '/customer-portal/contact' => [[['_route' => 'ibexa.corporate_account.customer_portal.contact', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\CorporatePortal\\ContactController::showAction', 'siteaccess_group_whitelist' => 'corporate_group'], null, null, null, false, false, null]],
        '/customer-portal/edit/billing_address' => [[['_route' => 'ibexa.corporate_account.customer_portal.edit_billing_address', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\CorporatePortal\\BillingAddressController::editAction', 'siteaccess_group_whitelist' => 'corporate_group'], null, null, null, false, false, null]],
        '/customer-portal/members' => [[['_route' => 'ibexa.corporate_account.customer_portal.members', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\CorporatePortal\\MembersController::showAction', 'siteaccess_group_whitelist' => 'corporate_group'], null, null, null, false, false, null]],
        '/customer-portal/members/add' => [[['_route' => 'ibexa.corporate_account.customer_portal.create_member', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\CorporatePortal\\MembersController::createAction', 'siteaccess_group_whitelist' => 'corporate_group'], null, null, null, false, false, null]],
        '/customer-portal/members/change_role' => [[['_route' => 'ibexa.corporate_account.customer_portal.change_corporate_role', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\CorporatePortal\\MembersController::changeRoleAction', 'siteaccess_group_whitelist' => 'corporate_group'], null, null, null, false, false, null]],
        '/customer-portal/address-book' => [[['_route' => 'ibexa.corporate_account.customer_portal.address_book', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\CorporatePortal\\AddressBookController::showAction', 'siteaccess_group_whitelist' => 'corporate_group'], null, null, null, false, false, null]],
        '/customer-portal/my-profile' => [[['_route' => 'ibexa.corporate_account.customer_portal.my_profile', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\CorporatePortal\\MyProfileController::showAction', 'siteaccess_group_whitelist' => 'corporate_group'], null, null, null, false, false, null]],
        '/customer-portal/my-profile/edit' => [[['_route' => 'ibexa.corporate_account.customer_portal.my_profile.edit', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\CorporatePortal\\MyProfileController::editAction', 'siteaccess_group_whitelist' => 'corporate_group'], null, null, null, false, false, null]],
        '/customer-portal/set-as-default-shipping-address' => [[['_route' => 'ibexa.corporate_account.customer_portal.update_default_shipping_address', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\CorporatePortal\\ShippingAddressController::setAsDefaultAction', 'siteaccess_group_whitelist' => 'corporate_group'], null, ['POST' => 0], null, false, false, null]],
        '/customer-portal/delete-shipping-address' => [[['_route' => 'ibexa.corporate_account.customer_portal.address_book_items.delete', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\CorporatePortal\\ShippingAddressController::deleteAction', 'siteaccess_group_whitelist' => 'corporate_group'], null, ['POST' => 0], null, false, false, null]],
        '/customer-portal/address/create' => [[['_route' => 'ibexa.corporate_account.customer_portal.create_address', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\CorporatePortal\\ShippingAddressController::createAction', 'siteaccess_group_whitelist' => 'corporate_group'], null, null, null, false, false, null]],
        '/customer-portal/invite' => [[['_route' => 'ibexa.corporate_account.customer_portal.company.invite', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\CorporatePortal\\InvitationController::sendInvitationsAction', 'siteaccess_group_whitelist' => 'corporate_group'], null, null, null, false, false, null]],
        '/customer-portal/reinvite' => [[['_route' => 'ibexa.corporate_account.customer_portal.company.reinvite', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\CorporatePortal\\InvitationController::reinviteAction', 'siteaccess_group_whitelist' => 'corporate_group'], null, null, null, false, false, null]],
        '/customer-portal/resend' => [[['_route' => 'ibexa.corporate_account.customer_portal.company.resend', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\CorporatePortal\\InvitationController::resendAction', 'siteaccess_group_whitelist' => 'corporate_group'], null, null, null, false, false, null]],
        '/customer-portal/register' => [[['_route' => 'ibexa.corporate_account.customer_portal.corporate_account.register', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\CorporatePortal\\ApplicationController::createAction', 'siteaccess_group_whitelist' => 'corporate_group'], null, null, null, false, false, null]],
        '/customer-portal/register/confirmation' => [[['_route' => 'ibexa.corporate_account.customer_portal.corporate_account.register.confirmation', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\CorporatePortal\\ApplicationController::confirmationAction', 'siteaccess_group_whitelist' => 'corporate_group'], null, null, null, false, false, null]],
        '/customer-portal/register/already-exists' => [[['_route' => 'ibexa.corporate_account.customer_portal.corporate_account.register.already_exists', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\CorporatePortal\\ApplicationController::alreadyExistsAction', 'siteaccess_group_whitelist' => 'corporate_group'], null, null, null, false, false, null]],
        '/customer-portal/register/wait' => [[['_route' => 'ibexa.corporate_account.customer_portal.corporate_account.register.wait', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\CorporatePortal\\ApplicationController::waitAction', 'siteaccess_group_whitelist' => 'corporate_group'], null, null, null, false, false, null]],
        '/api/ibexa/v2/corporate/companies' => [
            [['_route' => 'ibexa.rest.corporate_account.companies.create', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\REST\\CompanyController::createCompanyAction'], null, ['POST' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.corporate_account.companies.list', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\REST\\CompanyController::listCompaniesAction'], null, ['GET' => 0], null, false, false, null],
        ],
        '/api/ibexa/v2/corporate/sales-representatives' => [[['_route' => 'ibexa.rest.corporate_account.sales_representatives.get', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\REST\\SalesRepresentativesController::getAllAction'], null, ['GET' => 0], null, false, false, null]],
        '/api/ibexa/v2/corporate' => [[['_route' => 'ibexa.rest.corporate_account.root', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\REST\\RootController::getRootAction'], null, ['GET' => 0], null, false, false, null]],
        '/dashboard/customize-dashboard' => [[['_route' => 'ibexa.dashboard.customize_dashboard', '_controller' => 'Ibexa\\Bundle\\Dashboard\\Controller\\CustomizeDashboardController::customizeDashboardAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/dashboard/content-type' => [[['_route' => 'ibexa.dashboard.content_type', '_controller' => 'Ibexa\\Bundle\\Dashboard\\Controller\\DashboardContentTypeController::dashboardContentTypeAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['GET' => 0], null, false, false, null]],
        '/dashboard/hide-banner' => [[['_route' => 'ibexa.dashboard.hide_banner', '_controller' => 'Ibexa\\Bundle\\Dashboard\\Controller\\HideDashboardBannerController::hideDashboardBannerAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/form/preview' => [[['_route' => 'ibexa.form_builder.form.preview_form', '_controller' => 'Ibexa\\Bundle\\FormBuilder\\Controller\\FormBuilderController::formPreviewAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/submission/remove' => [[['_route' => 'ibexa.submission.remove', '_controller' => 'Ibexa\\Bundle\\FormBuilder\\Controller\\FormSubmissionController::removeAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/_ibexa_http_invalidatetoken' => [[['_route' => 'ibexa.http_cache.invalidate_token', '_controller' => 'Ibexa\\Bundle\\HttpCache\\Controller\\InvalidateTokenController:tokenAction'], null, null, null, false, false, null]],
        '/page/block/request-configuration-form' => [[['_route' => 'ibexa.page_builder.block.request_configuration_form', '_controller' => 'Ibexa\\Bundle\\PageBuilder\\Controller\\BlockController::requestBlockConfigurationAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/page/block/schedule/list-content' => [[['_route' => 'ibexa.page.block.schedule_block.list_content', '_controller' => 'Ibexa\\Bundle\\PageBuilder\\Controller\\ScheduleBlockController::listContentAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/page/timeline/events/get' => [[['_route' => 'ibexa.page_builder.timeline.events.get', '_controller' => 'Ibexa\\Bundle\\PageBuilder\\Controller\\TimelineController::getEventsAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/personalization/report/revenue' => [[['_route' => 'ibexa.personalization.report.revenue', '_controller' => 'Ibexa\\Bundle\\Personalization\\Controller\\ReportController::revenueReportAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['GET' => 0], null, false, false, null]],
        '/personalization/welcome' => [[['_route' => 'ibexa.personalization.welcome', '_controller' => 'Ibexa\\Bundle\\Personalization\\Controller\\WelcomeController::welcomeAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
        '/personalization/account/create' => [[['_route' => 'ibexa.personalization.account.create', '_controller' => 'Ibexa\\Bundle\\Personalization\\Controller\\AccountController::createAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
        '/personalization/account/details' => [[['_route' => 'ibexa.personalization.account.details', '_controller' => 'Ibexa\\Bundle\\Personalization\\Controller\\AccountController::detailsAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['GET' => 0], null, false, false, null]],
        '/product/list' => [[['_route' => 'ibexa.product_catalog.product.list', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\Product\\ListController::renderAction'], null, ['GET' => 0], null, false, false, null]],
        '/product/bulk/delete' => [[['_route' => 'ibexa.product_catalog.product.bulk_delete', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\Product\\BulkDeleteController::executeAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/product/create' => [[['_route' => 'ibexa.product_catalog.product.create_proxy', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\Product\\CreateController::createProxyAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
        '/product-type/list' => [[['_route' => 'ibexa.product_catalog.product_type.list', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\ProductType\\ListController::renderAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['GET' => 0], null, false, false, null]],
        '/product-type/bulk/delete' => [[['_route' => 'ibexa.product_catalog.product_type.bulk_delete', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\ProductType\\BulkDeleteController::executeAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/product-type/create' => [[['_route' => 'ibexa.product_catalog.product_type.create', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\ProductType\\CreateController::renderAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
        '/customer-group' => [[['_route' => 'ibexa.product_catalog.customer_group.list', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\CustomerGroup\\ListController::renderAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['GET' => 0], null, false, false, null]],
        '/customer-group/create' => [[['_route' => 'ibexa.product_catalog.customer_group.create', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\CustomerGroup\\CreateController::renderAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
        '/customer-group/bulk/delete' => [[['_route' => 'ibexa.product_catalog.customer_group.bulk_delete', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\CustomerGroup\\BulkDeleteController::executeAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/attribute-definition/bulk-delete' => [[['_route' => 'ibexa.product_catalog.attribute_definition.bulk_delete', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\AttributeDefinition\\BulkDeleteController::executeAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/attribute-definition/list' => [[['_route' => 'ibexa.product_catalog.attribute_definition.list', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\AttributeDefinition\\ListController::renderAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['GET' => 0], null, false, false, null]],
        '/attribute-definition/pre-create' => [[['_route' => 'ibexa.product_catalog.attribute_definition.pre_create', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\AttributeDefinition\\PreCreateController::executeAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/attribute-definition/translation/create' => [[['_route' => 'ibexa.product_catalog.attribute_definition.translation.create', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\AttributeDefinition\\CreateTranslationController::renderAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/attribute-definition/translation/delete' => [[['_route' => 'ibexa.product_catalog.attribute_definition.translation.delete', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\AttributeDefinition\\DeleteTranslationController::executeAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/attribute-group/bulk/delete' => [[['_route' => 'ibexa.product_catalog.attribute_group.bulk_delete', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\AttributeGroup\\BulkDeleteController::executeAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/attribute-group/create' => [[['_route' => 'ibexa.product_catalog.attribute_group.create', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\AttributeGroup\\CreateController::executeAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
        '/attribute-group' => [[['_route' => 'ibexa.product_catalog.attribute_group.list', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\AttributeGroup\\ListController::renderAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['GET' => 0], null, false, false, null]],
        '/attribute-group/translation/create' => [[['_route' => 'ibexa.product_catalog.attribute_group.translation.create', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\AttributeGroup\\CreateTranslationController::renderAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/attribute-group/translation/delete' => [[['_route' => 'ibexa.product_catalog.attribute_group.translation.delete', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\AttributeGroup\\DeleteTranslationController::executeAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/currencies/list' => [[['_route' => 'ibexa.product_catalog.currency.list', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\Currency\\ListController::renderAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['GET' => 0], null, false, false, null]],
        '/currencies/create' => [[['_route' => 'ibexa.product_catalog.currency.create', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\Currency\\CreateController::renderAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
        '/currencies/bulk/delete' => [[['_route' => 'ibexa.product_catalog.currency.bulk_delete', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\Currency\\BulkDeleteController::executeAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/region/list' => [[['_route' => 'ibexa.product_catalog.region.list', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\Regions\\ListController::renderAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['GET' => 0], null, false, false, null]],
        '/catalog' => [[['_route' => 'ibexa.product_catalog.catalog.list', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\Catalog\\ListController::renderAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['GET' => 0], null, false, false, null]],
        '/catalog/create' => [[['_route' => 'ibexa.product_catalog.catalog.create', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\Catalog\\CreateController::renderAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
        '/catalog/bulk/delete' => [[['_route' => 'ibexa.product_catalog.catalog.bulk_delete', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\Catalog\\BulkDeleteController::executeAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/catalog/products/preview' => [[['_route' => 'ibexa.product_catalog.catalog.products.preview.list', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\Catalog\\ProductsPreviewController::listAction', 'siteaccess_group_whitelist' => 'admin_group'], null, ['POST' => 0], null, false, false, null]],
        '/api/ibexa/v2/product/catalog/attributes/view' => [[['_route' => 'ibexa.product_catalog.rest.attributes.view', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\AttributeViewController::createView'], null, ['POST' => 0], null, false, false, null]],
        '/api/ibexa/v2/product/catalog/catalogs/view' => [[['_route' => 'ibexa.product_catalog.rest.catalogs.view', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\CatalogViewController::createView'], null, ['POST' => 0], null, false, false, null]],
        '/api/ibexa/v2/product/catalog/currencies/view' => [[['_route' => 'ibexa.product_catalog.rest.currencies.view', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\CurrencyViewController::createView'], null, ['POST' => 0], null, false, false, null]],
        '/api/ibexa/v2/product/catalog/attribute_groups/view' => [[['_route' => 'ibexa.product_catalog.rest.attribute_groups.view', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\AttributeGroupViewController::createView'], null, ['POST' => 0], null, false, false, null]],
        '/api/ibexa/v2/product/catalog/customer_groups/view' => [[['_route' => 'ibexa.product_catalog.rest.customer_groups.view', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\CustomerGroupViewController::createView'], null, ['POST' => 0], null, false, false, null]],
        '/api/ibexa/v2/product/catalog/regions/view' => [[['_route' => 'ibexa.product_catalog.rest.regions.view', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\RegionViewController::createView'], null, ['POST' => 0], null, false, false, null]],
        '/api/ibexa/v2/product/catalog/products/view' => [[['_route' => 'ibexa.product_catalog.rest.products.view', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\ProductViewController::createView'], null, ['POST' => 0], null, false, false, null]],
        '/api/ibexa/v2/product/catalog/product_types/view' => [[['_route' => 'ibexa.product_catalog.rest.product_types.view', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\ProductTypeViewController::createView'], null, ['POST' => 0], null, false, false, null]],
        '/api/ibexa/v2/product/catalog/attribute_groups' => [
            [['_route' => 'ibexa.product_catalog.rest.attribute_groups', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\AttributeGroupController::listAttributeGroupsAction'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'ibexa.product_catalog.rest.attribute_groups.create', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\AttributeGroupController::createAttributeGroupAction'], null, ['POST' => 0], null, false, false, null],
        ],
        '/api/ibexa/v2/product/catalog/attributes' => [
            [['_route' => 'ibexa.product_catalog.rest.attributes', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\AttributeController::listAttributesAction'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'ibexa.product_catalog.rest.attributes.create', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\AttributeController::createAttributeAction'], null, ['POST' => 0], null, false, false, null],
        ],
        '/api/ibexa/v2/product/catalog/attribute_types' => [[['_route' => 'ibexa.product_catalog.rest.attribute_types', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\AttributeController::listAttributeTypesAction'], null, ['GET' => 0], null, false, false, null]],
        '/api/ibexa/v2/product/catalog/catalogs/filters' => [[['_route' => 'ibexa.product_catalog.rest.catalogs.filters', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\CatalogController::getFiltersAction'], null, ['GET' => 0], null, false, false, null]],
        '/api/ibexa/v2/product/catalog/catalogs/sort_clauses' => [[['_route' => 'ibexa.product_catalog.rest.catalogs.sort_clauses', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\CatalogController::getSortClausesAction'], null, ['GET' => 0], null, false, false, null]],
        '/api/ibexa/v2/product/catalog/catalogs' => [[['_route' => 'ibexa.product_catalog.rest.catalogs.create', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\CatalogController::createCatalogAction'], null, ['POST' => 0], null, false, false, null]],
        '/api/ibexa/v2/product/catalog/currencies' => [
            [['_route' => 'ibexa.product_catalog.rest.currencies', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\CurrencyController::listCurrenciesAction'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'ibexa.product_catalog.rest.currencies.create', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\CurrencyController::createCurrencyAction'], null, ['POST' => 0], null, false, false, null],
        ],
        '/api/ibexa/v2/product/catalog/customer_groups' => [
            [['_route' => 'ibexa.product_catalog.rest.customer_groups.list', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\CustomerGroupController::listCustomerGroupsAction'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'ibexa.product_catalog.rest.customer_group.create', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\CustomerGroupController::createCustomerGroupAction'], null, ['POST' => 0], null, false, false, null],
        ],
        '/api/ibexa/v2/product/catalog/product_types' => [
            [['_route' => 'ibexa.product_catalog.rest.product_types', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\ProductTypeController::listProductTypesAction'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'ibexa.product_catalog.rest.product_types.create', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\ProductTypeController::createProductTypeAction'], null, ['POST' => 0], null, false, false, null],
        ],
        '/api/ibexa/v2/product/catalog/products' => [[['_route' => 'ibexa.product_catalog.rest.products', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\ProductController::listProductsAction'], null, ['GET' => 0], null, false, false, null]],
        '/api/ibexa/v2/product/catalog/regions' => [[['_route' => 'ibexa.product_catalog.rest.regions', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\RegionController::listRegionsAction'], null, ['GET' => 0], null, false, false, null]],
        '/engage' => [[['_route' => 'ibexa.qualifio.index', '_controller' => 'Ibexa\\Bundle\\ConnectorQualifio\\Controller\\QualifioController::promoteQualifio'], null, null, null, false, false, null]],
        '/api/ibexa/v2' => [
            [['_route' => 'ibexa.rest.load_root_resource', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Root:loadRootResource'], null, ['GET' => 0], null, true, false, null],
            [['_route' => 'ibexa.rest.options.', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET'], null, ['OPTIONS' => 0], null, true, false, null],
        ],
        '/api/ibexa/v2/content/sections' => [
            [['_route' => 'ibexa.rest.list_sections', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Section:listSections'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.create_section', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Section:createSection'], null, ['POST' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.options.content_sections', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET,POST'], null, ['OPTIONS' => 0], null, false, false, null],
        ],
        '/api/ibexa/v2/content/objects' => [
            [['_route' => 'ibexa.rest.redirect_content', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Content:redirectContent'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.create_content', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Content:createContent'], null, ['POST' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.options.content_objects', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET,POST'], null, ['OPTIONS' => 0], null, false, false, null],
        ],
        '/api/ibexa/v2/content/views' => [
            [['_route' => 'ibexa.rest.create_content_view', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Content:createView'], null, ['POST' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.options.content_views', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'POST'], null, ['OPTIONS' => 0], null, false, false, null],
        ],
        '/api/ibexa/v2/views' => [
            [['_route' => 'ibexa.rest.views.create', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Views:createView'], null, ['POST' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.views.list', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Views:listView'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.options.views', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'POST,GET'], null, ['OPTIONS' => 0], null, false, false, null],
        ],
        '/api/ibexa/v2/content/objectstategroups' => [
            [['_route' => 'ibexa.rest.load_object_state_groups', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\ObjectState:loadObjectStateGroups'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.create_object_state_group', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\ObjectState:createObjectStateGroup'], null, ['POST' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.options.content_objectstategroups', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET,POST'], null, ['OPTIONS' => 0], null, false, false, null],
        ],
        '/api/ibexa/v2/languages' => [
            [['_route' => 'ibexa.rest.languages.list', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Language::listLanguages'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.options.languages', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET'], null, ['OPTIONS' => 0], null, false, false, null],
        ],
        '/api/ibexa/v2/content/locations' => [
            [['_route' => 'ibexa.rest.redirect_location', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Location:redirectLocation'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.options.content_locations', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET'], null, ['OPTIONS' => 0], null, false, false, null],
        ],
        '/api/ibexa/v2/content/typegroups' => [
            [['_route' => 'ibexa.rest.load_content_type_group_list', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\ContentType:loadContentTypeGroupList'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.create_content_type_group', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\ContentType:createContentTypeGroup'], null, ['POST' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.options.content_typegroups', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET,POST'], null, ['OPTIONS' => 0], null, false, false, null],
        ],
        '/api/ibexa/v2/content/types' => [
            [['_route' => 'ibexa.rest.list_content_types', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\ContentType:listContentTypes'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.options.content_types', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET'], null, ['OPTIONS' => 0], null, false, false, null],
        ],
        '/api/ibexa/v2/content/trash' => [
            [['_route' => 'ibexa.rest.load_trash_items', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Trash:loadTrashItems'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.empty_trash', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Trash:emptyTrash'], null, ['DELETE' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.options.content_trash', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET,DELETE'], null, ['OPTIONS' => 0], null, false, false, null],
        ],
        '/api/ibexa/v2/content/urlwildcards' => [
            [['_route' => 'ibexa.rest.list_url_wildcards', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\URLWildcard:listURLWildcards'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.create_url_wildcard', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\URLWildcard:createURLWildcard'], null, ['POST' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.options.content_urlwildcards', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET,POST'], null, ['OPTIONS' => 0], null, false, false, null],
        ],
        '/api/ibexa/v2/user/policies' => [
            [['_route' => 'ibexa.rest.list_policies_for_user', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Role:listPoliciesForUser'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.options.user_policies', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET'], null, ['OPTIONS' => 0], null, false, false, null],
        ],
        '/api/ibexa/v2/user/roles' => [
            [['_route' => 'ibexa.rest.list_roles', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Role:listRoles'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.create_role', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Role:createRole'], null, ['POST' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.options.user_roles', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET,POST'], null, ['OPTIONS' => 0], null, false, false, null],
        ],
        '/api/ibexa/v2/user/users' => [
            [['_route' => 'ibexa.rest.verify_users', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\User:verifyUsers'], null, ['HEAD' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.load_users', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\User:loadUsers'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.options.user_users', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'HEAD,GET'], null, ['OPTIONS' => 0], null, false, false, null],
        ],
        '/api/ibexa/v2/user/current' => [
            [['_route' => 'ibexa.rest.current_user', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\User::redirectToCurrentUser'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.options.user_current', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET'], null, ['OPTIONS' => 0], null, false, false, null],
        ],
        '/api/ibexa/v2/user/groups' => [
            [['_route' => 'ibexa.rest.load_user_groups', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\User:loadUserGroups'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.options.user_groups', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET'], null, ['OPTIONS' => 0], null, false, false, null],
        ],
        '/api/ibexa/v2/user/groups/root' => [
            [['_route' => 'ibexa.rest.load_root_user_group', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\User:loadRootUserGroup'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.options.user_groups_root', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET'], null, ['OPTIONS' => 0], null, false, false, null],
        ],
        '/api/ibexa/v2/user/groups/subgroups' => [
            [['_route' => 'ibexa.rest.create_root_user_group', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\User:createUserGroup'], null, ['POST' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.options.user_groups_subgroups', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'POST'], null, ['OPTIONS' => 0], null, false, false, null],
        ],
        '/api/ibexa/v2/user/sessions' => [
            [['_route' => 'ibexa.rest.create_session', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\SessionController:createSessionAction', 'csrf_protection' => false], null, ['POST' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.options.user_sessions', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'csrf_protection' => false, 'allowedMethods' => 'POST'], null, ['OPTIONS' => 0], null, false, false, null],
        ],
        '/api/ibexa/v2/user/sessions/current' => [[['_route' => 'ibexa.rest.check_session', 'csrf_protection' => false, '_controller' => 'Ibexa\\Rest\\Server\\Controller\\SessionController::checkSessionAction'], null, ['GET' => 0], null, false, false, null]],
        '/api/ibexa/v2/content/urlaliases' => [
            [['_route' => 'ibexa.rest.list_global_url_aliases', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\URLAlias:listGlobalURLAliases'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.create_url_alias', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\URLAlias:createURLAlias'], null, ['POST' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.options.content_urlaliases', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET,POST'], null, ['OPTIONS' => 0], null, false, false, null],
        ],
        '/api/ibexa/v2/services/countries' => [
            [['_route' => 'ibexa.rest.load_country_list', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Services:loadCountryList'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.options.services_countries', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET'], null, ['OPTIONS' => 0], null, false, false, null],
        ],
        '/api/ibexa/v2/bookmark' => [
            [['_route' => 'ibexa.rest.load_bookmarks', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Bookmark:loadBookmarks'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.options.bookmark', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET'], null, ['OPTIONS' => 0], null, false, false, null],
        ],
        '/api/ibexa/v2/user/token/jwt' => [
            [['_route' => 'ibexa.rest.create_token', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\JWT::createToken'], null, ['POST' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.options.user_token_jwt', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'POST'], null, ['OPTIONS' => 0], null, false, false, null],
        ],
        '/content/schedule-hide' => [[['_route' => 'ibexa.scheduler.content.schedule.hide', '_controller' => 'Ibexa\\Bundle\\Scheduler\\Controller\\DateBasedContentController::scheduleHideAction'], null, ['POST' => 0], null, false, false, null]],
        '/content/schedule-hide/cancel' => [[['_route' => 'ibexa.scheduler.content.schedule.hide.cancel', '_controller' => 'Ibexa\\Bundle\\Scheduler\\Controller\\DateBasedContentController::scheduleHideCancelAction'], null, ['POST' => 0], null, false, false, null]],
        '/search' => [[['_route' => 'ibexa.search', '_controller' => 'Ibexa\\Bundle\\Search\\Controller\\SearchController::searchAction'], null, ['GET' => 0], null, false, false, null]],
        '/suggestion' => [[['_route' => 'ibexa.search.suggestion', '_controller' => 'Ibexa\\Bundle\\Search\\Controller\\SuggestionController::suggestAction'], null, ['GET' => 0], null, false, false, null]],
        '/segmentation/group/list' => [[['_route' => 'ibexa.segmentation.group.list', '_controller' => 'Ibexa\\Bundle\\Segmentation\\Controller\\SegmentController::groupListAction'], null, null, null, false, false, null]],
        '/segmentation/group/create' => [[['_route' => 'ibexa.segmentation.group.create', '_controller' => 'Ibexa\\Bundle\\Segmentation\\Controller\\SegmentController::groupCreateAction'], null, ['POST' => 0], null, false, false, null]],
        '/segmentation/group/delete' => [[['_route' => 'ibexa.segmentation.group.delete', '_controller' => 'Ibexa\\Bundle\\Segmentation\\Controller\\SegmentController::groupDeleteAction'], null, ['POST' => 0], null, false, false, null]],
        '/segmentation/group/bulk_delete' => [[['_route' => 'ibexa.segmentation.group.bulk_delete', '_controller' => 'Ibexa\\Bundle\\Segmentation\\Controller\\SegmentController::segmentGroupBulkDeleteAction'], null, ['POST' => 0], null, false, false, null]],
        '/segmentation/segment/create' => [[['_route' => 'ibexa.segmentation.segment.create', '_controller' => 'Ibexa\\Bundle\\Segmentation\\Controller\\SegmentController::segmentCreateAction'], null, ['POST' => 0], null, false, false, null]],
        '/segmentation/segment/bulk_delete' => [[['_route' => 'ibexa.segmentation.segment.bulk_delete', '_controller' => 'Ibexa\\Bundle\\Segmentation\\Controller\\SegmentController::segmentBulkDeleteAction'], null, ['POST' => 0], null, false, false, null]],
        '/api/ibexa/v2/segment_groups' => [[['_route' => 'ibexa.segmentation.rest.segment_groups.list', '_controller' => 'Ibexa\\Bundle\\Segmentation\\Controller\\REST\\SegmentGroup\\SegmentGroupListController::createView'], null, ['GET' => 0], null, false, false, null]],
        '/site-context/no-preview-available' => [[['_route' => 'ibexa.site_context.no_preview_available', '_controller' => 'Ibexa\\Bundle\\SiteContext\\Controller\\LocationPreviewController::noPreviewAvailableAction', 'siteaccess_group_whitelist' => 'admin_group'], null, null, null, false, false, null]],
        '/site/create' => [[['_route' => 'ibexa.site_factory.create', '_controller' => 'Ibexa\\Bundle\\SiteFactory\\Controller\\SiteController::createAction'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
        '/site/delete' => [[['_route' => 'ibexa.site_factory.delete', '_controller' => 'Ibexa\\Bundle\\SiteFactory\\Controller\\SiteController::deleteAction'], null, ['POST' => 0], null, false, false, null]],
        '/site/bulk/delete' => [[['_route' => 'ibexa.site_factory.bulk_delete', '_controller' => 'Ibexa\\Bundle\\SiteFactory\\Controller\\SiteController::bulkDeleteAction'], null, ['POST' => 0], null, false, false, null]],
        '/systeminfo' => [[['_route' => 'ibexa.systeminfo', '_controller' => 'Ibexa\\Bundle\\SystemInfo\\Controller\\SystemInfoController::infoAction'], null, null, null, false, false, null]],
        '/systeminfo/phpinfo' => [[['_route' => 'ibexa.systeminfo.php', '_controller' => 'Ibexa\\Bundle\\SystemInfo\\Controller\\SystemInfoController::phpinfoAction'], null, null, null, false, false, null]],
        '/user/change-password' => [[['_route' => 'ibexa.user_profile.change_password', '_controller' => 'Ibexa\\Bundle\\User\\Controller\\PasswordChangeController::userPasswordChangeAction'], null, null, null, false, false, null]],
        '/user/forgot-password' => [[['_route' => 'ibexa.user.forgot_password', '_controller' => 'Ibexa\\Bundle\\User\\Controller\\PasswordResetController::userForgotPasswordAction'], null, null, null, false, false, null]],
        '/user/forgot-password/migration' => [[['_route' => 'ibexa.user.forgot_password.migration', '_controller' => 'Ibexa\\Bundle\\User\\Controller\\PasswordResetController::userForgotPasswordAction', 'reason' => 'migration'], null, null, null, false, false, null]],
        '/user/forgot-password/login' => [[['_route' => 'ibexa.user.forgot_password.login', '_controller' => 'Ibexa\\Bundle\\User\\Controller\\PasswordResetController::userForgotPasswordLoginAction'], null, null, null, false, false, null]],
        '/register' => [[['_route' => 'ibexa.user.register', '_controller' => 'Ibexa\\Bundle\\User\\Controller\\UserRegisterController::registerAction'], null, null, null, false, false, null]],
        '/user/register' => [[['_route' => 'ibexa.user.user_register', '_controller' => 'Ibexa\\Bundle\\User\\Controller\\UserRegisterController::registerAction'], null, null, null, false, false, null]],
        '/register-confirm' => [[['_route' => 'ibexa.user.register_confirmation', '_controller' => 'Ibexa\\Bundle\\User\\Controller\\UserRegisterController::registerConfirmAction'], null, null, null, false, false, null]],
        '/user/register-confirm' => [[['_route' => 'ibexa.user.user_register_confirmation', '_controller' => 'Ibexa\\Bundle\\User\\Controller\\UserRegisterController::registerConfirmAction'], null, null, null, false, false, null]],
        '/user/invite' => [[['_route' => 'ibexa.user.invite_user', '_controller' => 'Ibexa\\Bundle\\User\\Controller\\UserInvitationController::inviteUser'], null, ['POST' => 0, 'GET' => 1], null, false, false, null]],
        '/user/default_profile_image/initials.svg' => [[['_route' => 'ibexa.user.default_profile_image.initials', '_controller' => 'Ibexa\\Bundle\\User\\Controller\\DefaultProfileImageController::initialsAction'], null, null, null, false, false, null]],
        '/workflow/list' => [[['_route' => 'ibexa.workflow.list', '_controller' => 'Ibexa\\Bundle\\Workflow\\Controller\\WorkflowController::listAction'], null, null, null, false, false, null]],
        '/_profiler' => [[['_route' => '_profiler_home', '_controller' => 'web_profiler.controller.profiler::homeAction'], null, null, null, true, false, null]],
        '/_profiler/search' => [[['_route' => '_profiler_search', '_controller' => 'web_profiler.controller.profiler::searchAction'], null, null, null, false, false, null]],
        '/_profiler/search_bar' => [[['_route' => '_profiler_search_bar', '_controller' => 'web_profiler.controller.profiler::searchBarAction'], null, null, null, false, false, null]],
        '/_profiler/phpinfo' => [[['_route' => '_profiler_phpinfo', '_controller' => 'web_profiler.controller.profiler::phpinfoAction'], null, null, null, false, false, null]],
        '/_profiler/open' => [[['_route' => '_profiler_open_file', '_controller' => 'web_profiler.controller.profiler::openAction'], null, null, null, false, false, null]],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/graphiql/([^/]++)(*:25)'
                .'|/js/routing(?:\\.(js|json))?(*:59)'
                .'|/_(?'
                    .'|error/(\\d+)(?:\\.([^/]++))?(*:97)'
                    .'|wdt/([^/]++)(*:116)'
                    .'|profiler/([^/]++)(?'
                        .'|/(?'
                            .'|search/results(*:162)'
                            .'|router(*:176)'
                            .'|exception(?'
                                .'|(*:196)'
                                .'|\\.css(*:209)'
                            .')'
                        .')'
                        .'|(*:219)'
                    .')'
                .')'
                .'|/v(?'
                    .'|iew/(?'
                        .'|content/([^/]++)(?'
                            .'|/([^/]++)/([^/]++)/translation/([^/]++)(?:/([^/]++))?(*:313)'
                            .'|(?:/([^/]++)(?:/([^/]++)(?:/([^/]++))?)?)?(*:363)'
                        .')'
                        .'|asset/([^/]++)/([^/]++)(?:/([^/]++))?(*:409)'
                    .')'
                    .'|ersion(?'
                        .'|/(?'
                            .'|has\\-no\\-conflict/([^/]++)/([^/]++)(?:/([^/]++))?(*:480)'
                            .'|compar(?'
                                .'|e\\-split/([^/]++)/([^/]++)/([^/]++)(?:/([^/]++)(?:/([^/]++))?)?(*:560)'
                                .'|ison(?'
                                    .'|\\-unified/([^/]++)/([^/]++)/([^/]++)(?:/([^/]++)(?:/([^/]++))?)?(*:639)'
                                    .'|/([^/]++)/([^/]++)(?:/([^/]++))?(*:679)'
                                .')'
                            .')'
                            .'|side\\-by\\-side\\-comparison/([^/]++)/([^/]++)(?:/([^/]++))?(*:747)'
                        .')'
                        .'|\\-draft/has\\-no\\-conflict/([^/]++)/([^/]++)(?:/([^/]++))?(*:813)'
                    .')'
                .')'
                .'|/c(?'
                    .'|o(?'
                        .'|ntent(?'
                            .'|/(?'
                                .'|versionview/([^/]++)/([^/]++)/([^/]++)(?'
                                    .'|/site_access(?:/([^/]++))?(*:911)'
                                    .'|(*:919)'
                                .')'
                                .'|download/(?'
                                    .'|([^/]++)/([^/]++)/([^/]++)(*:966)'
                                    .'|(\\d+)/(\\d+)(*:985)'
                                .')'
                                .'|create/proxy/([^/]++)/([^/]++)/([^/]++)(*:1033)'
                                .'|([^/]++)/(?'
                                    .'|preview/([^/]++)(?:/([^/]++)(?:/([^/]++))?)?(*:1098)'
                                    .'|translate/(?'
                                        .'|proxy/([^/]++)(?:/([^/]++))?(*:1148)'
                                        .'|([^/]++)(?:/([^/]++))?(*:1179)'
                                    .')'
                                    .'|location/([^/]++)/translate/(?'
                                        .'|proxy/([^/]++)(?:/([^/]++))?(*:1248)'
                                        .'|([^/]++)(?:/([^/]++))?(*:1279)'
                                    .')'
                                    .'|check\\-edit\\-permission(?:/([^/]++))?(*:1326)'
                                .')'
                                .'|edit/(?'
                                    .'|draft/([^/]++)/([^/]++)(?'
                                        .'|/([^/]++)/([^/]++)/cancel(*:1395)'
                                        .'|(?:/([^/]++)(?:/([^/]++))?)?(*:1432)'
                                    .')'
                                    .'|on\\-the\\-fly/([^/]++)/([^/]++)/([^/]++)(?:/([^/]++))?(*:1495)'
                                .')'
                                .'|create/(?'
                                    .'|on\\-the\\-fly/([^/]++)/([^/]++)/([^/]++)(?'
                                        .'|(*:1557)'
                                        .'|/has\\-access(*:1578)'
                                    .')'
                                    .'|nodraft/([^/]++)/([^/]++)/([^/]++)(*:1622)'
                                    .'|draft(?:/([^/]++)(?:/([^/]++)(?:/([^/]++))?)?)?(*:1678)'
                                .')'
                                .'|preview/([^/]++)/([^/]++)/([^/]++)/site_access/([^/]++)(?:/([^/]++))?(*:1757)'
                            .')'
                            .'|typegroup/(?'
                                .'|(\\d+)(?:/(\\d+))?(*:1796)'
                                .'|(\\d+)/update(*:1817)'
                                .'|(\\d+)/delete(*:1838)'
                                .'|(\\d+)/contenttype/list(*:1869)'
                                .'|(\\d+)/contenttype/add(*:1899)'
                                .'|(\\d+)/contenttype/([^/]++)/edit(*:1939)'
                                .'|(\\d+)/contenttype/([^/]++)/copy(*:1979)'
                                .'|(\\d+)/contenttype/([^/]++)/update(?:/([^/]++)(?:/([^/]++))?)?(*:2049)'
                                .'|(\\d+)/contenttype/([^/]++)(*:2084)'
                                .'|(\\d+)/contenttype/([^/]++)(?:/([^/]++))?(*:2133)'
                                .'|(\\d+)/contenttype/([^/]++)/field_definition_form/([^/]++)(?:/([^/]++)(?:/([^/]++))?)?(*:2227)'
                            .')'
                            .'|_type/(\\d+)/bulk\\-delete(*:2261)'
                        .')'
                        .'|mpany/(?'
                            .'|de(?'
                                .'|tails/([^/]++)(*:2299)'
                                .'|activate/([^/]++)(*:2325)'
                            .')'
                            .'|activate/([^/]++)(*:2352)'
                            .'|edit/([^/]++)(?'
                                .'|(*:2377)'
                                .'|/(?'
                                    .'|b(?'
                                        .'|asic_information(*:2410)'
                                        .'|illing_address(*:2433)'
                                    .')'
                                    .'|contact(*:2450)'
                                .')'
                            .')'
                            .'|([^/]++)/(?'
                                .'|shipping_address/(?'
                                    .'|create(*:2499)'
                                    .'|([^/]++)/edit(*:2521)'
                                    .'|delete(*:2536)'
                                    .'|set\\-as\\-default(*:2561)'
                                .')'
                                .'|member/(?'
                                    .'|create(*:2587)'
                                    .'|([^/]++)/(?'
                                        .'|edit(*:2612)'
                                        .'|activate(*:2629)'
                                        .'|deactivate(*:2648)'
                                    .')'
                                .')'
                                .'|invite(?'
                                    .'|(*:2668)'
                                    .'|/re(?'
                                        .'|send(*:2687)'
                                        .'|invite(*:2702)'
                                    .')'
                                .')'
                            .')'
                        .')'
                    .')'
                    .'|u(?'
                        .'|stomer\\-(?'
                            .'|portal/(?'
                                .'|members/(?'
                                    .'|edit/([^/]++)(*:2765)'
                                    .'|activate/([^/]++)(*:2791)'
                                    .'|deactivate_member/([^/]++)(*:2826)'
                                .')'
                                .'|address/([^/]++)/edit(*:2857)'
                            .')'
                            .'|group/([^/]++)(?'
                                .'|(*:2884)'
                                .'|/(?'
                                    .'|delete(*:2903)'
                                    .'|update(?:/([^/]++)(?:/([^/]++))?)?(*:2946)'
                                    .'|translation/(?'
                                        .'|create(*:2976)'
                                        .'|delete(*:2991)'
                                    .')'
                                .')'
                            .')'
                        .')'
                        .'|rrencies/([^/]++)/update(*:3028)'
                    .')'
                    .'|atalog/(?'
                        .'|([^/]++)(?'
                            .'|(*:3059)'
                            .'|/(?'
                                .'|delete(*:3078)'
                                .'|update(*:3093)'
                            .')'
                        .')'
                        .'|copy(*:3108)'
                        .'|([^/]++)/trans(?'
                            .'|ition(*:3139)'
                            .'|lation/(?'
                                .'|create(*:3164)'
                                .'|delete(*:3179)'
                            .')'
                        .')'
                    .')'
                .')'
                .'|/s(?'
                    .'|e(?'
                        .'|ction/(?'
                            .'|view/([^/]++)(*:3223)'
                            .'|update(?:/([^/]++))?(*:3252)'
                            .'|delete/([^/]++)(*:3276)'
                            .'|assign\\-content/([^/]++)(*:3309)'
                        .')'
                        .'|gmentation/(?'
                            .'|group/(?'
                                .'|view/(\\d+)(*:3352)'
                                .'|update/(\\d+)(*:3373)'
                            .')'
                            .'|segment/update/(\\d+)(*:3403)'
                        .')'
                    .')'
                    .'|tate/(?'
                        .'|group/(?'
                            .'|update/([^/]++)(*:3446)'
                            .'|delete/([^/]++)(*:3470)'
                            .'|([^/]++)(*:3487)'
                        .')'
                        .'|state/(?'
                            .'|create/([^/]++)(*:3521)'
                            .'|([^/]++)(*:3538)'
                            .'|update/([^/]++)(*:3562)'
                            .'|delete/([^/]++)(*:3586)'
                            .'|bulk\\-delete/([^/]++)(*:3616)'
                        .')'
                        .'|contentstate/update/([^/]++)/group/([^/]++)(*:3669)'
                    .')'
                    .'|ubmission/(\\d+)/([^/]++)/download(*:3712)'
                    .'|ite(?'
                        .'|\\-context/(?'
                            .'|change/([^/]++)(*:3755)'
                            .'|location\\-preview/([^/]++)(*:3790)'
                            .'|toggle\\-fullscreen/([^/]++)(*:3826)'
                        .')'
                        .'|/(?'
                            .'|grid(?:/([^/]++))?(*:3858)'
                            .'|list(?:/([^/]++))?(*:3885)'
                            .'|(\\d+)/edit(*:3904)'
                            .'|(\\d+)(*:3918)'
                        .')'
                    .')'
                .')'
                .'|/l(?'
                    .'|anguage/(?'
                        .'|view/([^/]++)(*:3959)'
                        .'|edit(?:/([^/]++))?(*:3986)'
                        .'|delete/([^/]++)(*:4010)'
                    .')'
                    .'|inkmanagement/(?'
                        .'|edit/(\\d+)(*:4047)'
                        .'|view/(\\d+)(*:4066)'
                    .')'
                .')'
                .'|/r(?'
                    .'|ole/(?'
                        .'|(\\d+)(?:/(\\d+)(?:/(\\d+))?)?(*:4116)'
                        .'|(\\d+)/copy(*:4135)'
                        .'|(\\d+)/update(*:4156)'
                        .'|(\\d+)/delete(*:4177)'
                        .'|(\\d+)/policy/list(*:4203)'
                        .'|(\\d+)/policy/create(*:4231)'
                        .'|(\\d+)/policy/(\\d+)/update(*:4265)'
                        .'|(\\d+)/policy/create/(\\w+)/(\\w+)(*:4305)'
                        .'|(\\d+)/policy/(\\d+)(*:4332)'
                        .'|(\\d+)/policy/bulk\\-delete(*:4366)'
                        .'|([^/]++)/assignment(*:4394)'
                        .'|(\\d+)/assignment/create(*:4426)'
                        .'|([^/]++)/assignment/([^/]++)/delete(*:4470)'
                        .'|(\\d+)/assignment/bulk\\-delete(*:4508)'
                    .')'
                    .'|elation/([^/]++)(*:4534)'
                .')'
                .'|/u(?'
                    .'|ser/(?'
                        .'|invite/to\\-group/([^/]++)(*:4581)'
                        .'|profile/([^/]++)/view(*:4611)'
                        .'|create/(?'
                            .'|on\\-the\\-fly/([^/]++)/([^/]++)/([^/]++)(?'
                                .'|(*:4672)'
                                .'|/has\\-access(*:4693)'
                            .')'
                            .'|([^/]++)/([^/]++)/([^/]++)(*:4729)'
                        .')'
                        .'|edit/on\\-the\\-fly/([^/]++)/([^/]++)/([^/]++)/([^/]++)(*:4792)'
                        .'|update/([^/]++)/([^/]++)/([^/]++)(*:4834)'
                        .'|reset\\-password/([^/]++)(*:4867)'
                        .'|from\\-invite/register/([^/]++)(*:4906)'
                        .'|settings/(?'
                            .'|list(?:/(\\d+))?(*:4942)'
                            .'|update/(.+)(*:4962)'
                        .')'
                    .')'
                    .'|rl\\-wildcard/update/([^/]++)(*:5001)'
                .')'
                .'|/notification(?'
                    .'|s(?'
                        .'|(?:/(\\d+)(?:/(\\d+))?)?(*:5053)'
                        .'|/render/page(?:/(\\d+))?(*:5085)'
                    .')'
                    .'|/read/(\\d+)(*:5106)'
                .')'
                .'|/p(?'
                    .'|er(?'
                        .'|mission/limitation/language/content\\-(?'
                            .'|create/(\\d+)(*:5178)'
                            .'|edit/(\\d+)(*:5197)'
                            .'|read/(\\d+)(*:5216)'
                        .')'
                        .'|sonalization/([^/]++)/(?'
                            .'|dashboard(*:5260)'
                            .'|model(?'
                                .'|s(*:5278)'
                                .'|/(?'
                                    .'|([^/]++)(?'
                                        .'|(*:5302)'
                                        .'|/edit(*:5316)'
                                    .')'
                                    .'|attribute/([^/]++)/([^/]++)(?:/([^/]++)(?:/([^/]++))?)?(*:5381)'
                                .')'
                            .')'
                            .'|s(?'
                                .'|cenario(?'
                                    .'|s(*:5407)'
                                    .'|/(?'
                                        .'|create(*:5426)'
                                        .'|de(?'
                                            .'|tails/([^/]++)(*:5454)'
                                            .'|lete(*:5467)'
                                        .')'
                                        .'|preview/([^/]++)(*:5493)'
                                        .'|edit/([^/]++)(*:5515)'
                                    .')'
                                .')'
                                .'|earch/attributes(*:5542)'
                            .')'
                            .'|import(*:5558)'
                            .'|chart/data(*:5577)'
                            .'|re(?'
                                .'|port/recommendation\\-detailed(*:5620)'
                                .'|commendation/preview/([^/]++)(*:5658)'
                            .')'
                            .'|output\\-type/attributes/(?'
                                .'|item\\-type\\-id/(\\d+)(*:5715)'
                                .'|scenario/([a-zA-Z0-9\\_\\-\\/]+)(*:5753)'
                            .')'
                        .')'
                    .')'
                    .'|age/(?'
                        .'|edit(?:/(\\d+)(?:/([^/]++))?)?(*:5801)'
                        .'|create/([^/]++)/(\\d+)(?:/([^/]++)(?:/([^/]++))?)?(*:5859)'
                        .'|layout(?:/([^/]++)(?:/([^/]++))?)?(*:5902)'
                        .'|block/(?'
                            .'|configure/([^/]++)(?:/([^/]++))?(*:5952)'
                            .'|preview/siteaccess/([^/]++)(*:5988)'
                        .')'
                        .'|site/preview/([^/]++)/([^/]++)/([^/]++)/([^/]++)(*:6046)'
                    .')'
                    .'|roduct(?'
                        .'|/(?'
                            .'|asset/(?'
                                .'|create/([^/]++)(*:6093)'
                                .'|group/([^/]++)(*:6116)'
                                .'|delete/([^/]++)(*:6140)'
                            .')'
                            .'|create/([^/]++)/([^/]++)(*:6174)'
                            .'|edit/([^/]++)(?:/([^/]++)(?:/([^/]++))?)?(*:6224)'
                            .'|variant(?'
                                .'|/(?'
                                    .'|create/([^/]++)(*:6262)'
                                    .'|edit/([^/]++)(*:6284)'
                                .')'
                                .'|\\-generator/([^/]++)(*:6314)'
                            .')'
                            .'|delete/([^/]++)(*:6339)'
                            .'|translation/(?'
                                .'|create/([^/]++)(*:6378)'
                                .'|delete/([^/]++)(*:6402)'
                            .')'
                            .'|([^/]++)(?'
                                .'|(*:6423)'
                                .'|/(?'
                                    .'|price/(?'
                                        .'|create/([^/]++)(*:6460)'
                                        .'|update/([^/]++)(*:6484)'
                                        .'|bulk_delete(*:6504)'
                                    .')'
                                    .'|availability/(?'
                                        .'|create(*:6536)'
                                        .'|update(*:6551)'
                                    .')'
                                .')'
                            .')'
                        .')'
                        .'|\\-type/(?'
                            .'|view/([^/]++)(*:6587)'
                            .'|delete/([^/]++)(*:6611)'
                            .'|edit/([^/]++)(?:/([^/]++)(?:/([^/]++))?)?(*:6661)'
                            .'|translation/(?'
                                .'|create/([^/]++)(*:6700)'
                                .'|delete/([^/]++)(*:6724)'
                            .')'
                        .')'
                    .')'
                .')'
                .'|/a(?'
                    .'|p(?'
                        .'|i/(?'
                            .'|ibexa/v2/(?'
                                .'|l(?'
                                    .'|ocation/tree/(?'
                                        .'|load\\-subitems/(\\d+)(?:/([^/]++)(?:/([^/]++))?)?(*:6831)'
                                        .'|([^/]++)/extended\\-info(*:6863)'
                                    .')'
                                    .'|anguages/([^/]++)(?'
                                        .'|(*:6893)'
                                    .')'
                                .')'
                                .'|c(?'
                                    .'|o(?'
                                        .'|ntent(?'
                                            .'|typegroup/(?'
                                                .'|(\\d+)/contenttype/([^/]++)/add_field_definition(?:/([^/]++))?(*:6994)'
                                                .'|(\\d+)/contenttype/([^/]++)/remove_field_definition(*:7053)'
                                                .'|(\\d+)/contenttype/([^/]++)/reorder_field_definitions(*:7114)'
                                            .')'
                                            .'|/(?'
                                                .'|assets/images/([^/]++)/([^/]++)(*:7159)'
                                                .'|binary/images/(?'
                                                    .'|([^/]++)/([^/]++)/variations/([^/]++)(*:7222)'
                                                    .'|(\\d+-\\d+(?:-\\d+)?)/variations/([^/]++)(?'
                                                        .'|(*:7272)'
                                                    .')'
                                                .')'
                                                .'|objects(?'
                                                    .'|/(?'
                                                        .'|(\\d+)/versions/(\\d+)/fields/([^/]++)/query/results(*:7347)'
                                                        .'|(\\d+)(?'
                                                            .'|(*:7364)'
                                                        .')'
                                                        .'|([^/]++)/translations/([^/]++)(*:7404)'
                                                        .'|(\\d+)/relations(*:7428)'
                                                        .'|(\\d+)/versions(*:7451)'
                                                        .'|(\\d+)/versions/(\\d+)/relations(?'
                                                            .'|(*:7493)'
                                                        .')'
                                                        .'|(\\d+)/versions/(\\d+)/relations/(\\d+)(?'
                                                            .'|(*:7542)'
                                                        .')'
                                                        .'|(\\d+)/versions/(\\d+)(?'
                                                            .'|(*:7575)'
                                                        .')'
                                                        .'|(\\d+)/versions/(\\d+)/translations/([^/]++)(*:7627)'
                                                        .'|(\\d+)/versions/(\\d+)(?'
                                                            .'|(*:7659)'
                                                        .')'
                                                        .'|(\\d+)/currentversion(?'
                                                            .'|(*:7692)'
                                                        .')'
                                                        .'|(\\d+)/hide(*:7712)'
                                                        .'|(\\d+)/reveal(*:7733)'
                                                        .'|(\\d+)/objectstates(?'
                                                            .'|(*:7763)'
                                                        .')'
                                                        .'|(\\d+)/locations(?'
                                                            .'|(*:7791)'
                                                        .')'
                                                        .'|(\\d+)(*:7806)'
                                                        .'|([^/]++)/translations/([^/]++)(*:7845)'
                                                        .'|(\\d+)/relations(*:7869)'
                                                        .'|(\\d+)/versions(*:7892)'
                                                        .'|(\\d+)/versions/(\\d+)/relations(*:7931)'
                                                        .'|(\\d+)/versions/(\\d+)/relations/(\\d+)(*:7976)'
                                                        .'|(\\d+)/versions/(\\d+)/translations/([^/]++)(*:8027)'
                                                        .'|(\\d+)/versions/(\\d+)(*:8056)'
                                                        .'|(\\d+)/currentversion(*:8085)'
                                                        .'|(\\d+)/hide(*:8104)'
                                                        .'|(\\d+)/reveal(*:8125)'
                                                        .'|(\\d+)/objectstates(*:8152)'
                                                        .'|(\\d+)/locations(*:8176)'
                                                    .')'
                                                    .'|tategroups/(?'
                                                        .'|(\\d+)(?'
                                                            .'|(*:8208)'
                                                        .')'
                                                        .'|(\\d+)/objectstates(?'
                                                            .'|(*:8239)'
                                                        .')'
                                                        .'|(\\d+)/objectstates/(\\d+)(?'
                                                            .'|(*:8276)'
                                                        .')'
                                                        .'|(\\d+)(*:8291)'
                                                        .'|(\\d+)/objectstates(*:8318)'
                                                        .'|(\\d+)/objectstates/(\\d+)(*:8351)'
                                                    .')'
                                                .')'
                                                .'|sections/(\\d+)(?'
                                                    .'|(*:8379)'
                                                .')'
                                                .'|locations/(?'
                                                    .'|([0-9/]+)(?'
                                                        .'|(*:8414)'
                                                    .')'
                                                    .'|([0-9/]+)/children(*:8442)'
                                                    .'|([0-9/]+)/urlaliases(*:8471)'
                                                    .'|([0-9/]+)(*:8489)'
                                                    .'|([0-9/]+)/children(*:8516)'
                                                    .'|([0-9/]+)/urlaliases(*:8545)'
                                                .')'
                                                .'|t(?'
                                                    .'|ype(?'
                                                        .'|groups/(?'
                                                            .'|(\\d+)(?'
                                                                .'|(*:8583)'
                                                            .')'
                                                            .'|(\\d+)/types(?'
                                                                .'|(*:8607)'
                                                            .')'
                                                            .'|(\\d+)(*:8622)'
                                                            .'|(\\d+)/types(*:8642)'
                                                        .')'
                                                        .'|s/(?'
                                                            .'|(\\d+)(?'
                                                                .'|(*:8665)'
                                                            .')'
                                                            .'|(\\d+)/draft(*:8686)'
                                                            .'|(\\d+)/fieldDefinitions(*:8717)'
                                                            .'|(\\d+)/fieldDefinitions/(\\d+)(*:8754)'
                                                            .'|(\\d+)/fieldDefinition/(\\w+)(*:8790)'
                                                            .'|(\\d+)/draft(?'
                                                                .'|(*:8813)'
                                                            .')'
                                                            .'|(\\d+)/draft/fieldDefinitions(?'
                                                                .'|(*:8854)'
                                                            .')'
                                                            .'|(\\d+)/draft/fieldDefinitions/(\\d+)(?'
                                                                .'|(*:8901)'
                                                            .')'
                                                            .'|(\\d+)/groups(?'
                                                                .'|(*:8926)'
                                                            .')'
                                                            .'|(\\d+)/groups/(\\d+)(*:8954)'
                                                            .'|(\\d+)(*:8968)'
                                                            .'|(\\d+)/fieldDefinitions(*:8999)'
                                                            .'|(\\d+)/fieldDefinitions/(\\d+)(*:9036)'
                                                            .'|(\\d+)/fieldDefinition/(\\w+)(*:9072)'
                                                            .'|(\\d+)/draft(*:9092)'
                                                            .'|(\\d+)/draft/fieldDefinitions(*:9129)'
                                                            .'|(\\d+)/draft/fieldDefinitions/(\\d+)(*:9172)'
                                                            .'|(\\d+)/groups(*:9193)'
                                                            .'|(\\d+)/groups/(\\d+)(*:9220)'
                                                        .')'
                                                    .')'
                                                    .'|rash/(\\d+)(?'
                                                        .'|(*:9244)'
                                                    .')'
                                                .')'
                                                .'|url(?'
                                                    .'|wildcards/(\\d+)(?'
                                                        .'|(*:9279)'
                                                    .')'
                                                    .'|aliases/([^/]++)(?'
                                                        .'|(*:9308)'
                                                    .')'
                                                .')'
                                            .')'
                                        .')'
                                        .'|rporate/companies/(?'
                                            .'|(\\d+)(?'
                                                .'|(*:9350)'
                                            .')'
                                            .'|(\\d+)/members(?'
                                                .'|(*:9376)'
                                            .')'
                                            .'|(\\d+)/members/(\\d+)(?'
                                                .'|(*:9408)'
                                            .')'
                                        .')'
                                    .')'
                                    .'|alendar/event/([^/]++)(*:9442)'
                                .')'
                                .'|module/universal\\-discovery/(?'
                                    .'|location/([^/]++)(?'
                                        .'|(*:9503)'
                                        .'|/gridview(*:9521)'
                                    .')'
                                    .'|accordion/([^/]++)(?'
                                        .'|(*:9552)'
                                        .'|/gridview(*:9570)'
                                    .')'
                                .')'
                                .'|image/download/(\\d+(?:,\\d+)*)(*:9610)'
                                .'|p(?'
                                    .'|ersonalization/v1/(?'
                                        .'|content/(?'
                                            .'|list/([^/]++)(*:9668)'
                                            .'|id/(\\d+)(*:9685)'
                                            .'|remote\\-id/([a-zA-Z0-9\\_\\-\\/]+)(*:9725)'
                                        .')'
                                        .'|export/download/([a-zA-Z0-9\\_\\-\\/]+)(*:9771)'
                                        .'|product_variant/(?'
                                            .'|code/([^/]++)(*:9812)'
                                            .'|list/([^/]++)(*:9834)'
                                        .')'
                                    .')'
                                    .'|roduct/catalog/(?'
                                        .'|c(?'
                                            .'|atalogs/(?'
                                                .'|([^/]++)(?'
                                                    .'|/products/view(*:9903)'
                                                    .'|(*:9912)'
                                                .')'
                                                .'|copy/([^/]++)(*:9935)'
                                            .')'
                                            .'|u(?'
                                                .'|rrencies/(\\d+)(?'
                                                    .'|(*:9966)'
                                                .')'
                                                .'|stomer_groups/(?'
                                                    .'|(\\d+)(*:9998)'
                                                    .'|([^/]++)(*:10015)'
                                                    .'|(\\d+)(?'
                                                        .'|(*:10033)'
                                                    .')'
                                                .')'
                                            .')'
                                        .')'
                                        .'|product(?'
                                            .'|_(?'
                                                .'|variant(?'
                                                    .'|s/(?'
                                                        .'|view/([^/]++)(*:10089)'
                                                        .'|([^/]++)(*:10107)'
                                                        .'|generate/([^/]++)(*:10134)'
                                                        .'|([^/]++)(?'
                                                            .'|(*:10155)'
                                                        .')'
                                                    .')'
                                                    .'|/([^/]++)(*:10176)'
                                                .')'
                                                .'|types/(?'
                                                    .'|([^/]++)(?'
                                                        .'|(*:10207)'
                                                    .')'
                                                    .'|is_used/([^/]++)(*:10234)'
                                                .')'
                                            .')'
                                            .'|s/([^/]++)(?'
                                                .'|/prices(?'
                                                    .'|(*:10269)'
                                                    .'|/([^/]++)(?'
                                                        .'|/customer\\-group/([^/]++)(*:10316)'
                                                        .'|(*:10326)'
                                                        .'|(*:10336)'
                                                    .')'
                                                .')'
                                                .'|(*:10348)'
                                                .'|(*:10358)'
                                                .'|(*:10368)'
                                                .'|(*:10378)'
                                            .')'
                                        .')'
                                        .'|attribute(?'
                                            .'|_(?'
                                                .'|groups/(?'
                                                    .'|([^/]++)(?'
                                                        .'|(*:10427)'
                                                    .')'
                                                    .'|translation/([^/]++)/([^/]++)(*:10467)'
                                                .')'
                                                .'|types/([^/]++)(*:10492)'
                                            .')'
                                            .'|s/(?'
                                                .'|([^/]++)(?'
                                                    .'|(*:10519)'
                                                    .'|/([^/]++)(*:10538)'
                                                    .'|(*:10548)'
                                                .')'
                                                .'|translation/([^/]++)/([^/]++)(*:10588)'
                                            .')'
                                        .')'
                                        .'|regions/([^/]++)(*:10616)'
                                        .'|vat/([^/]++)(?'
                                            .'|(*:10641)'
                                            .'|/([^/]++)(*:10660)'
                                        .')'
                                    .')'
                                .')'
                                .'|views/([^/]++)(?'
                                    .'|(*:10690)'
                                    .'|/results(?'
                                        .'|(*:10711)'
                                    .')'
                                    .'|(*:10722)'
                                .')'
                                .'|user/(?'
                                    .'|roles/(?'
                                        .'|(\\d+)(?'
                                            .'|(*:10758)'
                                        .')'
                                        .'|(\\d+)/draft(*:10780)'
                                        .'|(\\d+)(*:10795)'
                                        .'|(\\d+)/draft(?'
                                            .'|(*:10819)'
                                        .')'
                                        .'|(\\d+)(*:10835)'
                                        .'|(\\d+)/draft(*:10856)'
                                        .'|(\\d+)/policies(?'
                                            .'|(*:10883)'
                                        .')'
                                        .'|(\\d+)/policies/(\\d+)(?'
                                            .'|(*:10917)'
                                        .')'
                                        .'|(\\d+)(*:10933)'
                                        .'|(\\d+)/draft(*:10954)'
                                        .'|(\\d+)/policies(*:10978)'
                                        .'|(\\d+)/policies/(\\d+)(*:11008)'
                                    .')'
                                    .'|users/(?'
                                        .'|(\\d+)(?'
                                            .'|(*:11036)'
                                        .')'
                                        .'|(\\d+)/groups(?'
                                            .'|(*:11062)'
                                        .')'
                                        .'|(\\d+)/groups/(\\d+)(*:11091)'
                                        .'|(\\d+)/drafts(*:11113)'
                                        .'|(\\d+)/roles(?'
                                            .'|(*:11137)'
                                        .')'
                                        .'|(\\d+)/roles/(\\d+)(?'
                                            .'|(*:11168)'
                                        .')'
                                        .'|(\\d+)(*:11184)'
                                        .'|(\\d+)/groups(*:11206)'
                                        .'|(\\d+)/groups/(\\d+)(*:11234)'
                                        .'|(\\d+)/drafts(*:11256)'
                                        .'|(\\d+)/roles(*:11277)'
                                        .'|(\\d+)/roles/(\\d+)(*:11304)'
                                        .'|(\\d+)/segments(?'
                                            .'|(*:11331)'
                                        .')'
                                        .'|(\\d+)/segments/([^/]++)(*:11365)'
                                    .')'
                                    .'|groups/(?'
                                        .'|([0-9/]+)(?'
                                            .'|(*:11398)'
                                        .')'
                                        .'|([0-9/]+)/subgroups(?'
                                            .'|(*:11431)'
                                        .')'
                                        .'|([0-9/]+)/users(?'
                                            .'|(*:11460)'
                                        .')'
                                        .'|([0-9/]+)/roles(?'
                                            .'|(*:11489)'
                                        .')'
                                        .'|([0-9/]+)/roles/(\\d+)(?'
                                            .'|(*:11524)'
                                        .')'
                                        .'|([0-9/]+)(*:11544)'
                                        .'|([0-9/]+)/subgroups(*:11573)'
                                        .'|([0-9/]+)/users(*:11598)'
                                        .'|([0-9/]+)/roles(*:11623)'
                                        .'|([0-9/]+)/roles/(\\d+)(*:11654)'
                                    .')'
                                    .'|sessions/(?'
                                        .'|([^/]++)(?'
                                            .'|(*:11688)'
                                            .'|/refresh(*:11706)'
                                        .')'
                                        .'|current(*:11724)'
                                        .'|([^/]++)(?'
                                            .'|(*:11745)'
                                            .'|/refresh(*:11763)'
                                        .')'
                                    .')'
                                .')'
                                .'|bookmark/([0-9]+)(?'
                                    .'|(*:11796)'
                                .')'
                                .'|segment(?'
                                    .'|s/([^/]++)(*:11827)'
                                    .'|_groups/([^/]++)(?'
                                        .'|/segments(*:11865)'
                                        .'|(*:11875)'
                                    .')'
                                .')'
                                .'|taxonomy/(?'
                                    .'|([A-Za-z0-9_/-]+)/entries(*:11924)'
                                    .'|([A-Za-z0-9_/-]+)/entries/move(*:11964)'
                                    .'|([A-Za-z0-9_/-]+)/entry/(\\d+)(*:12003)'
                                    .'|([A-Za-z0-9_/-]+)/entry/identifier/([A-Za-z0-9_/-]+)(*:12065)'
                                    .'|([A-Za-z0-9_/-]+)/entry/content\\-id/(\\d+)(*:12116)'
                                    .'|([A-Za-z0-9_/-]+)/entry\\-assignments/assign\\-to\\-content(*:12182)'
                                    .'|([A-Za-z0-9_/-]+)/entry\\-assignments/content\\-id/(\\d+)(*:12246)'
                                    .'|([A-Za-z0-9_/-]+)/entry\\-assignments/unassign\\-from\\-content(*:12316)'
                                    .'|([A-Za-z0-9_/-]+)/entry\\-assignment/(\\d+)(*:12367)'
                                .')'
                            .')'
                            .'|datebasedpublisher/v1/content/(?'
                                .'|objects/(?'
                                    .'|(\\d+)/versions/(\\d+)/schedule/(\\d+)(?'
                                        .'|(*:12461)'
                                    .')'
                                    .'|(\\d+)/versions/(\\d+)(*:12492)'
                                    .'|(\\d+)/versions/(\\d+)/schedule(*:12531)'
                                    .'|(\\d+)/schedule(?:/(\\d+)(?:/(\\d+))?)?(*:12577)'
                                .')'
                                .'|schedule(?'
                                    .'|(?:/(\\d+)(?:/(\\d+))?)?(*:12621)'
                                    .'|/user(?:/(\\d+)(?:/(\\d+))?)?(*:12658)'
                                .')'
                            .')'
                        .')'
                        .'|plication/(?'
                            .'|details/([^/]++)(*:12700)'
                            .'|edit/([^/]++)(?'
                                .'|(*:12726)'
                                .'|/internal(*:12745)'
                            .')'
                            .'|([^/]++)/workflow/([^/]++)(*:12782)'
                        .')'
                    .')'
                    .'|ddress/form/([^/]++)/([^/]++)/([^/]++)/(?'
                        .'|create/([^/]++)/name/([^/]++)/country/([^/]++)(*:12882)'
                        .'|update/([^/]++)/name/([^/]++)/country/([^/]++)(*:12938)'
                    .')'
                    .'|ttribute\\-(?'
                        .'|definition/(?'
                            .'|create/([^/]++)/([^/]++)(?:/([^/]++))?(*:13014)'
                            .'|([^/]++)/update(?:/([^/]++)(?:/([^/]++))?)?(*:13067)'
                            .'|delete/([^/]++)(*:13092)'
                            .'|([^/]++)(*:13110)'
                        .')'
                        .'|group/([^/]++)(?'
                            .'|/update(?:/([^/]++)(?:/([^/]++))?)?(*:13173)'
                            .'|(*:13183)'
                        .')'
                    .')'
                .')'
                .'|/dashboard/change\\-active/([^/]++)(*:13230)'
                .'|/block/render/(\\w+)/(\\d+)/(\\d+)/(\\w+)/([^/]++)(*:13286)'
                .'|/f(?'
                    .'|orm/(?'
                        .'|preview/([^/]++)/([^/]++)(*:13333)'
                        .'|field/(?'
                            .'|request\\-configuration\\-form/([^/]++)(*:13389)'
                            .'|configure/([^/]++)/([^/]++)(*:13426)'
                        .')'
                        .'|captcha/get\\-url/([^/]++)(*:13462)'
                    .')'
                    .'|rom\\-invite/register/([^/]++)(*:13502)'
                .')'
                .'|/image\\-editor/(?'
                    .'|update/([^/]++)(?:/([^/]++))?(*:13560)'
                    .'|create\\-from/([^/]++)(?:/([^/]++))?(*:13605)'
                    .'|base64/([^/]++)/([^/]++)(?:/([^/]++)(?:/([^/]++))?)?(*:13667)'
                .')'
                .'|/oauth2/c(?'
                    .'|onnect/([^/]++)(*:13705)'
                    .'|heck/([^/]++)(*:13728)'
                .')'
                .'|/t(?'
                    .'|axonomy/(?'
                        .'|([A-Za-z0-9_/-]+)/entry/create(*:13785)'
                        .'|([A-Za-z0-9_/-]+)/entry/delete(*:13825)'
                        .'|([A-Za-z0-9_/-]+)/entry/move(*:13863)'
                        .'|([A-Za-z0-9_/-]+)/entry/assign(*:13903)'
                        .'|([A-Za-z0-9_/-]+)/entry/unassign(*:13945)'
                        .'|([^/]++)/tree/(?'
                            .'|root(*:13976)'
                            .'|subtree(*:13993)'
                        .')'
                        .'|([A-Za-z0-9_/-]+)/tree/(\\d+)(*:14032)'
                        .'|([A-Za-z0-9_/-]+)/tree/search(*:14071)'
                    .')'
                    .'|ranslations(?:/([\\w]+)(?:\\.(js|json))?)?(*:14122)'
                .')'
                .'|/workflow/(?'
                    .'|(.+)/content/(\\d+)/(\\d+)/transition/list(*:14186)'
                    .'|view/([^/]++)(*:14209)'
                    .'|unlock/(?'
                        .'|(\\d+)/(\\d+)(*:14240)'
                        .'|(\\d+)/(\\d+)/ask/(\\d+)(*:14271)'
                    .')'
                    .'|(.+)/transition/(.+)/reviewers\\-suggest/content\\-create/content\\-type/(.+)/language/(.+)/location/(\\d+)(*:14385)'
                    .'|(.+)/transition/(.+)/reviewers\\-suggest/content\\-edit/content/(\\d+)/version/(\\d+)/location/(\\d+)(*:14491)'
                .')'
                .'|/media/cache/resolve/(?'
                    .'|([A-z0-9_-]*)/rc/([^/]++)/(.+)(*:14556)'
                    .'|([A-z0-9_-]*)/(.+)(*:14584)'
                .')'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        25 => [[['_route' => 'overblog_graphiql_endpoint_multiple', '_controller' => 'Overblog\\GraphiQLBundle\\Controller\\GraphiQLController::indexAction'], ['schemaName'], null, null, false, true, null]],
        59 => [[['_route' => 'fos_js_routing_js', '_controller' => 'fos_js_routing.controller::indexAction', '_format' => 'js'], ['_format'], ['GET' => 0], null, false, true, null]],
        97 => [[['_route' => '_preview_error', '_controller' => 'error_controller::preview', '_format' => 'html'], ['code', '_format'], null, null, false, true, null]],
        116 => [[['_route' => '_wdt', '_controller' => 'web_profiler.controller.profiler::toolbarAction'], ['token'], null, null, false, true, null]],
        162 => [[['_route' => '_profiler_search_results', '_controller' => 'web_profiler.controller.profiler::searchResultsAction'], ['token'], null, null, false, false, null]],
        176 => [[['_route' => '_profiler_router', '_controller' => 'web_profiler.controller.router::panelAction'], ['token'], null, null, false, false, null]],
        196 => [[['_route' => '_profiler_exception', '_controller' => 'web_profiler.controller.exception_panel::body'], ['token'], null, null, false, false, null]],
        209 => [[['_route' => '_profiler_exception_css', '_controller' => 'web_profiler.controller.exception_panel::stylesheet'], ['token'], null, null, false, false, null]],
        219 => [[['_route' => '_profiler', '_controller' => 'web_profiler.controller.profiler::panelAction'], ['token'], null, null, false, true, null]],
        313 => [[['_route' => 'ibexa.content.translation.view', '_controller' => 'ibexa_content::viewAction', 'viewType' => 'full', 'locationId' => null, 'layout' => true], ['contentId', 'viewType', 'layout', 'languageCode', 'locationId'], null, null, false, true, null]],
        363 => [[['_route' => 'ibexa.content.view', '_controller' => 'ibexa_content::viewAction', 'viewType' => 'full', 'locationId' => null, 'layout' => true], ['contentId', 'viewType', 'layout', 'locationId'], null, null, false, true, null]],
        409 => [[['_route' => 'ibexa.connector.dam.asset_view', 'transformation' => null, '_controller' => 'Ibexa\\Bundle\\Connector\\Dam\\Controller\\AssetViewController::viewAction'], ['destinationContentId', 'assetSource', 'transformation'], ['GET' => 0], null, false, true, null]],
        480 => [[['_route' => 'ibexa.version.has_no_conflict', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\Version\\VersionConflictController::versionHasNoConflictAction', 'languageCode' => null, 'siteaccess_group_whitelist' => 'admin_group'], ['contentId', 'versionNo', 'languageCode'], null, null, false, true, null]],
        560 => [[['_route' => 'ibexa.version.compare.split', '_controller' => 'Ibexa\\Bundle\\VersionComparison\\Controller\\VersionComparisonController::splitAction', 'versionNoB' => null, 'versionBLanguageCode' => null], ['contentInfoId', 'versionNoA', 'versionALanguageCode', 'versionNoB', 'versionBLanguageCode'], null, null, false, true, null]],
        639 => [[['_route' => 'ibexa.version.compare.unified', '_controller' => 'Ibexa\\Bundle\\VersionComparison\\Controller\\VersionComparisonController::unifiedAction', 'versionNoB' => null, 'versionBLanguageCode' => null], ['contentInfoId', 'versionNoA', 'versionALanguageCode', 'versionNoB', 'versionBLanguageCode'], null, null, false, true, null]],
        679 => [[['_route' => 'ibexa.version.compare', '_controller' => 'Ibexa\\Bundle\\VersionComparison\\Controller\\VersionComparisonController::compareAction', 'versionNoB' => null], ['contentInfoId', 'versionNoA', 'versionNoB'], null, null, false, true, null]],
        747 => [[['_route' => 'ibexa.version.side_by_side_comparison', '_controller' => 'Ibexa\\Bundle\\VersionComparison\\Controller\\VersionComparisonController::sideBySideCompareAction', 'versionNoB' => null], ['contentInfoId', 'versionNoA', 'versionNoB'], null, null, false, true, null]],
        813 => [[['_route' => 'ibexa.version_draft.has_no_conflict', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\Content\\VersionDraftConflictController::draftHasNoConflictAction', 'locationId' => null, 'siteaccess_group_whitelist' => 'admin_group'], ['contentId', 'languageCode', 'locationId'], null, null, false, true, null]],
        911 => [[['_route' => 'ibexa.version.preview', 'siteAccessName' => null, '_controller' => 'ibexa.controller.content.preview:previewContentAction'], ['contentId', 'versionNo', 'language', 'siteAccessName'], ['GET' => 0], null, false, true, null]],
        919 => [[['_route' => 'ibexa.content.preview.default', '_controller' => 'ibexa.controller.content.preview:previewContentAction'], ['contentId', 'versionNo', 'language'], ['GET' => 0], null, false, true, null]],
        966 => [[['_route' => 'ibexa.content.download', '_controller' => 'Ibexa\\Core\\MVC\\Symfony\\Controller\\Content\\DownloadController:downloadBinaryFileAction'], ['contentId', 'fieldIdentifier', 'filename'], null, null, false, true, null]],
        985 => [[['_route' => 'ibexa.content.download.field_id', '_controller' => 'Ibexa\\Core\\MVC\\Symfony\\Controller\\Content\\DownloadController:downloadBinaryFileByIdAction'], ['contentId', 'fieldId'], null, null, false, true, null]],
        1033 => [[['_route' => 'ibexa.content.create.proxy', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\ContentController::proxyCreateAction', 'siteaccess_group_whitelist' => 'admin_group'], ['contentTypeIdentifier', 'languageCode', 'parentLocationId'], null, null, false, true, null]],
        1098 => [[['_route' => 'ibexa.content.preview', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\ContentController::previewAction', 'languageCode' => null, 'locationId' => null, 'siteaccess_group_whitelist' => 'admin_group'], ['contentId', 'versionNo', 'languageCode', 'locationId'], ['GET' => 0], null, false, true, null]],
        1148 => [[['_route' => 'ibexa.content.translate.proxy', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\ContentEditController::proxyTranslateAction', 'fromLanguageCode' => null, 'siteaccess_group_whitelist' => 'admin_group'], ['contentId', 'toLanguageCode', 'fromLanguageCode'], ['GET' => 0, 'POST' => 1], null, false, true, null]],
        1179 => [[['_route' => 'ibexa.content.translate', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\ContentEditController::translateAction', 'fromLanguageCode' => null, 'siteaccess_group_whitelist' => 'admin_group'], ['contentId', 'toLanguageCode', 'fromLanguageCode'], ['GET' => 0, 'POST' => 1], null, false, true, null]],
        1248 => [[['_route' => 'ibexa.content.translate_with_location.proxy', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\ContentEditController::proxyTranslateAction', 'fromLanguageCode' => null, 'siteaccess_group_whitelist' => 'admin_group'], ['contentId', 'locationId', 'toLanguageCode', 'fromLanguageCode'], ['GET' => 0, 'POST' => 1], null, false, true, null]],
        1279 => [[['_route' => 'ibexa.content.translate_with_location', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\ContentEditController::translateAction', 'fromLanguageCode' => null, 'siteaccess_group_whitelist' => 'admin_group'], ['contentId', 'locationId', 'toLanguageCode', 'fromLanguageCode'], ['GET' => 0, 'POST' => 1], null, false, true, null]],
        1326 => [[['_route' => 'ibexa.content.check_edit_permission', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\ContentController::checkEditPermissionAction', 'languageCode' => null, 'siteaccess_group_whitelist' => 'admin_group'], ['contentId', 'languageCode'], null, null, false, true, null]],
        1395 => [[['_route' => 'ibexa.content.draft.edit.cancel', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\ContentEditController::cancelEditVersionDraftAction', 'siteaccess_group_whitelist' => 'admin_group'], ['contentId', 'versionNo', 'languageCode', 'referrerLocationId'], null, null, false, false, null]],
        1432 => [[['_route' => 'ibexa.content.draft.edit', '_controller' => 'ibexa_content_edit:editVersionDraftAction', 'language' => null, 'locationId' => null], ['contentId', 'versionNo', 'language', 'locationId'], null, null, false, true, null]],
        1495 => [[['_route' => 'ibexa.content.on_the_fly.edit', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\ContentOnTheFlyController::editContentAction', 'locationId' => null, 'siteaccess_group_whitelist' => 'admin_group'], ['contentId', 'versionNo', 'languageCode', 'locationId'], ['GET' => 0, 'POST' => 1], null, false, true, null]],
        1557 => [[['_route' => 'ibexa.content.on_the_fly.create', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\ContentOnTheFlyController::createContentAction', 'siteaccess_group_whitelist' => 'admin_group'], ['contentTypeIdentifier', 'languageCode', 'locationId'], ['GET' => 0, 'POST' => 1], null, false, true, null]],
        1578 => [[['_route' => 'ibexa.content.on_the_fly.has_access', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\ContentOnTheFlyController::hasCreateAccessAction', 'siteaccess_group_whitelist' => 'admin_group'], ['contentTypeIdentifier', 'languageCode', 'locationId'], ['GET' => 0], null, false, false, null]],
        1622 => [[['_route' => 'ibexa.content.create_no_draft', '_controller' => 'ibexa_content_edit:createWithoutDraftAction'], ['contentTypeIdentifier', 'language', 'parentLocationId'], null, null, false, true, null]],
        1678 => [[['_route' => 'ibexa.content.draft.create', '_controller' => 'ibexa_content_edit:createContentDraftAction', 'contentId' => null, 'fromVersionNo' => null, 'fromLanguage' => null], ['contentId', 'fromVersionNo', 'fromLanguage'], null, null, false, true, null]],
        1757 => [[['_route' => 'ibexa.page_builder.content.preview', 'editorial_mode' => true, 'locationId' => null, '_controller' => 'Ibexa\\Bundle\\PageBuilder\\Controller\\PageController::previewAction'], ['contentId', 'versionNo', 'language', 'siteAccessName', 'locationId'], ['GET' => 0, 'POST' => 1], null, false, true, null]],
        1796 => [[['_route' => 'ibexa.content_type_group.view', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\ContentTypeGroupController::viewAction', 'page' => 1, 'siteaccess_group_whitelist' => 'admin_group'], ['contentTypeGroupId', 'page'], ['GET' => 0], null, false, true, null]],
        1817 => [[['_route' => 'ibexa.content_type_group.update', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\ContentTypeGroupController::updateAction', 'siteaccess_group_whitelist' => 'admin_group'], ['contentTypeGroupId'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        1838 => [[['_route' => 'ibexa.content_type_group.delete', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\ContentTypeGroupController::deleteAction', 'siteaccess_group_whitelist' => 'admin_group'], ['contentTypeGroupId'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        1869 => [[['_route' => 'ibexa.content_type.list', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\ContentTypeController::listAction', 'siteaccess_group_whitelist' => 'admin_group'], ['contentTypeGroupId'], ['GET' => 0], null, false, false, null]],
        1899 => [[['_route' => 'ibexa.content_type.add', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\ContentTypeController::addAction', 'siteaccess_group_whitelist' => 'admin_group'], ['contentTypeGroupId'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        1939 => [[['_route' => 'ibexa.content_type.edit', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\ContentTypeController::editAction', 'siteaccess_group_whitelist' => 'admin_group'], ['contentTypeGroupId', 'contentTypeId'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        1979 => [[['_route' => 'ibexa.content_type.copy', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\ContentTypeController::copyAction', 'siteaccess_group_whitelist' => 'admin_group'], ['contentTypeGroupId', 'contentTypeId'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        2049 => [[['_route' => 'ibexa.content_type.update', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\ContentTypeController::updateAction', 'toLanguageCode' => null, 'fromLanguageCode' => null, 'siteaccess_group_whitelist' => 'admin_group'], ['contentTypeGroupId', 'contentTypeId', 'toLanguageCode', 'fromLanguageCode'], ['GET' => 0, 'POST' => 1], null, false, true, null]],
        2084 => [[['_route' => 'ibexa.content_type.delete', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\ContentTypeController::deleteAction', 'siteaccess_group_whitelist' => 'admin_group'], ['contentTypeGroupId', 'contentTypeId'], ['DELETE' => 0], null, false, true, null]],
        2133 => [[['_route' => 'ibexa.content_type.view', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\ContentTypeController::viewAction', 'languageCode' => null, 'siteaccess_group_whitelist' => 'admin_group'], ['contentTypeGroupId', 'contentTypeId', 'languageCode'], ['GET' => 0], null, false, true, null]],
        2227 => [[['_route' => 'ibexa.content_type.field_definition_form', 'toLanguageCode' => null, 'fromLanguageCode' => null, '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\ContentTypeController::addFieldDefinitionFormAction', 'siteaccess_group_whitelist' => 'admin_group'], ['contentTypeGroupId', 'contentTypeId', 'fieldDefinitionIdentifier', 'toLanguageCode', 'fromLanguageCode'], ['GET' => 0], null, false, true, null]],
        2261 => [[['_route' => 'ibexa.content_type.bulk_delete', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\ContentTypeController::bulkDeleteAction', 'siteaccess_group_whitelist' => 'admin_group'], ['contentTypeGroupId'], ['POST' => 0], null, false, false, null]],
        2299 => [[['_route' => 'ibexa.corporate_account.company.details', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\CompanyDetailsController::detailsAction'], ['companyId'], null, null, false, true, null]],
        2325 => [[['_route' => 'ibexa.corporate_account.company.deactivate', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\CompanyStatusController::deactivateAction'], ['companyId'], null, null, false, true, null]],
        2352 => [[['_route' => 'ibexa.corporate_account.company.activate', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\CompanyStatusController::activateAction'], ['companyId'], null, null, false, true, null]],
        2377 => [[['_route' => 'ibexa.corporate_account.company.edit', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\CompanyEditController::editAction'], ['companyId'], null, null, false, true, null]],
        2410 => [[['_route' => 'ibexa.corporate_account.company.basic_information.edit', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\CompanyEditController::editBasicInformationAction'], ['companyId'], null, null, false, false, null]],
        2433 => [[['_route' => 'ibexa.corporate_account.company.billing_address.edit', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\CompanyEditController::editBillingAddressAction'], ['companyId'], null, null, false, false, null]],
        2450 => [[['_route' => 'ibexa.corporate_account.company.contact.edit', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\CompanyEditController::editContactAction'], ['companyId'], null, null, false, false, null]],
        2499 => [[['_route' => 'ibexa.corporate_account.company.shipping_address.create', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\ShippingAddressCreateController::createAction'], ['companyId'], null, null, false, false, null]],
        2521 => [[['_route' => 'ibexa.corporate_account.company.shipping_address.edit', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\ShippingAddressEditController::editAction'], ['companyId', 'shippingAddressId'], null, null, false, false, null]],
        2536 => [[['_route' => 'ibexa.corporate_account.company.shipping_address.delete', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\ShippingAddressDeleteController::deleteAction'], ['companyId'], ['POST' => 0], null, false, false, null]],
        2561 => [[['_route' => 'ibexa.corporate_account.company.shipping_address.set_as_default', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\ShippingAddressSetAsDefaultController::setAsDefaultAction'], ['companyId'], ['POST' => 0], null, false, false, null]],
        2587 => [[['_route' => 'ibexa.corporate_account.company.member.create', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\MemberCreateController::createAction'], ['companyId'], null, null, false, false, null]],
        2612 => [[['_route' => 'ibexa.corporate_account.company.member.edit', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\MemberEditController::editAction'], ['companyId', 'memberId'], null, null, false, false, null]],
        2629 => [[['_route' => 'ibexa.corporate_account.company.member.activate', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\MemberEditController::activateAction'], ['companyId', 'memberId'], null, null, false, false, null]],
        2648 => [[['_route' => 'ibexa.corporate_account.company.member.deactivate', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\MemberEditController::deactivateAction'], ['companyId', 'memberId'], null, null, false, false, null]],
        2668 => [[['_route' => 'ibexa.corporate_account.company.invite', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\InvitationController::sendInvitationsAction'], ['companyId'], null, null, false, false, null]],
        2687 => [[['_route' => 'ibexa.corporate_account.company.invite.resend', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\InvitationController::resendAction'], ['companyId'], null, null, false, false, null]],
        2702 => [[['_route' => 'ibexa.corporate_account.company.invite.reinvite', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\InvitationController::reinviteAction'], ['companyId'], null, null, false, false, null]],
        2765 => [[['_route' => 'ibexa.corporate_account.customer_portal.edit_member', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\CorporatePortal\\MembersController::editAction', 'siteaccess_group_whitelist' => 'corporate_group'], ['memberId'], null, null, false, true, null]],
        2791 => [[['_route' => 'ibexa.corporate_account.customer_portal.activate_member', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\CorporatePortal\\MembersController::activateAction', 'siteaccess_group_whitelist' => 'corporate_group'], ['memberId'], null, null, false, true, null]],
        2826 => [[['_route' => 'ibexa.corporate_account.customer_portal.deactivate_member', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\CorporatePortal\\MembersController::deactivateAction', 'siteaccess_group_whitelist' => 'corporate_group'], ['memberId'], null, null, false, true, null]],
        2857 => [[['_route' => 'ibexa.corporate_account.customer_portal.edit_address', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\CorporatePortal\\ShippingAddressController::editAction', 'siteaccess_group_whitelist' => 'corporate_group'], ['shippingAddressId'], null, null, false, false, null]],
        2884 => [[['_route' => 'ibexa.product_catalog.customer_group.view', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\CustomerGroup\\ViewController::renderAction', 'siteaccess_group_whitelist' => 'admin_group'], ['customerGroupId'], ['GET' => 0], null, false, true, null]],
        2903 => [[['_route' => 'ibexa.product_catalog.customer_group.delete', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\CustomerGroup\\DeleteController::executeAction', 'siteaccess_group_whitelist' => 'admin_group'], ['customerGroupId'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        2946 => [[['_route' => 'ibexa.product_catalog.customer_group.update', 'toLanguageCode' => null, 'fromLanguageCode' => null, '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\CustomerGroup\\UpdateController::renderAction', 'siteaccess_group_whitelist' => 'admin_group'], ['customerGroupId', 'toLanguageCode', 'fromLanguageCode'], ['GET' => 0, 'POST' => 1], null, false, true, null]],
        2976 => [[['_route' => 'ibexa.product_catalog.customer_group.translation.create', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\CustomerGroup\\CreateTranslationController::renderAction', 'siteaccess_group_whitelist' => 'admin_group'], ['customerGroupId'], ['POST' => 0], null, false, false, null]],
        2991 => [[['_route' => 'ibexa.product_catalog.customer_group.translation.delete', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\CustomerGroup\\DeleteTranslationController::executeAction', 'siteaccess_group_whitelist' => 'admin_group'], ['customerGroupId'], ['POST' => 0], null, false, false, null]],
        3028 => [[['_route' => 'ibexa.product_catalog.currency.update', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\Currency\\UpdateController::renderAction', 'siteaccess_group_whitelist' => 'admin_group'], ['currencyCode'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        3059 => [[['_route' => 'ibexa.product_catalog.catalog.view', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\Catalog\\ViewController::renderAction', 'siteaccess_group_whitelist' => 'admin_group'], ['catalogId'], ['GET' => 0], null, false, true, null]],
        3078 => [[['_route' => 'ibexa.product_catalog.catalog.delete', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\Catalog\\DeleteController::executeAction', 'siteaccess_group_whitelist' => 'admin_group'], ['catalogId'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        3093 => [[['_route' => 'ibexa.product_catalog.catalog.update', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\Catalog\\UpdateController::renderAction', 'siteaccess_group_whitelist' => 'admin_group'], ['catalogId'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        3108 => [[['_route' => 'ibexa.product_catalog.catalog.copy', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\Catalog\\CopyController::renderAction', 'siteaccess_group_whitelist' => 'admin_group'], [], ['POST' => 0], null, false, false, null]],
        3139 => [[['_route' => 'ibexa.product_catalog.catalog.transition', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\Catalog\\TransitionController::renderAction', 'siteaccess_group_whitelist' => 'admin_group'], ['catalogId'], ['POST' => 0], null, false, false, null]],
        3164 => [[['_route' => 'ibexa.product_catalog.catalog.translation.create', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\Catalog\\CreateTranslationController::renderAction', 'siteaccess_group_whitelist' => 'admin_group'], ['catalogId'], ['POST' => 0], null, false, false, null]],
        3179 => [[['_route' => 'ibexa.product_catalog.catalog.translation.delete', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\Catalog\\DeleteTranslationController::executeAction', 'siteaccess_group_whitelist' => 'admin_group'], ['catalogId'], ['POST' => 0], null, false, false, null]],
        3223 => [[['_route' => 'ibexa.section.view', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\SectionController::viewAction', 'siteaccess_group_whitelist' => 'admin_group'], ['sectionId'], null, null, false, true, null]],
        3252 => [[['_route' => 'ibexa.section.update', 'sectionId' => null, '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\SectionController::updateAction', 'siteaccess_group_whitelist' => 'admin_group'], ['sectionId'], null, null, false, true, null]],
        3276 => [[['_route' => 'ibexa.section.delete', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\SectionController::deleteAction', 'siteaccess_group_whitelist' => 'admin_group'], ['sectionId'], null, null, false, true, null]],
        3309 => [[['_route' => 'ibexa.section.assign_content', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\SectionController::assignContentAction', 'siteaccess_group_whitelist' => 'admin_group'], ['sectionId'], null, null, false, true, null]],
        3352 => [[['_route' => 'ibexa.segmentation.group.view', '_controller' => 'Ibexa\\Bundle\\Segmentation\\Controller\\SegmentController::groupViewAction'], ['segmentGroupId'], null, null, false, true, null]],
        3373 => [[['_route' => 'ibexa.segmentation.group.update', '_controller' => 'Ibexa\\Bundle\\Segmentation\\Controller\\SegmentController::groupUpdateAction'], ['segmentGroupId'], ['POST' => 0], null, false, true, null]],
        3403 => [[['_route' => 'ibexa.segmentation.segment.update', '_controller' => 'Ibexa\\Bundle\\Segmentation\\Controller\\SegmentController::segmentUpdateAction'], ['segmentId'], ['POST' => 0], null, false, true, null]],
        3446 => [[['_route' => 'ibexa.object_state.group.update', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\ObjectStateGroupController::updateAction', 'siteaccess_group_whitelist' => 'admin_group'], ['objectStateGroupId'], null, null, false, true, null]],
        3470 => [[['_route' => 'ibexa.object_state.group.delete', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\ObjectStateGroupController::deleteAction', 'siteaccess_group_whitelist' => 'admin_group'], ['objectStateGroupId'], null, null, false, true, null]],
        3487 => [[['_route' => 'ibexa.object_state.group.view', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\ObjectStateGroupController::viewAction', 'siteaccess_group_whitelist' => 'admin_group'], ['objectStateGroupId'], null, null, false, true, null]],
        3521 => [[['_route' => 'ibexa.object_state.state.add', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\ObjectStateController::addAction', 'siteaccess_group_whitelist' => 'admin_group'], ['objectStateGroupId'], null, null, false, true, null]],
        3538 => [[['_route' => 'ibexa.object_state.state.view', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\ObjectStateController::viewAction', 'siteaccess_group_whitelist' => 'admin_group'], ['objectStateId'], null, null, false, true, null]],
        3562 => [[['_route' => 'ibexa.object_state.state.update', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\ObjectStateController::updateAction', 'siteaccess_group_whitelist' => 'admin_group'], ['objectStateId'], null, null, false, true, null]],
        3586 => [[['_route' => 'ibexa.object_state.state.delete', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\ObjectStateController::deleteAction', 'siteaccess_group_whitelist' => 'admin_group'], ['objectStateId'], null, null, false, true, null]],
        3616 => [[['_route' => 'ibexa.object_state.state.bulk_delete', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\ObjectStateController::bulkDeleteAction', 'siteaccess_group_whitelist' => 'admin_group'], ['objectStateGroupId'], null, null, false, true, null]],
        3669 => [[['_route' => 'ibexa.object_state.content_state.update', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\ObjectStateController::updateContentStateAction', 'siteaccess_group_whitelist' => 'admin_group'], ['contentInfoId', 'objectStateGroupId'], null, null, false, true, null]],
        3712 => [[['_route' => 'ibexa.submission.download', '_controller' => 'Ibexa\\Bundle\\FormBuilder\\Controller\\FormSubmissionController::downloadAction', 'siteaccess_group_whitelist' => 'admin_group'], ['contentId', 'languageCode'], ['GET' => 0], null, false, false, null]],
        3755 => [[['_route' => 'ibexa.site_context.change', '_controller' => 'Ibexa\\Bundle\\SiteContext\\Controller\\ChangeContextController::changeAction', 'siteaccess_group_whitelist' => 'admin_group'], ['locationId'], null, null, false, true, null]],
        3790 => [[['_route' => 'ibexa.site_context.location_preview', '_controller' => 'Ibexa\\Bundle\\SiteContext\\Controller\\LocationPreviewController::executeAction', 'siteaccess_group_whitelist' => 'admin_group'], ['locationId'], null, null, false, true, null]],
        3826 => [[['_route' => 'ibexa.site_context.toggle_fullscreen', '_controller' => 'Ibexa\\Bundle\\SiteContext\\Controller\\FullscreenPreviewController::toggleFullscreenAction', 'siteaccess_group_whitelist' => 'admin_group'], ['locationId'], null, null, false, true, null]],
        3858 => [[['_route' => 'ibexa.site_factory.grid', '_controller' => 'Ibexa\\Bundle\\SiteFactory\\Controller\\SiteController::gridAction', 'locationId' => null], ['locationId'], ['GET' => 0, 'POST' => 1], null, false, true, null]],
        3885 => [[['_route' => 'ibexa.site_factory.list', '_controller' => 'Ibexa\\Bundle\\SiteFactory\\Controller\\SiteController::listAction', 'locationId' => null], ['locationId'], ['GET' => 0, 'POST' => 1], null, false, true, null]],
        3904 => [[['_route' => 'ibexa.site_factory.edit', '_controller' => 'Ibexa\\Bundle\\SiteFactory\\Controller\\SiteController::editAction'], ['siteId'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        3918 => [[['_route' => 'ibexa.site_factory.view', '_controller' => 'Ibexa\\Bundle\\SiteFactory\\Controller\\SiteController::viewAction'], ['siteId'], null, null, false, true, null]],
        3959 => [[['_route' => 'ibexa.language.view', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\LanguageController::viewAction', 'siteaccess_group_whitelist' => 'admin_group'], ['languageId'], null, null, false, true, null]],
        3986 => [[['_route' => 'ibexa.language.edit', 'languageId' => null, '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\LanguageController::editAction', 'siteaccess_group_whitelist' => 'admin_group'], ['languageId'], null, null, false, true, null]],
        4010 => [[['_route' => 'ibexa.language.delete', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\LanguageController::deleteAction', 'siteaccess_group_whitelist' => 'admin_group'], ['languageId'], null, null, false, true, null]],
        4047 => [[['_route' => 'ibexa.link_manager.edit', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\LinkManagerController::editAction', 'siteaccess_group_whitelist' => 'admin_group'], ['urlId'], null, null, false, true, null]],
        4066 => [[['_route' => 'ibexa.link_manager.view', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\LinkManagerController::viewAction', 'siteaccess_group_whitelist' => 'admin_group'], ['urlId'], null, null, false, true, null]],
        4116 => [[['_route' => 'ibexa.role.view', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\RoleController::viewAction', 'policyPage' => 1, 'assignmentPage' => 1, 'siteaccess_group_whitelist' => 'admin_group'], ['roleId', 'policyPage', 'assignmentPage'], ['GET' => 0], null, false, true, null]],
        4135 => [[['_route' => 'ibexa.role.copy', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\RoleController::copyAction', 'siteaccess_group_whitelist' => 'admin_group'], ['roleId'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        4156 => [[['_route' => 'ibexa.role.update', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\RoleController::updateAction', 'siteaccess_group_whitelist' => 'admin_group'], ['roleId'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        4177 => [[['_route' => 'ibexa.role.delete', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\RoleController::deleteAction', 'siteaccess_group_whitelist' => 'admin_group'], ['roleId'], ['POST' => 0], null, false, false, null]],
        4203 => [[['_route' => 'ibexa.policy.list', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\PolicyController::listAction', 'siteaccess_group_whitelist' => 'admin_group'], ['roleId'], ['GET' => 0], null, false, false, null]],
        4231 => [[['_route' => 'ibexa.policy.create', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\PolicyController::createAction', 'siteaccess_group_whitelist' => 'admin_group'], ['roleId'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        4265 => [[['_route' => 'ibexa.policy.update', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\PolicyController::updateAction', 'siteaccess_group_whitelist' => 'admin_group'], ['roleId', 'policyId'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        4305 => [[['_route' => 'ibexa.policy.create_with_limitation', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\PolicyController::createWithLimitationAction', 'siteaccess_group_whitelist' => 'admin_group'], ['roleId', 'policyModule', 'policyFunction'], ['GET' => 0, 'POST' => 1], null, false, true, null]],
        4332 => [[['_route' => 'ibexa.policy.delete', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\PolicyController::deleteAction', 'siteaccess_group_whitelist' => 'admin_group'], ['roleId', 'policyId'], ['POST' => 0], null, false, true, null]],
        4366 => [[['_route' => 'ibexa.policy.bulk_delete', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\PolicyController::bulkDeleteAction', 'siteaccess_group_whitelist' => 'admin_group'], ['roleId'], ['POST' => 0], null, false, false, null]],
        4394 => [[['_route' => 'ibexa.role_assignment.list', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\RoleAssignmentController::listAction', 'siteaccess_group_whitelist' => 'admin_group'], ['roleId'], ['GET' => 0], null, false, false, null]],
        4426 => [[['_route' => 'ibexa.role_assignment.create', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\RoleAssignmentController::createAction', 'siteaccess_group_whitelist' => 'admin_group'], ['roleId'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        4470 => [[['_route' => 'ibexa.role_assignment.delete', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\RoleAssignmentController::deleteAction', 'siteaccess_group_whitelist' => 'admin_group'], ['roleId', 'assignmentId'], ['POST' => 0], null, false, false, null]],
        4508 => [[['_route' => 'ibexa.role_assignment.bulk_delete', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\RoleAssignmentController::bulkDeleteAction', 'siteaccess_group_whitelist' => 'admin_group'], ['roleId'], ['POST' => 0], null, false, false, null]],
        4534 => [[['_route' => 'ibexa.relation', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\ContentController::relationViewAction', 'siteaccess_group_whitelist' => 'admin_group'], ['contentId'], ['GET' => 0, 'POST' => 1], null, false, true, null]],
        4581 => [[['_route' => 'ibexa.user.invite.to_group', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\User\\InvitationController::sendInvitationsAction', 'siteaccess_group_whitelist' => 'admin_group'], ['userGroupId'], ['POST' => 0], null, false, true, null]],
        4611 => [[['_route' => 'ibexa.user.profile.view', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\User\\ProfileViewController::viewAction', 'siteaccess_group_whitelist' => 'admin_group'], ['userId'], ['GET' => 0], null, false, false, null]],
        4672 => [[['_route' => 'ibexa.user.on_the_fly.create', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\UserOnTheFlyController::createUserAction', 'siteaccess_group_whitelist' => 'admin_group'], ['contentTypeIdentifier', 'languageCode', 'locationId'], ['GET' => 0, 'POST' => 1], null, false, true, null]],
        4693 => [[['_route' => 'ibexa.user.on_the_fly.has_access', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\UserOnTheFlyController::hasCreateAccessAction', 'siteaccess_group_whitelist' => 'admin_group'], ['contentTypeIdentifier', 'languageCode', 'locationId'], ['GET' => 0], null, false, false, null]],
        4729 => [[['_route' => 'ibexa.user.create', '_controller' => 'Ibexa\\Bundle\\ContentForms\\Controller\\UserController:createAction'], ['contentTypeIdentifier', 'language', 'parentLocationId'], null, null, false, true, null]],
        4792 => [[['_route' => 'ibexa.user.on_the_fly.edit', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\UserOnTheFlyController::editUserAction', 'siteaccess_group_whitelist' => 'admin_group'], ['contentId', 'versionNo', 'languageCode', 'locationId'], ['GET' => 0, 'POST' => 1], null, false, true, null]],
        4834 => [[['_route' => 'ibexa.user.update', '_controller' => 'Ibexa\\Bundle\\ContentForms\\Controller\\UserController:editAction'], ['contentId', 'versionNo', 'language'], null, null, false, true, null]],
        4867 => [[['_route' => 'ibexa.user.reset_password', '_controller' => 'Ibexa\\Bundle\\User\\Controller\\PasswordResetController::userResetPasswordAction'], ['hashKey'], null, null, false, true, null]],
        4906 => [[['_route' => 'ibexa.user.from_invite.user_register', '_controller' => 'Ibexa\\Bundle\\User\\Controller\\UserRegisterController::registerFromInvitationAction'], ['inviteHash'], null, null, false, true, null]],
        4942 => [[['_route' => 'ibexa.user_settings.list', '_controller' => 'Ibexa\\Bundle\\User\\Controller\\UserSettingsController::listAction', 'page' => 1], ['page'], null, null, false, true, null]],
        4962 => [[['_route' => 'ibexa.user_settings.update', '_controller' => 'Ibexa\\Bundle\\User\\Controller\\UserSettingsController::updateAction'], ['identifier'], null, null, false, true, null]],
        5001 => [[['_route' => 'ibexa.url_wildcard.update', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\URLWildcardController::updateAction', 'siteaccess_group_whitelist' => 'admin_group'], ['urlWildcardId'], ['GET' => 0, 'POST' => 1], null, false, true, null]],
        5053 => [[['_route' => 'ibexa.notifications.get', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\NotificationController::getNotificationsAction', 'offset' => 0, 'limit' => 10, 'siteaccess_group_whitelist' => 'admin_group'], ['offset', 'limit'], ['GET' => 0], null, false, true, null]],
        5085 => [[['_route' => 'ibexa.notifications.render.page', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\NotificationController::renderNotificationsPageAction', 'page' => 1, 'siteaccess_group_whitelist' => 'admin_group'], ['page'], ['GET' => 0], null, false, true, null]],
        5106 => [[['_route' => 'ibexa.notifications.mark_as_read', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\NotificationController::markNotificationAsReadAction', 'siteaccess_group_whitelist' => 'admin_group'], ['notificationId'], ['GET' => 0], null, false, true, null]],
        5178 => [[['_route' => 'ibexa.permission.limitation.language.content_create', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\Permission\\LanguageLimitationController::loadLanguageLimitationsForContentCreateAction', 'siteaccess_group_whitelist' => 'admin_group'], ['locationId'], ['GET' => 0], null, false, true, null]],
        5197 => [[['_route' => 'ibexa.permission.limitation.language.content_edit', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\Permission\\LanguageLimitationController::loadLanguageLimitationsForContentEditAction', 'siteaccess_group_whitelist' => 'admin_group'], ['contentInfoId'], ['GET' => 0], null, false, true, null]],
        5216 => [[['_route' => 'ibexa.permission.limitation.language.content_read', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\Permission\\LanguageLimitationController::loadLanguageLimitationsForContentReadAction', 'siteaccess_group_whitelist' => 'admin_group'], ['contentInfoId'], ['GET' => 0], null, false, true, null]],
        5260 => [[['_route' => 'ibexa.personalization.dashboard', '_controller' => 'Ibexa\\Bundle\\Personalization\\Controller\\DashboardController::dashboardAction', 'siteaccess_group_whitelist' => 'admin_group'], ['customerId'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        5278 => [[['_route' => 'ibexa.personalization.models', '_controller' => 'Ibexa\\Bundle\\Personalization\\Controller\\ModelController::listAction', 'siteaccess_group_whitelist' => 'admin_group'], ['customerId'], ['GET' => 0], null, false, false, null]],
        5302 => [[['_route' => 'ibexa.personalization.model.details', '_controller' => 'Ibexa\\Bundle\\Personalization\\Controller\\ModelController::detailsAction', 'siteaccess_group_whitelist' => 'admin_group'], ['customerId', 'referenceCode'], ['GET' => 0], null, false, true, null]],
        5316 => [[['_route' => 'ibexa.personalization.model.edit', '_controller' => 'Ibexa\\Bundle\\Personalization\\Controller\\ModelController::editAction', 'siteaccess_group_whitelist' => 'admin_group'], ['customerId', 'referenceCode'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        5381 => [[['_route' => 'ibexa.personalization.model.attribute', 'attributeSource' => null, 'source' => null, '_controller' => 'Ibexa\\Bundle\\Personalization\\Controller\\ModelController::attributeAction', 'siteaccess_group_whitelist' => 'admin_group'], ['customerId', 'attributeType', 'attributeKey', 'attributeSource', 'source'], ['GET' => 0], null, false, true, null]],
        5407 => [[['_route' => 'ibexa.personalization.scenarios', '_controller' => 'Ibexa\\Bundle\\Personalization\\Controller\\ScenarioController::scenariosAction', 'siteaccess_group_whitelist' => 'admin_group'], ['customerId'], ['GET' => 0], null, false, false, null]],
        5426 => [[['_route' => 'ibexa.personalization.scenario.create', '_controller' => 'Ibexa\\Bundle\\Personalization\\Controller\\ScenarioController::createAction', 'siteaccess_group_whitelist' => 'admin_group'], ['customerId'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        5454 => [[['_route' => 'ibexa.personalization.scenario.details', '_controller' => 'Ibexa\\Bundle\\Personalization\\Controller\\ScenarioController::detailsAction', 'siteaccess_group_whitelist' => 'admin_group'], ['customerId', 'name'], ['GET' => 0], null, false, true, null]],
        5467 => [[['_route' => 'ibexa.personalization.scenario.delete', '_controller' => 'Ibexa\\Bundle\\Personalization\\Controller\\ScenarioController::deleteAction', 'siteaccess_group_whitelist' => 'admin_group'], ['customerId'], ['POST' => 0], null, false, false, null]],
        5493 => [[['_route' => 'ibexa.personalization.scenario.preview', '_controller' => 'Ibexa\\Bundle\\Personalization\\Controller\\ScenarioController::previewAction', 'siteaccess_group_whitelist' => 'admin_group'], ['customerId', 'name'], ['GET' => 0], null, false, true, null]],
        5515 => [[['_route' => 'ibexa.personalization.scenario.edit', '_controller' => 'Ibexa\\Bundle\\Personalization\\Controller\\ScenarioController::editAction', 'siteaccess_group_whitelist' => 'admin_group'], ['customerId', 'name'], ['GET' => 0, 'POST' => 1], null, false, true, null]],
        5542 => [[['_route' => 'ibexa.personalization.search.attributes', '_controller' => 'Ibexa\\Bundle\\Personalization\\Controller\\SearchController::searchAttributesAction', 'siteaccess_group_whitelist' => 'admin_group'], ['customerId'], ['GET' => 0], null, true, false, null]],
        5558 => [[['_route' => 'ibexa.personalization.import', '_controller' => 'Ibexa\\Bundle\\Personalization\\Controller\\ImportController::importAction', 'siteaccess_group_whitelist' => 'admin_group'], ['customerId'], null, null, false, false, null]],
        5577 => [[['_route' => 'ibexa.personalization.chart.data', '_controller' => 'Ibexa\\Bundle\\Personalization\\Controller\\ChartController::getDataAction', 'siteaccess_group_whitelist' => 'admin_group'], ['customerId'], ['GET' => 0], null, false, false, null]],
        5620 => [[['_route' => 'ibexa.personalization.report.recommendation_detailed', '_controller' => 'Ibexa\\Bundle\\Personalization\\Controller\\ReportController::recommendationDetailedReportAction', 'siteaccess_group_whitelist' => 'admin_group'], ['customerId'], ['GET' => 0], null, false, false, null]],
        5658 => [[['_route' => 'ibexa.personalization.recommendation.preview', '_controller' => 'Ibexa\\Bundle\\Personalization\\Controller\\RecommendationPreviewController::previewAction', 'siteaccess_group_whitelist' => 'admin_group'], ['customerId', 'name'], ['POST' => 0], null, false, true, null]],
        5715 => [[['_route' => 'ibexa.personalization.output_type.attributes.item_type_id', '_controller' => 'Ibexa\\Bundle\\Personalization\\Controller\\OutputTypeController::getOutputTypeAttributesByItemTypeIdAction', 'siteaccess_group_whitelist' => 'admin_group'], ['customerId', 'itemTypeId'], ['GET' => 0], null, false, true, null]],
        5753 => [[['_route' => 'ibexa.personalization.output_type.attributes.scenario', '_controller' => 'Ibexa\\Bundle\\Personalization\\Controller\\OutputTypeController::getOutputTypeAttributesByScenarioAction', 'siteaccess_group_whitelist' => 'admin_group'], ['customerId', 'scenarioName'], ['GET' => 0], null, false, true, null]],
        5801 => [[['_route' => 'ibexa.page_builder.edit.original', '_controller' => 'Ibexa\\Bundle\\PageBuilder\\Controller\\PageController::createDraftAction', 'locationId' => null, 'siteaccessName' => null, 'siteaccess_group_whitelist' => 'admin_group'], ['locationId', 'siteaccessName'], null, null, false, true, null]],
        5859 => [[['_route' => 'ibexa.page_builder.create', '_controller' => 'Ibexa\\Bundle\\PageBuilder\\Controller\\PageController::createAction', 'language' => null, 'siteaccessName' => null, 'siteaccess_group_whitelist' => 'admin_group'], ['contentTypeIdentifier', 'parentLocationId', 'language', 'siteaccessName'], null, null, false, true, null]],
        5902 => [[['_route' => 'ibexa.page_builder.layout', '_controller' => 'Ibexa\\Bundle\\PageBuilder\\Controller\\PreviewController::siteaccessLayoutPreviewAction', 'siteaccessName' => null, 'layoutId' => null, 'siteaccess_group_whitelist' => 'admin_group'], ['siteaccessName', 'layoutId'], null, null, false, true, null]],
        5952 => [[['_route' => 'ibexa.page_builder.block.configure', '_controller' => 'Ibexa\\Bundle\\PageBuilder\\Controller\\BlockController::configureBlockAction', 'languageCode' => null, 'siteaccess_group_whitelist' => 'admin_group'], ['blockType', 'languageCode'], ['POST' => 0], null, false, true, null]],
        5988 => [[['_route' => 'ibexa.page_builder.block.siteaccess_preview', '_controller' => 'Ibexa\\Bundle\\PageBuilder\\Controller\\PreviewController::siteaccessBlockPreviewAction', 'siteaccess_group_whitelist' => 'admin_group'], ['siteaccessName'], ['POST' => 0], null, false, true, null]],
        6046 => [[['_route' => 'ibexa.page_builder.site.preview', '_controller' => 'ibexa.controller.content.preview:previewContentAction', 'siteaccess_group_whitelist' => 'admin_group'], ['contentId', 'versionNo', 'language', 'siteAccessName'], ['GET' => 0], null, false, true, null]],
        6093 => [[['_route' => 'ibexa.product_catalog.product.asset.create', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\Product\\CreateAssetController::executeAction', 'siteaccess_group_whitelist' => 'admin_group'], ['productCode'], ['GET' => 0, 'POST' => 1], null, false, true, null]],
        6116 => [[['_route' => 'ibexa.product_catalog.product.asset.tag', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\Product\\TagAssetController::executeAction', 'siteaccess_group_whitelist' => 'admin_group'], ['productCode'], ['GET' => 0, 'POST' => 1], null, false, true, null]],
        6140 => [[['_route' => 'ibexa.product_catalog.product.asset.delete', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\Product\\DeleteAssetController::executeAction', 'siteaccess_group_whitelist' => 'admin_group'], ['productCode'], ['GET' => 0, 'POST' => 1], null, false, true, null]],
        6174 => [[['_route' => 'ibexa.product_catalog.product.create', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\Product\\CreateController::createAction', 'siteaccess_group_whitelist' => 'admin_group'], ['productTypeIdentifier', 'languageCode'], ['GET' => 0, 'POST' => 1], null, false, true, null]],
        6224 => [[['_route' => 'ibexa.product_catalog.product.edit', 'languageCode' => null, 'baseLanguageCode' => null, '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\Product\\UpdateController::updateAction', 'siteaccess_group_whitelist' => 'admin_group'], ['productCode', 'languageCode', 'baseLanguageCode'], ['GET' => 0, 'POST' => 1], null, false, true, null]],
        6262 => [[['_route' => 'ibexa.product_catalog.product.variant.create', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\Product\\CreateVariantController::executeAction', 'siteaccess_group_whitelist' => 'admin_group'], ['productCode'], ['GET' => 0, 'POST' => 1], null, false, true, null]],
        6284 => [[['_route' => 'ibexa.product_catalog.product.variant.edit', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\Product\\UpdateVariantController::executeAction', 'siteaccess_group_whitelist' => 'admin_group'], ['productCode'], ['GET' => 0, 'POST' => 1], null, false, true, null]],
        6314 => [[['_route' => 'ibexa.product_catalog.product.variant_generator', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\Product\\GenerateVariantController::executeAction', 'siteaccess_group_whitelist' => 'admin_group'], ['productCode'], ['GET' => 0, 'POST' => 1], null, false, true, null]],
        6339 => [[['_route' => 'ibexa.product_catalog.product.delete', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\Product\\DeleteController::execute', 'siteaccess_group_whitelist' => 'admin_group'], ['productCode'], ['GET' => 0, 'POST' => 1], null, false, true, null]],
        6378 => [[['_route' => 'ibexa.product_catalog.product.translation.create', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\Product\\CreateTranslationController::executeAction', 'siteaccess_group_whitelist' => 'admin_group'], ['productCode'], ['POST' => 0], null, false, true, null]],
        6402 => [[['_route' => 'ibexa.product_catalog.product.translation.delete', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\Product\\DeleteTranslationController::executeAction', 'siteaccess_group_whitelist' => 'admin_group'], ['productCode'], ['POST' => 0], null, false, true, null]],
        6423 => [[['_route' => 'ibexa.product_catalog.product.view', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\Product\\ViewController::renderAction', 'siteaccess_group_whitelist' => 'admin_group'], ['productCode'], ['GET' => 0], null, false, true, null]],
        6460 => [[['_route' => 'ibexa.product_catalog.product.price.create', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\Product\\Price\\CreateController::renderAction', 'siteaccess_group_whitelist' => 'admin_group'], ['productCode', 'currencyCode'], ['GET' => 0, 'POST' => 1], null, false, true, null]],
        6484 => [[['_route' => 'ibexa.product_catalog.product.price.update', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\Product\\Price\\UpdateController::renderAction', 'siteaccess_group_whitelist' => 'admin_group'], ['productCode', 'currencyCode'], ['GET' => 0, 'POST' => 1], null, false, true, null]],
        6504 => [[['_route' => 'ibexa.product_catalog.product.price.delete', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\Product\\Price\\DeleteController::renderAction', 'siteaccess_group_whitelist' => 'admin_group'], ['productCode'], ['POST' => 0], null, false, false, null]],
        6536 => [[['_route' => 'ibexa.product_catalog.product.availability.create', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\Product\\Availability\\CreateController::renderAction', 'siteaccess_group_whitelist' => 'admin_group'], ['productCode'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        6551 => [[['_route' => 'ibexa.product_catalog.product.availability.update', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\Product\\Availability\\UpdateController::renderAction', 'siteaccess_group_whitelist' => 'admin_group'], ['productCode'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        6587 => [[['_route' => 'ibexa.product_catalog.product_type.view', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\ProductType\\ViewController::renderAction', 'siteaccess_group_whitelist' => 'admin_group'], ['productTypeIdentifier'], ['GET' => 0], null, false, true, null]],
        6611 => [[['_route' => 'ibexa.product_catalog.product_type.delete', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\ProductType\\DeleteController::executeAction', 'siteaccess_group_whitelist' => 'admin_group'], ['productTypeIdentifier'], ['GET' => 0, 'POST' => 1], null, false, true, null]],
        6661 => [[['_route' => 'ibexa.product_catalog.product_type.edit', 'languageCode' => null, 'baseLanguageCode' => null, '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\ProductType\\UpdateController::updateAction', 'siteaccess_group_whitelist' => 'admin_group'], ['productTypeIdentifier', 'languageCode', 'baseLanguageCode'], ['GET' => 0, 'POST' => 1], null, false, true, null]],
        6700 => [[['_route' => 'ibexa.product_catalog.product_type.translation.create', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\ProductType\\CreateTranslationController::executeAction', 'siteaccess_group_whitelist' => 'admin_group'], ['productTypeIdentifier'], ['POST' => 0], null, false, true, null]],
        6724 => [[['_route' => 'ibexa.product_catalog.product_type.translation.delete', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\ProductType\\DeleteTranslationController::executeAction', 'siteaccess_group_whitelist' => 'admin_group'], ['productTypeIdentifier'], ['POST' => 0], null, false, true, null]],
        6831 => [[['_route' => 'ibexa.rest.location.tree.load_children', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\Content\\ContentTreeController::loadChildrenAction', 'limit' => 10, 'offset' => 0], ['parentLocationId', 'limit', 'offset'], ['GET' => 0], null, false, true, null]],
        6863 => [[['_route' => 'ibexa.rest.location.tree.load_node_extended_info', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\Content\\ContentTreeController::loadNodeExtendedInfoAction'], ['locationId'], ['GET' => 0], null, false, false, null]],
        6893 => [
            [['_route' => 'ibexa.rest.languages.view', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Language::loadLanguage'], ['languageCode'], ['GET' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.options.languages_{languageCode}', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET'], ['languageCode'], ['OPTIONS' => 0], null, false, true, null],
        ],
        6994 => [[['_route' => 'ibexa.content_type.field_definition.create', 'toLanguageCode' => null, '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\FieldDefinitionController::addFieldDefinitionAction'], ['contentTypeGroupId', 'contentTypeId', 'toLanguageCode'], ['POST' => 0], null, false, true, null]],
        7053 => [[['_route' => 'ibexa.content_type.field_definition.remove', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\FieldDefinitionController::removeFieldDefinitionAction'], ['contentTypeGroupId', 'contentTypeId'], ['DELETE' => 0], null, false, false, null]],
        7114 => [[['_route' => 'ibexa.content_type.field_definition.reorder', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\FieldDefinitionController::reorderFieldDefinitionsAction'], ['contentTypeGroupId', 'contentTypeId'], ['PUT' => 0], null, false, false, null]],
        7159 => [[['_route' => 'ibexa.connector.dam.asset', '_controller' => 'Ibexa\\Bundle\\Connector\\Dam\\Controller\\Rest\\AssetController::getAsset'], ['assetId', 'assetSource'], ['GET' => 0], null, false, true, null]],
        7222 => [[['_route' => 'ibexa.connector.dam.asset_variation', '_controller' => 'Ibexa\\Bundle\\Connector\\Dam\\Controller\\AssetVariationController::getExternalAssetVariation'], ['assetId', 'assetSource', 'transformationName'], ['GET' => 0], null, false, true, null]],
        7272 => [
            [['_route' => 'ibexa.rest.binary_content.get_image_variation', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\BinaryContent:getImageVariation'], ['imageId', 'variationIdentifier'], ['GET' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.options.content_binary_images_{imageId}_variations_{variationIdentifier}', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET'], ['imageId', 'variationIdentifier'], ['OPTIONS' => 0], null, false, true, null],
        ],
        7347 => [[['_route' => 'ibexa.query.rest.field_results', '_controller' => 'Ibexa\\Bundle\\FieldTypeQuery\\Controller\\QueryFieldRestController:getResults'], ['contentId', 'versionNumber', 'fieldDefinitionIdentifier'], ['GET' => 0], null, false, false, null]],
        7364 => [
            [['_route' => 'ibexa.rest.update_content_metadata', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Content:updateContentMetadata'], ['contentId'], ['PATCH' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.load_content', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Content:loadContent'], ['contentId'], ['GET' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.delete_content', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Content:deleteContent'], ['contentId'], ['DELETE' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.copy_content', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Content:copyContent'], ['contentId'], ['COPY' => 0], null, false, true, null],
        ],
        7404 => [[['_route' => 'ibexa.rest.delete_content_translation', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Content:deleteContentTranslation'], ['contentId', 'languageCode'], ['DELETE' => 0], null, false, true, null]],
        7428 => [[['_route' => 'ibexa.rest.redirect_current_version_relations', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Content:redirectCurrentVersionRelations'], ['contentId'], ['GET' => 0], null, false, false, null]],
        7451 => [[['_route' => 'ibexa.rest.load_content_versions', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Content:loadContentVersions'], ['contentId'], ['GET' => 0], null, false, false, null]],
        7493 => [
            [['_route' => 'ibexa.rest.load_version_relations', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Content:loadVersionRelations'], ['contentId', 'versionNumber'], ['GET' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.create_relation', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Content:createRelation'], ['contentId', 'versionNumber'], ['POST' => 0], null, false, false, null],
        ],
        7542 => [
            [['_route' => 'ibexa.rest.load_version_relation', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Content:loadVersionRelation'], ['contentId', 'versionNumber', 'relationId'], ['GET' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.remove_relation', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Content:removeRelation'], ['contentId', 'versionNumber', 'relationId'], ['DELETE' => 0], null, false, true, null],
        ],
        7575 => [
            [['_route' => 'ibexa.rest.load_content_in_version', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Content:loadContentInVersion'], ['contentId', 'versionNumber'], ['GET' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.update_version', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Content:updateVersion'], ['contentId', 'versionNumber'], ['PATCH' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.delete_content_version', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Content:deleteContentVersion'], ['contentId', 'versionNumber'], ['DELETE' => 0], null, false, true, null],
        ],
        7627 => [[['_route' => 'ibexa.rest.delete_translation_from_draft', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Content:deleteTranslationFromDraft'], ['contentId', 'versionNumber', 'languageCode'], ['DELETE' => 0], null, false, true, null]],
        7659 => [
            [['_route' => 'ibexa.rest.create_draft_from_version', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Content:createDraftFromVersion'], ['contentId', 'versionNumber'], ['COPY' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.publish_version', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Content:publishVersion'], ['contentId', 'versionNumber'], ['PUBLISH' => 0], null, false, true, null],
        ],
        7692 => [
            [['_route' => 'ibexa.rest.redirect_current_version', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Content:redirectCurrentVersion'], ['contentId'], ['GET' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.create_draft_from_current_version', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Content:createDraftFromCurrentVersion'], ['contentId'], ['COPY' => 0], null, false, false, null],
        ],
        7712 => [[['_route' => 'ibexa.rest.hide_content', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Content:hideContent'], ['contentId'], ['POST' => 0], null, false, false, null]],
        7733 => [[['_route' => 'ibexa.rest.reveal_content', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Content:revealContent'], ['contentId'], ['POST' => 0], null, false, false, null]],
        7763 => [
            [['_route' => 'ibexa.rest.get_object_states_for_content', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\ObjectState:getObjectStatesForContent'], ['contentId'], ['GET' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.set_object_states_for_content', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\ObjectState:setObjectStatesForContent'], ['contentId'], ['PATCH' => 0], null, false, false, null],
        ],
        7791 => [
            [['_route' => 'ibexa.rest.load_locations_for_content', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Location:loadLocationsForContent'], ['contentId'], ['GET' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.create_location', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Location:createLocation'], ['contentId'], ['POST' => 0], null, false, false, null],
        ],
        7806 => [[['_route' => 'ibexa.rest.options.content_objects_{contentId}', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'PATCH,GET,DELETE,COPY'], ['contentId'], ['OPTIONS' => 0], null, false, true, null]],
        7845 => [[['_route' => 'ibexa.rest.options.content_objects_{contentId}_translations_{languageCode}', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'DELETE'], ['contentId', 'languageCode'], ['OPTIONS' => 0], null, false, true, null]],
        7869 => [[['_route' => 'ibexa.rest.options.content_objects_{contentId}_relations', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET'], ['contentId'], ['OPTIONS' => 0], null, false, false, null]],
        7892 => [[['_route' => 'ibexa.rest.options.content_objects_{contentId}_versions', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET'], ['contentId'], ['OPTIONS' => 0], null, false, false, null]],
        7931 => [[['_route' => 'ibexa.rest.options.content_objects_{contentId}_versions_{versionNumber}_relations', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET,POST'], ['contentId', 'versionNumber'], ['OPTIONS' => 0], null, false, false, null]],
        7976 => [[['_route' => 'ibexa.rest.options.content_objects_{contentId}_versions_{versionNumber}_relations_{relationId}', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET,DELETE'], ['contentId', 'versionNumber', 'relationId'], ['OPTIONS' => 0], null, false, true, null]],
        8027 => [[['_route' => 'ibexa.rest.options.content_objects_{contentId}_versions_{versionNumber}_translations_{languageCode}', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'DELETE'], ['contentId', 'versionNumber', 'languageCode'], ['OPTIONS' => 0], null, false, true, null]],
        8056 => [[['_route' => 'ibexa.rest.options.content_objects_{contentId}_versions_{versionNumber}', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET,PATCH,DELETE,COPY,PUBLISH'], ['contentId', 'versionNumber'], ['OPTIONS' => 0], null, false, true, null]],
        8085 => [[['_route' => 'ibexa.rest.options.content_objects_{contentId}_currentversion', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET,COPY'], ['contentId'], ['OPTIONS' => 0], null, false, false, null]],
        8104 => [[['_route' => 'ibexa.rest.options.content_objects_{contentId}_hide', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'POST'], ['contentId'], ['OPTIONS' => 0], null, false, false, null]],
        8125 => [[['_route' => 'ibexa.rest.options.content_objects_{contentId}_reveal', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'POST'], ['contentId'], ['OPTIONS' => 0], null, false, false, null]],
        8152 => [[['_route' => 'ibexa.rest.options.content_objects_{contentId}_objectstates', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET,PATCH'], ['contentId'], ['OPTIONS' => 0], null, false, false, null]],
        8176 => [[['_route' => 'ibexa.rest.options.content_objects_{contentId}_locations', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET,POST'], ['contentId'], ['OPTIONS' => 0], null, false, false, null]],
        8208 => [
            [['_route' => 'ibexa.rest.load_object_state_group', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\ObjectState:loadObjectStateGroup'], ['objectStateGroupId'], ['GET' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.update_object_state_group', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\ObjectState:updateObjectStateGroup'], ['objectStateGroupId'], ['PATCH' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.delete_object_state_group', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\ObjectState:deleteObjectStateGroup'], ['objectStateGroupId'], ['DELETE' => 0], null, false, true, null],
        ],
        8239 => [
            [['_route' => 'ibexa.rest.load_object_states', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\ObjectState:loadObjectStates'], ['objectStateGroupId'], ['GET' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.create_object_state', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\ObjectState:createObjectState'], ['objectStateGroupId'], ['POST' => 0], null, false, false, null],
        ],
        8276 => [
            [['_route' => 'ibexa.rest.load_object_state', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\ObjectState:loadObjectState'], ['objectStateGroupId', 'objectStateId'], ['GET' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.update_object_state', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\ObjectState:updateObjectState'], ['objectStateGroupId', 'objectStateId'], ['PATCH' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.delete_object_state', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\ObjectState:deleteObjectState'], ['objectStateGroupId', 'objectStateId'], ['DELETE' => 0], null, false, true, null],
        ],
        8291 => [[['_route' => 'ibexa.rest.options.content_objectstategroups_{objectStateGroupId}', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET,PATCH,DELETE'], ['objectStateGroupId'], ['OPTIONS' => 0], null, false, true, null]],
        8318 => [[['_route' => 'ibexa.rest.options.content_objectstategroups_{objectStateGroupId}_objectstates', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET,POST'], ['objectStateGroupId'], ['OPTIONS' => 0], null, false, false, null]],
        8351 => [[['_route' => 'ibexa.rest.options.content_objectstategroups_{objectStateGroupId}_objectstates_{objectStateId}', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET,PATCH,DELETE'], ['objectStateGroupId', 'objectStateId'], ['OPTIONS' => 0], null, false, true, null]],
        8379 => [
            [['_route' => 'ibexa.rest.load_section', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Section:loadSection'], ['sectionId'], ['GET' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.update_section', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Section:updateSection'], ['sectionId'], ['PATCH' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.delete_section', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Section:deleteSection'], ['sectionId'], ['DELETE' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.options.content_sections_{sectionId}', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET,PATCH,DELETE'], ['sectionId'], ['OPTIONS' => 0], null, false, true, null],
        ],
        8414 => [
            [['_route' => 'ibexa.rest.load_location', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Location:loadLocation'], ['locationPath'], ['GET' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.update_location', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Location:updateLocation'], ['locationPath'], ['PATCH' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.delete_subtree', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Location:deleteSubtree'], ['locationPath'], ['DELETE' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.copy_subtree', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Location:copySubtree'], ['locationPath'], ['COPY' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.move_subtree', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Location:moveSubtree'], ['locationPath'], ['MOVE' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.swap_location', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Location:swapLocation'], ['locationPath'], ['SWAP' => 0], null, false, true, null],
        ],
        8442 => [[['_route' => 'ibexa.rest.load_location_children', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Location:loadLocationChildren'], ['locationPath'], ['GET' => 0], null, false, false, null]],
        8471 => [[['_route' => 'ibexa.rest.list_location_url_aliases', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\URLAlias:listLocationURLAliases'], ['locationPath'], ['GET' => 0], null, false, false, null]],
        8489 => [[['_route' => 'ibexa.rest.options.content_locations_{locationPath}', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET,PATCH,DELETE,COPY,MOVE,SWAP'], ['locationPath'], ['OPTIONS' => 0], null, false, true, null]],
        8516 => [[['_route' => 'ibexa.rest.options.content_locations_{locationPath}_children', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET'], ['locationPath'], ['OPTIONS' => 0], null, false, false, null]],
        8545 => [[['_route' => 'ibexa.rest.options.content_locations_{locationPath}_urlaliases', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET'], ['locationPath'], ['OPTIONS' => 0], null, false, false, null]],
        8583 => [
            [['_route' => 'ibexa.rest.load_content_type_group', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\ContentType:loadContentTypeGroup'], ['contentTypeGroupId'], ['GET' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.update_content_type_group', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\ContentType:updateContentTypeGroup'], ['contentTypeGroupId'], ['PATCH' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.delete_content_type_group', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\ContentType:deleteContentTypeGroup'], ['contentTypeGroupId'], ['DELETE' => 0], null, false, true, null],
        ],
        8607 => [
            [['_route' => 'ibexa.rest.list_content_types_for_group', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\ContentType:listContentTypesForGroup'], ['contentTypeGroupId'], ['GET' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.create_content_type', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\ContentType:createContentType'], ['contentTypeGroupId'], ['POST' => 0], null, false, false, null],
        ],
        8622 => [[['_route' => 'ibexa.rest.options.content_typegroups_{contentTypeGroupId}', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET,PATCH,DELETE'], ['contentTypeGroupId'], ['OPTIONS' => 0], null, false, true, null]],
        8642 => [[['_route' => 'ibexa.rest.options.content_typegroups_{contentTypeGroupId}_types', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET,POST'], ['contentTypeGroupId'], ['OPTIONS' => 0], null, false, false, null]],
        8665 => [
            [['_route' => 'ibexa.rest.copy_content_type', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\ContentType:copyContentType'], ['contentTypeId'], ['COPY' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.load_content_type', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\ContentType:loadContentType'], ['contentTypeId'], ['GET' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.create_content_type_draft', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\ContentType:createContentTypeDraft'], ['contentTypeId'], ['POST' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.delete_content_type', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\ContentType:deleteContentType'], ['contentTypeId'], ['DELETE' => 0], null, false, true, null],
        ],
        8686 => [[['_route' => 'ibexa.rest.delete_content_type_draft', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\ContentType:deleteContentTypeDraft'], ['contentTypeId'], ['DELETE' => 0], null, false, false, null]],
        8717 => [[['_route' => 'ibexa.rest.load_content_type_field_definition_list', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\ContentType:loadContentTypeFieldDefinitionList'], ['contentTypeId'], ['GET' => 0], null, false, false, null]],
        8754 => [[['_route' => 'ibexa.rest.load_content_type_field_definition', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\ContentType:loadContentTypeFieldDefinition'], ['contentTypeId', 'fieldDefinitionId'], ['GET' => 0], null, false, true, null]],
        8790 => [[['_route' => 'ibexa.rest.load_content_type_field_definition_by_identifier', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\ContentType::loadContentTypeFieldDefinitionByIdentifier'], ['contentTypeId', 'fieldDefinitionIdentifier'], ['GET' => 0], null, false, true, null]],
        8813 => [
            [['_route' => 'ibexa.rest.load_content_type_draft', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\ContentType:loadContentTypeDraft'], ['contentTypeId'], ['GET' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.update_content_type_draft', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\ContentType:updateContentTypeDraft'], ['contentTypeId'], ['PATCH' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.publish_content_type_draft', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\ContentType:publishContentTypeDraft'], ['contentTypeId'], ['PUBLISH' => 0], null, false, false, null],
        ],
        8854 => [
            [['_route' => 'ibexa.rest.load_content_type_draft_field_definition_list', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\ContentType:loadContentTypeDraftFieldDefinitionList'], ['contentTypeId'], ['GET' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.add_content_type_draft_field_definition', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\ContentType:addContentTypeDraftFieldDefinition'], ['contentTypeId'], ['POST' => 0], null, false, false, null],
        ],
        8901 => [
            [['_route' => 'ibexa.rest.load_content_type_draft_field_definition', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\ContentType:loadContentTypeDraftFieldDefinition'], ['contentTypeId', 'fieldDefinitionId'], ['GET' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.update_content_type_draft_field_definition', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\ContentType:updateContentTypeDraftFieldDefinition'], ['contentTypeId', 'fieldDefinitionId'], ['PATCH' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.remove_content_type_draft_field_definition', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\ContentType:removeContentTypeDraftFieldDefinition'], ['contentTypeId', 'fieldDefinitionId'], ['DELETE' => 0], null, false, true, null],
        ],
        8926 => [
            [['_route' => 'ibexa.rest.load_groups_of_content_type', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\ContentType:loadGroupsOfContentType'], ['contentTypeId'], ['GET' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.link_content_type_to_group', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\ContentType:linkContentTypeToGroup'], ['contentTypeId'], ['POST' => 0], null, false, false, null],
        ],
        8954 => [[['_route' => 'ibexa.rest.unlink_content_type_from_group', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\ContentType:unlinkContentTypeFromGroup'], ['contentTypeId', 'contentTypeGroupId'], ['DELETE' => 0], null, false, true, null]],
        8968 => [[['_route' => 'ibexa.rest.options.content_types_{contentTypeId}', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'COPY,GET,POST,DELETE'], ['contentTypeId'], ['OPTIONS' => 0], null, false, true, null]],
        8999 => [[['_route' => 'ibexa.rest.options.content_types_{contentTypeId}_fieldDefinitions', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET'], ['contentTypeId'], ['OPTIONS' => 0], null, false, false, null]],
        9036 => [[['_route' => 'ibexa.rest.options.content_types_{contentTypeId}_fieldDefinitions_{fieldDefinitionId}', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET'], ['contentTypeId', 'fieldDefinitionId'], ['OPTIONS' => 0], null, false, true, null]],
        9072 => [[['_route' => 'ibexa.rest.options.content_types_{contentTypeId}_fieldDefinition_{fieldDefinitionIdentifier}', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET'], ['contentTypeId', 'fieldDefinitionIdentifier'], ['OPTIONS' => 0], null, false, true, null]],
        9092 => [[['_route' => 'ibexa.rest.options.content_types_{contentTypeId}_draft', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'DELETE,GET,PATCH,PUBLISH'], ['contentTypeId'], ['OPTIONS' => 0], null, false, false, null]],
        9129 => [[['_route' => 'ibexa.rest.options.content_types_{contentTypeId}_draft_fieldDefinitions', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET,POST'], ['contentTypeId'], ['OPTIONS' => 0], null, false, false, null]],
        9172 => [[['_route' => 'ibexa.rest.options.content_types_{contentTypeId}_draft_fieldDefinitions_{fieldDefinitionId}', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET,PATCH,DELETE'], ['contentTypeId', 'fieldDefinitionId'], ['OPTIONS' => 0], null, false, true, null]],
        9193 => [[['_route' => 'ibexa.rest.options.content_types_{contentTypeId}_groups', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET,POST'], ['contentTypeId'], ['OPTIONS' => 0], null, false, false, null]],
        9220 => [[['_route' => 'ibexa.rest.options.content_types_{contentTypeId}_groups_{contentTypeGroupId}', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'DELETE'], ['contentTypeId', 'contentTypeGroupId'], ['OPTIONS' => 0], null, false, true, null]],
        9244 => [
            [['_route' => 'ibexa.rest.load_trash_item', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Trash:loadTrashItem'], ['trashItemId'], ['GET' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.delete_trash_item', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Trash:deleteTrashItem'], ['trashItemId'], ['DELETE' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.restore_trash_item', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Trash:restoreTrashItem'], ['trashItemId'], ['MOVE' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.options.content_trash_{trashItemId}', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET,DELETE,MOVE'], ['trashItemId'], ['OPTIONS' => 0], null, false, true, null],
        ],
        9279 => [
            [['_route' => 'ibexa.rest.load_url_wildcard', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\URLWildcard:loadURLWildcard'], ['urlWildcardId'], ['GET' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.delete_url_wildcard', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\URLWildcard:deleteURLWildcard'], ['urlWildcardId'], ['DELETE' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.options.content_urlwildcards_{urlWildcardId}', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET,DELETE'], ['urlWildcardId'], ['OPTIONS' => 0], null, false, true, null],
        ],
        9308 => [
            [['_route' => 'ibexa.rest.load_url_alias', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\URLAlias:loadURLAlias'], ['urlAliasId'], ['GET' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.delete_url_alias', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\URLAlias:deleteURLAlias'], ['urlAliasId'], ['DELETE' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.options.content_urlaliases_{urlAliasId}', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET,DELETE'], ['urlAliasId'], ['OPTIONS' => 0], null, false, true, null],
        ],
        9350 => [
            [['_route' => 'ibexa.rest.corporate_account.companies.get', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\REST\\CompanyController::getCompanyAction'], ['companyId'], ['GET' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.corporate_account.companies.delete', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\REST\\CompanyController::deleteCompanyAction'], ['companyId'], ['DELETE' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.corporate_account.companies.update', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\REST\\CompanyController::updateCompanyAction'], ['companyId'], ['PATCH' => 0], null, false, true, null],
        ],
        9376 => [
            [['_route' => 'ibexa.rest.corporate_account.members.list', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\REST\\MemberController::getCompanyMembers'], ['companyId'], ['GET' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.corporate_account.members.create', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\REST\\MemberController::createMember'], ['companyId'], ['POST' => 0], null, false, false, null],
        ],
        9408 => [
            [['_route' => 'ibexa.rest.corporate_account.members.get', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\REST\\MemberController::getMember'], ['companyId', 'memberId'], ['GET' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.corporate_account.members.delete', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\REST\\MemberController::deleteMember'], ['companyId', 'memberId'], ['DELETE' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.corporate_account.members.patch', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\REST\\MemberController::updateMember'], ['companyId', 'memberId'], ['PATCH' => 0], null, false, true, null],
        ],
        9442 => [[['_route' => 'ibexa.calendar.rest.event.action', '_controller' => 'Ibexa\\Bundle\\Calendar\\Controller\\REST\\EventController::executeActionAction'], ['eventType'], ['POST' => 0], null, false, true, null]],
        9503 => [[['_route' => 'ibexa.udw.location.data', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\UniversalDiscoveryController::locationAction'], ['locationId'], ['GET' => 0], null, false, true, null]],
        9521 => [[['_route' => 'ibexa.udw.location.gridview.data', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\UniversalDiscoveryController::locationGridViewAction'], ['locationId'], ['GET' => 0], null, false, false, null]],
        9552 => [[['_route' => 'ibexa.udw.accordion.data', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\UniversalDiscoveryController::accordionAction'], ['locationId'], ['GET' => 0], null, false, true, null]],
        9570 => [[['_route' => 'ibexa.udw.accordion.gridview.data', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\UniversalDiscoveryController::accordionGridViewAction'], ['locationId'], ['GET' => 0], null, false, false, null]],
        9610 => [[['_route' => 'ibexa.rest.image.download', '_controller' => 'Ibexa\\Bundle\\AdminUi\\Controller\\DownloadImageController::downloadAction'], ['contentIdList'], ['GET' => 0], null, false, true, null]],
        9668 => [[['_route' => 'ibexa.personalization.rest.content_list', '_controller' => 'Ibexa\\Bundle\\Personalization\\Controller\\REST\\ContentController::getContentListAction'], ['contentIds'], ['GET' => 0], null, false, true, null]],
        9685 => [[['_route' => 'ibexa.personalization.rest.content.get_by_id', '_controller' => 'Ibexa\\Bundle\\Personalization\\Controller\\REST\\ContentController::getContentByIdAction'], ['contentId'], ['GET' => 0], null, false, true, null]],
        9725 => [[['_route' => 'ibexa.personalization.rest.content.get_by_remote_id', '_controller' => 'Ibexa\\Bundle\\Personalization\\Controller\\REST\\ContentController::getContentByRemoteIdAction'], ['remoteId'], ['GET' => 0], null, false, true, null]],
        9771 => [[['_route' => 'ibexa.personalization.rest.export.download', '_controller' => 'Ibexa\\Bundle\\Personalization\\Controller\\REST\\ExportController::downloadAction'], ['filePath'], null, null, false, true, null]],
        9812 => [[['_route' => 'ibexa.product_catalog.personalization.rest.product_variant.get_by_code', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\Personalization\\REST\\ProductVariantController::getProductVariantByCodeAction'], ['code'], ['GET' => 0], null, false, true, null]],
        9834 => [[['_route' => 'ibexa.product_catalog.personalization.rest.product_variant_list', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\Personalization\\REST\\ProductVariantController::getProductVariantListAction'], ['codes'], ['GET' => 0], null, false, true, null]],
        9903 => [[['_route' => 'ibexa.product_catalog.rest.catalogs.products.view', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\CatalogProductsViewController::createView'], ['catalogIdentifier'], ['POST' => 0], null, false, false, null]],
        9912 => [
            [['_route' => 'ibexa.product_catalog.rest.catalogs', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\CatalogController::getCatalogAction'], ['identifier'], ['GET' => 0], null, false, true, null],
            [['_route' => 'ibexa.product_catalog.rest.catalogs.update', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\CatalogController::updateCatalogAction'], ['identifier'], ['PATCH' => 0], null, false, true, null],
            [['_route' => 'ibexa.product_catalog.rest.catalogs.delete', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\CatalogController::deleteCatalogAction'], ['identifier'], ['DELETE' => 0], null, false, true, null],
        ],
        9935 => [[['_route' => 'ibexa.product_catalog.rest.catalogs.copy', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\CatalogController::copyCatalogAction'], ['identifier'], ['POST' => 0], null, false, true, null]],
        9966 => [
            [['_route' => 'ibexa.product_catalog.rest.currency', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\CurrencyController::getCurrencyAction'], ['id'], ['GET' => 0], null, false, true, null],
            [['_route' => 'ibexa.product_catalog.rest.currencies.update', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\CurrencyController::updateCurrencyAction'], ['id'], ['PATCH' => 0], null, false, true, null],
            [['_route' => 'ibexa.product_catalog.rest.currencies.delete', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\CurrencyController::deleteCurrencyAction'], ['id'], ['DELETE' => 0], null, false, true, null],
        ],
        9998 => [[['_route' => 'ibexa.product_catalog.rest.customer_group', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\CustomerGroupController::getCustomerGroupAction'], ['id'], ['GET' => 0], null, false, true, null]],
        10015 => [[['_route' => 'ibexa.product_catalog.rest.customer_groups.identifier', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\CustomerGroupController::getCustomerGroupByIdentifierAction'], ['identifier'], ['GET' => 0], null, false, true, null]],
        10033 => [
            [['_route' => 'ibexa.product_catalog.rest.customer_group.update', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\CustomerGroupController::updateCustomerGroupAction'], ['id'], ['PATCH' => 0], null, false, true, null],
            [['_route' => 'ibexa.product_catalog.rest.customer_groups.delete', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\CustomerGroupController::deleteCustomerGroupAction'], ['id'], ['DELETE' => 0], null, false, true, null],
        ],
        10089 => [[['_route' => 'ibexa.product_catalog.rest.product_variants.view', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\ProductVariantViewController::createView'], ['baseProductCode'], ['POST' => 0], null, false, true, null]],
        10107 => [[['_route' => 'ibexa.product_catalog.rest.product_variants.create', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\ProductVariantController::createProductVariantAction'], ['baseProductCode'], ['POST' => 0], null, false, true, null]],
        10134 => [[['_route' => 'ibexa.product_catalog.rest.product_variants.generate', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\ProductVariantController::generateProductVariantsAction'], ['baseProductCode'], ['POST' => 0], null, false, true, null]],
        10155 => [
            [['_route' => 'ibexa.product_catalog.rest.product_variants.update', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\ProductVariantController::updateProductVariantAction'], ['code'], ['PATCH' => 0], null, false, true, null],
            [['_route' => 'ibexa.product_catalog.rest.product_variants.delete', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\ProductVariantController::deleteProductVariantAction'], ['code'], ['DELETE' => 0], null, false, true, null],
        ],
        10176 => [[['_route' => 'ibexa.product_catalog.rest.product_variant', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\ProductVariantController::getProductVariantAction'], ['code'], ['GET' => 0], null, false, true, null]],
        10207 => [
            [['_route' => 'ibexa.product_catalog.rest.product_type', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\ProductTypeController::getProductTypeAction'], ['identifier'], ['GET' => 0], null, false, true, null],
            [['_route' => 'ibexa.product_catalog.rest.product_types.update', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\ProductTypeController::updateProductTypeAction'], ['identifier'], ['PATCH' => 0], null, false, true, null],
            [['_route' => 'ibexa.product_catalog.rest.product_types.delete', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\ProductTypeController::deleteProductTypeAction'], ['identifier'], ['DELETE' => 0], null, false, true, null],
        ],
        10234 => [[['_route' => 'ibexa.product_catalog.rest.product_types.is_used', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\ProductTypeController::isProductTypeUsedAction'], ['identifier'], ['GET' => 0], null, false, true, null]],
        10269 => [
            [['_route' => 'ibexa.product_catalog.rest.prices.create', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\PriceController::createPrice'], ['productCode'], ['POST' => 0], null, false, false, null],
            [['_route' => 'ibexa.product_catalog.rest.prices.list', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\PriceController::getPrices'], ['productCode'], ['GET' => 0], null, false, false, null],
        ],
        10316 => [[['_route' => 'ibexa.product_catalog.rest.prices.get.custom_price', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\PriceController::getPrice'], ['productCode', 'currencyCode', 'customerGroupIdentifier'], ['GET' => 0], null, false, true, null]],
        10326 => [[['_route' => 'ibexa.product_catalog.rest.prices.get.base_price', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\PriceController::getPrice'], ['productCode', 'currencyCode'], ['GET' => 0], null, false, true, null]],
        10336 => [
            [['_route' => 'ibexa.product_catalog.rest.prices.update', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\PriceController::updatePrice'], ['productCode', 'id'], ['PATCH' => 0], null, false, true, null],
            [['_route' => 'ibexa.product_catalog.rest.prices.delete', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\PriceController::deletePrice'], ['productCode', 'id'], ['DELETE' => 0], null, false, true, null],
        ],
        10348 => [[['_route' => 'ibexa.product_catalog.rest.product', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\ProductController::getProductAction'], ['code'], ['GET' => 0], null, false, true, null]],
        10358 => [[['_route' => 'ibexa.product_catalog.rest.products.create', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\ProductController::createProductAction'], ['productTypeIdentifier'], ['POST' => 0], null, false, true, null]],
        10368 => [[['_route' => 'ibexa.product_catalog.rest.products.update', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\ProductController::updateProductAction'], ['code'], ['PATCH' => 0], null, false, true, null]],
        10378 => [[['_route' => 'ibexa.product_catalog.rest.products.delete', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\ProductController::deleteProductAction'], ['identifier'], ['DELETE' => 0], null, false, true, null]],
        10427 => [
            [['_route' => 'ibexa.product_catalog.rest.attribute_group', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\AttributeGroupController::getAttributeGroupAction'], ['identifier'], ['GET' => 0], null, false, true, null],
            [['_route' => 'ibexa.product_catalog.rest.attribute_groups.update', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\AttributeGroupController::updateAttributeGroupAction'], ['identifier'], ['PATCH' => 0], null, false, true, null],
            [['_route' => 'ibexa.product_catalog.rest.attribute_groups.delete', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\AttributeGroupController::deleteAttributeGroupAction'], ['identifier'], ['DELETE' => 0], null, false, true, null],
        ],
        10467 => [[['_route' => 'ibexa.product_catalog.rest.attribute_groups.translation.delete', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\AttributeGroupController::deleteAttributeGroupTranslationAction'], ['identifier', 'languageCode'], ['DELETE' => 0], null, false, true, null]],
        10492 => [[['_route' => 'ibexa.product_catalog.rest.attribute_type', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\AttributeController::getAttributeTypeAction'], ['identifier'], ['GET' => 0], null, false, true, null]],
        10519 => [[['_route' => 'ibexa.product_catalog.rest.attribute', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\AttributeController::getAttributeAction'], ['identifier'], ['GET' => 0], null, false, true, null]],
        10538 => [[['_route' => 'ibexa.product_catalog.rest.attributes.update', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\AttributeController::updateAttributeAction'], ['identifier', 'groupIdentifier'], ['PATCH' => 0], null, false, true, null]],
        10548 => [[['_route' => 'ibexa.product_catalog.rest.attributes.delete', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\AttributeController::deleteAttributeAction'], ['identifier'], ['DELETE' => 0], null, false, true, null]],
        10588 => [[['_route' => 'ibexa.product_catalog.rest.attributes.translation.delete', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\AttributeController::deleteAttributeTranslationAction'], ['identifier', 'languageCode'], ['DELETE' => 0], null, false, true, null]],
        10616 => [[['_route' => 'ibexa.product_catalog.rest.region', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\RegionController::getRegionAction'], ['identifier'], ['GET' => 0], null, false, true, null]],
        10641 => [[['_route' => 'ibexa.product_catalog.rest.vat.list', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\VatController::getVatCategories'], ['region'], ['GET' => 0], null, false, true, null]],
        10660 => [[['_route' => 'ibexa.product_catalog.rest.vat', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\VatController::getVatCategoryAction'], ['region', 'identifier'], ['GET' => 0], null, false, true, null]],
        10690 => [[['_route' => 'ibexa.rest.views.load', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Views:getView'], ['viewId'], ['GET' => 0], null, false, true, null]],
        10711 => [
            [['_route' => 'ibexa.rest.views.load.results', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Views:loadViewResults'], ['viewId'], ['GET' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.options.views_{viewId}_results', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET'], ['viewId'], ['OPTIONS' => 0], null, false, false, null],
        ],
        10722 => [[['_route' => 'ibexa.rest.options.views_{viewId}', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET'], ['viewId'], ['OPTIONS' => 0], null, false, true, null]],
        10758 => [
            [['_route' => 'ibexa.rest.create_role_draft', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Role:createRoleDraft'], ['roleId'], ['POST' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.load_role', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Role:loadRole'], ['roleId'], ['GET' => 0], null, false, true, null],
        ],
        10780 => [[['_route' => 'ibexa.rest.load_role_draft', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Role:loadRoleDraft'], ['roleId'], ['GET' => 0], null, false, false, null]],
        10795 => [[['_route' => 'ibexa.rest.update_role', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Role:updateRole'], ['roleId'], ['PATCH' => 0], null, false, true, null]],
        10819 => [
            [['_route' => 'ibexa.rest.update_role_draft', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Role:updateRoleDraft'], ['roleId'], ['PATCH' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.publish_role_draft', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Role:publishRoleDraft'], ['roleId'], ['PUBLISH' => 0], null, false, false, null],
        ],
        10835 => [[['_route' => 'ibexa.rest.delete_role', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Role:deleteRole'], ['roleId'], ['DELETE' => 0], null, false, true, null]],
        10856 => [[['_route' => 'ibexa.rest.delete_role_draft', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Role:deleteRoleDraft'], ['roleId'], ['DELETE' => 0], null, false, false, null]],
        10883 => [
            [['_route' => 'ibexa.rest.load_policies', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Role:loadPolicies'], ['roleId'], ['GET' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.add_policy', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Role:addPolicy'], ['roleId'], ['POST' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.delete_policies', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Role:deletePolicies'], ['roleId'], ['DELETE' => 0], null, false, false, null],
        ],
        10917 => [
            [['_route' => 'ibexa.rest.load_policy', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Role:loadPolicy'], ['roleId', 'policyId'], ['GET' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.update_policy', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Role:updatePolicy'], ['roleId', 'policyId'], ['PATCH' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.delete_policy', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Role:deletePolicy'], ['roleId', 'policyId'], ['DELETE' => 0], null, false, true, null],
        ],
        10933 => [[['_route' => 'ibexa.rest.options.user_roles_{roleId}', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'POST,GET,PATCH,DELETE'], ['roleId'], ['OPTIONS' => 0], null, false, true, null]],
        10954 => [[['_route' => 'ibexa.rest.options.user_roles_{roleId}_draft', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET,PATCH,PUBLISH,DELETE'], ['roleId'], ['OPTIONS' => 0], null, false, false, null]],
        10978 => [[['_route' => 'ibexa.rest.options.user_roles_{roleId}_policies', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET,POST,DELETE'], ['roleId'], ['OPTIONS' => 0], null, false, false, null]],
        11008 => [[['_route' => 'ibexa.rest.options.user_roles_{roleId}_policies_{policyId}', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET,PATCH,DELETE'], ['roleId', 'policyId'], ['OPTIONS' => 0], null, false, true, null]],
        11036 => [
            [['_route' => 'ibexa.rest.load_user', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\User:loadUser'], ['userId'], ['GET' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.update_user', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\User:updateUser'], ['userId'], ['PATCH' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.delete_user', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\User:deleteUser'], ['userId'], ['DELETE' => 0], null, false, true, null],
        ],
        11062 => [
            [['_route' => 'ibexa.rest.load_user_groups_of_user', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\User:loadUserGroupsOfUser'], ['userId'], ['GET' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.assign_user_to_user_group', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\User:assignUserToUserGroup'], ['userId'], ['POST' => 0], null, false, false, null],
        ],
        11091 => [[['_route' => 'ibexa.rest.unassign_user_from_user_group', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\User:unassignUserFromUserGroup'], ['userId', 'groupPath'], ['DELETE' => 0], null, false, true, null]],
        11113 => [[['_route' => 'ibexa.rest.load_user_drafts', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\User:loadUserDrafts'], ['userId'], ['GET' => 0], null, false, false, null]],
        11137 => [
            [['_route' => 'ibexa.rest.load_role_assignments_for_user', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Role:loadRoleAssignmentsForUser'], ['userId'], ['GET' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.assign_role_to_user', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Role:assignRoleToUser'], ['userId'], ['POST' => 0], null, false, false, null],
        ],
        11168 => [
            [['_route' => 'ibexa.rest.load_role_assignment_for_user', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Role:loadRoleAssignmentForUser'], ['userId', 'roleId'], ['GET' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.unassign_role_from_user', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Role:unassignRoleFromUser'], ['userId', 'roleId'], ['DELETE' => 0], null, false, true, null],
        ],
        11184 => [[['_route' => 'ibexa.rest.options.user_users_{userId}', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET,PATCH,DELETE'], ['userId'], ['OPTIONS' => 0], null, false, true, null]],
        11206 => [[['_route' => 'ibexa.rest.options.user_users_{userId}_groups', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET,POST'], ['userId'], ['OPTIONS' => 0], null, false, false, null]],
        11234 => [[['_route' => 'ibexa.rest.options.user_users_{userId}_groups_{groupPath}', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'DELETE'], ['userId', 'groupPath'], ['OPTIONS' => 0], null, false, true, null]],
        11256 => [[['_route' => 'ibexa.rest.options.user_users_{userId}_drafts', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET'], ['userId'], ['OPTIONS' => 0], null, false, false, null]],
        11277 => [[['_route' => 'ibexa.rest.options.user_users_{userId}_roles', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET,POST'], ['userId'], ['OPTIONS' => 0], null, false, false, null]],
        11304 => [[['_route' => 'ibexa.rest.options.user_users_{userId}_roles_{roleId}', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET,DELETE'], ['userId', 'roleId'], ['OPTIONS' => 0], null, false, true, null]],
        11331 => [
            [['_route' => 'ibexa.segmentation.rest.user.segments.view', '_controller' => 'Ibexa\\Bundle\\Segmentation\\Controller\\REST\\UserSegment\\UserSegmentListController::createView'], ['userId'], ['GET' => 0], null, false, false, null],
            [['_route' => 'ibexa.segmentation.rest.user.segments.assign', '_controller' => 'Ibexa\\Bundle\\Segmentation\\Controller\\REST\\UserSegment\\UserSegmentAssignController::assignSegmentToUser'], ['userId'], ['POST' => 0], null, false, false, null],
        ],
        11365 => [[['_route' => 'ibexa.segmentation.rest.user.segments.unnassign', '_controller' => 'Ibexa\\Bundle\\Segmentation\\Controller\\REST\\UserSegment\\UserSegmentUnassignController::removeSegmentFromUser'], ['userId', 'segmentIdentifier'], ['DELETE' => 0], null, false, true, null]],
        11398 => [
            [['_route' => 'ibexa.rest.load_user_group', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\User:loadUserGroup'], ['groupPath'], ['GET' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.update_user_group', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\User:updateUserGroup'], ['groupPath'], ['PATCH' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.delete_user_group', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\User:deleteUserGroup'], ['groupPath'], ['DELETE' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.move_user_group', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\User:moveUserGroup'], ['groupPath'], ['MOVE' => 0], null, false, true, null],
        ],
        11431 => [
            [['_route' => 'ibexa.rest.load_sub_user_groups', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\User:loadSubUserGroups'], ['groupPath'], ['GET' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.create_user_group', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\User:createUserGroup'], ['groupPath'], ['POST' => 0], null, false, false, null],
        ],
        11460 => [
            [['_route' => 'ibexa.rest.load_users_from_group', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\User:loadUsersFromGroup'], ['groupPath'], ['GET' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.create_user', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\User:createUser'], ['groupPath'], ['POST' => 0], null, false, false, null],
        ],
        11489 => [
            [['_route' => 'ibexa.rest.load_role_assignments_for_user_group', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Role:loadRoleAssignmentsForUserGroup'], ['groupPath'], ['GET' => 0], null, false, false, null],
            [['_route' => 'ibexa.rest.assign_role_to_user_group', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Role:assignRoleToUserGroup'], ['groupPath'], ['POST' => 0], null, false, false, null],
        ],
        11524 => [
            [['_route' => 'ibexa.rest.load_role_assignment_for_user_group', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Role:loadRoleAssignmentForUserGroup'], ['groupPath', 'roleId'], ['GET' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.unassign_role_from_user_group', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Role:unassignRoleFromUserGroup'], ['groupPath', 'roleId'], ['DELETE' => 0], null, false, true, null],
        ],
        11544 => [[['_route' => 'ibexa.rest.options.user_groups_{groupPath}', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET,PATCH,DELETE,MOVE'], ['groupPath'], ['OPTIONS' => 0], null, false, true, null]],
        11573 => [[['_route' => 'ibexa.rest.options.user_groups_{groupPath}_subgroups', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET,POST'], ['groupPath'], ['OPTIONS' => 0], null, false, false, null]],
        11598 => [[['_route' => 'ibexa.rest.options.user_groups_{groupPath}_users', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET,POST'], ['groupPath'], ['OPTIONS' => 0], null, false, false, null]],
        11623 => [[['_route' => 'ibexa.rest.options.user_groups_{groupPath}_roles', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET,POST'], ['groupPath'], ['OPTIONS' => 0], null, false, false, null]],
        11654 => [[['_route' => 'ibexa.rest.options.user_groups_{groupPath}_roles_{roleId}', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET,DELETE'], ['groupPath', 'roleId'], ['OPTIONS' => 0], null, false, true, null]],
        11688 => [[['_route' => 'ibexa.rest.delete_session', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\SessionController:deleteSessionAction', 'csrf_protection' => false], ['sessionId'], ['DELETE' => 0], null, false, true, null]],
        11706 => [[['_route' => 'ibexa.rest.refresh_session', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\SessionController:refreshSessionAction', 'csrf_protection' => false], ['sessionId'], ['POST' => 0], null, false, false, null]],
        11724 => [[['_route' => 'ibexa.rest.options.user_sessions_current', 'csrf_protection' => false, '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'GET'], [], ['OPTIONS' => 0], null, false, false, null]],
        11745 => [[['_route' => 'ibexa.rest.options.user_sessions_{sessionId}', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'csrf_protection' => false, 'allowedMethods' => 'DELETE'], ['sessionId'], ['OPTIONS' => 0], null, false, true, null]],
        11763 => [[['_route' => 'ibexa.rest.options.user_sessions_{sessionId}_refresh', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'csrf_protection' => false, 'allowedMethods' => 'POST'], ['sessionId'], ['OPTIONS' => 0], null, false, false, null]],
        11796 => [
            [['_route' => 'ibexa.rest.create_bookmark', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Bookmark:createBookmark'], ['locationId'], ['POST' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.delete_bookmark', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Bookmark:deleteBookmark'], ['locationId'], ['DELETE' => 0], null, false, true, null],
            [['_route' => 'ibexa.rest.is_bookmarked', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Bookmark:isBookmarked'], ['locationId'], ['GET' => 0, 'HEAD' => 1], null, false, true, null],
            [['_route' => 'ibexa.rest.options.bookmark_{locationId}', '_controller' => 'Ibexa\\Rest\\Server\\Controller\\Options:getRouteOptions', 'allowedMethods' => 'POST,DELETE,GET,HEAD'], ['locationId'], ['OPTIONS' => 0], null, false, true, null],
        ],
        11827 => [[['_route' => 'ibexa.segmentation.rest.segments.view', '_controller' => 'Ibexa\\Bundle\\Segmentation\\Controller\\REST\\Segment\\SegmentViewController::createView'], ['identifier'], ['GET' => 0], null, false, true, null]],
        11865 => [[['_route' => 'ibexa.segmentation.rest.segment_groups.list.segments', '_controller' => 'Ibexa\\Bundle\\Segmentation\\Controller\\REST\\Segment\\SegmentListController::createView'], ['identifier'], ['GET' => 0], null, false, false, null]],
        11875 => [[['_route' => 'ibexa.segmentation.rest.segment_groups.view', '_controller' => 'Ibexa\\Bundle\\Segmentation\\Controller\\REST\\SegmentGroup\\SegmentGroupViewController::createView'], ['identifier'], ['GET' => 0], null, false, true, null]],
        11924 => [[['_route' => 'ibexa.taxonomy.rest.entries.remove', '_controller' => 'Ibexa\\Bundle\\Taxonomy\\Controller\\REST\\TaxonomyEntryController::bulkRemoveAction'], ['taxonomyName'], ['DELETE' => 0], null, false, false, null]],
        11964 => [[['_route' => 'ibexa.taxonomy.rest.entries.move', '_controller' => 'Ibexa\\Bundle\\Taxonomy\\Controller\\REST\\TaxonomyEntryController::bulkMoveAction'], ['taxonomyName'], ['POST' => 0], null, false, false, null]],
        12003 => [[['_route' => 'ibexa.taxonomy.rest.entry.load_by_id', '_controller' => 'Ibexa\\Bundle\\Taxonomy\\Controller\\REST\\TaxonomyEntryController::loadByIdAction'], ['taxonomyName', 'id'], ['GET' => 0], null, false, true, null]],
        12065 => [[['_route' => 'ibexa.taxonomy.rest.entry.load_by_identifier', '_controller' => 'Ibexa\\Bundle\\Taxonomy\\Controller\\REST\\TaxonomyEntryController::loadByIdentifierAction'], ['taxonomyName', 'identifier'], ['GET' => 0], null, false, true, null]],
        12116 => [[['_route' => 'ibexa.taxonomy.rest.entry.load_by_content_id', '_controller' => 'Ibexa\\Bundle\\Taxonomy\\Controller\\REST\\TaxonomyEntryController::loadByContentIdAction'], ['taxonomyName', 'contentId'], ['GET' => 0], null, false, true, null]],
        12182 => [[['_route' => 'ibexa.taxonomy.rest.entry_assignments.assign_to_content', '_controller' => 'Ibexa\\Bundle\\Taxonomy\\Controller\\REST\\TaxonomyEntryAssignmentController::assignToContentAction'], ['taxonomyName'], ['POST' => 0], null, false, false, null]],
        12246 => [[['_route' => 'ibexa.taxonomy.rest.entry_assignments.load', '_controller' => 'Ibexa\\Bundle\\Taxonomy\\Controller\\REST\\TaxonomyEntryAssignmentController::loadAssignmentsAction'], ['taxonomyName', 'contentId'], ['GET' => 0], null, false, true, null]],
        12316 => [[['_route' => 'ibexa.taxonomy.rest.entry_assignments.unassign_from_content', '_controller' => 'Ibexa\\Bundle\\Taxonomy\\Controller\\REST\\TaxonomyEntryAssignmentController::unassignFromContentAction'], ['taxonomyName'], ['POST' => 0], null, false, false, null]],
        12367 => [[['_route' => 'ibexa.taxonomy.rest.entry_assignment.load_by_id', '_controller' => 'Ibexa\\Bundle\\Taxonomy\\Controller\\REST\\TaxonomyEntryAssignmentController::loadAssignmentByIdAction'], ['taxonomyName', 'id'], ['GET' => 0], null, false, true, null]],
        12461 => [
            [['_route' => 'ibexa.scheduler.rest.schedule_version', '_controller' => 'Ibexa\\Bundle\\Scheduler\\Controller\\DateBasedPublisherController:scheduleVersionAction'], ['contentId', 'versionNumber', 'publicationTimestamp'], ['PUT' => 0], null, false, true, null],
            [['_route' => 'ibexa.scheduler.rest.reschedule_version', '_controller' => 'Ibexa\\Bundle\\Scheduler\\Controller\\DateBasedPublisherController:rescheduleVersionAction'], ['contentId', 'versionNumber', 'publicationTimestamp'], ['PATCH' => 0], null, false, true, null],
        ],
        12492 => [[['_route' => 'ibexa.scheduler.rest.unschedule_version', '_controller' => 'Ibexa\\Bundle\\Scheduler\\Controller\\DateBasedPublisherController:unscheduleVersionAction'], ['contentId', 'versionNumber'], ['DELETE' => 0], null, false, true, null]],
        12531 => [[['_route' => 'ibexa.scheduler.rest.get_scheduled_version', '_controller' => 'Ibexa\\Bundle\\Scheduler\\Controller\\DateBasedPublisherController:getScheduledVersionAction'], ['contentId', 'versionNumber'], ['GET' => 0], null, false, false, null]],
        12577 => [[['_route' => 'ibexa.scheduler.rest.get_scheduled_content_versions', '_controller' => 'Ibexa\\Bundle\\Scheduler\\Controller\\DateBasedPublisherController:getScheduledContentVersionsAction', 'page' => 0, 'limit' => 10], ['contentId', 'page', 'limit'], ['GET' => 0], null, false, true, null]],
        12621 => [[['_route' => 'ibexa.scheduler.rest.get_scheduled_versions', '_controller' => 'Ibexa\\Bundle\\Scheduler\\Controller\\DateBasedPublisherController:getScheduledVersionsAction', 'page' => 0, 'limit' => 10], ['page', 'limit'], ['GET' => 0], null, false, true, null]],
        12658 => [[['_route' => 'ibexa.scheduler.rest.get_user_scheduled_versions', '_controller' => 'Ibexa\\Bundle\\Scheduler\\Controller\\DateBasedPublisherController:getUserScheduledVersionsAction', 'page' => 0, 'limit' => 10], ['page', 'limit'], ['GET' => 0], null, false, true, null]],
        12700 => [[['_route' => 'ibexa.corporate_account.application.details', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\ApplicationDetailsController::detailsAction'], ['applicationId'], null, null, false, true, null]],
        12726 => [[['_route' => 'ibexa.corporate_account.application.edit', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\ApplicationEditController::editAction'], ['applicationId'], null, null, false, true, null]],
        12745 => [[['_route' => 'ibexa.corporate_account.application.edit.internal', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\ApplicationEditController::editInternalAction'], ['applicationId'], null, null, false, false, null]],
        12782 => [[['_route' => 'ibexa.corporate_account.application.workflow.state', '_controller' => 'Ibexa\\Bundle\\CorporateAccount\\Controller\\ApplicationWorkflowController::dispatchAction'], ['applicationId', 'state'], null, null, false, true, null]],
        12882 => [[['_route' => 'ibexa.address.country.form.create', '_controller' => 'Ibexa\\Bundle\\FieldTypeAddress\\Controller\\AddressController::countryCreateFormAction'], ['contentTypeIdentifier', 'fieldIdentifier', 'languageCode', 'parentLocationId', 'formName', 'country'], ['GET' => 0], null, false, true, null]],
        12938 => [[['_route' => 'ibexa.address.country.form.update', '_controller' => 'Ibexa\\Bundle\\FieldTypeAddress\\Controller\\AddressController::countryUpdateFormAction'], ['contentTypeIdentifier', 'fieldIdentifier', 'languageCode', 'contentId', 'formName', 'country'], ['GET' => 0], null, false, true, null]],
        13014 => [[['_route' => 'ibexa.product_catalog.attribute_definition.create', 'attributeGroupIdentifier' => null, '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\AttributeDefinition\\CreateController::executeAction', 'siteaccess_group_whitelist' => 'admin_group'], ['attributeType', 'languageCode', 'attributeGroupIdentifier'], ['GET' => 0, 'POST' => 1], null, false, true, null]],
        13067 => [[['_route' => 'ibexa.product_catalog.attribute_definition.update', 'toLanguageCode' => null, 'fromLanguageCode' => null, '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\AttributeDefinition\\UpdateController::executeAction', 'siteaccess_group_whitelist' => 'admin_group'], ['attributeDefinitionIdentifier', 'toLanguageCode', 'fromLanguageCode'], ['GET' => 0, 'POST' => 1], null, false, true, null]],
        13092 => [[['_route' => 'ibexa.product_catalog.attribute_definition.delete', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\AttributeDefinition\\DeleteController::executeAction', 'siteaccess_group_whitelist' => 'admin_group'], ['attributeDefinitionIdentifier'], ['GET' => 0, 'POST' => 1], null, false, true, null]],
        13110 => [[['_route' => 'ibexa.product_catalog.attribute_definition.view', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\AttributeDefinition\\ViewController::renderAction', 'siteaccess_group_whitelist' => 'admin_group'], ['attributeDefinitionIdentifier'], ['GET' => 0], null, false, true, null]],
        13173 => [[['_route' => 'ibexa.product_catalog.attribute_group.update', 'toLanguageCode' => null, 'fromLanguageCode' => null, '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\AttributeGroup\\UpdateController::executeAction', 'siteaccess_group_whitelist' => 'admin_group'], ['attributeGroupIdentifier', 'toLanguageCode', 'fromLanguageCode'], ['GET' => 0, 'POST' => 1], null, false, true, null]],
        13183 => [[['_route' => 'ibexa.product_catalog.attribute_group.view', '_controller' => 'Ibexa\\Bundle\\ProductCatalog\\Controller\\AttributeGroup\\ViewController::renderAction', 'siteaccess_group_whitelist' => 'admin_group'], ['attributeGroupIdentifier'], ['GET' => 0], null, false, true, null]],
        13230 => [[['_route' => 'ibexa.dashboard.change_active', '_controller' => 'Ibexa\\Bundle\\Dashboard\\Controller\\ChangeActiveDashboardController::changeActiveAction', 'siteaccess_group_whitelist' => 'admin_group'], ['locationId'], ['POST' => 0], null, false, true, null]],
        13286 => [[['_route' => 'ibexa.page.block.render', '_controller' => 'Ibexa\\Bundle\\FieldTypePage\\Controller\\BlockController::renderAction'], ['blockId', 'contentId', 'versionNo', 'languageCode', 'locationId'], null, null, false, true, null]],
        13333 => [[['_route' => 'ibexa.form_builder.form.preview_form_field', '_controller' => 'Ibexa\\Bundle\\FormBuilder\\Controller\\FormBuilderController::formFieldPreviewAction', 'siteaccess_group_whitelist' => 'admin_group'], ['contentId', 'languageCode'], ['GET' => 0], null, false, true, null]],
        13389 => [[['_route' => 'ibexa.form_builder.field.request_configuration_form', '_controller' => 'Ibexa\\Bundle\\FormBuilder\\Controller\\FieldController::requestFieldConfigurationAction', 'siteaccess_group_whitelist' => 'admin_group'], ['languageCode'], ['POST' => 0], null, false, true, null]],
        13426 => [[['_route' => 'ibexa.form_builder.field.configure', '_controller' => 'Ibexa\\Bundle\\FormBuilder\\Controller\\FieldController::configureFieldAction', 'siteaccess_group_whitelist' => 'admin_group'], ['fieldIdentifier', 'languageCode'], ['POST' => 0], null, false, true, null]],
        13462 => [[['_route' => 'ibexa.form_builder.captcha.get_url', '_controller' => 'Ibexa\\Bundle\\FormBuilder\\Controller\\CaptchaController::getCaptchaPathAction'], ['fieldId'], ['GET' => 0], null, false, true, null]],
        13502 => [[['_route' => 'ibexa.user.from_invite.register', '_controller' => 'Ibexa\\Bundle\\User\\Controller\\UserRegisterController::registerFromInvitationAction'], ['inviteHash'], null, null, false, true, null]],
        13560 => [[['_route' => 'ibexa.image_editor.update_image_asset', 'languageCode' => null, '_controller' => 'Ibexa\\Bundle\\ImageEditor\\Controller\\ImageAssetController::updateExistingImageAsset'], ['contentId', 'languageCode'], ['PUT' => 0], null, false, true, null]],
        13605 => [[['_route' => 'ibexa.image_editor.create_from_image_asset', 'languageCode' => null, '_controller' => 'Ibexa\\Bundle\\ImageEditor\\Controller\\ImageAssetController::createNewImageAsset'], ['fromContentId', 'languageCode'], ['POST' => 0], null, false, true, null]],
        13667 => [[['_route' => 'ibexa.image_editor.get_base_64', 'versionNo' => null, 'languageCode' => null, '_controller' => 'Ibexa\\Bundle\\ImageEditor\\Controller\\Base64Controller::getBase64'], ['contentId', 'fieldIdentifier', 'versionNo', 'languageCode'], ['GET' => 0], null, false, true, null]],
        13705 => [[['_route' => 'ibexa.oauth2.connect', '_controller' => 'Ibexa\\Bundle\\OAuth2Client\\Controller\\OAuth2Controller::connectAction'], ['identifier'], null, null, false, true, null]],
        13728 => [[['_route' => 'ibexa.oauth2.check', '_controller' => 'Ibexa\\Bundle\\OAuth2Client\\Controller\\OAuth2Controller::checkAction'], ['identifier'], null, null, false, true, null]],
        13785 => [[['_route' => 'ibexa.taxonomy.entry.create.proxy', '_controller' => 'Ibexa\\Bundle\\Taxonomy\\Controller\\ContentProxyController::createProxyAction'], ['taxonomyName'], ['POST' => 0], null, false, false, null]],
        13825 => [[['_route' => 'ibexa.taxonomy.entry.delete', '_controller' => 'Ibexa\\Bundle\\Taxonomy\\Controller\\ContentController::deleteAction'], ['taxonomyName'], ['POST' => 0], null, false, false, null]],
        13863 => [[['_route' => 'ibexa.taxonomy.entry.move', '_controller' => 'Ibexa\\Bundle\\Taxonomy\\Controller\\ContentController::moveAction'], ['taxonomyName'], ['POST' => 0], null, false, false, null]],
        13903 => [[['_route' => 'ibexa.taxonomy.entry.assign', '_controller' => 'Ibexa\\Bundle\\Taxonomy\\Controller\\ContentController::assignTaxonomyEntryAction'], ['taxonomyName'], ['POST' => 0], null, false, false, null]],
        13945 => [[['_route' => 'ibexa.taxonomy.entry.unassign', '_controller' => 'Ibexa\\Bundle\\Taxonomy\\Controller\\ContentController::unassignTaxonomyEntryAction'], ['taxonomyName'], ['POST' => 0], null, false, false, null]],
        13976 => [[['_route' => 'ibexa.taxonomy.tree.root', 'taxonomyName' => null, '_controller' => 'Ibexa\\Bundle\\Taxonomy\\Controller\\TreeController::getRootAction'], ['taxonomyName'], ['GET' => 0], null, false, false, null]],
        13993 => [[['_route' => 'ibexa.taxonomy.tree.subtree', '_controller' => 'Ibexa\\Bundle\\Taxonomy\\Controller\\TreeController::getSubtreeAction'], ['taxonomyName'], ['GET' => 0], null, false, false, null]],
        14032 => [[['_route' => 'ibexa.taxonomy.tree.node', '_controller' => 'Ibexa\\Bundle\\Taxonomy\\Controller\\TreeController::getNodeAction'], ['taxonomyName', 'entryId'], ['GET' => 0], null, false, true, null]],
        14071 => [[['_route' => 'ibexa.taxonomy.tree.search', '_controller' => 'Ibexa\\Bundle\\Taxonomy\\Controller\\TreeController::nodeSearchAction'], ['taxonomyName'], ['GET' => 0], null, false, false, null]],
        14122 => [[['_route' => 'bazinga_jstranslation_js', '_controller' => 'bazinga.jstranslation.controller::getTranslationsAction', 'domain' => 'messages', '_format' => 'js'], ['domain', '_format'], ['GET' => 0], null, false, true, null]],
        14186 => [[['_route' => 'ibexa.workflow.transition.list', '_controller' => 'Ibexa\\Bundle\\Workflow\\Controller\\TransitionController::getTransitionListSnippetAction'], ['workflowName', 'contentId', 'versionNo'], null, null, false, false, null]],
        14209 => [[['_route' => 'ibexa.workflow.view', '_controller' => 'Ibexa\\Bundle\\Workflow\\Controller\\WorkflowController::viewAction'], ['workflowName'], null, null, false, true, null]],
        14240 => [[['_route' => 'ibexa.workflow.unlock', '_controller' => 'Ibexa\\Bundle\\Workflow\\Controller\\WorkflowController::unlockVersionAction'], ['contentId', 'versionNo'], null, null, false, true, null]],
        14271 => [[['_route' => 'ibexa.workflow.unlock.ask', '_controller' => 'Ibexa\\Bundle\\Workflow\\Controller\\WorkflowController::askUnlockVersionAction'], ['contentId', 'versionNo', 'userId'], null, null, false, true, null]],
        14385 => [[['_route' => 'ibexa.workflow.content_create.reviewer_suggest', '_controller' => 'Ibexa\\Bundle\\Workflow\\Controller\\SuggestReviewerController::findForContentCreateAction'], ['workflowName', 'transitionName', 'contentTypeIdentifier', 'languageCode', 'locationId'], null, null, false, true, null]],
        14491 => [[['_route' => 'ibexa.workflow.content_edit.reviewer_suggest', '_controller' => 'Ibexa\\Bundle\\Workflow\\Controller\\SuggestReviewerController::findForContentEditAction'], ['workflowName', 'transitionName', 'contentId', 'versionNo', 'locationId'], null, null, false, true, null]],
        14556 => [[['_route' => 'liip_imagine_filter_runtime', '_controller' => 'Liip\\ImagineBundle\\Controller\\ImagineController::filterRuntimeAction'], ['filter', 'hash', 'path'], ['GET' => 0], null, false, true, null]],
        14584 => [
            [['_route' => 'liip_imagine_filter', '_controller' => 'Liip\\ImagineBundle\\Controller\\ImagineController::filterAction'], ['filter', 'path'], ['GET' => 0], null, false, true, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
