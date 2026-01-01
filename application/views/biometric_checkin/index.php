<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-clock-o"></i> <?php echo $this->lang->line('biometric'); ?> Check-in Report
        </h1>
    </section>

    <section class="content">
        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-lg-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="fa fa-users"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Staff</span>
                        <span class="info-box-number"><?php echo $statistics['staff']['total']; ?></span>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa fa-check"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Staff Checked In</span>
                        <span class="info-box-number"><?php echo $statistics['staff']['checked_in']; ?> <small>(<?php echo $statistics['staff']['percentage']; ?>%)</small></span>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="fa fa-graduation-cap"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Students</span>
                        <span class="info-box-number"><?php echo $statistics['students']['total']; ?></span>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa fa-check"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Students Checked In</span>
                        <span class="info-box-number"><?php echo $statistics['students']['checked_in']; ?> <small>(<?php echo $statistics['students']['percentage']; ?>%)</small></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> Select Report Type</h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="panel panel-info">
                                    <div class="panel-heading">
                                        <h3 class="panel-title"><i class="fa fa-users"></i> Staff Check-in Report</h3>
                                    </div>
                                    <div class="panel-body">
                                        <form action="<?php echo base_url('biometric_checkin_report/staff_checkin'); ?>" method="post">
                                            <div class="form-group">
                                                <label>Select Date</label>
                                                <input type="date" name="date" class="form-control" value="<?php echo $date; ?>" required>
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-block">
                                                <i class="fa fa-search"></i> View Staff Check-in Report
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="panel panel-success">
                                    <div class="panel-heading">
                                        <h3 class="panel-title"><i class="fa fa-graduation-cap"></i> Student Check-in Report</h3>
                                    </div>
                                    <div class="panel-body">
                                        <form action="<?php echo base_url('biometric_checkin_report/student_checkin'); ?>" method="post" id="studentForm">
                                            <div class="form-group">
                                                <label>Select Date</label>
                                                <input type="date" name="date" class="form-control" value="<?php echo $date; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Class (Optional)</label>
                                                <select name="class_id" id="class_id" class="form-control">
                                                    <option value="">All Classes</option>
                                                    <?php foreach ($classlist as $class) { ?>
                                                        <option value="<?php echo $class['id']; ?>"><?php echo $class['class']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Section (Optional)</label>
                                                <select name="section_id" id="section_id" class="form-control">
                                                    <option value="">All Sections</option>
                                                </select>
                                            </div>
                                            <button type="submit" class="btn btn-success btn-block">
                                                <i class="fa fa-search"></i> View Student Check-in Report
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Stats -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <h4><i class="fa fa-info-circle"></i> Today's Summary (<?php echo date('F d, Y', strtotime($date)); ?>)</h4>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Staff:</strong> <?php echo $statistics['staff']['checked_in']; ?> out of <?php echo $statistics['staff']['total']; ?> have checked in (<?php echo $statistics['staff']['percentage']; ?>%)</p>
                                            <p><strong>Not Checked In:</strong> <?php echo $statistics['staff']['not_checked_in']; ?> staff members</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Students:</strong> <?php echo $statistics['students']['checked_in']; ?> out of <?php echo $statistics['students']['total']; ?> have checked in (<?php echo $statistics['students']['percentage']; ?>%)</p>
                                            <p><strong>Not Checked In:</strong> <?php echo $statistics['students']['not_checked_in']; ?> students</p>
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

<script>
$(document).ready(function() {
    // Load sections when class is selected
    $('#class_id').change(function() {
        var class_id = $(this).val();
        if (class_id) {
            $.ajax({
                url: '<?php echo base_url('biometric_checkin_report/getSectionByClass'); ?>',
                type: 'POST',
                data: {class_id: class_id},
                dataType: 'json',
                success: function(data) {
                    $('#section_id').html('<option value="">All Sections</option>');
                    $.each(data, function(key, value) {
                        $('#section_id').append('<option value="' + value.section_id + '">' + value.section + '</option>');
                    });
                }
            });
        } else {
            $('#section_id').html('<option value="">All Sections</option>');
        }
    });
});
</script>

