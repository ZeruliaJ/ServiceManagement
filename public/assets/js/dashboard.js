(function () {
    'use strict';

    var monthlyTrendChart = null;
    var donut2w = null;
    var donut3w = null;
    var personalRadial = null;
    var dailyPerformanceChart = null;

    var colors = {
        primary: '#fd0d0d',
        success: '#10b981',
        info: '#3b82f6',
        warning: '#f59e0b',
        danger: '#ef4444',
        pink: '#ec4899',
        orange: '#f97316',
        gray: '#6b7280',
        purple: '#8b5cf6'
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

    // ─── Monthly Trend Line Chart (2W vs 3W) ─────────────────────
    function renderMonthlyTrend(data) {
        if (monthlyTrendChart) monthlyTrendChart.destroy();
        var el = document.querySelector('#monthlyTrendChart');
        if (!el) return;

        var lang = window.DashboardLang || {};
        if (!data || !data.labels || data.labels.length === 0) {
            el.innerHTML = '<div class="text-center text-muted py-5">' + (lang.no_data || 'No data') + '</div>';
            return;
        }

        monthlyTrendChart = new ApexCharts(el, {
            series: [
                { name: '2W', data: data.data_2w || [] },
                { name: '3W', data: data.data_3w || [] }
            ],
            chart: {
                type: 'line',
                height: 350,
                fontFamily: 'inherit',
                toolbar: { show: false },
                zoom: { enabled: false }
            },
            colors: [colors.primary, colors.warning],
            stroke: { curve: 'smooth', width: 3 },
            markers: {
                size: 5,
                strokeWidth: 2,
                strokeColors: '#fff',
                hover: { size: 7 }
            },
            xaxis: {
                categories: data.labels,
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: {
                    style: { colors: colors.gray, fontSize: '11px' },
                    rotate: -45,
                    rotateAlways: false
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
                fontSize: '13px',
                fontWeight: 600,
                markers: { radius: 3, width: 10, height: 10 },
                itemMargin: { horizontal: 12 }
            },
            tooltip: {
                shared: true,
                intersect: false,
                y: { formatter: function (v) { return v + ' inspections'; } }
            },
            dataLabels: {
                enabled: true,
                style: { fontSize: '11px', fontWeight: 600 },
                background: { enabled: true, borderRadius: 4, padding: 4 },
                offsetY: -8
            }
        });
        monthlyTrendChart.render();
    }

    // ─── Status Donut (reusable for 2W / 3W) ─────────────────────
    function renderDonut(elSelector, data, existingChart) {
        if (existingChart) existingChart.destroy();
        var el = document.querySelector(elSelector);
        if (!el) return null;

        var lang = window.DashboardLang || {};
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
            return null;
        }

        var chart = new ApexCharts(el, {
            series: values,
            chart: { type: 'donut', height: 340, fontFamily: 'inherit' },
            colors: [colors.success, colors.info, colors.danger, colors.warning, colors.orange],
            labels: labels,
            plotOptions: {
                pie: {
                    donut: {
                        size: '72%',
                        labels: {
                            show: true,
                            name: { show: true, fontSize: '13px', color: colors.gray },
                            value: { show: true, fontSize: '24px', fontWeight: 700, color: '#1f2937' },
                            total: { show: true, label: 'Total', fontSize: '13px', color: colors.gray, formatter: function () { return total; } }
                        }
                    }
                }
            },
            dataLabels: { enabled: false },
            legend: { position: 'bottom', fontSize: '12px', markers: { radius: 3 } },
            stroke: { width: 2, colors: ['#fff'] },
            tooltip: { y: { formatter: function (v) { return v + ' inspections'; } } }
        });
        chart.render();
        return chart;
    }

    // ─── Personal Radial (Technician) ───────────────────────────────
    function renderPersonalRadial(passRate) {
        if (personalRadial) personalRadial.destroy();
        var el = document.querySelector('#personalRadial');
        if (!el) return;

        personalRadial = new ApexCharts(el, {
            series: [passRate],
            chart: { type: 'radialBar', height: 260, fontFamily: 'inherit' },
            colors: [colors.success],
            plotOptions: {
                radialBar: {
                    hollow: { size: '65%' },
                    track: { background: '#f3f4f6', strokeWidth: '100%' },
                    dataLabels: {
                        name: { show: true, fontSize: '14px', color: colors.gray, offsetY: -10 },
                        value: { show: true, fontSize: '28px', fontWeight: 700, color: '#1f2937', offsetY: 5 }
                    }
                }
            },
            labels: ['Pass Rate'],
            stroke: { lineCap: 'round' }
        });
        personalRadial.render();
    }

    // ─── Top 10 Vehicle Performance Table ──────────────────────────
    function updateTopVehicles(items) {
        var tbody = document.getElementById('top-vehicles-body');
        if (!tbody || !items) return;

        var lang = window.DashboardLang || {};

        if (items.length === 0) {
            tbody.innerHTML = '<tr><td colspan="10" class="text-center text-muted py-4">' + (lang.no_data || 'No data') + '</td></tr>';
            return;
        }

        var html = '';
        items.forEach(function (v, i) {
            var rankClass = i < 3 ? 'rank-' + (i + 1) : 'rank-default';
            var catBadge = v.category === '2W'
                ? '<span class="badge bg-primary-transparent fw-semibold">2W</span>'
                : v.category === '3W'
                    ? '<span class="badge bg-warning-transparent fw-semibold">3W</span>'
                    : '-';

            html += '<tr>'
                + '<td><span class="rank-badge ' + rankClass + '">' + (i + 1) + '</span></td>'
                + '<td class="fw-semibold">' + v.name + '</td>'
                + '<td class="text-center">' + catBadge + '</td>'
                + '<td class="text-center fw-semibold">' + v.total + '</td>'
                + '<td class="text-center text-success fw-semibold">' + v.completed + '</td>'
                + '<td class="text-center text-info fw-semibold">' + v.in_progress + '</td>'
                + '<td class="text-center text-danger fw-semibold">' + v.rework + '</td>'
                + '<td class="text-center text-warning fw-semibold">' + v.under_review + '</td>'
                + '<td class="text-center fw-semibold" style="color:#f97316;">' + v.pending_approval + '</td>'
                + '<td>'
                +   '<div class="d-flex align-items-center gap-2">'
                +     '<div class="progress progress-xs flex-fill">'
                +       '<div class="progress-bar bg-success" style="width:' + v.completion_rate + '%"></div>'
                +     '</div>'
                +     '<span class="fw-semibold" style="font-size:0.8rem;min-width:40px;">' + v.completion_rate + '%</span>'
                +   '</div>'
                + '</td>'
                + '</tr>';
        });
        tbody.innerHTML = html;
    }

    // ─── Daily Performance Line Chart ────────────────────────────────
    function renderDailyPerformance(data) {
        if (dailyPerformanceChart) dailyPerformanceChart.destroy();
        var el = document.querySelector('#dailyPerformanceChart');
        if (!el) return;

        var lang = window.DashboardLang || {};
        if (!data || !data.labels || data.labels.length === 0) {
            el.innerHTML = '<div class="text-center text-muted py-5">' + (lang.no_data || 'No data') + '</div>';
            return;
        }

        dailyPerformanceChart = new ApexCharts(el, {
            series: [
                { name: lang.completed || 'Completed', data: data.completed || [] },
                { name: lang.in_progress || 'In Progress', data: data.in_progress || [] },
                { name: lang.under_review || 'Under Review', data: data.under_review || [] },
                { name: lang.rework || 'Rework', data: data.rework || [] },
                { name: lang.pending_approval || 'Pending', data: data.pending_approval || [] }
            ],
            chart: {
                type: 'area',
                height: 380,
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
        dailyPerformanceChart.render();
    }

    // ─── Update stat cards on AJAX ──────────────────────────────────
    function updateStatCards(data) {
        var fields = {
            'stat-total': data.totalInspections,
            'stat-completed': data.completedCount,
            'stat-in-progress': data.inProgressCount,
            'stat-rework': data.reworkCount,
            'stat-under-review': data.underReviewCount,
            'stat-pending': data.pendingApproval,
            'stat-pass-rate': data.passRate,
            'stat-discarded': data.discardedCount
        };

        Object.keys(fields).forEach(function (id) {
            var el = document.getElementById(id);
            if (!el) return;
            if (id === 'stat-pass-rate') {
                el.textContent = fields[id] + '%';
            } else {
                animateValue(el, parseInt(el.textContent) || 0, fields[id], 500);
            }
        });

        // Discarded split
        var discardedCard = document.getElementById('stat-discarded');
        if (discardedCard) {
            var splitEl = discardedCard.closest('.stat-card-inner').querySelector('.stat-card-split');
            if (splitEl) {
                var s2w = data.discardedSplit2w !== undefined ? data.discardedSplit2w : 0;
                var s3w = data.discardedSplit3w !== undefined ? data.discardedSplit3w : 0;
                splitEl.innerHTML = '<span><span class="badge bg-primary-transparent">2W</span> ' + s2w + '</span>'
                    + '<span><span class="badge bg-warning-transparent">3W</span> ' + s3w + '</span>';
            }
        }

        // Trend arrow
        var trendEl = document.getElementById('stat-trend');
        if (trendEl) {
            var t = data.totalTrend;
            var up = t >= 0;
            trendEl.className = 'stat-card-trend ' + (up ? 'trend-up' : 'trend-down');
            trendEl.innerHTML = '<i class="bx ' + (up ? 'bx-trending-up' : 'bx-trending-down') + '"></i> '
                + (up ? '+' : '') + t + '%';
        }
    }

    // ─── Recent Inspections table ───────────────────────────────────
    function updateRecentInspections(items) {
        var tbody = document.getElementById('recent-inspections-body');
        if (!tbody || !items) return;

        if (items.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">' + (window.DashboardLang ? window.DashboardLang.no_data : 'No data') + '</td></tr>';
            return;
        }

        var sc = { completed: 'success', in_progress: 'info', rework: 'danger', under_review: 'warning', pending_approval: 'orange' };
        var sl = {
            completed: window.DashboardLang ? window.DashboardLang.completed : 'Completed',
            in_progress: window.DashboardLang ? window.DashboardLang.in_progress : 'In Progress',
            rework: window.DashboardLang ? window.DashboardLang.rework : 'Rework',
            under_review: window.DashboardLang ? window.DashboardLang.under_review : 'Under Review',
            pending_approval: window.DashboardLang ? window.DashboardLang.pending_approval : 'Pending'
        };

        var html = '';
        items.forEach(function (r) {
            var catBadge = r.category === '2W' ? '<span class="badge bg-primary-transparent fw-semibold">2W</span>'
                : r.category === '3W' ? '<span class="badge bg-warning-transparent fw-semibold">3W</span>' : '-';
            html += '<tr>'
                + '<td><span class="fw-semibold">' + (r.job_card || '-') + '</span></td>'
                + '<td>' + (r.model || '-') + '</td>'
                + '<td>' + catBadge + '</td>'
                + '<td>' + (r.technician || '-') + '</td>'
                + '<td>' + (r.date || '-') + '</td>'
                + '<td><span class="badge bg-' + (sc[r.status] || 'secondary') + '-transparent">' + (sl[r.status] || r.status) + '</span></td>'
                + '</tr>';
        });
        tbody.innerHTML = html;
    }

    // ─── Period switch ──────────────────────────────────────────────
    function loadPeriodData(period) {
        var buttons = document.querySelectorAll('#period-filter .period-btn');
        var slider = document.querySelector('#period-filter .period-slider');
        buttons.forEach(function (btn) {
            btn.classList.remove('active');
            if (btn.dataset.period === period) {
                btn.classList.add('active');
                if (slider) slider.setAttribute('data-pos', btn.dataset.index);
            }
        });

        $.ajax({
            url: window.DashboardDataUrl || '/dashboard/data',
            data: { period: period },
            dataType: 'json',
            success: function (data) {
                updateStatCards(data);
                renderMonthlyTrend(data.monthlyTrend);
                renderDailyPerformance(data.dailyPerformance);
                updateTopVehicles(data.topVehicles);
                donut2w = renderDonut('#statusDonut2w', data.split2w, donut2w);
                donut3w = renderDonut('#statusDonut3w', data.split3w, donut3w);
                updateRecentInspections(data.recentInspections);
                if (data.isTechnician && document.querySelector('#personalRadial')) {
                    renderPersonalRadial(data.passRate);
                }
            }
        });
    }

    // ─── Init ───────────────────────────────────────────────────────
    $(document).ready(function () {
        var data = window.DashboardData;
        if (!data) return;

        renderMonthlyTrend(data.monthlyTrend);
        renderDailyPerformance(data.dailyPerformance);
        updateTopVehicles(data.topVehicles);
        donut2w = renderDonut('#statusDonut2w', data.split2w, donut2w);
        donut3w = renderDonut('#statusDonut3w', data.split3w, donut3w);

        if (data.isTechnician && document.querySelector('#personalRadial')) {
            renderPersonalRadial(data.passRate);
        }

        $(document).on('click', '#period-filter .period-btn', function (e) {
            e.preventDefault();
            loadPeriodData($(this).data('period'));
        });
    });
})();
