(function () {
    'use strict';

    var dailyChart = null;
    var statusDonut = null;
    var firstPassRadial = null;

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

    // ─── Daily Performance Area Chart ────────────────────────────
    function renderDailyChart(data) {
        if (dailyChart) dailyChart.destroy();
        var el = document.querySelector('#reportDailyChart');
        if (!el) return;

        var lang = window.ReportLang || {};
        if (!data || !data.labels || data.labels.length === 0) {
            el.innerHTML = '<div class="text-center text-muted py-5">' + (lang.no_data || 'No data') + '</div>';
            return;
        }

        var isMobile = window.innerWidth < 768;
        dailyChart = new ApexCharts(el, {
            series: [
                { name: lang.completed || 'Completed', data: data.completed || [] },
                { name: lang.in_progress || 'In Progress', data: data.in_progress || [] },
                { name: lang.under_review || 'Under Review', data: data.under_review || [] },
                { name: lang.rework || 'Rework', data: data.rework || [] },
                { name: lang.pending_approval || 'Pending', data: data.pending_approval || [] }
            ],
            chart: {
                type: 'area',
                height: isMobile ? 260 : 380,
                fontFamily: 'inherit',
                toolbar: { show: false },
                zoom: { enabled: false }
            },
            colors: [colors.success, colors.info, colors.warning, colors.danger, colors.orange],
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

    // ─── Status Distribution Donut ──────────────────────────────
    function renderStatusDonut(data) {
        if (statusDonut) statusDonut.destroy();
        var el = document.querySelector('#reportStatusDonut');
        if (!el) return;

        var lang = window.ReportLang || {};
        var labels = [
            lang.completed || 'Completed',
            lang.in_progress || 'In Progress',
            lang.rework || 'Rework',
            lang.under_review || 'Under Review',
            lang.pending_approval || 'Pending'
        ];
        var values = [
            data.completed || 0,
            data.in_progress || 0,
            data.rework || 0,
            data.under_review || 0,
            data.pending_approval || 0
        ];
        var total = values.reduce(function (a, b) { return a + b; }, 0);

        if (total === 0) {
            el.innerHTML = '<div class="text-center text-muted py-5">' + (lang.no_data || 'No data') + '</div>';
            return;
        }

        var isMobile = window.innerWidth < 768;
        statusDonut = new ApexCharts(el, {
            series: values,
            chart: { type: 'donut', height: isMobile ? 260 : 300, fontFamily: 'inherit' },
            colors: [colors.success, colors.info, colors.danger, colors.warning, colors.orange],
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
                                label: lang.total_inspections || 'Total',
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
        statusDonut.render();
    }

    // ─── First Pass Rate Radial ─────────────────────────────────
    function renderFirstPassRadial(rate) {
        if (firstPassRadial) firstPassRadial.destroy();
        var el = document.querySelector('#reportFirstPassRadial');
        if (!el) return;

        var lang = window.ReportLang || {};

        var isMobile = window.innerWidth < 768;
        firstPassRadial = new ApexCharts(el, {
            series: [rate],
            chart: { type: 'radialBar', height: isMobile ? 250 : 300, fontFamily: 'inherit' },
            colors: [rate >= 80 ? colors.success : rate >= 50 ? colors.warning : colors.danger],
            plotOptions: {
                radialBar: {
                    hollow: { size: '65%' },
                    track: { background: '#f3f4f6', strokeWidth: '100%' },
                    dataLabels: {
                        name: { show: true, fontSize: isMobile ? '12px' : '14px', color: colors.gray, offsetY: -10 },
                        value: { show: true, fontSize: isMobile ? '22px' : '28px', fontWeight: 700, color: '#1f2937', offsetY: 5 }
                    }
                }
            },
            labels: [lang.first_pass_rate || 'First Pass Rate'],
            stroke: { lineCap: 'round' }
        });
        firstPassRadial.render();
    }

    // ─── Update Stat Cards ──────────────────────────────────────
    function updateStatCards(data) {
        var fields = {
            'rpt-total': data.totalInspections,
            'rpt-completed': data.completedCount,
            'rpt-in-progress': data.inProgressCount,
            'rpt-rework': data.reworkCount,
            'rpt-under-review': data.underReviewCount,
            'rpt-pending': data.pendingApproval
        };

        Object.keys(fields).forEach(function (id) {
            var el = document.getElementById(id);
            if (!el) return;
            animateValue(el, parseInt(el.textContent) || 0, fields[id], 500);
        });

        // Avg completion time
        var avgEl = document.getElementById('rpt-avg-time');
        if (avgEl && data.avgCompletionTime) {
            var lang = window.ReportLang || {};
            avgEl.innerHTML = data.avgCompletionTime.hours + '<span class="time-unit"> ' + (lang.hours || 'hrs') + '</span> '
                + data.avgCompletionTime.minutes + '<span class="time-unit"> ' + (lang.minutes || 'min') + '</span>';
        }
    }

    // ─── Update Leaderboard ─────────────────────────────────────
    function updateLeaderboard(data) {
        var section = document.querySelector('.leaderboard-section');
        var tbody = document.getElementById('leaderboard-body');
        if (!section || !tbody) return;

        if (!data || data.length === 0) {
            section.classList.remove('show');
            return;
        }

        section.classList.add('show');

        var lang = window.ReportLang || {};
        var html = '';
        data.forEach(function (tech, i) {
            var rankClass = i < 3 ? 'rank-' + (i + 1) : 'rank-default';
            var avgRw = tech.avg_rework_time
                ? tech.avg_rework_time.hours + (lang.hours || 'hrs') + ' ' + tech.avg_rework_time.minutes + (lang.minutes || 'min')
                : '-';
            html += '<tr>'
                + '<td><span class="rank-badge ' + rankClass + '">' + (i + 1) + '</span></td>'
                + '<td class="fw-semibold">' + tech.name + '</td>'
                + '<td class="text-center">' + tech.total + '</td>'
                + '<td class="text-center text-success fw-semibold">' + tech.completed + '</td>'
                + '<td class="text-center text-danger fw-semibold">' + tech.rework + '</td>'
                + '<td class="text-center">' + tech.first_pass_rate + '%</td>'
                + '<td class="text-center fw-semibold text-info">' + tech.reworks_done + '</td>'
                + '<td class="text-center">' + avgRw + '</td>'
                + '<td>'
                +   '<div class="d-flex align-items-center gap-2">'
                +     '<div class="progress progress-xs flex-fill">'
                +       '<div class="progress-bar bg-success" style="width:' + tech.completion_rate + '%"></div>'
                +     '</div>'
                +     '<span class="fw-semibold" style="font-size:0.8rem;min-width:40px;">' + tech.completion_rate + '%</span>'
                +   '</div>'
                + '</td>'
                + '</tr>';
        });
        tbody.innerHTML = html;
    }

    // ─── Update Inspections Table ───────────────────────────────
    function updateInspectionsTable(data) {
        var tbody = document.getElementById('inspections-body');
        if (!tbody) return;

        var lang = window.ReportLang || {};

        if (!data || data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">' + (lang.no_data || 'No data') + '</td></tr>';
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
            html += '<tr>'
                + '<td><span class="fw-semibold">' + (r.job_card || '-') + '</span></td>'
                + '<td>' + (r.chassis || '-') + '</td>'
                + '<td>' + (r.model || '-') + '</td>'
                + '<td>' + (r.technician || '-') + '</td>'
                + '<td>' + (r.date || '-') + '</td>'
                + '<td><span class="badge bg-' + (sc[r.status] || 'secondary') + '-transparent">' + (sl[r.status] || r.status) + '</span></td>'
                + '<td class="text-center">' + (r.rework_cycle || 0) + '</td>'
                + '</tr>';
        });
        tbody.innerHTML = html;
    }

    // ─── Load Report Data via AJAX ──────────────────────────────
    function loadReportData(from, to, technicianId) {
        var params = { from: from, to: to };
        if (technicianId) params.technician_id = technicianId;

        $.ajax({
            url: window.ReportDataUrl || '/reports/data',
            data: params,
            dataType: 'json',
            beforeSend: function () {
                $('#btn-apply-filter').prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin me-1"></i> Loading...');
            },
            success: function (data) {
                updateStatCards(data);
                renderDailyChart(data.dailyPerformance);
                renderStatusDonut(data.statusDistribution);
                renderFirstPassRadial(data.firstPassRate);
                updateLeaderboard(data.leaderboard);
                updateInspectionsTable(data.inspections);
            },
            error: function () {
                var lang = window.ReportLang || {};
                Swal.fire({
                    icon: 'error',
                    title: lang.oops || 'Oops!',
                    text: lang.something_went_wrong || 'Something went wrong.'
                });
            },
            complete: function () {
                var lang = window.ReportLang || {};
                $('#btn-apply-filter').prop('disabled', false).html('<i class="bx bx-filter-alt me-1"></i> ' + (lang.apply_filter || 'Apply'));
            }
        });
    }

    // ─── Init ───────────────────────────────────────────────────
    $(document).ready(function () {
        var data = window.ReportData;
        if (!data) return;

        // Init Select2 on technician dropdown
        $('#report-technician').select2({
            width: '100%',
            allowClear: true,
            placeholder: window.ReportLang ? window.ReportLang.all_technicians : 'All Technicians'
        });

        // Render initial charts
        renderDailyChart(data.dailyPerformance);
        renderStatusDonut(data.statusDistribution);
        renderFirstPassRadial(data.firstPassRate);
        updateLeaderboard(data.leaderboard);
        updateInspectionsTable(data.inspections);

        // Show/hide leaderboard based on technician selection
        $('#report-technician').on('change', function () {
            var section = document.querySelector('.leaderboard-section');
            if (section) {
                if ($(this).val()) {
                    section.classList.remove('show');
                } else {
                    section.classList.add('show');
                }
            }
        });

        // Apply filter button
        $('#btn-apply-filter').on('click', function (e) {
            e.preventDefault();
            var from = $('#report-date-from').val();
            var to = $('#report-date-to').val();
            var techId = $('#report-technician').val();
            loadReportData(from, to, techId);
        });

        // Reset filter button
        $('#btn-reset-filter').on('click', function (e) {
            e.preventDefault();
            var defaults = window.ReportDefaults || {};
            $('#report-date-from').val(defaults.from || '');
            $('#report-date-to').val(defaults.to || '');
            $('#report-technician').val('').trigger('change');
            var section = document.querySelector('.leaderboard-section');
            if (section) section.classList.add('show');
            loadReportData(defaults.from, defaults.to, '');
        });

        // Print button
        $('#btn-print-report').on('click', function (e) {
            e.preventDefault();
            window.print();
        });
    });
})();
