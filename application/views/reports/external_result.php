<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>

<!-- Add DataTables CSS and JS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap.min.css">

<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
<style>
    .status-badge {
        padding: 4px 8px;
        border-radius: 3px;
        font-weight: 600;
        font-size: 11px;
        text-transform: uppercase;
    }
    
    .status-pass {
        background-color: #00a65a;
        color: white;
    }
    
    .status-fail {
        background-color: #dd4b39;
        color: white;
    }
    
    .status-absent {
        background-color: #f39c12;
        color: white;
    }
    
    .mark-pass {
        color: #00a65a;
        font-weight: 600;
    }
    
    .mark-fail {
        color: #dd4b39;
        font-weight: 600;
    }
    
    .mark-absent {
        color: #f39c12;
        font-weight: 600;
    }
    
    .table > thead > tr > th {
        background-color: #f4f4f4;
        font-weight: 600;
        text-align: center;
        vertical-align: middle;
    }
    
    .table > tbody > tr > td {
        vertical-align: middle;
        text-align: center;
    }
    
    .table > tbody > tr > td:nth-child(1),
    .table > tbody > tr > td:nth-child(2) {
        text-align: left;
    }
</style>
<div class="content-wrapper" style="min-height: 1126px;">
    <section class="content-header">
        <h1><i class="fa fa-line-chart"></i> <?php echo $this->lang->line('external_result_report'); ?> <small></small></h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <?php $this->load->view('reports/_result'); ?>
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <div class="box removeboxmius">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('select_criteria'); ?></h3>
                    </div>
                    <form role="form" action="<?php echo site_url('report/external_result') ?>" method="post" class="">
                        <div class="box-body row">
                            <?php echo $this->customlib->getCSRF(); ?>
                            
                            <div class="col-sm-3 col-lg-3 col-md-3">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('session'); ?><small class="req"> *</small></label>
                                    <select class="form-control" name="session_id" id="session_id" required>
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($sessionlist as $session) { ?>
                                            <option value="<?php echo $session['id'] ?>" <?php
                                                if ((isset($selected_session)) && ($selected_session == $session['id'])) {
                                                    echo "selected";
                                                }
                                            ?>><?php echo $session['session'] ?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('session_id'); ?></span>
                                </div>
                            </div>

                            <div class="col-sm-3 col-lg-3 col-md-3">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('external_result_type'); ?><small class="req"> *</small></label>
                                    <select class="form-control" name="exam_type_id" id="exam_type_id" required>
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php if (!empty($examtypelist)) {
                                            foreach ($examtypelist as $examtype) { ?>
                                                <option value="<?php echo $examtype['id'] ?>" <?php
                                                    if ((isset($selected_exam_type)) && ($selected_exam_type == $examtype['id'])) {
                                                        echo "selected";
                                                    }
                                                ?>><?php echo $examtype['examtype'] ?></option>
                                            <?php }
                                        } ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('exam_type_id'); ?></span>
                                </div>
                            </div>

                            <div class="col-sm-2 col-lg-2 col-md-2">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('class'); ?></label>
                                    <select class="form-control" name="class_id" id="class_id">
                                        <option value=""><?php echo $this->lang->line('all'); ?></option>
                                        <?php foreach ($classlist as $class) { ?>
                                            <option value="<?php echo $class['id'] ?>" <?php
                                                if ((isset($selected_class)) && ($selected_class == $class['id'])) {
                                                    echo "selected";
                                                }
                                            ?>><?php echo $class['class'] ?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('class_id'); ?></span>
                                </div>
                            </div>

                            <div class="col-sm-2 col-lg-2 col-md-2">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('section'); ?></label>
                                    <select class="form-control" name="section_id" id="section_id">
                                        <option value=""><?php echo $this->lang->line('all'); ?></option>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('section_id'); ?></span>
                                </div>
                            </div>

                            <div class="col-sm-2 col-lg-2 col-md-2">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('status'); ?></label>
                                    <select class="form-control" name="status" id="status">
                                        <?php foreach ($statuslist as $key => $status) { ?>
                                            <option value="<?php echo $key ?>" <?php
                                                if ((isset($selected_status)) && ($selected_status == $key)) {
                                                    echo "selected";
                                                }
                                            ?>><?php echo $status ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-sm-12 col-md-12">
                                <div class="form-group">
                                    <button type="submit" name="search" value="search" class="btn btn-primary btn-sm checkbox-toggle pull-right"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <?php if (isset($results) && !empty($results)) { ?>
                    <div class="box-body table-responsive">
                        <h4><?php echo $this->lang->line('external_result_report'); ?></h4>
                        
                        <?php
                        // Collect all unique subjects across all students
                        $all_subjects = array();
                        foreach ($results as $student) {
                            foreach ($student['sessions'] as $session) {
                                foreach ($session['exams'] as $exam) {
                                    foreach ($exam['subjects'] as $subject) {
                                        $subject_key = $subject['subject_code'];
                                        if (!isset($all_subjects[$subject_key])) {
                                            $all_subjects[$subject_key] = $subject['subject_name'];
                                        }
                                    }
                                }
                            }
                        }
                        ?>
                        
                        <table class="table table-striped table-bordered table-hover example" id="results_table">
                            <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('admission_no'); ?></th>
                                    <th><?php echo $this->lang->line('student_name'); ?></th>
                                    <th><?php echo $this->lang->line('class'); ?></th>
                                    <th><?php echo $this->lang->line('section'); ?></th>
                                    <?php foreach ($all_subjects as $subject_code => $subject_name) { ?>
                                        <th><?php echo $subject_name; ?></th>
                                    <?php } ?>
                                    <th><?php echo $this->lang->line('total'); ?></th>
                                    <th><?php echo $this->lang->line('percentage'); ?></th>
                                    <th><?php echo $this->lang->line('status'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($results as $student) { 
                                    foreach ($student['sessions'] as $session) {
                                        foreach ($session['exams'] as $exam) { ?>
                                            <tr>
                                                <td><?php echo $student['admission_no']; ?></td>
                                                <td><?php echo $student['student_name']; ?></td>
                                                <td><?php echo $student['class']; ?></td>
                                                <td><?php echo $student['section']; ?></td>
                                                
                                                <?php
                                                // Create a map of subject marks for this exam
                                                $subject_marks = array();
                                                foreach ($exam['subjects'] as $subject) {
                                                    $subject_marks[$subject['subject_code']] = $subject;
                                                }
                                                
                                                // Display marks for each subject column
                                                foreach ($all_subjects as $subject_code => $subject_name) {
                                                    if (isset($subject_marks[$subject_code])) {
                                                        $subject = $subject_marks[$subject_code];
                                                        $marks_display = $subject['actualmarks'] . '/' . $subject['maxmarks'];
                                                        
                                                        if ($subject['is_absent']) {
                                                            echo '<td class="mark-absent">AB/' . $subject['maxmarks'] . '</td>';
                                                        } elseif ($subject['pass']) {
                                                            echo '<td class="mark-pass">' . $marks_display . '</td>';
                                                        } else {
                                                            echo '<td class="mark-fail">' . $marks_display . '</td>';
                                                        }
                                                    } else {
                                                        echo '<td>-</td>';
                                                    }
                                                }
                                                ?>
                                                
                                                <td><strong><?php echo $exam['total_marks'] . '/' . $exam['total_max_marks']; ?></strong></td>
                                                <td><strong><?php echo is_numeric($exam['percentage']) ? $exam['percentage'] . '%' : $exam['percentage']; ?></strong></td>
                                                <td>
                                                    <?php
                                                    if ($exam['pass_status'] === 'absent') {
                                                        echo '<span class="status-badge status-absent">ABSENT</span>';
                                                    } elseif ($exam['pass_status'] === true) {
                                                        echo '<span class="status-badge status-pass">PASS</span>';
                                                    } else {
                                                        echo '<span class="status-badge status-fail">FAIL</span>';
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php }
                                    }
                                } ?>
                            </tbody>
                        </table>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>
</div>

<script type="text/javascript">
$(document).ready(function () {
    // Initialize DataTable
    $('.example').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        responsive: true,
        autoWidth: false,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]]
    });

    // Store pre-selected section from PHP
    var preSelectedSection = '<?php echo isset($selected_section) ? $selected_section : ''; ?>';

    // Initialize section dropdown on page load if class is pre-selected
    var preSelectedClass = $('#class_id').val();
    if (preSelectedClass) {
        $('#class_id').trigger('change');
    }

    // Handle session dropdown changes to load exam types
    $(document).on('change', '#session_id', function (e) {
        var session_id = $(this).val();
        var base_url = '<?php echo base_url() ?>';
        var examTypeDropdown = $('#exam_type_id');

        if (session_id) {
            $.ajax({
                type: "POST",
                url: base_url + "report/getExternalExamTypesBySession",
                data: {
                    'session_id': session_id,
                    '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
                },
                dataType: "json",
                success: function(data) {
                    examTypeDropdown.empty();
                    examTypeDropdown.append('<option value=""><?php echo $this->lang->line('select'); ?></option>');
                    
                    if (data && Array.isArray(data) && data.length > 0) {
                        $.each(data, function(i, examtype) {
                            examTypeDropdown.append(
                                $('<option></option>').val(examtype.id).text(examtype.examtype)
                            );
                        });
                    }
                    
                    // Re-select previously selected exam type if exists
                    var preSelectedExamType = '<?php echo isset($selected_exam_type) ? $selected_exam_type : ''; ?>';
                    if (preSelectedExamType) {
                        examTypeDropdown.val(preSelectedExamType);
                    }
                },
                error: function() {
                    examTypeDropdown.empty();
                    examTypeDropdown.append('<option value="">Error loading exam types</option>');
                }
            });
        } else {
            examTypeDropdown.empty();
            examTypeDropdown.append('<option value=""><?php echo $this->lang->line('select'); ?></option>');
        }
    });

    // Handle class dropdown changes for section population
    $(document).on('change', '#class_id', function (e) {
        var class_id = $(this).val();
        var base_url = '<?php echo base_url() ?>';
        var sectionDropdown = $('#section_id');

        if (class_id) {
            $.ajax({
                type: "GET",
                url: base_url + "sections/getByClass",
                data: {'class_id': class_id},
                dataType: "json",
                success: function(data) {
                    sectionDropdown.empty();
                    sectionDropdown.append('<option value=""><?php echo $this->lang->line('all'); ?></option>');
                    
                    if (data && Array.isArray(data)) {
                        $.each(data, function(i, obj) {
                            sectionDropdown.append(
                                $('<option></option>').val(obj.section_id).text(obj.section)
                            );
                        });
                    }
                    
                    // Re-select previously selected section
                    if (preSelectedSection) {
                        sectionDropdown.val(preSelectedSection);
                    }
                },
                error: function() {
                    sectionDropdown.empty();
                    sectionDropdown.append('<option value="">Error loading sections</option>');
                }
            });
        } else {
            sectionDropdown.empty();
            sectionDropdown.append('<option value=""><?php echo $this->lang->line('all'); ?></option>');
        }
    });
});
</script>
