(function () {
    'use strict';

    var dailyChart = null;
    var distributionDonut = null;

    var colors = {
        primary: '#fd0d0d',
        success: '#10b981',
        info: '#3b82f6',
        warning: '#f59e0b',
        danger: '#ef4444',
        orange: '#f97316',
        gray: '#6b7280'
    };

    function animateValue(el, start, end, duration) {
        if (start === end) { el.textContent = end; return; }
        var range = end - start;
        var startTime = null;
        function step(timestamp) {
            if (!startTime) startTime = timestamp;
            var progress = Math.min((timestamp - startTime) / duration, 1);
            el.textContent = Math.floor(progress * range + start);
            if (progress < 1) requestAnimationFrame(step);
            else el.textContent = end;
        }
        requestAnimationFrame(step);
    }

    // ─── Daily Rework Area Chart ─────────────────────────────────
    function renderDailyChart(data) {
        if (dailyChart) dailyChart.destroy();
        var el = document.querySelector('#reworkDailyChart');
        if (!el) return;

        var lang = window.ReworkReportLang || {};
        if (!data || !data.labels || data.labels.length === 0) {
            el.innerHTML = '<div class="text-center text-muted py-5">' + (lang.no_data || 'No data') + '</div>';
            return;
        }

        var isMobile = window.innerWidth < 768;
        dailyChart = new ApexCharts(el, {
            series: [
                { name: lang.rework || 'Rework', data: data.data || [] }
            ],
            chart: {
                type: 'area',
                height: isMobile ? 260 : 380,
                fontFamily: 'inherit',
                toolbar: { show: false },
                zoom: { enabled: false }
            },
            colors: [colors.danger],
            stroke: { curve: 'smooth', width: 2.5 },
            fill: {
                type: 'gradient',
                gradient: { shadeIntensity: 1, opacityFrom: 0.25, opacityTo: 0.05, stops: [0, 95, 100] }
            },
            markers: {
                size: 4,
                strokeWidth: 2,
                strokeColors: '#fff',
                hover: { size: 6 }
            },
            xaxis: {
                categories: data.labels,
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: {
                    style: { colors: colors.gray, fontSize: '10px' },
                    rotate: -45,
                    rotateAlways: data.labels.length > 15
                }
            },
            yaxis: {
                labels: { style: { colors: colors.gray, fontSize: '11px' } },
                min: 0,
                forceNiceScale: true
            },
            grid: { borderColor: '#f3f4f6', strokeDashArray: 4 },
            legend: {
                position: 'top',
                horizontalAlign: 'right',
                fontSize: '12px',
                fontWeight: 600,
                markers: { radius: 3, width: 10, height: 10 },
                itemMargin: { horizontal: 10 }
            },
            tooltip: {
                shared: true,
                intersect: false,
                y: { formatter: function (v) { return v + ' ' + (lang.inspections || 'inspections'); } }
            },
            dataLabels: { enabled: false }
        });
        dailyChart.render();
    }

    // ─── Rework Distribution Donut ───────────────────────────────
    function renderDistributionDonut(data) {
        if (distributionDonut) distributionDonut.destroy();
        var el = document.querySelector('#reworkDistributionDonut');
        if (!el) return;

        var lang = window.ReworkReportLang || {};
        var labels = [
            lang.not_ok_rework || 'Not OK',
            lang.supervisor_reopen_rework || 'Supervisor Reopen'
        ];
        var values = [
            data.not_ok || 0,
            data.supervisor_reopen || 0
        ];
        var total = values.reduce(function (a, b) { return a + b; }, 0);

        if (total === 0) {
            el.innerHTML = '<div class="text-center text-muted py-5">' + (lang.no_data || 'No data') + '</div>';
            return;
        }

        var isMobile = window.innerWidth < 768;
        distributionDonut = new ApexCharts(el, {
            series: values,
            chart: { type: 'donut', height: isMobile ? 260 : 300, fontFamily: 'inherit' },
            colors: [colors.danger, colors.warning],
            labels: labels,
            plotOptions: {
                pie: {
                    donut: {
                        size: '72%',
                        labels: {
                            show: true,
                            name: { show: true, fontSize: isMobile ? '11px' : '13px', color: colors.gray },
                            value: { show: true, fontSize: isMobile ? '20px' : '24px', fontWeight: 700, color: '#1f2937' },
                            total: {
                                show: true,
                                label: lang.total_reworked || 'Total',
                                fontSize: isMobile ? '11px' : '13px',
                                color: colors.gray,
                                formatter: function () { return total; }
                            }
                        }
                    }
                }
            },
            dataLabels: { enabled: false },
            legend: { position: 'bottom', fontSize: isMobile ? '10px' : '12px', markers: { radius: 3 } },
            stroke: { width: 2, colors: ['#fff'] },
            tooltip: { y: { formatter: function (v) { return v + ' ' + (lang.inspections || 'inspections'); } } }
        });
        distributionDonut.render();
    }

    // ─── Update Stat Cards ───────────────────────────────────────
    function updateStatCards(data) {
        var fields = {
            'rpt-total-reworked': data.totalReworked,
            'rpt-not-ok': data.notOkRework,
            'rpt-supervisor-reopen': data.supervisorReopened,
            'rpt-completed-rework': data.completedRework
        };

        Object.keys(fields).forEach(function (id) {
            var el = document.getElementById(id);
            if (!el) return;
            animateValue(el, parseInt(el.textContent) || 0, fields[id], 500);
        });

        // 2W/3W splits
        var splits = {
            'rpt-total-2w': data.total2w,
            'rpt-total-3w': data.total3w,
            'rpt-notok-2w': data.notOk2w,
            'rpt-notok-3w': data.notOk3w,
            'rpt-reopen-2w': data.reopen2w,
            'rpt-reopen-3w': data.reopen3w,
            'rpt-completed-2w': data.completed2w,
            'rpt-completed-3w': data.completed3w
        };

        Object.keys(splits).forEach(function (id) {
            var el = document.getElementById(id);
            if (el) el.textContent = splits[id];
        });

        // Avg rework time
        var avgEl = document.getElementById('rpt-avg-time');
        if (avgEl && data.avgReworkTime) {
            var lang = window.ReworkReportLang || {};
            avgEl.innerHTML = data.avgReworkTime.hours + '<span class="time-unit"> ' + (lang.hours || 'hrs') + '</span> '
                + data.avgReworkTime.minutes + '<span class="time-unit"> ' + (lang.minutes || 'min') + '</span>';
        }

        // Pending rework
        var pendingEl = document.getElementById('rpt-pending-rework');
        if (pendingEl) pendingEl.textContent = data.pendingRework;
    }

    // ─── Update Rework Table ─────────────────────────────────────
    function updateReworkTable(data) {
        var tbody = document.getElementById('rework-list-body');
        if (!tbody) return;

        var lang = window.ReworkReportLang || {};

        if (!data || data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-4">' + (lang.no_data || 'No data') + '</td></tr>';
            return;
        }

        var sc = { completed: 'success', in_progress: 'info', rework: 'danger', under_review: 'warning', pending_approval: 'orange' };
        var sl = {
            completed: lang.completed || 'Completed',
            in_progress: lang.in_progress || 'In Progress',
            rework: lang.rework || 'Rework',
            under_review: lang.under_review || 'Under Review',
            pending_approval: lang.pending_approval || 'Pending'
        };

        var html = '';
        data.forEach(function (r) {
            var typeBadge = r.rework_type === 'supervisor_reopen'
                ? '<span class="badge bg-warning-transparent">' + (lang.supervisor_reopen_rework || 'Supervisor Reopen') + '</span>'
                : '<span class="badge bg-danger-transparent">' + (lang.not_ok_rework || 'Not OK') + '</span>';

            html += '<tr>'
                + '<td><span class="fw-semibold">' + (r.job_card || '-') + '</span></td>'
                + '<td>' + (r.chassis || '-') + '</td>'
                + '<td>' + (r.model || '-') + '</td>'
                + '<td>' + (r.inspector || '-') + '</td>'
                + '<td>' + typeBadge + '</td>'
                + '<td>' + (r.assigned_to || '-') + '</td>'
                + '<td class="text-center">' + (r.rework_cycle || 0) + '</td>'
                + '<td><span class="badge bg-' + (sc[r.status] || 'secondary') + '-transparent">' + (sl[r.status] || r.status) + '</span></td>'
                + '<td>' + (r.date || '-') + '</td>'
                + '</tr>';
        });
        tbody.innerHTML = html;
    }

    // ─── Load Report Data via AJAX ───────────────────────────────
    function loadReportData(from, to, technicianId, reworkType) {
        var params = { from: from, to: to };
        if (technicianId) params.technician_id = technicianId;
        if (reworkType) params.rework_type = reworkType;

        $.ajax({
            url: window.ReworkReportDataUrl || '/rework-reports/data',
            data: params,
            dataType: 'json',
            beforeSend: function () {
                $('#btn-apply-filter').prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin me-1"></i> Loading...');
            },
            success: function (data) {
                updateStatCards(data);
                renderDailyChart(data.dailyRework);
                renderDistributionDonut(data.reworkDistribution);
                updateReworkTable(data.reworkList);
            },
            error: function () {
                var lang = window.ReworkReportLang || {};
                Swal.fire({
                    icon: 'error',
                    title: lang.oops || 'Oops!',
                    text: lang.something_went_wrong || 'Something went wrong.'
                });
            },
            complete: function () {
                var lang = window.ReworkReportLang || {};
                $('#btn-apply-filter').prop('disabled', false).html('<i class="bx bx-filter-alt me-1"></i> ' + (lang.apply_filter || 'Apply'));
            }
        });
    }

    // ─── Init ────────────────────────────────────────────────────
    $(document).ready(function () {
        var data = window.ReworkReportData;
        if (!data) return;

        // Init Select2 on technician dropdown
        $('#report-technician').select2({
            width: '100%',
            allowClear: true,
            placeholder: window.ReworkReportLang ? window.ReworkReportLang.all_technicians : 'All Technicians'
        });

        // Init Select2 on rework type dropdown
        $('#report-rework-type').select2({
            width: '100%',
            allowClear: true,
            placeholder: window.ReworkReportLang ? window.ReworkReportLang.all_types : 'All Types'
        });

        // Render initial charts
        renderDailyChart(data.dailyRework);
        renderDistributionDonut(data.reworkDistribution);

        // Apply filter button
        $('#btn-apply-filter').on('click', function (e) {
            e.preventDefault();
            var from = $('#report-date-from').val();
            var to = $('#report-date-to').val();
            var techId = $('#report-technician').val();
            var reworkType = $('#report-rework-type').val();
            loadReportData(from, to, techId, reworkType);
        });

        // Reset filter button
        $('#btn-reset-filter').on('click', function (e) {
            e.preventDefault();
            var defaults = window.ReworkReportDefaults || {};
            $('#report-date-from').val(defaults.from || '');
            $('#report-date-to').val(defaults.to || '');
            $('#report-technician').val('').trigger('change');
            $('#report-rework-type').val('').trigger('change');
            loadReportData(defaults.from, defaults.to, '', '');
        });

        // Print button
        $('#btn-print-report').on('click', function (e) {
            e.preventDefault();
            window.print();
        });
    });
})();
