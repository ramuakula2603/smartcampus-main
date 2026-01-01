<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-money"></i> <?php echo $this->lang->line('fees_collection'); ?></h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i>
                            <?php echo $this->lang->line('search_by_admission_no'); ?>
                        </h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <form role="form" action="<?php echo current_url(); ?>" method="post" class="form-inline">
                                    <?php echo $this->customlib->getCSRF(); ?>
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <label><?php echo $this->lang->line('admi_no'); ?>
                                            </label><small class="req"> *</small>
                                            <input autofocus="" id="admission_no" name="admission_no" placeholder="Enter Admission Number" type="text" class="form-control"  value="<?php echo set_value('admission_no'); ?>"/>
                                            <span class="text-danger"><?php echo form_error('admission_no'); ?></span>
                                        </div>
                                    </div>
                                    <div class="form-group align-text-top">
                                        <div class="col-sm-12">
                                            <button type="submit" name="search" value="search_filter" class="btn btn-primary btn-sm checkbox-toggle mmius15 smallbtn28"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php
if (isset($studentDetails) && !empty($studentDetails)) {
    ?>
                        <div class="ptt10">
                            <div class="box-header ptbnull">
                                <h3 class="box-title titlefix"><i class="fa fa-user"></i> <?php echo $this->lang->line('student_details'); ?></h3>
                                <div class="box-tools pull-right"></div>
                            </div>
                            <div class="box-body table-responsive">
                                <div class="download_label"><?php echo $this->lang->line('student_details'); ?></div>
                                <table class="table table-striped table-bordered table-hover example">
                                    <thead>
                                        <tr>
                                            <th><?php echo $this->lang->line('admission_no'); ?></th>
                                            <th><?php echo $this->lang->line('student_name'); ?></th>
                                            <th><?php echo $this->lang->line('class'); ?></th>
                                            <th><?php echo $this->lang->line('father_name'); ?></th>
                                            <th><?php echo $this->lang->line('date_of_birth'); ?></th>
                                            <th><?php echo $this->lang->line('gender'); ?></th>
                                            <th><?php echo $this->lang->line('category'); ?></th>
                                            <th><?php echo $this->lang->line('mobile_no'); ?></th>
                                            <th class="text text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (!empty($student_session)) {
                                            foreach ($student_session as $student) {
                                        ?>
                                            <tr>
                                                <td>
                                                    <?php echo isset($admission_no) ? $admission_no : ''; ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    // Get student details from studentDetails array
                                                    if(is_array($studentDetails)) {
                                                        echo $this->customlib->getFullName(
                                                            $studentDetails['firstname'],
                                                            isset($studentDetails['middlename']) ? $studentDetails['middlename'] : '',
                                                            isset($studentDetails['lastname']) ? $studentDetails['lastname'] : '',
                                                            $sch_setting->middlename,
                                                            $sch_setting->lastname
                                                        );
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php echo isset($student['class']) ? $student['class'] . " (" . $student['section'] . ")" : ''; ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if(is_array($studentDetails) && isset($studentDetails['father_name'])) {
                                                        echo $studentDetails['father_name'];
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if(is_array($studentDetails) && isset($studentDetails['dob'])) {
                                                        echo date($this->customlib->getSchoolDateFormat(), strtotime($studentDetails['dob']));
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if(is_array($studentDetails) && isset($studentDetails['gender'])) {
                                                        echo $studentDetails['gender'];
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if(is_array($studentDetails) && isset($studentDetails['category'])) {
                                                        echo $studentDetails['category'];
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if(is_array($studentDetails) && isset($studentDetails['mobileno'])) {
                                                        echo $studentDetails['mobileno'];
                                                    }
                                                    ?>
                                                </td>
                                                <td class="text text-right">
                                                    <?php if(is_array($studentDetails) && isset($studentDetails['id'])): ?>
                                                    <a href="<?php echo base_url() ?>student/view/<?php echo $studentDetails['id']; ?>" class="btn btn-primary btn-xs" data-toggle="tooltip">
                                                        <i class="fa fa-list-alt"></i> <?php echo $this->lang->line('view'); ?>
                                                    </a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php
                                            }
                                        } else if (is_array($studentDetails) && !empty($studentDetails)) {
                                        ?>
                                            <tr>
                                                <td>
                                                    <?php echo isset($admission_no) ? $admission_no : ''; ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    echo $this->customlib->getFullName(
                                                        $studentDetails['firstname'],
                                                        isset($studentDetails['middlename']) ? $studentDetails['middlename'] : '',
                                                        isset($studentDetails['lastname']) ? $studentDetails['lastname'] : '',
                                                        $sch_setting->middlename,
                                                        $sch_setting->lastname
                                                    );
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    // Try to get class info from student details
                                                    if(isset($studentDetails['class']) && isset($studentDetails['section'])) {
                                                        echo $studentDetails['class'] . " (" . $studentDetails['section'] . ")";
                                                    } else {
                                                        echo "-";
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php echo isset($studentDetails['father_name']) ? $studentDetails['father_name'] : ''; ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if(isset($studentDetails['dob'])) {
                                                        echo date($this->customlib->getSchoolDateFormat(), strtotime($studentDetails['dob']));
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php echo isset($studentDetails['gender']) ? $studentDetails['gender'] : ''; ?>
                                                </td>
                                                <td>
                                                    <?php echo isset($studentDetails['category']) ? $studentDetails['category'] : ''; ?>
                                                </td>
                                                <td>
                                                    <?php echo isset($studentDetails['mobileno']) ? $studentDetails['mobileno'] : ''; ?>
                                                </td>
                                                <td class="text text-right">
                                                    <a href="<?php echo base_url() ?>student/view/<?php echo $studentDetails['id']; ?>" class="btn btn-primary btn-xs" data-toggle="tooltip">
                                                        <i class="fa fa-list-alt"></i> <?php echo $this->lang->line('view'); ?>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php
                                        } else {
                                        ?>
                                            <tr>
                                                <td colspan="9" class="text-center"><?php echo $this->lang->line('no_record_found'); ?></td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php
} else if (isset($studentDetails) && empty($studentDetails) && $this->input->post('admission_no')) {
?>
                        <div class="alert alert-info">
                            <?php echo $this->lang->line('no_student_found_with_this_admission_number'); ?>
                        </div>

                        <?php if(isset($debug_info)): ?>
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Debug Information</h3>
                            </div>
                            <div class="box-body">
                                <pre><?php print_r($debug_info); ?></pre>
                            </div>
                        </div>
                        <?php endif; ?>
<?php
}
?>
                </div>
            </div>
        </div>
    </section>
</div>