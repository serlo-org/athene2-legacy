/**
 * 
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author  Julian Kempff (julian.kempff@serlo.org)
 * @license LGPL-3.0
 * @license http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link        https://github.com/serlo-org/athene2 for the canonical source repository
 * @copyright Copyright (c) 2013 Gesellschaft für freie Bildung e.V. (http://www.open-education.eu/)
 * 
 */

/** maybe use http://dbushell.github.io/Nestable/ instead of jqueryui */

/*global define*/
define("sortable_list", ["jquery", "underscore", "common", "translator", "system_notification"], function ($, _, Common, t, SystemNotification) {
    "use strict";
    var SortableList;

    SortableList = function () {
        return $(this).each(function (group) {
            var $instance = $(this),
                $saveBtn = $('.sortable-save-action', this),
                $activateBtn = $('.sortable-activate-action', this),
                $abortBtn = $('.sortable-abort-action', this),
                activeClass = 'sortable-active',
                dataUrl,
                dataDepth,
                dataActive,
                originalHTML,
                originalData,
                updatedData;

            dataUrl = $instance.attr('data-action');

            if (!dataUrl) {
                throw new Error('No sort action given for sortable wrapper.');
            }

            dataActive = $instance.attr('data-active') || "true";
            dataActive = dataActive === "true" ? true : false;

            dataDepth = $instance.attr('data-depth') || 50;

            /**
             * @function cleanEmptyChildren
             * @param {Array}
             *
             * Removes empty children arrays from serialized nestable,
             * to be able to hide the $saveBtn
             **/
            function cleanEmptyChildren(array) {
                _.each(array, function (child) {
                    if (child.children) {
                        if (child.children.length) {
                            cleanEmptyChildren(child.children);
                        } else {
                            delete child.children;
                        }
                    }
                });
                return array;
            }

            function storeOriginalData() {
                originalHTML = $instance.find('> ol').first().html();
                originalData = cleanEmptyChildren($instance.nestable('serialize'));
            }

            function activate() {
                $instance.addClass(activeClass);

                $activateBtn.hide();
                $abortBtn.show();

                $instance.nestable({
                    rootClass: 'sortable',
                    listClass: 'sortable-list',
                    itemClass: 'sortable-item',
                    dragClass: 'sortable-dragel',
                    handleClass: 'sortable-handle',
                    collapsedClass: 'sortable-collapsed',
                    placeClass: 'sortable-placeholder',
                    noDragClass: 'sortable-nodrag',
                    emptyClass: 'sortable-empty',

                    expandBtnHTML: '',
                    collapseBtnHTML: '',
                    group: group,
                    maxDepth: dataDepth,
                    threshold: 20
                });

                storeOriginalData();
            }

            function deactivate() {
                // Router.reload();
                $instance
                    .removeClass(activeClass)
                    .nestable('destroy')
                    .find('> ol')
                    .first()
                    .html(originalHTML);

                $saveBtn.hide();
                $abortBtn.hide();
                $activateBtn.show();
            }

            $instance.on('change', function () {
                updatedData = cleanEmptyChildren($instance.nestable('serialize'));
                if (!_.isEqual(updatedData, originalData)) {
                    $saveBtn.show();
                } else {
                    $saveBtn.hide();
                }
            });

            $saveBtn.click(function (e) {
                e.preventDefault();
                $.ajax({
                    url: dataUrl,
                    data: {
                        sortable: updatedData
                    },
                    method: 'post'
                })
                    .success(function () {
                        SystemNotification.notify(t('Order successfully saved'), 'success');

                        storeOriginalData();

                        if (!dataActive) {
                            deactivate();
                        }

                        $saveBtn.hide();
                    })
                    .fail(function () {
                        Common.genericError(arguments);
                    });
                return;
            });

            $abortBtn.click(function () {
                if (!dataActive) {
                    deactivate();
                }
            });

            $activateBtn.click(function () {
                activate();
            });

            if (dataActive) {
                activate();
            }
        });
    };

    $.fn.SortableList = SortableList;
});