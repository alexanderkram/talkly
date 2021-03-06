// Foundation JavaScript
// Documentation can be found at: http://foundation.zurb.com/docs
$(document).foundation({
    orbit: {
        bullets: false
    }
});

$(document).ready(function () {
    $('[data-toggle]').click(function (event) {

        var id = $(this).data('toggle-target');
        var $toggleTarget = $("#" + id);

        if ($toggleTarget.is(':visible')) {
            $toggleTarget.hide('slide');
            $(this).removeClass('toggle-open');
        } else {
            $toggleTarget.show('slide');
            $(this).addClass('toggle-open');
            $toggleTarget.find('input,textarea').first().focus();
        }

        return false;
    }).each(function () {
        if ($(this).data('toggle-start-opened') == '1') {
            $(this).trigger('click');
        }
    });

    hljs.initHighlightingOnLoad();
});

var app = {
    addFlash: function (level, message) {
        var $message = $("<div data-alert />");
        var $flash = $("#flash");

        $message
            .addClass('alert-box')
            .addClass(level)
            .html(message)
            .append('<a href="#" class="close">&times;</a>')
            .appendTo($flash)
        ;

        // todo - events are not applied

    },

    baseUrl: function () { return $('body').data('baseurl'); }
};

angular.module('talklyApp', ['talklyApp.services', 'talklyApp.directives', 'headroom', 'angularMoment'])

    .controller('IndexController', ['$scope', 'Topics',
        function ($scope, Topics, User) {
            $scope.topics = [];
            $scope.sorting = [
                {label: 'votes', order: 'votes'},
                {label: 'scheduled date', order: 'lecture_date'},
                {label: 'date of submission', order: 'created_at'}
            ];
            $scope.order = $scope.sorting[0];
            $scope.reverse = true;

            Topics.query(function (data) {
                $scope.topics = data;
            });
        }]);
