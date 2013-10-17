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
/*global define, require*/
define("ATHENE2", ['jquery', 'side_navigation', 'translator', 'layout', 'sortable_list', 'system_notification'], function ($, SideNavigation, Translator, Layout) {
    "use strict";

    function init() {
        new SideNavigation();

        $('.sortable').SortableList();

        Translator.config({
            language: 'de_DE'
        });

        Layout.init();
    }

    return {
        initialize: function () {
            init();
        }
    };
});

require(['jquery', 'ATHENE2'], function ($, App) {
    "use strict";
    $(function () {
        App.initialize();
    });
});