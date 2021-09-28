import $ from 'jquery';
import toastr from 'toastr';

$(document).ready(function () {
    $('#save-api-token').on('click', function () {

        toastr.options = {
            "closeButton": true,
        };

        $.ajax({
            type: "POST",
            url: '/account/change-api-token',
               success: function (response) {
                   console.log(response.api_token);
                   toastr.success('Новый Api токен сформирован');

                   $('#exampleModal').find('button[data-bs-dismiss="modal"]').trigger('click');

                   $('#api_token').html('');
                   $('#api_token').html(response.api_token)
               },

        });

    });
});