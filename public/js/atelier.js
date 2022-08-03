$(function () {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    if (tooltipTriggerList) {
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    }

    $('form.page').on('submit', function () {
        return false;
    });
    $('.run-project-command').on('click', function () {
        let projectId = $(this).data('id');
        let command = $(this).data('command');
        let $parentRow = $(this).parents('tr');
        let $error = $parentRow.find('.error');
        let $success = $parentRow.find('.success');
        let $report = $('.report');
        let $loadingIcon = $parentRow.find('.loading-icon')
        let $runIcon = $parentRow.find('.run-icon');
        $runIcon.addClass('d-none')
        $loadingIcon.removeClass('d-none')
        $error.text('');
        $success.text('');
        $report.addClass('d-none');
        $.ajax({
            url: '/project-command/' + projectId + '/' + command,
            dataType: "json",
            type: 'PUT',
            data: {},
            success: function (resp) {
                $loadingIcon.addClass('d-none')
                $runIcon.removeClass('d-none')
                if (resp.success) {
                    $success.text('Команда отработала')
                    $report.html(resp.response).removeClass('d-none');
                    $parentRow.find('td:nth-child(3)')
                        .removeAttr('data-bs-original-title')
                        .html('<a href="#response" class="text-muted">Ответ...</a>');
                } else {
                    $error.text(resp.error);
                }
            }
        });

        return false;
    });
    $('.delete-machine-projects').on('click', function () {
        let machineId = $(this).data('id');
        $.ajax({
            url: '/garage/' + machineId,
            dataType: "json",
            type: 'DELETE',
            data: {},
            success: function () {
                document.location = '/garage';
            }
        });

        return false;
    });
    $('.delete-project').on('click', function () {
        let projectId = $(this).data('id');
        $.ajax({
            url: '/projects/' + projectId,
            dataType: "json",
            type: 'DELETE',
            data: {},
            success: function (response) {
                document.location = '/projects';
            }
        });
    });
    $('.scan-projects').on('click', function () {
        let machineId = $(this).data('id');
        auth(machineId, function (login, password) {
            let $report = $('#scan-projects-report-' + machineId);
            let $loading = $report.find('.loading');
            let $text = $report.find('.text');
            $report.removeClass('d-none');
            $loading.removeClass('d-none');
            $text.addClass('d-none');
            $.ajax({
                url: '/scan-projects/' + machineId,
                dataType: "json",
                type: 'PUT',
                data: {
                    login: login,
                    password: password
                },
                success: function (response) {
                    $loading.addClass('d-none');
                    $text.removeClass('d-none');
                    if (response.error) {
                        $text.html(response.error);
                    } else {
                        $text.html(response.report);
                    }
                }
            });
        });
    });

    function auth(machineId, callback) {
        $('#password').removeClass('is-invalid');
        $('#authError').addClass('d-none');
        let $submit = $('#authModal form [type=submit]');
        $submit.removeClass('disabled');
        let authModal = new bootstrap.Modal('#authModal');
        authModal.show();
        $('#authModal form').on('submit', function () {
            $('#password').removeClass('is-invalid');
            $('#authError').addClass('d-none');
            $submit.addClass('disabled');
            let login = $(this).find('[name=login]').val();
            let password = $(this).find('[name=password]').val();
            $.ajax({
                url: '/auth/' + machineId,
                dataType: "json",
                type: 'POST',
                data: {
                    login: login,
                    password: password
                },
                success: function (response) {
                    if (response.error) {
                        $('#password').addClass('is-invalid');
                        $('#authError').text(response.error).removeClass('d-none');
                        $submit.removeClass('disabled');
                    } else {
                        authModal.hide();
                        callback(login, password);
                    }
                }
            });

            return false;
        });
    }
});