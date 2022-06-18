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


    /**
     * Article Create Page
     */

    $('#articleTitle').val($('#fieldTheme').find('option:selected').text());

    $('#articleTitle').val($("#fieldTheme option[value='test']").text());
    $('#atricleTitleContent').text($("#fieldTheme option[value='test']").text())
    $('.article-content').html($("#fieldTheme option[value='test']").data('content'));

    $('#fieldTheme').on('change', function () {
        $('#articleTitle').val($(this).find('option:selected').text());
        $('#atricleTitleContent').text($(this).find('option:selected').text());
        $('.article-content').html($(this).find('option:selected').data('content'));
    });

    $('.promoted-word-delete').on('click', function () {
        deletePromotedWord(this);
    });

    $('.promoted-word-add').on('click', function () {
        addPromotedWord();
    });

    function addPromotedWord()
    {
        var promotedWordItemNumber = $('.promoted-word').length + 1;

        var promotedWordsTpl = `
            <div class="row promoted-word">
                <div class="col-md-5">
                    <div class="form-label-group">
                        <input type="text" id="promotedWordField`+promotedWordItemNumber+`" name="promoted_words[]" min="1" class="form-control" placeholder="Продвигаемое слово" required>
                        <label for ="promotedWordField`+promotedWordItemNumber+`">Продвигаемое слово</label>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-label-group">
                        <input type="number" id="promotedWordCountField`+promotedWordItemNumber+`" name="promoted_words_count[]" min="1" class="form-control" placeholder="кол-во" required>
                        <label for ="promotedWordCountField`+promotedWordItemNumber+`">кол-во</label>
                    </div>
                </div>
                <div class="col-md-2">
                     <button type="button" class="btn btn-danger btn-round-lg btn-lg bth-rounded promoted-word-delete"><i class="fas fa-trash" aria-hidden="true" title="Удалить продвигаемое слово"></i></button>
                </div>
            </div>

        `;

        $('.promoted-words').append($(promotedWordsTpl));

        $('.promoted-word').last().find('.promoted-word-delete').on('click', function () {
            deletePromotedWord(this);
        });
    }

    function deletePromotedWord(word)
    {
        $(word).closest('.promoted-word').remove();
            $('.promoted-word').each(function (index, item) {
                var promotedWordItemNumber = index + 1;
                $(item).find('input[name="promoted_words[]"]').attr('id', 'promotedWordField'+promotedWordItemNumber);
                $(item).find('input[name="promoted_words[]"]').closest('.form-label-group').find('label').attr('for', 'promotedWordField'+promotedWordItemNumber);
                $(item).find('input[name="promoted_words_count[]"]').attr('id', 'promotedWordCountField'+promotedWordItemNumber);
                $(item).find('input[name="promoted_words_count[]"]').closest('.form-label-group').find('label').attr('for', 'promotedWordCountField'+promotedWordItemNumber);
            });
        $()
    }

    $('#landingArticleCreateForm').on('submit', function (event) {
        var error = false;

        event.preventDefault();
        var data = $(this).serializeArray();

        var formData = new FormData();

        data.forEach(function (item) {
            formData.append(item.name, item.value);
        });

        $.ajax({
            type: "POST",
            cache: false,
            contentType: false,
            processData: false,
            dataType : 'json',
            url: "/article/create",
            data: formData,
            success: function (result) {
                var content = '<h2 class="card-title text-center mb-4">'+ result.title +'</h2>' + result.content;
                $('.article_content').html('');
                $('.article_content').html(content);
                $('#landingArticleCreateForm').remove();
            },
        });

    });

    $('#articleCreateForm').on('submit', function (event) {
        // toastr.success('Have fun storming the castle!', 'Miracle Max Says');
        // is-invalid

        var error = false;

        event.preventDefault();
        var data = $(this).serializeArray();


        data.forEach(function (item) {
            if (item.name == 'min_size' && parseInt(item.value) < 0) {
                error = true;
                toastr.warning('Минимальный размер не может быть меньше нуля', 'Неправильные данные');
                $('input[name="min_size"]').addClass('is-invalid');
            } else {
                $('input[name="min_size"]').removeClass('is-invalid');
            }

            if (item.name == 'max_size' && parseInt(item.value) < 0) {
                error = true;
                toastr.warning('Максимальный размер не может быть меньше нуля', 'Неправильные данные');
                $('input[name="max_size"]').addClass('is-invalid');
            } else {
                $('input[name="max_size"]').removeClass('is-invalid');
            }
        });


        var files = $("#chooseFile")[0].files;

        var formData = new FormData();
        data.forEach(function (item) {
            formData.append(item.name, item.value);
        });
        for (var i = 0; i < files.length; i++) {
            formData.append("image" + i, files[i], files[i].name);
        }

        if (error) {
            return false;
        }

        $.ajax({
            type: "POST",
            cache: false,
            contentType: false,
            processData: false,
            dataType : 'json',
            url: "/article/create",
            data: formData,
            success: function (result) {
                $('.article-content').html('');
                $('.article-content').html(result.content);
                $('#articleCreateForm').remove();
            },
        });
    });

    /**
     * Subscribe Page & Dashboard update subscribe
     */

    $('a[data-bs-target="#issueSubscribeModal"]').on('click', function () {
        let subscribe = $(this).attr('data-subscribe');
        $('#issueSubscribeModal').attr('data-subscribe', subscribe);
    });

    $('#issue-subscribe').on('click', function () {
        let subscribe = $(this).closest('#issueSubscribeModal').attr('data-subscribe');

        $.ajax({
            url: '/subscribe/issue',
            method: 'post',
            dataType: 'json',
            data: {
                subscribe: subscribe
            },
            success: function(result){
                toastr.success('Подписка улучшена до ' + result.subscribe_code);
                /**
                 * Улучшение подписки на Dashboard
                 */
                $('.user-subscribe-code').html(result.subscribe_code);
                $('.user-subscribe-expire-time').html(result.subscribe_expires_till_string).removeClass('alert-success').removeClass('alert-danger').addClass('alert-warning');
                if (result.next_subscribe_code) {
                    $('.update-user-subscribe').attr('data-subscribe', result.next_subscribe_code);
                } else {
                    $('.update-user-subscribe').hide();
                }

                /**
                 * Subscribe Page
                 */
                let subscribe_notify = "Подписка " + result.subscribe_code;
                let subscribe_issued_till = (result.subscribe_issued_till) ? " оформлена, до " +result.subscribe_issued_till : '';
                $('.user-subscribe').html(subscribe_notify + subscribe_issued_till).removeClass('alert-danger').addClass('alert-success');

                let subscribes = $('.subscribe');
                for (let i = 0; i < subscribes.length; i++) {
                    $(subscribes[i]).find('.can_issue').remove();
                    $(subscribes[i]).find('.subscribe-current').remove();

                    let subscribe_code = $(subscribes[i]).data('code');
                    let current_tpl = '<a href="#" class="btn btn-block btn-secondary text-uppercase subscribe-current" disabled="">Текущий уровень</a>';
                    let can_issue_tpl = '<a href="javascript:void(0)" class="btn btn-block btn-primary text-uppercase can_issue" data-bs-toggle="modal" data-bs-target="#issueSubscribeModal" data-subscribe="'+subscribe_code+'">Оформить</a>'

                    if (result.subscribes[i].current) {
                        $(subscribes[i]).find('.card-body').append(current_tpl);
                    }
                    if (result.subscribes[i].can_issue) {
                        $(subscribes[i]).find('.card-body').append(can_issue_tpl);

                        $('a[data-bs-target="#issueSubscribeModal"]').on('click', function () {
                            let subscribe = $(this).data('subscribe');
                            $('#issueSubscribeModal').attr('data-subscribe', subscribe);
                        });
                    }
                }
                $('#issueSubscribeModal').find('button[data-bs-dismiss="modal"]').trigger('click');
            }
        });
    });

    $('#prolongate-subscribe').on('click', function () {
        $.ajax({
            url: '/subscribe/prolongate',
            method: 'post',
            dataType: 'json',
            success: function(result){
                toastr.success('Подписка продлена');

                let subscribe_notify = "Подписка " + result.subscribe_code;
                let subscribe_issued_till = (result.subscribe_issued_till) ? " оформлена, до " +result.subscribe_issued_till : '';
                $('.user-subscribe').html(subscribe_notify + subscribe_issued_till).removeClass('alert-danger').addClass('alert-success');
                $('.subscribe-current').removeClass('btn-warning').addClass('btn-secondary').html('Текущий уровень');

                $('#prolongateSubscribeModal').find('button[data-bs-dismiss="modal"]').trigger('click');
            }
        });
    });

    /**
     * Modules Page
     */

    $('.module-delete-btn').on('click', function () {
        let module_id = $(this).attr('data-module_id');
        $('#moduleDeleteInput').val(module_id);
    });


    //==================================================================================================================

    /**
     * Custom Inpup File
     */

    $('#chooseFile').bind('change', function () {
        var filename = $("#chooseFile").val();

        var files = $("#chooseFile")[0].files;
        var filesArray = [];
        for (var i = 0; i < files.length; i++ ) {
            filesArray.push(files[i].name);
        }
        var filesString = filesArray.join(" , ");

        if (/^\s*$/.test(filename)) {
            $(".file-upload").removeClass('active');
            $("#noFile").text("No file chosen...");
        } else {
            $(".file-upload").addClass('active');
            $("#noFile").text(filesString);
        }
    });

});