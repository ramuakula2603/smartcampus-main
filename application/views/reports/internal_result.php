<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<style>
/* SumoSelect styling */
.SumoSelect {
    width: 100%;
}

.SumoSelect > .CaptionCont {
    border: 1px solid #d2d6de;
    border-radius: 3px;
    padding: 6px 12px;
    height: 34px;
    line-height: 20px;
    background-color: #fff;
}

.SumoSelect > .CaptionCont > span {
    font-size: 14px;
    color: #555;
    display: inline-block;
}

.SumoSelect > .CaptionCont > span.placeholder {
    color: #999;
}

.SumoSelect.open > .CaptionCont,
.SumoSelect:focus > .CaptionCont,
.SumoSelect:hover > .CaptionCont {
    border-color: #3c8dbc;
}

.SumoSelect .optWrapper {
    border: 1px solid #d2d6de;
    border-radius: 3px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    max-height: 300px;
    overflow-y: auto;
}

.SumoSelect .optWrapper ul.options {
    list-style: none;
    padding: 0;
    margin: 0;
}

.SumoSelect .optWrapper ul.options li {
    padding: 8px 12px;
    border-bottom: 1px solid #f4f4f4;
}

.SumoSelect .optWrapper ul.options li:hover {
    background-color: #f9f9f9;
}

.SumoSelect .optWrapper ul.options li.selected {
    background-color: #3c8dbc;
    color: #fff;
}

.SumoSelect .search-txt {
    border: 1px solid #d2d6de;
    border-radius: 3px;
    padding: 6px 12px;
    margin: 5px;
    width: calc(100% - 10px);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .SumoSelect > .CaptionCont {
        font-size: 13px;
        padding: 5px 10px;
        height: auto;
    }
}

@media (min-width: 992px) and (max-width: 1199px) {
    .SumoSelect > .CaptionCont {
        font-size: 12px;
    }
}

/* Select All styling */
.SumoSelect .select-all {
    font-weight: 600;
    padding: 10px 12px;
    background-color: #f5f5f5;
    border-bottom: 2px solid #ddd;
    cursor: pointer;
}

.SumoSelect .select-all:hover {
    background-color: #e9e9e9;
}

.SumoSelect .optWrapper .options li.opt {
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.SumoSelect .optWrapper .options li.opt:hover {
    background-color: #f0f8ff;
}

.SumoSelect .optWrapper .options li.opt.select-all {
    background-color: #f8f8f8;
    font-weight: 600;
    border-bottom: 2px solid #ddd;
}

.SumoSelect .optWrapper .options li.opt.select-all:hover {
    background-color: #e8e8e8;
}
</style>
<div class="content-wrapper" style="min-height: 1126px;">
    <section class="content-header">
        <h1><i class="fa fa-line-chart"></i> Result <small></small></h1>
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
                    <form role="form" action="<?php echo site_url('report/internal_result') ?>" method="post" class="">
                        <div class="box-body row">
                            <?php echo $this->customlib->getCSRF(); ?>
                            
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('session'); ?></label>
                                    <select id="session_id" name="session_id" class="form-control">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php
                                        foreach ($sessionlist as $session) {
                                            $is_selected = (isset($selected_session) && $selected_session == $session['id']) ? 'selected="selected"' : '';
                                            ?>
                                            <option value="<?php echo $session['id'] ?>" <?php echo $is_selected; ?>><?php echo $session['session'] ?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('session_id'); ?></span>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('internal_result_type'); ?></label>
                                    <select id="exam_type_id" name="exam_type_id" class="form-control">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php
                                        foreach ($examtypelist as $examtype) {
                                            $is_selected = (isset($selected_exam_type) && $selected_exam_type == $examtype['id']) ? 'selected="selected"' : '';
                                            ?>
                                            <option value="<?php echo $examtype['id'] ?>" <?php echo $is_selected; ?>><?php echo $examtype['examtype'] ?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('exam_type_id'); ?></span>
                                </div>
                            </div>

                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('class'); ?></label>
                                    <select id="class_id" name="class_id" class="form-control">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php
                                        foreach ($classlist as $class) {
                                            $is_selected = (isset($selected_class) && $selected_class == $class['id']) ? 'selected="selected"' : '';
                                            ?>
                                            <option value="<?php echo $class['id'] ?>" <?php echo $is_selected; ?>><?php echo $class['class'] ?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('class_id'); ?></span>
                                </div>
                            </div>

                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('section'); ?></label>
                                    <select id="section_id" name="section_id" class="form-control">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('section_id'); ?></span>
                                </div>
                            </div>

                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('status'); ?></label>
                                    <select id="status" name="status" class="form-control">
                                        <?php foreach ($statuslist as $key => $status_label) { ?>
                                            <option value="<?php echo $key ?>" <?php echo (isset($selected_status) && $selected_status == $key) ? 'selected' : ''; ?>>
                                                <?php echo $status_label; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('status'); ?></span>
                                </div>
                            </div>
                            
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <button type="submit" name="search" value="search" class="btn btn-primary btn-sm pull-right checkbox-toggle"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <?php if (isset($results) && !empty($results)) { ?>
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><i class="fa fa-users"></i> <?php echo $this->lang->line('student_list'); ?></h3>
                    </div>

                    <div class="box-body table-responsive overflow-visible">
                        <div class="download_label"><?php echo $this->lang->line('internal_results'); ?> <?php echo $this->lang->line('report'); ?></div>

                        <style>
                            .result-badge-pass {
                                background-color: #00a65a !important;
                                color: white !important;
                                padding: 4px 8px;
                                border-radius: 3px;
                                font-weight: bold;
                                font-size: 11px;
                                display: inline-block;
                            }
                            .result-badge-fail {
                                background-color: #dd4b39 !important;
                                color: white !important;
                                padding: 4px 8px;
                                border-radius: 3px;
                                font-weight: bold;
                                font-size: 11px;
                                display: inline-block;
                            }
                            .result-badge-absent {
                                background-color: #f39c12 !important;
                                color: white !important;
                                padding: 4px 8px;
                                border-radius: 3px;
                                font-weight: bold;
                                font-size: 11px;
                                display: inline-block;
                            }
                            .marks-cell {
                                font-weight: 600;
                                color: #333;
                            }
                            .marks-fail {
                                color: #dd4b39;
                            }
                            .marks-pass {
                                color: #00a65a;
                            }
                            @media print {
                                .no_print { display: none; }
                            }
                        </style>

                        <div id="printable_results">
                            <table class="table table-striped table-bordered table-hover example">
                                <thead>
                                    <tr>
                                        <?php if (!$adm_auto_insert) { ?>
                                            <th><?php echo $this->lang->line('admission_no'); ?></th>
                                        <?php } ?>
                                        <th><?php echo $this->lang->line('student_name'); ?></th>
                                        <th><?php echo $this->lang->line('class'); ?></th>
                                        <th><?php echo $this->lang->line('section'); ?></th>
                                        <?php
                                        // Get unique subjects from first student's first session's first exam
                                        $subjects_list = array();
                                        if (!empty($results)) {
                                            $first_student = reset($results);
                                            if (!empty($first_student['sessions'])) {
                                                $first_session = reset($first_student['sessions']);
                                                if (!empty($first_session['exams'])) {
                                                    $first_exam = reset($first_session['exams']);
                                                    if (!empty($first_exam['subjects'])) {
                                                        foreach ($first_exam['subjects'] as $subject) {
                                                            $subjects_list[] = $subject;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        
                                        // Display subject columns
                                        foreach ($subjects_list as $subject) {
                                            ?>
                                            <th class="text-center">
                                                <?php echo $subject['subject_name']; ?><br>
                                                <small>(<?php echo $subject['subject_code']; ?>)</small>
                                            </th>
                                        <?php } ?>
                                        <th class="text-center"><?php echo $this->lang->line('total'); ?></th>
                                        <th class="text-center"><?php echo $this->lang->line('percentage'); ?></th>
                                        <th class="text-center"><?php echo $this->lang->line('status'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (!empty($results)) {
                                        foreach ($results as $student) {
                                            foreach ($student['sessions'] as $session_data) {
                                                foreach ($session_data['exams'] as $exam_data) {
                                                    ?>
                                                    <tr>
                                                        <?php if (!$adm_auto_insert) { ?>
                                                            <td><?php echo $student['admission_no']; ?></td>
                                                        <?php } ?>
                                                        <td><?php echo $student['student_name']; ?></td>
                                                        <td><?php echo $student['class']; ?></td>
                                                        <td><?php echo $student['section']; ?></td>
                                                        
                                                        <?php
                                                        // Display marks for each subject
                                                        foreach ($exam_data['subjects'] as $subject) {
                                                            $marks_class = '';
                                                            if (isset($subject['is_absent']) && $subject['is_absent']) {
                                                                $marks_class = 'marks-fail';
                                                            } elseif ($subject['pass']) {
                                                                $marks_class = 'marks-pass';
                                                            } else {
                                                                $marks_class = 'marks-fail';
                                                            }
                                                            ?>
                                                            <td class="text-center marks-cell <?php echo $marks_class; ?>">
                                                                <?php echo $subject['actualmarks']; ?> / <?php echo $subject['maxmarks']; ?>
                                                            </td>
                                                        <?php } ?>
                                                        
                                                        <td class="text-center">
                                                            <strong>
                                                                <?php 
                                                                if ($exam_data['has_absent']) {
                                                                    echo 'AB';
                                                                } else {
                                                                    echo $exam_data['total_marks']; 
                                                                }
                                                                ?> / <?php echo $exam_data['total_max_marks']; ?>
                                                            </strong>
                                                        </td>
                                                        
                                                        <td class="text-center">
                                                            <strong>
                                                                <?php 
                                                                if ($exam_data['percentage'] === 'AB') {
                                                                    echo 'AB';
                                                                } else {
                                                                    echo $exam_data['percentage'] . '%';
                                                                }
                                                                ?>
                                                            </strong>
                                                        </td>
                                                        
                                                        <td class="text-center">
                                                            <?php if ($exam_data['pass_status'] === 'absent') { ?>
                                                                <span class="result-badge-absent">
                                                                    <i class="fa fa-ban"></i> AB
                                                                </span>
                                                            <?php } elseif ($exam_data['pass_status']) { ?>
                                                                <span class="result-badge-pass">
                                                                    <i class="fa fa-check"></i> PASS
                                                                </span>
                                                            <?php } else { ?>
                                                                <span class="result-badge-fail">
                                                                    <i class="fa fa-times"></i> FAIL
                                                                </span>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
                                            }
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php } elseif (isset($results)) { ?>
                    <div class="box-body">
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i> No results found for the selected criteria. Please try different filter options.
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>
</div>

<script type="text/javascript">
$(document).ready(function () {
    var base_url = '<?php echo base_url() ?>';

    // Load sections based on class selection (from standard getSectionByClass function)
    function getSectionByClass(class_id, section_id) {
        if (class_id != "") {
            $('#section_id').html("");
            var base_url = '<?php echo base_url() ?>';
            var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
            $.ajax({
                type: "GET",
                url: base_url + "sections/getByClass",
                data: {'class_id': class_id},
                dataType: "json",
                success: function (data) {
                    $.each(data, function (i, obj) {
                        var sel = "";
                        if (section_id == obj.section_id) {
                            sel = "selected";
                        }
                        div_data += "<option value=" + obj.section_id + " " + sel + ">" + obj.section + "</option>";
                    });
                    $('#section_id').append(div_data);
                }
            });
        }
    }

    // Handle class dropdown change
    $(document).on('change', '#class_id', function (e) {
        $('#section_id').html("");
        var class_id = $(this).val();
        getSectionByClass(class_id, 0);
    });

    // Initialize section dropdown on page load if class is pre-selected
    var preSelectedClass = $('#class_id').val();
    var preSelectedSection = '<?php echo isset($selected_section) ? $selected_section : ''; ?>';
    if (preSelectedClass) {
        getSectionByClass(preSelectedClass, preSelectedSection);
    }

    // Handle session dropdown changes to load exam types dynamically
    $(document).on('change', '#session_id', function (e) {
        var session_id = $(this).val();
        $('#exam_type_id').html('<option value=""><?php echo $this->lang->line('select'); ?></option>');

        if (session_id) {
            $.ajax({
                type: "POST",
                url: base_url + "report/getExamTypesBySession",
                data: {'session_id': session_id},
                dataType: "json",
                success: function(data) {
                    if (data && Array.isArray(data)) {
                        $.each(data, function(i, examtype) {
                            var selected = '';
                            <?php if (isset($selected_exam_type)) { ?>
                            if (examtype.id == '<?php echo $selected_exam_type; ?>') {
                                selected = 'selected';
                            }
                            <?php } ?>
                            $('#exam_type_id').append('<option value="' + examtype.id + '" ' + selected + '>' + examtype.examtype + '</option>');
                        });
                    }
                },
                error: function() {
                    console.error('Failed to load exam types');
                }
            });
        }
    });
});

function printResultsTable() {
    // Hide buttons during print
    var btnExport = document.getElementById('btnExport');
    var btnPrint = document.getElementById('btnPrint');
    
    if (btnExport) btnExport.style.display = 'none';
    if (btnPrint) btnPrint.style.display = 'none';
    
    window.print();
    
    // Show buttons after print
    if (btnExport) btnExport.style.display = 'inline-block';
    if (btnPrint) btnPrint.style.display = 'inline-block';
}

function exportResultsToExcel() {
    var table = document.querySelector('.internal-results-datatable');
    if (!table) return;
    
    var html = table.outerHTML;
    var url = 'data:application/vnd.ms-excel,' + encodeURIComponent(html);
    var downloadLink = document.createElement("a");
    document.body.appendChild(downloadLink);
    downloadLink.href = url;
    downloadLink.download = 'internal_results_report.xls';
    downloadLink.click();
    document.body.removeChild(downloadLink);
}
</script>
