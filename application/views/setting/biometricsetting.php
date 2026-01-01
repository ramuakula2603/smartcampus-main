<div class="content-wrapper" style="min-height: 348px;">
    <section class="content">
        <div class="row">

            <?php $this->load->view('setting/_settingmenu'); ?>

            <!-- left column -->
            <div class="col-md-10">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><i class="fa fa-fingerprint"></i> <?php echo $this->lang->line('biometricsetting'); ?> - Multiple Time Ranges</h3>
                        <div class="box-tools pull-right">
                        </div><!-- /.box-tools -->
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i> <strong>Note:</strong> Configure multiple check-in and check-out time ranges with automatic late marking. Lower priority numbers are checked first.
                        </div>

                        <!-- Check-in Time Ranges Section -->
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="session-head">
                                    <i class="fa fa-sign-in"></i> Check-in Time Ranges
                                    <button type="button" class="btn btn-sm btn-success pull-right" id="addCheckinRange">
                                        <i class="fa fa-plus"></i> Add Check-in Range
                                    </button>
                                </h4>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered" id="checkinTable">
                                        <thead>
                                            <tr>
                                                <th width="5%">Priority</th>
                                                <th width="20%">Range Name</th>
                                                <th width="15%">Start Time</th>
                                                <th width="15%">End Time</th>
                                                <th width="12%">Grace Period (min)</th>
                                                <th width="15%">Attendance Type</th>
                                                <th width="8%">Active</th>
                                                <th width="10%">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="checkinRangesBody">
                                            <!-- Will be populated by JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="settinghr"></div>

                        <!-- Check-out Time Ranges Section -->
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="session-head">
                                    <i class="fa fa-sign-out"></i> Check-out Time Ranges
                                    <button type="button" class="btn btn-sm btn-success pull-right" id="addCheckoutRange">
                                        <i class="fa fa-plus"></i> Add Check-out Range
                                    </button>
                                </h4>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered" id="checkoutTable">
                                        <thead>
                                            <tr>
                                                <th width="5%">Priority</th>
                                                <th width="20%">Range Name</th>
                                                <th width="15%">Start Time</th>
                                                <th width="15%">End Time</th>
                                                <th width="12%">Grace Period (min)</th>
                                                <th width="15%">Attendance Type</th>
                                                <th width="8%">Active</th>
                                                <th width="10%">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="checkoutRangesBody">
                                            <!-- Will be populated by JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="box-footer">
                            <?php if ($this->rbac->hasPrivilege('general_setting', 'can_edit')) { ?>
                                <button type="button" class="btn btn-primary pull-right" id="saveAllRanges" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Saving...">
                                    <i class="fa fa-save"></i> Save All Changes
                                </button>
                            <?php } ?>
                        </div>
                    </div><!-- /.box-body -->
                </div>









            </div><!--/.col (left) -->
            <!-- right column -->
        </div>
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

<!-- Add/Edit Time Range Modal -->
<div class="modal fade" id="timeRangeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="modalTitle">Add Time Range</h4>
            </div>
            <div class="modal-body">
                <form id="timeRangeForm">
                    <input type="hidden" id="rangeId" name="id">
                    <input type="hidden" id="rangeType" name="range_type">

                    <div class="form-group">
                        <label>Range Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="rangeName" name="range_name" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Start Time <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" id="timeStart" name="time_start" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>End Time <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" id="timeEnd" name="time_end" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Grace Period (minutes)</label>
                                <input type="number" class="form-control" id="gracePeriod" name="grace_period_minutes" value="0" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Priority <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="priority" name="priority" value="1" min="1" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Attendance Type <span class="text-danger">*</span></label>
                        <select class="form-control" id="attendanceTypeId" name="attendance_type_id" required>
                            <option value="1">Present</option>
                            <option value="2">Late</option>
                            <option value="3">Absent</option>
                            <option value="4">Half Day</option>
                            <option value="5">Holiday</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="isActive" name="is_active" checked> Active
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveTimeRange">Save</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
var base_url = '<?php echo base_url(); ?>';
var timeRanges = {
    checkin: [],
    checkout: []
};


$(document).ready(function() {
    // Load time ranges on page load
    loadTimeRanges();

    // Add Check-in Range Button
    $('#addCheckinRange').click(function() {
        openModal('checkin', null);
    });

    // Add Check-out Range Button
    $('#addCheckoutRange').click(function() {
        openModal('checkout', null);
    });

    // Save Time Range Button in Modal
    $('#saveTimeRange').click(function() {
        saveTimeRange();
    });

    // Save All Ranges Button
    $('#saveAllRanges').click(function() {
        saveAllRanges();
    });
});

// Load time ranges from server
function loadTimeRanges() {
    $.ajax({
        url: base_url + 'admin/biometric_timing/getTimeRanges',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 200) {
                timeRanges = response.data;
                renderTimeRanges();
            }
        },
        error: function() {
            errorMsg('Failed to load time ranges');
        }
    });
}

// Render time ranges in tables
function renderTimeRanges() {
    // Render check-in ranges
    var checkinHtml = '';
    if (timeRanges.checkin && timeRanges.checkin.length > 0) {
        timeRanges.checkin.forEach(function(range) {
            checkinHtml += buildRangeRow(range, 'checkin');
        });
    } else {
        checkinHtml = '<tr><td colspan="8" class="text-center">No check-in ranges configured</td></tr>';
    }
    $('#checkinRangesBody').html(checkinHtml);

    // Render check-out ranges
    var checkoutHtml = '';
    if (timeRanges.checkout && timeRanges.checkout.length > 0) {
        timeRanges.checkout.forEach(function(range) {
            checkoutHtml += buildRangeRow(range, 'checkout');
        });
    } else {
        checkoutHtml = '<tr><td colspan="8" class="text-center">No check-out ranges configured</td></tr>';
    }
    $('#checkoutRangesBody').html(checkoutHtml);
}

// Build HTML for a time range row
function buildRangeRow(range, type) {
    var attendanceTypes = {
        1: 'Present',
        2: 'Late',
        3: 'Absent',
        4: 'Half Day',
        5: 'Holiday'
    };

    var activeClass = range.is_active == 1 ? 'success' : 'danger';
    var activeText = range.is_active == 1 ? 'Active' : 'Inactive';

    return `
        <tr data-id="${range.id}" data-type="${type}">
            <td>${range.priority}</td>
            <td>${range.range_name}</td>
            <td>${formatTime(range.time_start)}</td>
            <td>${formatTime(range.time_end)}</td>
            <td>${range.grace_period_minutes}</td>
            <td><span class="label label-info">${attendanceTypes[range.attendance_type_id]}</span></td>
            <td><span class="label label-${activeClass}">${activeText}</span></td>
            <td>
                <button class="btn btn-xs btn-primary edit-range" data-id="${range.id}" data-type="${type}">
                    <i class="fa fa-edit"></i>
                </button>
                <button class="btn btn-xs btn-danger delete-range" data-id="${range.id}">
                    <i class="fa fa-trash"></i>
                </button>
            </td>
        </tr>
    `;
}

// Format time from HH:MM:SS to HH:MM AM/PM
function formatTime(time) {
    if (!time) return '';
    var parts = time.split(':');
    var hours = parseInt(parts[0]);
    var minutes = parts[1];
    var ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12;
    return hours + ':' + minutes + ' ' + ampm;
}

// Open modal for add/edit
function openModal(type, rangeId) {
    $('#timeRangeForm')[0].reset();
    $('#rangeType').val(type);

    if (rangeId) {
        // Edit mode
        var range = findRangeById(rangeId, type);
        if (range) {
            $('#modalTitle').text('Edit ' + (type === 'checkin' ? 'Check-in' : 'Check-out') + ' Range');
            $('#rangeId').val(range.id);
            $('#rangeName').val(range.range_name);
            $('#timeStart').val(range.time_start);
            $('#timeEnd').val(range.time_end);
            $('#gracePeriod').val(range.grace_period_minutes);
            $('#attendanceTypeId').val(range.attendance_type_id);
            $('#priority').val(range.priority);
            $('#isActive').prop('checked', range.is_active == 1);
        }
    } else {
        // Add mode
        $('#modalTitle').text('Add ' + (type === 'checkin' ? 'Check-in' : 'Check-out') + ' Range');
        $('#rangeId').val('');
        $('#isActive').prop('checked', true);
    }

    $('#timeRangeModal').modal('show');
}

// Find range by ID
function findRangeById(id, type) {
    var ranges = timeRanges[type];
    for (var i = 0; i < ranges.length; i++) {
        if (ranges[i].id == id) {
            return ranges[i];
        }
    }
    return null;
}

// Save time range (add or update)
function saveTimeRange() {
    var formData = $('#timeRangeForm').serialize();
    var rangeId = $('#rangeId').val();
    var url = rangeId ? base_url + 'admin/biometric_timing/updateTimeRange' : base_url + 'admin/biometric_timing/addTimeRange';

    $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.status === 200) {
                successMsg(response.message);
                $('#timeRangeModal').modal('hide');
                loadTimeRanges();
            } else {
                errorMsg(response.message);
            }
        },
        error: function() {
            errorMsg('Failed to save time range');
        }
    });
}

// Delete time range
$(document).on('click', '.delete-range', function() {
    var id = $(this).data('id');

    if (confirm('Are you sure you want to delete this time range?')) {
        $.ajax({
            url: base_url + 'admin/biometric_timing/deleteTimeRange',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function(response) {
                if (response.status === 200) {
                    successMsg(response.message);
                    loadTimeRanges();
                } else {
                    errorMsg(response.message);
                }
            },
            error: function() {
                errorMsg('Failed to delete time range');
            }
        });
    }
});

// Edit time range
$(document).on('click', '.edit-range', function() {
    var id = $(this).data('id');
    var type = $(this).data('type');
    openModal(type, id);
});

// Save all ranges (batch update)
function saveAllRanges() {
    var $btn = $('#saveAllRanges');
    $btn.button('loading');

    var allRanges = [];
    timeRanges.checkin.forEach(function(range) {
        allRanges.push(range);
    });
    timeRanges.checkout.forEach(function(range) {
        allRanges.push(range);
    });

    $.ajax({
        url: base_url + 'admin/biometric_timing/batchSaveTimeRanges',
        type: 'POST',
        data: { ranges: allRanges },
        dataType: 'json',
        success: function(response) {
            if (response.status === 200) {
                successMsg(response.message);
                loadTimeRanges();
            } else {
                errorMsg(response.message);
            }
        },
        error: function() {
            errorMsg('Failed to save time ranges');
        },
        complete: function() {
            $btn.button('reset');
        }
    });
}
</script>

<style>
.session-head {
    margin-top: 20px;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #f4f4f4;
}

.settinghr {
    margin: 30px 0;
    border-top: 2px solid #f4f4f4;
}

.table > tbody > tr > td {
    vertical-align: middle;
}

.modal-body .form-group label {
    font-weight: 600;
}

.text-danger {
    color: #dd4b39;
}
</style>