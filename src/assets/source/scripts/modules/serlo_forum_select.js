/*global define*/
define(['jquery', 'translator'], function ($, t) {
    "use strict";
    var ForumSelect;

    /* jshint validthis:true  */
    function selectForum(e) {
        var $that = $(this),
            url = $that.data('select-forum-href');

        e.preventDefault();

        $.get(url, function (data) {
            var $modal = $('<div class="modal fade"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title">' + t('You\'re almost done!') + '</h4></div><div class="modal-body"></div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">' + t('Abort') + '</button></div></div></div></div>');
            $('body').append($modal);
            $('.modal-body', $modal).html(data);
            $modal.modal('show');

            $('button.select').click(function () {
                var $this = $(this),
                    href = $this.data('action');
                console.log('feeek');
                $this.html(t('Please wait...'));

                $that.attr('action', href);
                $that.off('submit');
                $that.submit();
                // $that.submit();
                // $that.trigger('submit');
                // $that.trigger('submit');
            });
        });

        return false;
    }

    ForumSelect = function () {
        return $(this).each(function () {
            var $that = $(this);
            if ($that.data('select-forum-href')) {
                $that.on('submit', selectForum);
            }
        });
    };

    $.fn.ForumSelect = ForumSelect;
});