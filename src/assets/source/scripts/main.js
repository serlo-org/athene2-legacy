/**
 * 
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author  Julian Kempff (julian.kempff@serlo.org)
 * @license LGPL-3.0
 * @license http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link        https://github.com/serlo-org/athene2 for the canonical source repository
 * @copyright Copyright (c) 2013 Gesellschaft für freie Bildung e.V. (http://www.open-education.eu/)
 */
/*global require*/
require.config({
    name: 'ATHENE2',
    baseUrl: "/assets/build/scripts",
    paths: {
        "jquery": "../bower_components/jquery/jquery",
        "jquery-ui" : "../bower_components/jquery-ui/ui/jquery-ui",
        "underscore": "../bower_components/underscore/underscore",
        "bootstrap": "../bower_components/sass-bootstrap/dist/js/bootstrap",
        "moment" : "../bower_components/momentjs/min/moment.min",
        "moment_de": "../bower_components/momentjs/lang/de",
        "common" : "modules/serlo_common",
        "easing" : "libs/easing",
        "events": "libs/eventscope",
        "cache": "libs/cache",
        "polyfills": "libs/polyfills",
        "event_extensions": "libs/event_extensions",
        "referrer_history" : "modules/serlo_referrer_history",
        "side_navigation" : "modules/serlo_side_navigation",
        "ajax_overlay": "modules/serlo_ajax_overlay",
        "sortable_list" : "modules/serlo_sortable_list",
        "timeago" : "modules/serlo_timeago",
        "trigger" : "modules/serlo_trigger",
        "system_notification" : "modules/serlo_system_notification",
        "nestable" : "thirdparty/jquery.nestable",
        "datepicker" : "../bower_components/bootstrap-datepicker/js/bootstrap-datepicker",
        "translator" : "modules/serlo_translator",
        "i18n" : "modules/serlo_i18n",
        "side_element" : "modules/serlo_side_element",
        "content" : "modules/serlo_content",
        "spoiler" : "modules/serlo_spoiler",
        "injections" : "modules/serlo_injections",
        "search" : "modules/serlo_search",
        "support" : "modules/serlo_supporter",
        "modals" : "modules/serlo_modals",
        "router" : "modules/serlo_router",
        "forum_select" : "modules/serlo_forum_select",
        "toggle_action" : "modules/serlo_toggle",
        "mathjax_trigger" : "modules/serlo_mathjax_trigger",
        "affix" : "modules/serlo_affix"
    },
    shim: {
        underscore: {
            exports: '_'
        },
        bootstrap: {
            deps: ['jquery']
        },
        datepicker: {
            deps: ['jquery', 'bootstrap']
        },
        easing: {
            deps: ['jquery']
        },
        nestable: {
            deps: ['jquery']
        },
        ATHENE2: {
            deps: ['bootstrap', 'easing', 'nestable', 'polyfills', 'datepicker', 'event_extensions']
        }
    },
    waitSeconds: 2
});