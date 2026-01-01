<style>
@media print {
    .no-print, .no-print * {
        display: none !important;
    }
}
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-graduation-cap"></i> Student Check-in Report
            <small><?php echo date('F d, Y', strtotime($date)); ?></small>
        </h1>
    </section>

    <section class="content">
        <!-- Statistics -->
        <div class="row">
            <div class="col-md-3">
                <div class="info-box bg-aqua">
                    <span class="info-box-icon"><i class="fa fa-graduation-cap"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Students</span>
                        <span class="info-box-number"><?php echo $statistics['students']['total']; ?></span>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="info-box bg-green">
                    <span class="info-box-icon"><i class="fa fa-check"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Checked In</span>
                        <span class="info-box-number"><?php echo $statistics['students']['checked_in']; ?></span>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="info-box bg-red">
                    <span class="info-box-icon"><i class="fa fa-times"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Not Checked In</span>
                        <span class="info-box-number"><?php echo $statistics['students']['not_checked_in']; ?></span>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="info-box bg-yellow">
                    <span class="info-box-icon"><i class="fa fa-percent"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Attendance Rate</span>
                        <span class="info-box-number"><?php echo $statistics['students']['percentage']; ?>%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Report -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border no-print">
                        <h3 class="box-title">Student Check-in Details</h3>
                        <div class="box-tools pull-right">
                            <a href="<?php echo base_url('biometric_checkin_report'); ?>" class="btn btn-sm btn-default">
                                <i class="fa fa-arrow-left"></i> Back
                            </a>
                            <button onclick="window.print()" class="btn btn-sm btn-primary">
                                <i class="fa fa-print"></i> Print
                            </button>
                            <a href="<?php echo base_url('biometric_checkin_report/export_student_excel?date=' . $date . '&class_id=' . $class_id . '&section_id=' . $section_id); ?>" class="btn btn-sm btn-success">
                                <i class="fa fa-file-excel-o"></i> Export Excel
                            </a>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover" id="studentCheckinTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Admission No</th>
                                        <th>Name</th>
                                        <th>Class</th>
                                        <th>Section</th>
                                        <th>Status</th>
                                        <th>First Check-in</th>
                                        <th>Last Check-in</th>
                                        <th>Check-in Count</th>
                                        <th class="no-print">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $count = 1;
                                    foreach ($student_list as $student) { 
                                        $row_class = $student['has_checked_in'] ? '' : 'danger';
                                    ?>
                                    <tr class="<?php echo $row_class; ?>">
                                        <td><?php echo $count++; ?></td>
                                        <td><?php echo $student['admission_no']; ?></td>
                                        <td><?php echo $student['name']; ?></td>
                                        <td><?php echo $student['class']; ?></td>
                                        <td><?php echo $student['section']; ?></td>
                                        <td>
                                            <?php if ($student['has_checked_in']) { ?>
                                                <span class="label label-success"><?php echo $student['status']; ?></span>
                                            <?php } else { ?>
                                                <span class="label label-danger">Not Checked In</span>
                                            <?php } ?>
                                        </td>
                                        <td><?php echo $student['first_checkin_time'] ?: '-'; ?></td>
                                        <td><?php echo $student['last_checkin_time'] ?: '-'; ?></td>
                                        <td>
                                            <?php if ($student['checkin_count'] > 0) { ?>
                                                <span class="badge bg-blue"><?php echo $student['checkin_count']; ?></span>
                                            <?php } else { ?>
                                                <span class="badge bg-gray">0</span>
                                            <?php } ?>
                                        </td>
                                        <td class="no-print">
                                            <?php if ($student['has_checked_in']) { ?>
                                                <button class="btn btn-xs btn-info view-details" 
                                                        data-id="<?php echo $student['student_session_id']; ?>" 
                                                        data-type="student" 
                                                        data-name="<?php echo $student['name']; ?>">
                                                    <i class="fa fa-eye"></i> View Details
                                                </button>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-clock-o"></i> Check-in Details - <span id="personName"></span></h4>
            </div>
            <div class="modal-body">
                <div id="detailsContent">
                    <div class="text-center">
                        <i class="fa fa-spinner fa-spin fa-3x"></i>
                        <p>Loading...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#studentCheckinTable').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "pageLength": 25,
        "order": [[5, "asc"], [3, "asc"], [4, "asc"], [2, "asc"]] // Sort by status, class, section, name
    });

    // View details
    $('.view-details').click(function() {
        var id = $(this).data('id');
        var type = $(this).data('type');
        var name = $(this).data('name');
        var date = '<?php echo $date; ?>';

        $('#personName').text(name);
        $('#detailsModal').modal('show');

        $.ajax({
            url: '<?php echo base_url('biometric_checkin_report/getCheckinDetails'); ?>',
            type: 'POST',
            data: {
                id: id,
                type: type,
                date: date
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 200 && response.data.total_count > 0) {
                    var data = response.data;
                    var html = '';

                    // Summary section
                    html += '<div class="row">';
                    html += '<div class="col-md-12">';
                    html += '<div class="alert alert-info">';
                    html += '<strong><i class="fa fa-info-circle"></i> Summary:</strong> ';
                    html += 'Total Punches: <strong>' + data.total_count + '</strong>';

                    if (data.summary.checkin) {
                        html += ' | Check-ins: <strong>' + data.summary.checkin + '</strong>';
                    }
                    if (data.summary.checkout) {
                        html += ' | Check-outs: <strong>' + data.summary.checkout + '</strong>';
                    }

                    html += '</div>';
                    html += '</div>';
                    html += '</div>';

                    // Grouped by time range
                    html += '<h4><i class="fa fa-clock-o"></i> Check-ins by Time Range</h4>';

                    $.each(data.grouped, function(rangeName, rangeData) {
                        var badgeClass = rangeData.range_type === 'checkin' ? 'bg-green' : 'bg-blue';
                        var iconClass = rangeData.range_type === 'checkin' ? 'fa-sign-in' : 'fa-sign-out';

                        html += '<div class="box box-solid collapsed-box" style="margin-bottom: 10px;">';
                        html += '<div class="box-header with-border" style="cursor: pointer;" data-widget="collapse">';
                        html += '<h4 class="box-title">';
                        html += '<i class="fa ' + iconClass + '"></i> ';
                        html += '<strong>' + rangeData.range_name + '</strong> ';
                        html += '<span class="badge ' + badgeClass + '">' + rangeData.range_type_label + '</span> ';
                        html += '<small class="text-muted">' + rangeData.time_range_display + '</small>';
                        html += '</h4>';
                        html += '<div class="box-tools pull-right">';
                        html += '<span class="badge bg-yellow">' + rangeData.count + ' time(s)</span> ';
                        html += '<button type="button" class="btn btn-box-tool" data-widget="collapse">';
                        html += '<i class="fa fa-plus"></i></button>';
                        html += '</div>';
                        html += '</div>';
                        html += '<div class="box-body" style="display: none;">';
                        html += '<table class="table table-condensed table-bordered">';
                        html += '<thead><tr><th width="50">#</th><th>Time</th><th>Status</th><th>Remark</th></tr></thead>';
                        html += '<tbody>';

                        $.each(rangeData.records, function(index, record) {
                            html += '<tr>';
                            html += '<td>' + (index + 1) + '</td>';
                            html += '<td><i class="fa fa-clock-o"></i> ' + record.time_display + '</td>';
                            html += '<td><span class="label label-success">' + record.attendance_type + '</span></td>';
                            html += '<td>' + (record.remark || '-') + '</td>';
                            html += '</tr>';
                        });

                        html += '</tbody></table>';
                        html += '</div>';
                        html += '</div>';
                    });

                    // All records chronologically
                    html += '<h4 style="margin-top: 20px;"><i class="fa fa-list"></i> All Punches (Chronological)</h4>';
                    html += '<table class="table table-striped table-bordered">';
                    html += '<thead><tr><th width="50">#</th><th>Time</th><th>Type</th><th>Range</th><th>Status</th></tr></thead>';
                    html += '<tbody>';

                    $.each(data.all_records, function(index, record) {
                        var typeClass = record.range_type === 'checkin' ? 'label-primary' : 'label-info';
                        html += '<tr>';
                        html += '<td>' + (index + 1) + '</td>';
                        html += '<td><strong>' + record.time_display + '</strong></td>';
                        html += '<td><span class="label ' + typeClass + '">' + record.range_type_label + '</span></td>';
                        html += '<td>' + record.range_name + '</td>';
                        html += '<td>' + record.attendance_type + '</td>';
                        html += '</tr>';
                    });

                    html += '</tbody></table>';

                    $('#detailsContent').html(html);

                    // Initialize collapse functionality
                    $('[data-widget="collapse"]').on('click', function() {
                        var box = $(this).closest('.box');
                        var icon = $(this).find('i');

                        if (box.hasClass('collapsed-box')) {
                            box.removeClass('collapsed-box');
                            icon.removeClass('fa-plus').addClass('fa-minus');
                        } else {
                            box.addClass('collapsed-box');
                            icon.removeClass('fa-minus').addClass('fa-plus');
                        }

                        box.find('.box-body').slideToggle();
                    });
                } else {
                    $('#detailsContent').html('<div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> No check-in details found.</div>');
                }
            },
            error: function() {
                $('#detailsContent').html('<div class="alert alert-danger"><i class="fa fa-times-circle"></i> Error loading details. Please try again.</div>');
            }
        });
    });
});
</script>

