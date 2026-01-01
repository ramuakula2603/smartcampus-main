<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>

<style>
/* Multi-select dropdown enhancements */
.SumoSelect {
    width: 100% !important;
}

.SumoSelect > .CaptionCont {
    border: 1px solid #d2d6de;
    border-radius: 3px;
    background-color: #fff;
    min-height: 34px;
    padding: 6px 12px;
}

.SumoSelect > .CaptionCont > span {
    line-height: 1.42857143;
    color: #555;
    padding-right: 20px;
}

.SumoSelect > .CaptionCont > span.placeholder {
    color: #999;
    font-style: italic;
}

.SumoSelect.open > .CaptionCont,
.SumoSelect:focus > .CaptionCont,
.SumoSelect:hover > .CaptionCont {
    border-color: #66afe9;
    box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6);
}

.SumoSelect .optWrapper {
    border: 1px solid #d2d6de;
    border-radius: 3px;
    box-shadow: 0 6px 12px rgba(0,0,0,.175);
    background-color: #fff;
    z-index: 9999;
}

.SumoSelect .optWrapper ul.options {
    max-height: 200px;
    overflow-y: auto;
}

.SumoSelect .optWrapper ul.options li {
    padding: 8px 12px;
    border-bottom: 1px solid #f4f4f4;
}

.SumoSelect .optWrapper ul.options li:hover {
    background-color: #f5f5f5;
}

.SumoSelect .optWrapper ul.options li.selected {
    background-color: #337ab7;
    color: #fff;
}

.SumoSelect .search-txt {
    border: 1px solid #d2d6de;
    border-radius: 3px;
    padding: 6px 12px;
    margin: 5px;
    width: calc(100% - 10px);
}

/* Select all/clear all button styling */
.SumoSelect .select-all {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    padding: 8px 12px;
    font-weight: 600;
    color: #495057;
    cursor: pointer;
    display: block !important;
}

.SumoSelect .select-all:hover {
    background-color: #e9ecef;
}

/* Ensure Select All option is visible */
.SumoSelect .optWrapper .options li.opt {
    display: list-item !important;
    padding: 6px 12px;
    cursor: pointer;
}

.SumoSelect .optWrapper .options li.opt:hover {
    background-color: #f5f5f5;
}

/* Select All specific styling */
.SumoSelect .optWrapper .options li.opt.select-all {
    background-color: #e3f2fd;
    border-bottom: 1px solid #bbdefb;
    font-weight: 600;
    color: #1976d2;
}

.SumoSelect .optWrapper .options li.opt.select-all:hover {
    background-color: #bbdefb;
}

/* Loading state for dropdowns */
.SumoSelect.loading > .CaptionCont {
    opacity: 0.6;
    pointer-events: none;
}

.SumoSelect.loading > .CaptionCont:after {
    content: "";
    position: absolute;
    right: 10px;
    top: 50%;
    margin-top: -8px;
    width: 16px;
    height: 16px;
    border: 2px solid #ccc;
    border-top-color: #337ab7;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsive design improvements */
@media (max-width: 768px) {
    .col-sm-3 {
        margin-bottom: 15px;
    }

    .SumoSelect > .CaptionCont {
        min-height: 40px;
        padding: 8px 12px;
    }

    .form-group label {
        font-weight: 600;
        margin-bottom: 5px;
    }
}

@media (max-width: 480px) {
    .SumoSelect > .CaptionCont {
        min-height: 44px;
        padding: 10px 12px;
    }
}

/* Success highlight animation for updated rows */
.table tbody tr.success {
    background-color: #d4edda !important;
    transition: background-color 0.3s ease;
}

.table tbody tr.success td {
    border-color: #c3e6cb !important;
}

/* Alert message styling */
.alert-message {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    border-radius: 4px;
}

.alert-message .close {
    font-size: 18px;
    font-weight: bold;
    line-height: 1;
    color: inherit;
    opacity: 0.8;
}

.alert-message .close:hover {
    opacity: 1;
}
</style>










<div class="content-wrapper">
    <section class="content-header">
        <!-- <h1><i class="fa fa-newspaper-o"></i> <?php //echo $this->lang->line('certificate'); ?></h1> -->
    </section>
    <!-- Main content -->
    <section class="content">
        <?php if ($this->session->flashdata('msg')) {?>
            <?php 
                echo $this->session->flashdata('msg');
                $this->session->unset_userdata('msg');
            ?>
        <?php }?>
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">

                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> Select Discount</h3>
                    </div>
                    
                    <div class="box-body">
                        <div class="row">
                            <form role="form" action="<?php echo site_url('admin/feesdiscountapproval/search') ?>" method="post" class="">
                                
                                <?php echo $this->customlib->getCSRF(); ?>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('class'); ?></label>
                                        <select id="class_id" name="class_id[]" class="form-control multiselect-dropdown" multiple>
                                            <?php
                                            if (isset($classlist) && !empty($classlist)) {
                                                foreach ($classlist as $class) {
                                                    ?>
                                                    <option value="<?php echo $class['id'] ?>" <?php if (set_value('class_id') == $class['id']) {
                                                        echo "selected=selected";
                                                    }
                                                    ?>><?php echo $class['class'] ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                        <span class="text-danger" id="error_class_id"></span>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('section'); ?></label>
                                        <select id="section_id" name="section_id[]" class="form-control multiselect-dropdown" multiple>
                                        </select>
                                        <span class="text-danger" id="error_section_id"></span>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('session'); ?></label>
                                        <select id="session_id" name="session_id[]" class="form-control multiselect-dropdown" multiple>
                                            <option value="">All Sessions</option>
                                            <?php
                                            if (isset($sessionlist)) {
                                                foreach ($sessionlist as $session) {
                                                    ?>
                                                    <option value="<?php echo $session['id'] ?>" <?php if (set_value('session_id') == $session['id']) {
                                                        echo "selected=selected";
                                                    }
                                                    ?>><?php echo $session['session'] ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                        <span class="text-danger" id="error_session_id"></span>
                                    </div>
                                </div>





                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('discount_status'); ?></label>
                                        <select class="form-control multiselect-dropdown" name="progress_id[]" id="progress_id" multiple>
                                            <?php
                                            foreach ($progresslist as $key => $value) {
                                                ?>
                                                <option value="<?php echo $key; ?>"
                                                    <?php
                                                    if (set_value('progress_id') == $key) {echo "selected";}
                                                    ?>>
                                                    <?php echo $value; ?>
                                                </option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                        <span class="text-danger" id="error_progress_id"></span>
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <button type="submit" name="search" value="search_filter" class="btn btn-primary btn-sm pull-right checkbox-toggle"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                
                    <div class="nav-tabs-custom border0 navnoshadow">
                        <div class="box-header ptbnull"></div>
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="true"><i class="fa fa-list"></i> <?php echo $this->lang->line('list_view'); ?></a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active table-responsive no-padding overflow-visible-lg" id="tab_1">
                                <div class="download_label export_title"><?php echo $this->lang->line('student_list'); ?></div>
                                <table class="table table-striped table-bordered table-hover fees-discount-list" data-export-title="<?php echo $this->lang->line('student_list'); ?>">
                                    <thead>
                                        <tr>
                                            <th class="no-sort"><input type="checkbox" id="select_all" /></th>
                                            <th><?php echo $this->lang->line('admission_no'); ?></th>
                                            <th><?php echo $this->lang->line('student_name'); ?></th>
                                            <th><?php echo $this->lang->line('class'); ?></th>
                                            <th><?php echo $this->lang->line('father_name'); ?></th>
                                            <th><?php echo $this->lang->line('date_of_birth'); ?></th>
                                            <th><?php echo $this->lang->line('gender'); ?></th>
                                            <th><?php echo $this->lang->line('category'); ?></th>
                                            <th><?php echo $this->lang->line('mobile_number'); ?></th>
                                            <th><?php echo $this->lang->line('fee_group');?></th>
                                            <th><?php echo $this->lang->line('discount_amountt'); ?></th>
                                            <th><?php echo $this->lang->line('note'); ?></th>
                                            <th><?php echo $this->lang->line('discount_status'); ?></th>
                                            <th class="text-center no-sort"><?php echo $this->lang->line('action'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>




<div class="delmodal modal fade" id="confirm-approved" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('confirmation'); ?></h4>
            </div>
            <div class="modal-body">

                <p><?php echo $this->lang->line('are_you_sure_to_approve_discount') ?></p>

                <input type="hidden" name="main_invoice"  id="main_invoice" value="">
                <!-- <input type="hidden" name="sub_invoice" id="sub_invoice"  value=""> -->


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                <a class="btn btn-danger approved-btn-ok"><?php echo $this->lang->line('yes'); ?></a>
            </div>
        </div>
    </div>
</div>


<div class="delmodal modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('confirmation'); ?></h4>
            </div>
            <div class="modal-body">

                <p><?php echo $this->lang->line('are_you_sure_to_reject_discount') ?></p>

                <input type="hidden" name="main_invoicee"  id="main_invoicee" value="">
                <!-- <input type="hidden" name="sub_invoice" id="sub_invoice"  value=""> -->


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                <a class="btn btn-danger btn-ok"><?php echo $this->lang->line('yes'); ?></a>
            </div>
        </div>
    </div>
</div>


<div class="delmodal modal fade" id="confirm-retrive" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('confirmation'); ?></h4>
            </div>
            <div class="modal-body">

                <p><?php echo $this->lang->line('are_you_sure_to_retrive_discount') ?></p>

                <input type="hidden" name="main_invoic"  id="main_invoic" value="">
                <input type="hidden" name="sub_invoic" id="sub_invoic"  value="">


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                <a class="btn btn-danger retrive-btn-ok"><?php echo $this->lang->line('yes'); ?></a>
            </div>
        </div>
    </div>
</div>




<script>

        $(document).ready(function () {
            // Old event handlers removed to prevent conflicts
            // New event handlers are defined below using $(document).on() for dynamic content

            // Old revert event handler removed to prevent conflicts
            // New event handler is defined below using $(document).on() for dynamic content

            
            
        });

        $('#confirm-delete').on('click', '.btn-ok', function (e) {
            var $modalDiv = $(e.delegateTarget);
            var studentID = $('#main_invoicee').val();

            console.log('🔴 Disapproval button clicked, Student ID:', studentID);

            if (!studentID) {
                showMessage('Error: Student ID not found', 'error');
                return;
            }

            $modalDiv.addClass('modalloading');

            $.ajax({
                url: '<?php echo site_url("admin/feesdiscountapproval/dismissapprovalsingle") ?>',
                type: 'post',
                dataType: "json",
                data: {'dataa': studentID},
                success: function (response) {
                    console.log('✅ Disapproval response:', response);
                    $modalDiv.removeClass('modalloading');

                    if (response.status === 'success') {
                        $('#confirm-delete').modal('hide');

                        // Update the DataTable row instead of reloading
                        try {
                            updateDataTableRow(studentID, 'rejected');
                            showMessage('Discount rejected successfully!', 'success');
                        } catch (error) {
                            console.error('Error updating row:', error);
                            // Fallback to table refresh
                            refreshDataTable();
                            showMessage('Discount rejected successfully!', 'success');
                        }
                    } else {
                        showMessage('Failed to disapprove discount: ' + (response.message || 'Unknown error'), 'error');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('❌ Disapproval AJAX error:', status, error);
                    console.error('Response:', xhr.responseText);
                    $modalDiv.removeClass('modalloading');
                    showMessage('Network error occurred while disapproving. Please try again.', 'error');
                }
            });
        });

        $('#confirm-approved').on('click', '.approved-btn-ok', function (e) {
            var $modalDiv = $(e.delegateTarget);
            var studentID = $('#main_invoice').val();

            console.log('🟢 Approval button clicked, Student ID:', studentID);

            if (!studentID) {
                showMessage('Error: Student ID not found', 'error');
                return;
            }

            $modalDiv.addClass('modalloading');

            $.ajax({
                url: '<?php echo site_url("admin/feesdiscountapproval/approvalsingle") ?>',
                type: 'post',
                dataType: "json",
                data: {'dataa': studentID},
                success: function (response) {
                    console.log('✅ Approval response:', response);
                    $modalDiv.removeClass('modalloading');

                    if (response.status === 'success') {
                        $('#confirm-approved').modal('hide');

                        // Update the DataTable row instead of reloading
                        try {
                            updateDataTableRow(studentID, 'approved');
                            showMessage('Discount approved successfully!', 'success');
                        } catch (error) {
                            console.error('Error updating row:', error);
                            // Fallback to table refresh
                            refreshDataTable();
                            showMessage('Discount approved successfully!', 'success');
                        }
                    } else {
                        showMessage('Failed to approve discount: ' + (response.message || 'Unknown error'), 'error');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('❌ Approval AJAX error:', status, error);
                    console.error('Response:', xhr.responseText);
                    $modalDiv.removeClass('modalloading');
                    showMessage('Network error occurred while approving. Please try again.', 'error');
                }
            });
        });

        $('#confirm-retrive').on('click', '.retrive-btn-ok', function (e) {
            var $modalDiv = $(e.delegateTarget);
            var studentID = $('#main_invoic').val();
            var certificateId = $('#sub_invoic').val();

            console.log('🔄 Revert button clicked, Student ID:', studentID, 'Payment ID:', certificateId);

            if (!studentID) {
                showMessage('Error: Student ID not found', 'error');
                return;
            }

            $modalDiv.addClass('modalloading');

            $.ajax({
                url: '<?php echo site_url("admin/feesdiscountapproval/retrive") ?>',
                type: 'post',
                dataType: "json",
                data: {'dataa': studentID,'certificate_id': certificateId},
                success: function (response) {
                    console.log('✅ Revert response:', response);
                    $modalDiv.removeClass('modalloading');

                    if (response.status === 'success') {
                        $('#confirm-retrive').modal('hide');

                        // Update the DataTable row instead of reloading
                        try {
                            updateDataTableRow(studentID, 'pending');
                            showMessage('Discount reverted successfully! The discount has been removed from student fees and status changed to pending.', 'success');
                        } catch (error) {
                            console.error('Error updating row:', error);
                            // Fallback to table refresh
                            refreshDataTable();
                            showMessage('Discount reverted successfully! The discount has been removed from student fees and status changed to pending.', 'success');
                        }
                    } else {
                        showMessage('Failed to revert discount: ' + (response.message || 'Unknown error'), 'error');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('❌ Revert AJAX error:', status, error);
                    console.error('Response:', xhr.responseText);
                    $modalDiv.removeClass('modalloading');
                    showMessage('Network error occurred while reverting. Please try again.', 'error');
                }
            });
        });

</script>





<script type="text/javascript">
    function getSectionByClass(class_id, section_id) {
        if (class_id != "" && section_id != "") {
            $('#section_id').html("");
            var base_url = '<?php echo base_url() ?>';
            var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
            $.ajax({
                type: "GET",
                url: base_url + "sections/getByClass",
                data: {'class_id': class_id},
                dataType: "json",
                success: function (data) {
                    $.each(data, function (i, obj)
                    {
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
    $(document).ready(function () {
        var class_id = $('#class_id').val();
        var section_id = '<?php echo set_value('section_id') ?>';
        getSectionByClass(class_id, section_id);
        $(document).on('change', '#class_id', function (e) {
            $('#section_id').html("");
            var class_id = $(this).val();
            var base_url = '<?php echo base_url() ?>';
            var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
            $.ajax({
                type: "GET",
                url: base_url + "sections/getByClass",
                data: {'class_id': class_id},
                dataType: "json",
                success: function (data) {
                    $.each(data, function (i, obj)
                    {
                        div_data += "<option value=" + obj.section_id + ">" + obj.section + "</option>";
                    });
                    $('#section_id').append(div_data);
                }
            });
        });
    });
</script>


<script type="text/javascript">
    $(document).ready(function () {
        $('#select_all').on('click', function () {
            if (this.checked) {
                $('.checkbox').each(function () {
                    this.checked = true;
                });
            } else {
                $('.checkbox').each(function () {
                    this.checked = false;
                });
            }
        });

        $('.checkbox').on('click', function () {
            if ($('.checkbox:checked').length == $('.checkbox').length) {
                $('#select_all').prop('checked', true);
            } else {
                $('#select_all').prop('checked', false);
            }
        });
    });
</script>


<script type="text/javascript">
    $(document).ready(function () {
        $(document).on('click', '.printSelected', function () {
            var array_to_print = [];
            var classId = $("#class_id").val();
            var certificateId = $("#certificate_id").val();
            $.each($("input[name='check']:checked"), function () {
                var studentId = $(this).data('student_id');
                item = {}
                item ["student_id"] = studentId;
                array_to_print.push(item);
            });
            
            if (array_to_print.length == 0) {
                alert("<?php echo $this->lang->line('no_record_selected'); ?>");
            } else {
                $.ajax({
                    url: '<?php echo site_url("admin/feesdiscountapproval/generatemultiple") ?>',
                    type: 'post',
                    dataType: "html",
                    data: {'data': JSON.stringify(array_to_print), 'class_id': classId,'certificate_id': certificateId},
                    success: function (response) {
                        // Refresh the DataTable instead of reloading the page
                        refreshDataTable();
                        showMessage('Multiple discounts approved successfully!', 'success');
                    },
                    error: function() {
                        showMessage('Error occurred while processing bulk approval.', 'error');
                    }
                });
            }
        });
    });
</script>




<script type="text/javascript">
    $(document).ready(function () {
        $(document).on('click', '.disapprovalprintSelected', function () {
            var array_to_print = [];
            var classId = $("#class_id").val();
            var certificateId = $("#certificate_id").val();
            $.each($("input[name='check']:checked"), function () {
                var studentId = $(this).data('student_id');
                item = {}
                item ["student_id"] = studentId;
                array_to_print.push(item);
            });
            
            if (array_to_print.length == 0) {
                alert("<?php echo $this->lang->line('no_record_selected'); ?>");
            } else {
                $.ajax({
                    url: '<?php echo site_url("admin/feesdiscountapproval/dismissapprovalgeneratemultiple") ?>',
                    type: 'post',
                    dataType: "html",
                    data: {'data': JSON.stringify(array_to_print), 'class_id': classId,'certificate_id': certificateId},
                    success: function (response) {
                        // Refresh the DataTable instead of reloading the page
                        refreshDataTable();
                        showMessage('Multiple discounts rejected successfully!', 'success');
                    },
                    error: function() {
                        showMessage('Error occurred while processing bulk rejection.', 'error');
                    }
                });
            }
        });
    });
</script>


<script type="text/javascript">
    
    
                                                                

    $(document).ready(function () {

        $('#myFeesModal').modal({
            backdrop: 'static',
            keyboard: false,
            show: false
        });

    });
     
    
</script>



<script type="text/javascript">
    

    $(document).ready(function() {
        $(document).on('click', '.save_button', function() {

        var $this = $(this);
        var action = $this.data('action');

        $this.button('loading');
        // var form = $(this).attr('frm');
        // var feetype = $('#feetype_').val();

        var date = $('#date').val();
        var student_session_id = $('#std_id').val();     //ok
        var amount = 0;
        var amount_discount = $('#amount_discount').val();  //ok
        var amount_fine = 0;
        var description = $('#description').val();

        var guardian_phone = $('#guardian_phone').val();   //ok
        var guardian_email = $('#guardian_email').val();   //ok

    

        // var student_fees_master_id = $('#student_fees_master_id').val(); //ok
        
        var fee_groups_feetype_id = $('#student_fees_master_id').val();

        // var transport_fees_id = $('#transport_fees_id').val();
        // var fee_category = $('#fee_category').val();

        var payment_mode = $('input[name="payment_mode_fee"]:checked').val(); //ok

        // var student_fees_discount_id = $('#discount_group').val();

        var studentID = student_session_id;
        var classId = $("#class_id").val();
        var certificateId = $("#certificate_id").val();



        $.ajax({
            url: '<?php echo site_url("admin/feesdiscountapproval/addstudentfee") ?>',
            type: 'post',
            dataType: 'json',
            data: {
                'student_session_id': student_session_id,
                'fee_groups_feetype_id': fee_groups_feetype_id,
                'amount':amount,
                'amount_discount':amount_discount,
                'amount_fine':amount_fine,
                'date':date,
                'description':description,
                'guardian_phone':guardian_phone,
                'payment_mode':payment_mode,
            },
            success: function(response) {
                $this.button('reset');
                if (response.status === 'success') {
                    $.ajax({
                        url: '<?php echo site_url("admin/feesdiscountapproval/approvalsingle") ?>',
                        type: 'post',
                        dataType: "html",
                        data: {'data': studentID, 'class_id': classId,'certificate_id': certificateId},
                        success: function (response) {
                            // Refresh the DataTable instead of reloading the page
                            refreshDataTable();
                            showMessage('Fee added and discount approved successfully!', 'success');
                        },
                        error: function() {
                            showMessage('Error occurred while approving discount.', 'error');
                        }
                    });
                    // alert('Status: ' + response.status + '\nMessage: ' + response.message);
                } else {
                    alert('Failed to add fee');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $this.button('reset');
                alert('An error occurred: ' + textStatus + ', ' + errorThrown);
            }
        });




        });

    });

    // Multi-select dropdown initialization
    $(document).ready(function () {
        // Check if SumoSelect is available
        console.log('Initializing multi-select dropdowns...');

        if (typeof $.fn.SumoSelect === 'undefined') {
            console.error('❌ SumoSelect plugin not loaded!');
            return;
        }

        // Initialize SumoSelect for all multi-select dropdowns
        $('.multiselect-dropdown').SumoSelect({
            placeholder: 'Select Options',
            csvDispCount: 3,
            captionFormat: '{0} Selected',
            captionFormatAllSelected: 'All Selected ({0})',
            selectAll: true,
            search: true,
            searchText: 'Search...',
            noMatch: 'No matches found',
            okCancelInMulti: true,
            isClickAwayOk: true
        });

        // Handle class selection change to populate sections
        $('#class_id').on('sumo:closed', function() {
            var class_ids = $(this).val();
            console.log('Selected classes:', class_ids);

            // Clear section dropdown
            $('#section_id').html('');

            // Refresh SumoSelect to clear previous selections
            if ($('#section_id')[0].sumo) {
                $('#section_id')[0].sumo.reload();
            }

            if (class_ids && class_ids.length > 0) {
                var base_url = '<?php echo base_url() ?>';

                // Show loading state
                showDropdownLoading('#section_id');

                $.ajax({
                    url: base_url + "admin/ajax/getClassSections",
                    type: "POST",
                    data: {class_ids: class_ids},
                    dataType: "json",
                    success: function (data) {
                        console.log('Sections data received:', data);

                        var allSections = [];

                        // Collect all sections from all selected classes
                        $.each(data, function(class_id, sections) {
                            $.each(sections, function(i, section) {
                                // Avoid duplicates
                                var exists = allSections.some(function(s) {
                                    return s.value === section.section_id;
                                });

                                if (!exists) {
                                    allSections.push({
                                        value: section.section_id,
                                        text: section.section
                                    });
                                }
                            });
                        });

                        // Sort sections alphabetically
                        allSections.sort(function(a, b) {
                            return a.text.localeCompare(b.text);
                        });

                        // Populate section dropdown
                        var div_data = '';
                        $.each(allSections, function(i, section) {
                            div_data += "<option value='" + section.value + "'>" + section.text + "</option>";
                        });
                        $('#section_id').html(div_data);

                        // Refresh SumoSelect after adding options
                        if ($('#section_id')[0].sumo) {
                            $('#section_id')[0].sumo.reload();
                        }

                        console.log('Sections loaded for selected classes:', allSections.length);
                        hideDropdownLoading('#section_id');
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading sections:', error);
                        hideDropdownLoading('#section_id');
                    }
                });
            }
        });
    });

    // Helper functions for loading states
    function showDropdownLoading(selector) {
        $(selector).prop('disabled', true);
        $(selector).next('.SumoSelect').addClass('loading');
    }

    function hideDropdownLoading(selector) {
        $(selector).prop('disabled', false);
        $(selector).next('.SumoSelect').removeClass('loading');
    }

</script>

<script type="text/javascript">
// Global variables
var baseurl = '<?php echo base_url() ?>';

$(document).ready(function(){
    console.log('🚀 Fees Discount Approval Page Loaded');

    // Initialize empty DataTable
    emptyDatatable('fees-discount-list','data');
    console.log('📊 Empty DataTable initialized');

    // Handle form submission
    $("form").on('submit', function(e){
        e.preventDefault(); // avoid to execute the actual submit of the form.

        var form = $(this);
        var url = form.attr('action');
        var form_data = form.serializeArray();

        console.log('🚀 FEES DISCOUNT SEARCH STARTED');
        console.log('Form URL:', url);
        console.log('Form Data:', form_data);

        $.ajax({
            url: '<?php echo site_url("admin/feesdiscountapproval/searchvalidation") ?>',
            type: "POST",
            dataType: 'json',
            data: form_data,
            beforeSend: function () {
                $('[id^=error]').html("");
            },
            success: function(response) {
                console.log('✅ AJAX Response Received:', response);

                if(!response.status){
                    console.error('❌ Validation Error:', response.error);
                    $.each(response.error, function(key, value) {
                        $('#error_' + key).html(value);
                    });
                } else {
                    console.log('🎯 Fees Discount Search - Initializing DataTable...');
                    console.log('DataTable ID: fees-discount-list');
                    console.log('DataTable URL: admin/feesdiscountapproval/dtfeesdiscountlist');
                    console.log('DataTable Params:', response.params);

                    $('[id^=error]').html("");
                    initDatatable('fees-discount-list','admin/feesdiscountapproval/dtfeesdiscountlist',response.params,[],100);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                alert('Network error occurred. Please check your connection and try again.');
            }
        });
    });

    // Handle dynamic event binding for DataTable buttons
    $(document).on('click', '.approve-btn', function () {
        var studentID = $(this).data('studentid');
        console.log('🟢 Approve button clicked (DataTable), Student ID:', studentID);

        // Set the student ID in the modal
        $('#main_invoice').val(studentID);

        // Show the modal
        $('#confirm-approved').modal('show');
    });

    $(document).on('click', '.disapprove-btn', function () {
        var studentID = $(this).data('studentid');
        console.log('🔴 Disapprove button clicked (DataTable), Student ID:', studentID);

        // Set the student ID in the modal
        $('#main_invoicee').val(studentID);

        // Show the modal
        $('#confirm-delete').modal('show');
    });

    $(document).on('click', '.btn-xs[data-target="#confirm-retrive"]', function () {
        var studentID = $(this).data('studentid');
        var paymentid = $(this).data('paymentid');

        console.log('🔄 Revert button clicked (DataTable), Student ID:', studentID, 'Payment ID:', paymentid);

        // Set the values in the modal
        $('#main_invoic').val(studentID);
        $('#sub_invoic').val(paymentid);

        // Show the modal
        $('#confirm-retrive').modal('show');
    });

    // Handle checkbox selection in DataTable
    $(document).on('change', '#select_all', function () {
        if (this.checked) {
            $('.checkbox').each(function () {
                this.checked = true;
            });
        } else {
            $('.checkbox').each(function () {
                this.checked = false;
            });
        }
    });

    $(document).on('change', '.checkbox', function () {
        if ($('.checkbox:checked').length == $('.checkbox').length) {
            $('#select_all').prop('checked', true);
        } else {
            $('#select_all').prop('checked', false);
        }
    });
});

// Helper function to initialize empty DataTable
function emptyDatatable(selector, message) {
    if ($.fn.DataTable.isDataTable('.' + selector)) {
        $('.' + selector).DataTable().destroy();
    }

    $('.' + selector).DataTable({
        "dom": '<"row"<"col-sm-6 mb-xs"B><"col-sm-6"f>><"table-responsive"tr>p',
        "lengthChange": false,
        "pageLength": 100,
        "columnDefs": [
            {"orderable": false, "targets": 'no-sort'},
            {"orderable": false, "targets": [-1],'class':'action'}
        ],
        "buttons": [
            {
                extend: 'copyHtml5',
                text: '<i class="ri-file-copy-2-line"></i>',
                titleAttr: 'Copy',
                title: $('.export_title').html(),
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'excelHtml5',
                text: '<i class="ri-file-excel-2-line"></i>',
                titleAttr: 'Excel',
                title: $('.export_title').html(),
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'csvHtml5',
                text: '<i class="ri-file-list-2-line"></i>',
                titleAttr: 'CSV',
                title: $('.export_title').html(),
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="ri-file-pdf-2-line"></i>',
                titleAttr: 'PDF',
                title: $('.export_title').html(),
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'print',
                text: '<i class="ri-printer-line"></i>',
                titleAttr: 'Print',
                title: $('.export_title').html(),
                exportOptions: {
                    columns: ':visible'
                }
            }
        ],
        "language": {
            "emptyTable": "Please search to view results"
        }
    });
}

// Helper function to show success/error messages
function showMessage(message, type) {
    // Remove any existing messages
    $('.alert-message').remove();

    var alertClass = 'alert-danger';
    var iconClass = 'fa-exclamation-triangle';

    if (type === 'success') {
        alertClass = 'alert-success';
        iconClass = 'fa-check-circle';
    } else if (type === 'info') {
        alertClass = 'alert-info';
        iconClass = 'fa-info-circle';
    }

    var alertHtml = '<div class="alert ' + alertClass + ' alert-dismissible alert-message" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">' +
                    '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' +
                    '<i class="fa ' + iconClass + '"></i> ' + message +
                    '</div>';

    $('body').append(alertHtml);

    // Auto-hide after 5 seconds
    setTimeout(function() {
        $('.alert-message').fadeOut(500, function() {
            $(this).remove();
        });
    }, 5000);
}

// Helper function to refresh DataTable
function refreshDataTable() {
    console.log('🔄 Refreshing DataTable...');
    var table = $('.fees-discount-list').DataTable();

    // Check if the table has ajax reload capability
    if (table.ajax && typeof table.ajax.reload === 'function') {
        console.log('📡 Using AJAX reload');
        table.ajax.reload(null, false);
    } else {
        // If no ajax, trigger a form resubmission to reload data
        console.log('📝 Using form resubmission');
        var $form = $('form');
        if ($form.length > 0) {
            $form.trigger('submit');
        } else {
            // Last resort - reload the page but preserve search parameters
            console.log('🔄 Reloading page with current parameters');
            var currentUrl = window.location.href;
            window.location.href = currentUrl;
        }
    }
}

// Alternative function to update row by refreshing table data
function updateRowByRefresh(studentID, newStatus, successMessage) {
    console.log('🔄 Updating row by refreshing table data for student ID:', studentID);

    // Show loading indicator
    showMessage('Updating...', 'info');

    // Refresh the entire table
    setTimeout(function() {
        refreshDataTable();

        // Show success message after refresh
        setTimeout(function() {
            showMessage(successMessage, 'success');
        }, 500);
    }, 100);
}

// Helper function to update DataTable row after status change
function updateDataTableRow(studentID, newStatus) {
    console.log('🔄 Updating row for student ID:', studentID, 'to status:', newStatus);

    var table = $('.fees-discount-list').DataTable();
    var updated = false;

    // First try to find the row using DataTable API
    table.rows().every(function(rowIdx, tableLoop, rowLoop) {
        var $row = $(this.node());
        var $actionCell = $row.find('td:last-child');

        // Check if this row contains the student ID in action buttons
        if ($actionCell.find('[data-studentid="' + studentID + '"]').length > 0) {
            console.log('✅ Found row to update for student ID:', studentID);

            // Find status column (second to last column)
            var $statusCell = $row.find('td:nth-last-child(2)');
            var $checkboxCell = $row.find('td:first-child');

            // Generate new status HTML
            var newStatusHtml = '';
            var newActionHtml = '';

            // Get the existing view button to preserve the correct student ID for the link
            var $existingViewBtn = $actionCell.find('a[href*="student/view"]');
            var viewBtnHtml = '';
            if ($existingViewBtn.length > 0) {
                viewBtnHtml = $existingViewBtn[0].outerHTML + ' ';
            } else {
                // Fallback if no existing view button found
                viewBtnHtml = '<a href="' + baseurl + 'student/view/' + studentID + '" class="btn btn-default btn-xs" data-toggle="tooltip" title="View"><i class="fa fa-reorder"></i></a> ';
            }

            if (newStatus === 'approved') {
                newStatusHtml = '<span class="label label-success">Approved</span>';
                // For approved status, show view and revert buttons
                newActionHtml = viewBtnHtml +
                               '<button class="btn btn-default btn-xs" data-toggle="modal" data-target="#confirm-retrive" title="Revert" data-studentid="' + studentID + '" data-paymentid=""><i class="fa fa-undo"></i></button>';
                // Remove checkbox for approved items
                $checkboxCell.html('');

            } else if (newStatus === 'rejected') {
                newStatusHtml = '<span class="label label-danger">Rejected</span>';
                // For rejected status, show only view button
                newActionHtml = viewBtnHtml;
                // Remove checkbox for rejected items
                $checkboxCell.html('');

            } else if (newStatus === 'pending') {
                newStatusHtml = '<span class="label label-warning">Pending</span>';
                // For pending status, show all action buttons
                newActionHtml = viewBtnHtml +
                               '<span style="margin-right:3px; cursor:pointer;" class="label label-success approve-btn" data-toggle="modal" data-target="#confirm-approved" data-studentid="' + studentID + '">Approve</span> ' +
                               '<span style="cursor:pointer;" class="label label-danger disapprove-btn" data-studentid="' + studentID + '" data-toggle="modal" data-target="#confirm-delete">Disapprove</span>';
                // Add checkbox back for pending items - get the original student ID from existing checkbox if available
                var $existingCheckbox = $checkboxCell.find('input[type="checkbox"]');
                var checkboxStudentId = studentID;
                if ($existingCheckbox.length > 0) {
                    checkboxStudentId = $existingCheckbox.val() || studentID;
                }
                $checkboxCell.html('<input type="checkbox" class="checkbox center-block" name="check" data-student_id="' + checkboxStudentId + '" value="' + checkboxStudentId + '">');
            }

            // Update the DOM directly
            console.log('🔄 Updating status cell with:', newStatusHtml);
            console.log('🔄 Updating action cell with:', newActionHtml);

            $statusCell.html(newStatusHtml);
            $actionCell.html(newActionHtml);

            // Add visual highlight effect
            $row.addClass('success');
            setTimeout(function() {
                $row.removeClass('success');
            }, 3000);

            updated = true;
            console.log('✅ Row updated successfully for student ID:', studentID);
            return false; // Break the loop
        }
    });

    // If DataTable API didn't work, try direct DOM search as fallback
    if (!updated) {
        console.log('⚠️ DataTable API search failed, trying direct DOM search...');
        $('.fees-discount-list tbody tr').each(function() {
            var $row = $(this);
            var $actionCell = $row.find('td:last-child');

            if ($actionCell.find('[data-studentid="' + studentID + '"]').length > 0) {
                console.log('✅ Found row via DOM search for student ID:', studentID);

                var $statusCell = $row.find('td:nth-last-child(2)');
                var newStatusHtml = '';

                if (newStatus === 'approved') {
                    newStatusHtml = '<span class="label label-success">Approved</span>';
                } else if (newStatus === 'rejected') {
                    newStatusHtml = '<span class="label label-danger">Rejected</span>';
                } else if (newStatus === 'pending') {
                    newStatusHtml = '<span class="label label-warning">Pending</span>';
                }

                $statusCell.html(newStatusHtml);
                $row.addClass('success');
                setTimeout(function() {
                    $row.removeClass('success');
                }, 3000);

                updated = true;
                console.log('✅ Row updated via DOM search for student ID:', studentID);
                return false;
            }
        });
    }

    if (!updated) {
        console.warn('⚠️ Could not find row to update for student ID:', studentID, '- will refresh table');
        throw new Error('Row not found for update');
    }
}
</script>


