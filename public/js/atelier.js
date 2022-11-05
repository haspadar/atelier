$(function () {
    function getHash() {
        var url = document.location.toString();

        return url.split('#')[1];
    }
    (function handleTabs(callback) {
        // Javascript to enable link to tab
        var url = document.location.toString();
        if (url.match('#')) {
            var $tab = $('.nav-tabs a[href="#' + getHash() + '"]');
            $tab.tab('show');
            if (url.split('#').length > 2) {
                var $subTab = $(
                    '.nav-tabs a[href=#'
                    + url.split('#')[1]
                    + '#' + url.split('#')[2]
                    + ']'
                );
                if (callback) {
                    callback($subTab);
                }

                $subTab.tab('show');
            }
        } 

        //Change hash for page-reload
        $('.nav-tabs a').on('shown.bs.tab', function (e) {
            var hash = e.target.hash;
            hash = hash.replace(/^#/, '');
            var fx, node = $('#' + hash);
            if (node.length) {
                node.attr('id', '');
                fx = $('<div></div>')
                    .css({
                        position: 'absolute',
                        visibility: 'hidden',
                        top: $(document).scrollTop() + 'px'
                    })
                    .attr('id', hash)
                    .appendTo(document.body);
            }

            document.location.hash = hash;
            if (node.length) {
                fx.remove();
                node.attr('id', hash);
            }
        });
    })();

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    if (tooltipTriggerList) {
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    }

    $('form.page').on('submit', function () {
        return false;
    });
    $('.show-access-log-traffic').on('click', function () {
        let projectId = $(this).data('projectId');
        let $column = $(this).parents('td');
        $.ajax({
            url: '/get-access-log-traffic/' + projectId,
            dataType: "json",
            type: 'GET',
            data: {},
            success: function (resp) {
                let uniqueId = 'access-log-' + Math.floor(Math.random() * 100);
                $column.html('<div title="' + resp.traffic + '" id="' + uniqueId + '">' + resp.access_log + '</div>');
                $(document.createElement('div')).attr('id', 'theID')
                new bootstrap.Tooltip(document.getElementById(uniqueId));
            }
        });

        return false;
    });
    $('.run-project-command').on('click', function () {
        let projectId = $(this).data('id');
        let command = $(this).data('command');
        let $parentRow = $(this).parents('tr');
        let $error = $parentRow.find('.error');
        let $report = $('.report');
        let $loadingIcon = $parentRow.find('.loading-icon')
        let $runIcon = $parentRow.find('.run-icon');
        $runIcon.addClass('d-none')
        $loadingIcon.removeClass('d-none')
        $error.text('');
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
                    $report.html(resp.response).removeClass('d-none');
                    $parentRow.parent('tbody').find('.response-link').html('');
                    $parentRow.find('td:nth-child(3)')
                        .removeAttr('data-bs-original-title')
                        .html('<a href="#response" class="text-success response-link">Команда отработала</a>');
                } else {
                    $error.text(resp.error);
                }
            }
        });

        return false;
    });
    $('.ignore-check').on('click', function () {
        let checkId = $(this).data('id');
        $.ajax({
            url: '/checks/' + checkId,
            dataType: "json",
            type: 'DELETE',
            data: {},
            success: function (response) {
                let $tr = $(this).parents('tr');
                if ($tr.length) {
                    document.location.reload();
                } else {
                    document.location = response.redirect_url;
                }
            }
        });

        return false;
    });
    $('.ignore-check-project').on('click', function () {
        let checkId = $(this).data('id');
        $.ajax({
            url: '/check-projects/' + checkId,
            dataType: "json",
            type: 'DELETE',
            data: {},
            success: function (response) {
                let $tr = $(this).parents('tr');
                if ($tr.length) {
                    document.location.reload();
                } else {
                    document.location = response.redirect_url;
                }
            }
        });

        return false;
    });
    $('.ignore-check-machine').on('click', function () {
        let checkId = $(this).data('id');
        $.ajax({
            url: '/check-machines/' + checkId,
            dataType: "json",
            type: 'DELETE',
            data: {},
            success: function (response) {
                let $tr = $(this).parents('tr');
                if ($tr.length) {
                    document.location.reload();
                } else {
                    document.location = response.redirect_url;
                }
            }
        });

        return false;
    });
    $('.delete-machine-projects').on('click', function () {
        let machineId = $(this).data('id');
        $.ajax({
            url: '/machines/' + machineId,
            dataType: "json",
            type: 'DELETE',
            data: {},
            success: function () {
                document.location = '/machines';
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
                // document.location = '/projects';
                window.history.back();
            }
        });
    });
    $('.scan-projects').off('click').on('click', function () {
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