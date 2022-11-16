$(function () {
    function getHash() {
        var url = document.location.toString();

        return url.split('#')[1];
    }

    function goBack() {
        window.history.go(-1);
        setTimeout(() => {
            location.reload();
        }, 0);
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
            url: '/machine-projects/' + machineId,
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
                if (document.referrer !== "") {
                    goBack();
                } else {
                    document.location = '/checks#CRITICAL'
                }

            }
        });
    });
    $('.save-command-project-types').off('click').on('click', function () {
        let commandId = $(this).data('commandId');
        let typeIds = [];
        $('.command-project-types input:checked').each(function (key, value) {
            return typeIds.push($(this).val());
        });
        $.ajax({
            url: '/command-project-types/' + commandId,
            dataType: "json",
            type: 'PUT',
            data: {
                type_ids: typeIds
            },
            success: function (response) {
                document.location.reload();
            }
        });
    });

    function formatDatesValuesForChart(notFormatted) {
        let formatted = [];
        $.each(notFormatted, function (projectName, projectTraffic) {
            let projectFormattedTraffic = [];
            $.each(projectTraffic, function (date, dateTraffic) {
                let dateParts = date.split('-');
                projectFormattedTraffic.push([
                    Date.UTC(dateParts[0], parseInt(dateParts[1]),  parseInt(dateParts[2])),
                    parseInt(dateTraffic)
                ]);
            });
            formatted.push({name: projectName, data: projectFormattedTraffic});
        });

        return formatted;
    }

    function loadMachineNginxTraffic(machineId, chartId) {
        $.ajax({
            url: '/get-machine-access-log-traffic/' + machineId,
            dataType: "json",
            type: 'GET',
            data: [],
            success: function (response) {
                loadZoomChart(chartId, response.traffic, 'Nginx traffic');
            }
        });
    }

    function loadMachinePhpFpmTraffic(machineId, chartId) {
        $.ajax({
            url: '/get-machine-php-fpm-traffic/' + machineId,
            dataType: "json",
            type: 'GET',
            data: [],
            success: function (response) {
                loadZoomChart(chartId, response.traffic, 'PHP-FPM traffic');
            }
        });
    }

    function loadZoomChart(chartId, response, title) {
        let formatted = (function formatTimestampsValues(notFormatted) {
            let formatted = [];
            $.each(notFormatted, function (projectName, projectTraffic) {
                let projectFormattedTraffic = [];
                $.each(projectTraffic, function (dateTime, dateTraffic) {
                    projectFormattedTraffic.push([
                        new Date(dateTime).valueOf(),
                        parseInt(dateTraffic)
                    ]);
                });
                formatted.push({name: projectName, data: projectFormattedTraffic, type: 'area'});
            });

            return formatted;
        })(response);

        Highcharts.chart(chartId, {
            chart: {
                zoomType: 'x'
            },
            title: {
                text: title
            },
            subtitle: {
                text: 'С поддержкой zoom'
            },
            xAxis: {
                type: 'datetime'
            },
            yAxis: {
                title: {
                    text: 'Запросы в секунду'
                }
            },
            legend: {
                enabled: false
            },
            plotOptions: {
                area: {
                    fillColor: {
                        linearGradient: {
                            x1: 0,
                            y1: 0,
                            x2: 0,
                            y2: 1
                        },
                        stops: [
                            [0, Highcharts.getOptions().colors[0]],
                            [1, Highcharts.color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                        ]
                    },
                    marker: {
                        radius: 2
                    },
                    lineWidth: 1,
                    states: {
                        hover: {
                            lineWidth: 1
                        }
                    },
                    threshold: null
                }
            },
            series: formatted
        });
    }

    if ($('#machine-nginx-traffic')) {
        let machineId = $('#machine-nginx-traffic').data('machineId');
        loadMachineNginxTraffic(machineId, 'machine-nginx-traffic');
        loadMachinePhpFpmTraffic(machineId, 'machine-php-fpm-traffic');
    }

    $('#deleteMachineModal .ok').on('click', function () {
        let machineId = $('#deleteMachineModal [name=id]').val();
        $.ajax({
            url: '/machines/' + machineId,
            dataType: "json",
            type: 'DELETE',
            success: function (response) {
                document.location = '/machines';
            }
        });
    });

    $('.remove-machine').on('click', function () {
        let id = $(this).data('id');
        $('#deleteMachineModal [name=id]').val(id);
        let deleteMachineModal = new bootstrap.Modal('#deleteMachineModal');
        deleteMachineModal.show();
    });

    $('form.machine').on('submit', function () {
        let $form = $(this);
        let values = $form.serialize();
        $.ajax({
            url: '/machines/' + $form.find('[name=id]').val(),
            dataType: "json",
            type: 'PUT',
            data: values,
            success: function (response) {
                if (response.errors) {
                    $.each(response.errors, function (field, text) {
                        $form.find('[name=' + field + ']').addClass('is-invalid')
                        $form.find('.invalid-' + field).text(text)
                    });
                } else {
                    document.location = '/machines';
                }
            }
        });

        return false;
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

    $('.add-machine').on('click', function () {
        let $form = $('#addMachineModal form');
        $form.find('input').removeClass('is-invalid');
        $form.find('.invalid-feedback').text('');
        let addMachineModal = new bootstrap.Modal('#addMachineModal');
        addMachineModal.show();
    });
    $('#addMachineModal form').on('submit', function () {
        let $form = $(this);
        let values = $form.serialize();
        $.ajax({
            url: '/machines/',
            dataType: "json",
            type: 'POST',
            data: values,
            success: function (response) {
                if (response.errors) {
                    $.each(response.errors, function (field, text) {
                        $form.find('[name=' + field + ']').addClass('is-invalid')
                        $form.find('.invalid-' + field).text(text)
                    });
                //     $('#password').addClass('is-invalid');
                //     $('#authError').text(response.error).removeClass('d-none');
                //     $submit.removeClass('disabled');
                } else {
                    document.location.reload();
                //     authModal.hide();
                //     callback(login, password);
                }
            }
        });

        return false;
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