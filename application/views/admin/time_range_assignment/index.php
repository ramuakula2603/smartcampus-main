<div class="content-wrapper" style="min-height: 348px;">
    <section class="content">
        <div class="row">
            <?php $this->load->view('setting/_settingmenu'); ?>

            <div class="col-md-10">
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix">
                            <i class="fa fa-users"></i> <?php echo $this->lang->line('time_range_assignments'); ?> - Assign Time Ranges to Staff & Students
                        </h3>
                    </div>

                    <div class="box-body">
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i> <strong>Note:</strong> 
                            Assign specific check-in and check-out time ranges to individual staff members and students. 
                            If no assignments are made, all time ranges are available (backward compatible).
                        </div>

                        <!-- Tab Navigation -->
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#staff_tab" aria-controls="staff_tab" role="tab" data-toggle="tab">
                                    <i class="fa fa-user-md"></i> Staff Assignments
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#student_tab" aria-controls="student_tab" role="tab" data-toggle="tab">
                                    <i class="fa fa-graduation-cap"></i> Student Assignments
                                </a>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content" style="padding-top: 20px;">
                            
                            <!-- STAFF TAB -->
                            <div role="tabpanel" class="tab-pane active" id="staff_tab">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Search Staff</label>
                                            <input type="text" class="form-control" id="staff_search" placeholder="Search by name or employee ID...">
                                        </div>
                                        <div class="form-group">
                                            <label>Select Staff Member</label>
                                            <select class="form-control select2" id="staff_select" style="width: 100%;">
                                                <option value="">-- Select Staff --</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div id="staff_assignment_panel" style="display: none;">
                                            <h4>
                                                <i class="fa fa-clock-o"></i> Assigned Time Ranges for: 
                                                <span id="staff_name_display" class="text-primary"></span>
                                            </h4>
                                            <div class="alert alert-warning">
                                                <i class="fa fa-exclamation-triangle"></i> 
                                                <strong>Important:</strong> If no time ranges are selected, this staff member can use ALL time ranges.
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h5><i class="fa fa-sign-in text-success"></i> Check-in Ranges</h5>
                                                    <div id="staff_checkin_ranges"></div>
                                                </div>
                                                <div class="col-md-6">
                                                    <h5><i class="fa fa-sign-out text-primary"></i> Check-out Ranges</h5>
                                                    <div id="staff_checkout_ranges"></div>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group" style="margin-top: 20px;">
                                                <button type="button" class="btn btn-success" id="save_staff_assignments">
                                                    <i class="fa fa-save"></i> Save Staff Assignments
                                                </button>
                                                <button type="button" class="btn btn-default" id="cancel_staff_assignments">
                                                    <i class="fa fa-times"></i> Cancel
                                                </button>
                                            </div>
                                        </div>
                                        <div id="staff_no_selection" class="text-center text-muted" style="padding: 50px;">
                                            <i class="fa fa-user-plus fa-3x"></i>
                                            <p style="margin-top: 20px;">Select a staff member to manage their time range assignments</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- STUDENT TAB -->
                            <div role="tabpanel" class="tab-pane" id="student_tab">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Class</label>
                                            <select class="form-control" id="student_class">
                                                <option value="">-- Select Class --</option>
                                                <?php foreach ($this->class_model->get() as $class) { ?>
                                                    <option value="<?php echo $class['id']; ?>"><?php echo $class['class']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Section</label>
                                            <select class="form-control" id="student_section">
                                                <option value="">-- Select Section --</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Search Student</label>
                                            <input type="text" class="form-control" id="student_search" placeholder="Search by name or admission no...">
                                        </div>
                                        <div class="form-group">
                                            <label>Select Student</label>
                                            <select class="form-control select2" id="student_select" style="width: 100%;">
                                                <option value="">-- Select Student --</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div id="student_assignment_panel" style="display: none;">
                                            <h4>
                                                <i class="fa fa-clock-o"></i> Assigned Time Ranges for: 
                                                <span id="student_name_display" class="text-primary"></span>
                                            </h4>
                                            <div class="alert alert-warning">
                                                <i class="fa fa-exclamation-triangle"></i> 
                                                <strong>Important:</strong> If no time ranges are selected, this student can use ALL time ranges.
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h5><i class="fa fa-sign-in text-success"></i> Check-in Ranges</h5>
                                                    <div id="student_checkin_ranges"></div>
                                                </div>
                                                <div class="col-md-6">
                                                    <h5><i class="fa fa-sign-out text-primary"></i> Check-out Ranges</h5>
                                                    <div id="student_checkout_ranges"></div>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group" style="margin-top: 20px;">
                                                <button type="button" class="btn btn-success" id="save_student_assignments">
                                                    <i class="fa fa-save"></i> Save Student Assignments
                                                </button>
                                                <button type="button" class="btn btn-default" id="cancel_student_assignments">
                                                    <i class="fa fa-times"></i> Cancel
                                                </button>
                                            </div>
                                        </div>
                                        <div id="student_no_selection" class="text-center text-muted" style="padding: 50px;">
                                            <i class="fa fa-graduation-cap fa-3x"></i>
                                            <p style="margin-top: 20px;">Select class, section, and student to manage their time range assignments</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
.time-range-checkbox {
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background-color: #f9f9f9;
}
.time-range-checkbox:hover {
    background-color: #f0f0f0;
}
.time-range-checkbox label {
    font-weight: normal;
    margin-bottom: 0;
    cursor: pointer;
}
.time-range-checkbox input[type="checkbox"] {
    margin-right: 10px;
}
.time-range-info {
    font-size: 12px;
    color: #666;
    margin-left: 25px;
}
</style>

<script>
var base_url = '<?php echo base_url(); ?>';
var current_staff_id = null;
var current_student_session_id = null;

$(document).ready(function() {
    // Initialize Select2
    $('#staff_select').select2({
        placeholder: '-- Select Staff --',
        allowClear: true
    });
    
    $('#student_select').select2({
        placeholder: '-- Select Student --',
        allowClear: true
    });
    
    // Load staff list on page load
    loadStaffList();
    
    // Staff search
    $('#staff_search').on('keyup', function() {
        loadStaffList($(this).val());
    });
    
    // Staff selection
    $('#staff_select').on('change', function() {
        var staff_id = $(this).val();
        if (staff_id) {
            current_staff_id = staff_id;
            var staff_name = $('#staff_select option:selected').text();
            loadStaffAssignments(staff_id, staff_name);
        } else {
            $('#staff_assignment_panel').hide();
            $('#staff_no_selection').show();
        }
    });
    
    // Save staff assignments
    $('#save_staff_assignments').on('click', function() {
        saveStaffAssignments();
    });
    
    // Cancel staff assignments
    $('#cancel_staff_assignments').on('click', function() {
        $('#staff_select').val('').trigger('change');
        $('#staff_assignment_panel').hide();
        $('#staff_no_selection').show();
    });
    
    // Student class change
    $('#student_class').on('change', function() {
        var class_id = $(this).val();
        if (class_id) {
            loadSections(class_id);
        } else {
            $('#student_section').html('<option value="">-- Select Section --</option>');
            $('#student_select').html('<option value="">-- Select Student --</option>');
        }
    });
    
    // Student section change
    $('#student_section').on('change', function() {
        var class_id = $('#student_class').val();
        var section_id = $(this).val();
        if (class_id && section_id) {
            loadStudentList(class_id, section_id);
        }
    });
    
    // Student search
    $('#student_search').on('keyup', function() {
        var class_id = $('#student_class').val();
        var section_id = $('#student_section').val();
        if (class_id && section_id) {
            loadStudentList(class_id, section_id, $(this).val());
        }
    });
    
    // Student selection
    $('#student_select').on('change', function() {
        var student_session_id = $(this).val();
        if (student_session_id) {
            current_student_session_id = student_session_id;
            var student_name = $('#student_select option:selected').text();
            loadStudentAssignments(student_session_id, student_name);
        } else {
            $('#student_assignment_panel').hide();
            $('#student_no_selection').show();
        }
    });
    
    // Save student assignments
    $('#save_student_assignments').on('click', function() {
        saveStudentAssignments();
    });
    
    // Cancel student assignments
    $('#cancel_student_assignments').on('click', function() {
        $('#student_select').val('').trigger('change');
        $('#student_assignment_panel').hide();
        $('#student_no_selection').show();
    });
});

// Load staff list
function loadStaffList(search = '') {
    $.ajax({
        url: base_url + 'admin/time_range_assignment/getStaffList',
        type: 'POST',
        data: { search: search },
        dataType: 'json',
        success: function(response) {
            if (response.status === 200) {
                var options = '<option value="">-- Select Staff --</option>';
                $.each(response.data, function(index, staff) {
                    options += '<option value="' + staff.id + '">' + staff.name + ' ' + staff.surname + ' (' + staff.employee_id + ')</option>';
                });
                $('#staff_select').html(options);
                // Trigger Select2 to refresh
                $('#staff_select').trigger('change.select2');
            }
        }
    });
}

// Load staff assignments
function loadStaffAssignments(staff_id, staff_name) {
    $.ajax({
        url: base_url + 'admin/time_range_assignment/getStaffAssignments',
        type: 'POST',
        data: { staff_id: staff_id },
        dataType: 'json',
        success: function(response) {
            if (response.status === 200) {
                $('#staff_name_display').text(staff_name);

                var checkin_html = '';
                var checkout_html = '';

                $.each(response.data.all_ranges, function(index, range) {
                    var checked = range.is_assigned ? 'checked' : '';
                    var checkbox_html = '<div class="time-range-checkbox">' +
                        '<label>' +
                        '<input type="checkbox" name="staff_time_ranges[]" value="' + range.id + '" ' + checked + '> ' +
                        '<strong>' + range.range_name + '</strong>' +
                        '</label>' +
                        '<div class="time-range-info">' +
                        '<i class="fa fa-clock-o"></i> ' + range.time_start + ' - ' + range.time_end +
                        ' | Grace: ' + range.grace_period_minutes + ' min' +
                        '</div>' +
                        '</div>';

                    if (range.range_type === 'checkin') {
                        checkin_html += checkbox_html;
                    } else {
                        checkout_html += checkbox_html;
                    }
                });

                $('#staff_checkin_ranges').html(checkin_html || '<p class="text-muted">No check-in ranges available</p>');
                $('#staff_checkout_ranges').html(checkout_html || '<p class="text-muted">No check-out ranges available</p>');

                $('#staff_no_selection').hide();
                $('#staff_assignment_panel').show();
            }
        }
    });
}

// Save staff assignments
function saveStaffAssignments() {
    var time_range_ids = [];
    $('input[name="staff_time_ranges[]"]:checked').each(function() {
        time_range_ids.push($(this).val());
    });

    $.ajax({
        url: base_url + 'admin/time_range_assignment/saveStaffAssignments',
        type: 'POST',
        data: {
            staff_id: current_staff_id,
            time_range_ids: time_range_ids
        },
        dataType: 'json',
        beforeSend: function() {
            $('#save_staff_assignments').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
        },
        success: function(response) {
            if (response.status === 200) {
                toastr.success(response.message);
            } else {
                toastr.error(response.message);
            }
        },
        complete: function() {
            $('#save_staff_assignments').prop('disabled', false).html('<i class="fa fa-save"></i> Save Staff Assignments');
        }
    });
}

// Load sections
function loadSections(class_id) {
    $.ajax({
        url: base_url + 'sections/getByClass',
        type: 'POST',
        data: { class_id: class_id },
        dataType: 'json',
        success: function(response) {
            var options = '<option value="">-- Select Section --</option>';
            if (response && response.length > 0) {
                $.each(response, function(index, section) {
                    options += '<option value="' + section.section_id + '">' + section.section + '</option>';
                });
            }
            $('#student_section').html(options);
        }
    });
}

// Load student list
function loadStudentList(class_id, section_id, search = '') {
    $.ajax({
        url: base_url + 'admin/time_range_assignment/getStudentList',
        type: 'POST',
        data: {
            class_id: class_id,
            section_id: section_id,
            search: search
        },
        dataType: 'json',
        success: function(response) {
            if (response.status === 200) {
                var options = '<option value="">-- Select Student --</option>';
                $.each(response.data, function(index, student) {
                    var name = student.firstname + ' ' + (student.lastname || '');
                    options += '<option value="' + student.student_session_id + '">' + name + ' (' + student.admission_no + ')</option>';
                });
                $('#student_select').html(options);
                // Trigger Select2 to refresh
                $('#student_select').trigger('change.select2');
            }
        }
    });
}

// Load student assignments
function loadStudentAssignments(student_session_id, student_name) {
    $.ajax({
        url: base_url + 'admin/time_range_assignment/getStudentAssignments',
        type: 'POST',
        data: { student_session_id: student_session_id },
        dataType: 'json',
        success: function(response) {
            if (response.status === 200) {
                $('#student_name_display').text(student_name);

                var checkin_html = '';
                var checkout_html = '';

                $.each(response.data.all_ranges, function(index, range) {
                    var checked = range.is_assigned ? 'checked' : '';
                    var checkbox_html = '<div class="time-range-checkbox">' +
                        '<label>' +
                        '<input type="checkbox" name="student_time_ranges[]" value="' + range.id + '" ' + checked + '> ' +
                        '<strong>' + range.range_name + '</strong>' +
                        '</label>' +
                        '<div class="time-range-info">' +
                        '<i class="fa fa-clock-o"></i> ' + range.time_start + ' - ' + range.time_end +
                        ' | Grace: ' + range.grace_period_minutes + ' min' +
                        '</div>' +
                        '</div>';

                    if (range.range_type === 'checkin') {
                        checkin_html += checkbox_html;
                    } else {
                        checkout_html += checkbox_html;
                    }
                });

                $('#student_checkin_ranges').html(checkin_html || '<p class="text-muted">No check-in ranges available</p>');
                $('#student_checkout_ranges').html(checkout_html || '<p class="text-muted">No check-out ranges available</p>');

                $('#student_no_selection').hide();
                $('#student_assignment_panel').show();
            }
        }
    });
}

// Save student assignments
function saveStudentAssignments() {
    var time_range_ids = [];
    $('input[name="student_time_ranges[]"]:checked').each(function() {
        time_range_ids.push($(this).val());
    });

    $.ajax({
        url: base_url + 'admin/time_range_assignment/saveStudentAssignments',
        type: 'POST',
        data: {
            student_session_id: current_student_session_id,
            time_range_ids: time_range_ids
        },
        dataType: 'json',
        beforeSend: function() {
            $('#save_student_assignments').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
        },
        success: function(response) {
            if (response.status === 200) {
                toastr.success(response.message);
            } else {
                toastr.error(response.message);
            }
        },
        complete: function() {
            $('#save_student_assignments').prop('disabled', false).html('<i class="fa fa-save"></i> Save Student Assignments');
        }
    });
}
</script>


