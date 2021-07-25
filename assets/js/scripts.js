import $ from 'jquery';

$(function () {
    $('[data-item=likes]').on('click', function (e) {
        e.preventDefault();
        let href = $(this).data('href');

        $.ajax({
            url: href,
            method: 'POST'
        }).then(function (data) {
            $('[data-item=likesCount]').text(data.likes);
        });
    });
});
