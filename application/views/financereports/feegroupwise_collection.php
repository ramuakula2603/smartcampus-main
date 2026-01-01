<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<style>
/* Custom styles for fee group-wise report */
.fee-group-card {
    background: #fff;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.2s, box-shadow 0.2s;
    height: 100%;
}

.fee-group-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.fee-group-card .card-header {
    font-size: 14px;
    font-weight: 600;
    color: #333;
    margin-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
    padding-bottom: 10px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.fee-group-card .amount-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    font-size: 13px;
}

.fee-group-card .amount-label {
    color: #666;
}

.fee-group-card .amount-value {
    font-weight: 600;
    color: #333;
}

.fee-group-card .progress-container {
    margin-top: 15px;
}

.fee-group-card .progress {
    height: 25px;
    border-radius: 5px;
    background-color: #f0f0f0;
}

.fee-group-card .progress-bar {
    line-height: 25px;
    font-size: 12px;
    font-weight: 600;
}

.summary-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.summary-card h4 {
    color: white;
    margin-bottom: 15px;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    padding: 8px 0;
    border-bottom: 1px solid rgba(255,255,255,0.2);
}

.summary-item:last-child {
    border-bottom: none;
}

.filter-section {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.grid-container {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 30px;
}

@media (max-width: 1400px) {
    .grid-container {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 992px) {
    .grid-container {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 576px) {
    .grid-container {
        grid-template-columns: 1fr;
    }
}

.chart-container {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.no-data-message {
    text-align: center;
    padding: 40px;
    color: #999;
    font-size: 16px;
}

.export-buttons {
    margin-bottom: 15px;
}

.btn-export {
    margin-right: 10px;
}
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-bar-chart"></i> Fee Group-wise Collection Report
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?php echo base_url(); ?>financereports/finance">Finance Reports</a></li>
            <li class="active">Fee Group-wise Collection</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- Filter Section -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-filter"></i> Filters</h3>
                    </div>
                    <div class="box-body">
                        <form id="filterForm" method="post">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Session <span class="req">*</span></label>
                                        <select class="form-control" name="session_id" id="session_id" required>
                                            <?php foreach ($sessionlist as $session) { ?>
                                                <option value="<?php echo $session['id']; ?>" <?php echo ($session['id'] == $session_id) ? 'selected' : ''; ?>>
                                                    <?php echo $session['session']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Class</label>
                                        <select class="form-control multiselect-dropdown" name="class_ids[]" id="class_ids" multiple="multiple" style="width: 100%;">
                                            <?php foreach ($classlist as $class) { ?>
                                                <option value="<?php echo $class['id']; ?>"><?php echo $class['class']; ?></option>
                                            <?php } ?>
                                        </select>
                                        <small class="text-muted">Hold Ctrl to select multiple</small>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Section</label>
                                        <select class="form-control multiselect-dropdown" name="section_ids[]" id="section_ids" multiple="multiple" style="width: 100%;">
                                            <option value="">Select Class First</option>
                                        </select>
                                        <small class="text-muted">Hold Ctrl to select multiple</small>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Fee Group</label>
                                        <select class="form-control multiselect-dropdown" name="feegroup_ids[]" id="feegroup_ids" multiple="multiple" style="width: 100%;">
                                            <option value="">Loading...</option>
                                        </select>
                                        <small class="text-muted">Hold Ctrl to select multiple</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>From Date</label>
                                        <input type="date" class="form-control" name="from_date" id="from_date">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>To Date</label>
                                        <input type="date" class="form-control" name="to_date" id="to_date">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Date Grouping</label>
                                        <select class="form-control" name="date_grouping" id="date_grouping">
                                            <option value="none">None</option>
                                            <option value="daily">Daily</option>
                                            <option value="weekly">Weekly</option>
                                            <option value="monthly">Monthly</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="submit" class="btn btn-primary btn-block" id="searchBtn">
                                            <i class="fa fa-search"></i> Search
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Summary Section -->
                <div id="summarySection" style="display: none;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="summary-card">
                                <h4><i class="fa fa-dashboard"></i> Summary Statistics</h4>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="summary-item">
                                            <span>Total Fee Groups:</span>
                                            <strong id="summary_total_groups">0</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="summary-item">
                                            <span>Total Amount:</span>
                                            <strong><?php echo $currency_symbol; ?> <span id="summary_total_amount">0.00</span></strong>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="summary-item">
                                            <span>Amount Collected:</span>
                                            <strong><?php echo $currency_symbol; ?> <span id="summary_collected">0.00</span></strong>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="summary-item">
                                            <span>Balance Amount:</span>
                                            <strong><?php echo $currency_symbol; ?> <span id="summary_balance">0.00</span></strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="margin-top: 15px;">
                                    <div class="col-md-12">
                                        <div class="summary-item">
                                            <span>Overall Collection Percentage:</span>
                                            <strong><span id="summary_percentage">0</span>%</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4x4 Grid Section -->
                <div id="gridSection" style="display: none;">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-th"></i> Fee Group-wise Collection (Top 16)</h3>
                        </div>
                        <div class="box-body">
                            <div class="grid-container" id="feeGroupGrid">
                                <!-- Grid cards will be populated here -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div id="chartsSection" style="display: none;">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="chart-container">
                                <h4><i class="fa fa-pie-chart"></i> Collection Distribution</h4>
                                <canvas id="collectionPieChart" height="300"></canvas>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="chart-container">
                                <h4><i class="fa fa-bar-chart"></i> Fee Group Comparison</h4>
                                <canvas id="collectionBarChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Table Section -->
                <div id="tableSection" style="display: none;">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-table"></i> Detailed Fee Collection Records</h3>
                            <div class="box-tools pull-right export-buttons">
                                <button type="button" class="btn btn-success btn-sm btn-export" id="exportExcel">
                                    <i class="fa fa-file-excel-o"></i> Export Excel
                                </button>
                                <button type="button" class="btn btn-info btn-sm btn-export" id="exportCSV">
                                    <i class="fa fa-file-text-o"></i> Export CSV
                                </button>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="feeGroupTable">
                                    <thead>
                                        <tr>
                                            <th>Admission No</th>
                                            <th>Student Name</th>
                                            <th>Class</th>
                                            <th>Section</th>
                                            <th>Fee Group</th>
                                            <th>Total Fee</th>
                                            <th>Collected</th>
                                            <th>Balance</th>
                                            <th>Collection %</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="feeGroupTableBody">
                                        <!-- Table rows will be populated here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- No Data Message -->
                <div id="noDataSection" style="display: none;">
                    <div class="box box-warning">
                        <div class="box-body">
                            <div class="no-data-message">
                                <i class="fa fa-info-circle fa-3x"></i>
                                <p>No data available for the selected filters. Please adjust your search criteria.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
var currency_symbol = '<?php echo $currency_symbol; ?>';
var base_url = '<?php echo base_url(); ?>';
var pieChart = null;
var barChart = null;
var currentData = null;

$(document).ready(function() {
    // Initialize SumoSelect for all multi-select dropdowns
    if (typeof $.fn.SumoSelect !== 'undefined') {
        $('.multiselect-dropdown').SumoSelect({
            placeholder: 'Select Options',
            csvDispCount: 3,
            captionFormat: '{0} Selected',
            captionFormatAllSelected: 'All Selected ({0})',
            selectAll: true,
            search: true,
            searchText: 'Search...',
            noMatch: 'No matches found "{0}"',
            okCancelInMulti: true,
            isClickAwayOk: true,
            locale: ['OK', 'Cancel', 'Select All'],
            up: false,
            showTitle: true
        });
    }

    // Initialize Select2
    $('.select2').select2({
        placeholder: 'Select options',
        allowClear: true
    });

    // Load fee groups on page load
    loadFeeGroups();

    // Load sections when class is selected
    $('#class_ids').on('change', function() {
        loadSections();
    });

    // Handle form submission
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        loadFeeGroupData();
    });

    // Export buttons
    $('#exportExcel').on('click', function() {
        exportReport('excel');
    });

    $('#exportCSV').on('click', function() {
        exportReport('csv');
    });

    // Initialize DataTable
    initializeDataTable();
});

/**
 * Load fee groups for filter dropdown
 */
function loadFeeGroups() {
    var session_id = $('#session_id').val();

    $.ajax({
        url: base_url + 'admin/feegroup/get_feegroup',
        type: 'POST',
        data: { session_id: session_id },
        dataType: 'json',
        success: function(response) {
            var options = '<option value="">All Fee Groups</option>';
            if (response && response.length > 0) {
                $.each(response, function(index, group) {
                    options += '<option value="' + group.id + '">' + group.name + '</option>';
                });
            }
            $('#feegroup_ids').html(options);
            $('#feegroup_ids').select2({
                placeholder: 'Select fee groups',
                allowClear: true
            });
        },
        error: function() {
            $('#feegroup_ids').html('<option value="">Error loading fee groups</option>');
        }
    });
}

/**
 * Load sections based on selected classes
 */
function loadSections() {
    var class_ids = $('#class_ids').val();

    if (!class_ids || class_ids.length === 0) {
        $('#section_ids').html('<option value="">Select Class First</option>');
        return;
    }

    $.ajax({
        url: base_url + 'sections/getByClass',
        type: 'POST',
        data: { class_id: class_ids },
        dataType: 'json',
        success: function(response) {
            var options = '<option value="">All Sections</option>';
            if (response && response.length > 0) {
                $.each(response, function(index, section) {
                    options += '<option value="' + section.section_id + '">' + section.section + '</option>';
                });
            }
            $('#section_ids').html(options);
            $('#section_ids').select2({
                placeholder: 'Select sections',
                allowClear: true
            });
        },
        error: function() {
            $('#section_ids').html('<option value="">Error loading sections</option>');
        }
    });
}

/**
 * Load fee group-wise data
 */
function loadFeeGroupData() {
    var formData = {
        session_id: $('#session_id').val(),
        class_ids: $('#class_ids').val() || [],
        section_ids: $('#section_ids').val() || [],
        feegroup_ids: $('#feegroup_ids').val() || [],
        from_date: $('#from_date').val(),
        to_date: $('#to_date').val(),
        date_grouping: $('#date_grouping').val()
    };

    // Show loading
    $('#searchBtn').html('<i class="fa fa-spinner fa-spin"></i> Loading...').prop('disabled', true);

    $.ajax({
        url: base_url + 'financereports/getFeeGroupwiseData',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.status == 1) {
                currentData = response;

                if (response.grid_data && response.grid_data.length > 0) {
                    // Update summary
                    updateSummary(response.summary);

                    // Populate grid
                    populateGrid(response.grid_data);

                    // Populate charts
                    populateCharts(response.grid_data);

                    // Populate table
                    populateTable(response.detailed_data);

                    // Show sections
                    $('#summarySection').show();
                    $('#gridSection').show();
                    $('#chartsSection').show();
                    $('#tableSection').show();
                    $('#noDataSection').hide();
                } else {
                    // No data found
                    hideAllSections();
                    $('#noDataSection').show();
                }
            } else {
                alert('Error: ' + response.message);
                hideAllSections();
            }
        },
        error: function(xhr, status, error) {
            alert('An error occurred while loading data. Please try again.');
            console.error(error);
            hideAllSections();
        },
        complete: function() {
            $('#searchBtn').html('<i class="fa fa-search"></i> Search').prop('disabled', false);
        }
    });
}

/**
 * Update summary statistics
 */
function updateSummary(summary) {
    $('#summary_total_groups').text(summary.total_fee_groups);
    $('#summary_total_amount').text(formatNumber(summary.total_amount));
    $('#summary_collected').text(formatNumber(summary.total_collected));
    $('#summary_balance').text(formatNumber(summary.total_balance));
    $('#summary_percentage').text(summary.collection_percentage);
}

/**
 * Populate 4x4 grid with fee group cards
 */
function populateGrid(data) {
    var html = '';
    var maxCards = 16; // 4x4 grid
    var displayData = data.slice(0, maxCards);

    $.each(displayData, function(index, item) {
        var progressColor = getProgressColor(item.collection_percentage);

        html += '<div class="fee-group-card">';
        html += '  <div class="card-header" title="' + item.fee_group_name + '">' + item.fee_group_name + '</div>';
        html += '  <div class="amount-row">';
        html += '    <span class="amount-label">Total Amount:</span>';
        html += '    <span class="amount-value">' + currency_symbol + ' ' + formatNumber(item.total_amount) + '</span>';
        html += '  </div>';
        html += '  <div class="amount-row">';
        html += '    <span class="amount-label">Collected:</span>';
        html += '    <span class="amount-value text-success">' + currency_symbol + ' ' + formatNumber(item.amount_collected) + '</span>';
        html += '  </div>';
        html += '  <div class="amount-row">';
        html += '    <span class="amount-label">Balance:</span>';
        html += '    <span class="amount-value text-danger">' + currency_symbol + ' ' + formatNumber(item.balance_amount) + '</span>';
        html += '  </div>';
        html += '  <div class="progress-container">';
        html += '    <div class="progress">';
        html += '      <div class="progress-bar ' + progressColor + '" role="progressbar" style="width: ' + item.collection_percentage + '%">';
        html += '        ' + item.collection_percentage + '%';
        html += '      </div>';
        html += '    </div>';
        html += '  </div>';
        html += '</div>';
    });

    $('#feeGroupGrid').html(html);
}

/**
 * Get progress bar color based on percentage
 */
function getProgressColor(percentage) {
    if (percentage >= 80) return 'progress-bar-success';
    if (percentage >= 50) return 'progress-bar-warning';
    return 'progress-bar-danger';
}

/**
 * Populate charts
 */
function populateCharts(data) {
    var maxChartData = 10; // Show top 10 in charts
    var chartData = data.slice(0, maxChartData);

    var labels = [];
    var collectedData = [];
    var balanceData = [];
    var colors = generateColors(chartData.length);

    $.each(chartData, function(index, item) {
        labels.push(item.fee_group_name);
        collectedData.push(parseFloat(item.amount_collected));
        balanceData.push(parseFloat(item.balance_amount));
    });

    // Destroy existing charts
    if (pieChart) pieChart.destroy();
    if (barChart) barChart.destroy();

    // Pie Chart
    var pieCtx = document.getElementById('collectionPieChart').getContext('2d');
    pieChart = new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: collectedData,
                backgroundColor: colors,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        boxWidth: 12,
                        font: {
                            size: 11
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + currency_symbol + ' ' + formatNumber(context.parsed);
                        }
                    }
                }
            }
        }
    });

    // Bar Chart
    var barCtx = document.getElementById('collectionBarChart').getContext('2d');
    barChart = new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Collected',
                    data: collectedData,
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Balance',
                    data: balanceData,
                    backgroundColor: 'rgba(255, 99, 132, 0.6)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return currency_symbol + ' ' + formatNumber(value);
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + currency_symbol + ' ' + formatNumber(context.parsed.y);
                        }
                    }
                }
            }
        }
    });
}

/**
 * Generate random colors for charts
 */
function generateColors(count) {
    var colors = [
        'rgba(255, 99, 132, 0.6)',
        'rgba(54, 162, 235, 0.6)',
        'rgba(255, 206, 86, 0.6)',
        'rgba(75, 192, 192, 0.6)',
        'rgba(153, 102, 255, 0.6)',
        'rgba(255, 159, 64, 0.6)',
        'rgba(199, 199, 199, 0.6)',
        'rgba(83, 102, 255, 0.6)',
        'rgba(255, 99, 255, 0.6)',
        'rgba(99, 255, 132, 0.6)'
    ];

    while (colors.length < count) {
        colors.push('rgba(' + Math.floor(Math.random() * 255) + ',' +
                    Math.floor(Math.random() * 255) + ',' +
                    Math.floor(Math.random() * 255) + ', 0.6)');
    }

    return colors.slice(0, count);
}

/**
 * Populate detailed table
 */
function populateTable(data) {
    // Destroy existing DataTable if it exists
    if ($.fn.DataTable.isDataTable('#feeGroupTable')) {
        $('#feeGroupTable').DataTable().destroy();
    }

    // Clear the table body
    $('#feeGroupTableBody').empty();

    if (data && data.length > 0) {
        // Prepare data for DataTable
        var tableData = [];

        $.each(data, function(index, item) {
            var statusClass = '';
            if (item.payment_status == 'Paid') statusClass = 'label-success';
            else if (item.payment_status == 'Partial') statusClass = 'label-warning';
            else statusClass = 'label-danger';

            tableData.push([
                item.admission_no,
                item.student_name,
                item.class_name,
                item.section_name,
                item.fee_group_name,
                currency_symbol + ' ' + formatNumber(item.total_amount),
                currency_symbol + ' ' + formatNumber(item.amount_collected),
                currency_symbol + ' ' + formatNumber(item.balance_amount),
                item.collection_percentage + '%',
                '<span class="label ' + statusClass + '">' + item.payment_status + '</span>'
            ]);
        });

        // Initialize DataTable with data
        $('#feeGroupTable').DataTable({
            "data": tableData,
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "order": [[0, "asc"]],
            "columnDefs": [
                { "orderable": true, "targets": "_all" },
                { "className": "text-right", "targets": [5, 6, 7, 8] }
            ],
            "dom": '<"row"<"col-sm-6"l><"col-sm-6"f>>rtip'
        });
    } else {
        // Initialize empty DataTable
        initializeDataTable();
    }
}

/**
 * Initialize DataTable
 */
function initializeDataTable() {
    $('#feeGroupTable').DataTable({
        "pageLength": 25,
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        "order": [[0, "asc"]],
        "columnDefs": [
            { "orderable": true, "targets": "_all" }
        ],
        "dom": '<"row"<"col-sm-6"l><"col-sm-6"f>>rtip'
    });
}

/**
 * Export report
 */
function exportReport(format) {
    if (!currentData || !currentData.detailed_data || currentData.detailed_data.length === 0) {
        alert('No data available to export');
        return;
    }

    var formData = {
        export_format: format,
        session_id: $('#session_id').val(),
        class_ids: $('#class_ids').val() || [],
        section_ids: $('#section_ids').val() || [],
        feegroup_ids: $('#feegroup_ids').val() || [],
        from_date: $('#from_date').val(),
        to_date: $('#to_date').val()
    };

    // Create a form and submit
    var form = $('<form>', {
        'method': 'POST',
        'action': base_url + 'financereports/exportFeeGroupwiseReport'
    });

    $.each(formData, function(key, value) {
        if (Array.isArray(value)) {
            $.each(value, function(i, v) {
                form.append($('<input>', {
                    'type': 'hidden',
                    'name': key + '[]',
                    'value': v
                }));
            });
        } else {
            form.append($('<input>', {
                'type': 'hidden',
                'name': key,
                'value': value
            }));
        }
    });

    $('body').append(form);
    form.submit();
    form.remove();
}

/**
 * Hide all sections
 */
function hideAllSections() {
    $('#summarySection').hide();
    $('#gridSection').hide();
    $('#chartsSection').hide();
    $('#tableSection').hide();
    $('#noDataSection').hide();
}

/**
 * Format number with commas
 */
function formatNumber(num) {
    return parseFloat(num).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}
</script>

