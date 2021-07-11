import $ from 'jquery';

$(function () {
    $('[data-item=likes]').on('click', function (e) {
        e.preventDefault();

        let type = $(this).data('type');

        $.ajax({
            url: `/articles/10/${type}/`,
            method: 'POST'
        }).then(function (data) {
            $('[data-item=likesCount]').text(data.likes);
        });
    });
});
