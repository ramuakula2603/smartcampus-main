<?php $currency_symbol = $this->customlib->getSchoolCurrencyFormat(); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-money"></i> <?php echo $this->lang->line('fees_collection'); ?></h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <?php if ($this->rbac->hasPrivilege('fees_master', 'can_add') || $this->rbac->hasPrivilege('fees_master', 'can_edit')) { ?>
                <div class="col-md-4">
                    <!-- Horizontal Form -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><?php echo $this->lang->line('edit_fees_master') . " : " . $this->setting_model->getCurrentSessionName(); ?></h3>
                        </div><!-- /.box-header -->
                        <form id="form1" action="<?php echo site_url("admin/feemaster/edit/" . $feegroup_type->id) ?>"  id="feemasterform" name="feemasterform" method="post" accept-charset="utf-8">
                            <div class="box-body">
                                <?php if ($this->session->flashdata('msg')) { ?>
                                    <?php 
                                        echo $this->session->flashdata('msg');
                                        $this->session->unset_userdata('msg'); 
                                    ?>
                                <?php } ?>

                                <?php echo $this->customlib->getCSRF(); ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <input type="hidden" name="id" value="<?php echo $feegroup_type->id; ?>">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1"><?php echo $this->lang->line('fees_group'); ?></label><small class="req"> *</small>
                                            <select autofocus="" id="fee_groups_id" name="fee_groups_id" class="form-control" >
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                <?php
                                                foreach ($feegroupList as $feegroup) {
                                                    ?>
                                                    <option value="<?php echo $feegroup['id'] ?>"<?php
                                                    if (set_value('fee_groups_id', $feegroup_type->fee_groups_id) == $feegroup['id']) {
                                                        echo "selected =selected";
                                                    }
                                                    ?>><?php echo $feegroup['name'] ?></option>

                                                    <?php
                                                    $count++;
                                                }
                                                ?>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('fee_groups_id'); ?></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputEmail1"><?php echo $this->lang->line('fees_type'); ?></label><small class="req"> *</small>

                                            <select  id="feetype_id" name="feetype_id" class="form-control" >
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                <?php
                                                foreach ($feetypeList as $feetype) {
                                                    ?>
                                                    <option value="<?php echo $feetype['id'] ?>"<?php
                                                    if (set_value('feetype_id', $feegroup_type->feetype_id) == $feetype['id']) {
                                                        echo "selected =selected";
                                                    }
                                                    ?>><?php echo $feetype['type'] ?></option>
                                                    <?php
                                                    $count++;
                                                }
                                                ?>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('feetype_id'); ?></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputEmail1"><?php echo $this->lang->line('due_date'); ?></label>
                                            <input id="due_date" name="due_date" placeholder="" type="text" class="form-control date"  value="<?php echo set_value('due_date', $this->customlib->dateformat($feegroup_type->due_date)); ?>" />
                                            <span class="text-danger"><?php echo form_error('due_date'); ?></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputEmail1"><?php echo $this->lang->line('amount'); ?> (<?php echo $currency_symbol; ?>)</label><small class="req">*</small>
                                            <input id="amount" name="amount" placeholder="" type="text" class="form-control"  value="<?php echo set_value('amount', convertBaseAmountCurrencyFormat($feegroup_type->amount)); ?>" />
                                            <span class="text-danger"><?php echo form_error('amount'); ?></span>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-12">
                                                <label for="input-type"><?php echo $this->lang->line('fine_type'); ?></label>
                                                <div id="input-type" class="row">
                                                    <div class="col-sm-4">
                                                        <label class="radio-inline">
                                                            <input name="account_type" class="finetype" id="input-type-student" value="none" type="radio" <?php echo set_radio('account_type', 'none', (set_value('none', $feegroup_type->fine_type) == "none") ? TRUE : FALSE); ?>/><?php echo $this->lang->line('none') ?>
                                                        </label>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <label class="radio-inline">
                                                            <input name="account_type" class="finetype" id="input-type-student" value="percentage" type="radio" <?php echo set_radio('account_type', 'percentage', (set_value('percentage', $feegroup_type->fine_type) == "percentage") ? TRUE : FALSE ); ?> /><?php echo $this->lang->line('percentage') ?>
                                                        </label>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <label class="radio-inline">
                                                            <input name="account_type" class="finetype" id="input-type-tutor" value="fix" type="radio"  <?php echo set_radio('account_type', 'fix', (set_value('fix', $feegroup_type->fine_type) == "fix") ? TRUE : FALSE); ?> /><?php echo $this->lang->line('fix_amount'); ?>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('percentage') ?> (%)</label><small class="req"> *</small>
                                                    <input id="fine_percentage" name="fine_percentage" placeholder="" type="text" class="form-control"  value="<?php echo set_value('fine_percentage', $feegroup_type->fine_percentage); ?>" />
                                                    <span class="text-danger"><?php echo form_error('fine_percentage'); ?></span>
                                                </div>    
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">  
                                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('fix_amount'); ?> (<?php echo $currency_symbol; ?>)</label><small class="req"> *</small>
                                                    <input id="fine_amount" name="fine_amount" placeholder="" type="text" class="form-control"  value="<?php echo set_value('fine_amount', convertBaseAmountCurrencyFormat($feegroup_type->fine_amount)); ?>" />
                                                    <span class="text-danger"><?php echo form_error('fine_amount'); ?></span>
                                                </div>  
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="box-footer">
                                <button type="submit" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                            </div>
                    </div><!-- /.box-body -->
                    </form>
                </div>
                <div class="col-md-<?php
                if ($this->rbac->hasPrivilege('fees_master', 'can_add') || $this->rbac->hasPrivilege('fees_master', 'can_edit')) {
                    echo "8";
                } else {
                    echo "12";
                }
                ?>">
                    <!-- Horizontal Form -->
                    <div class="box box-primary">
                        <div class="box-header ptbnull">
                            <h3 class="box-title titlefix"><?php echo $this->lang->line('fees_master_list') . " : " . $this->setting_model->getCurrentSessionName(); ?></h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#setDefaultClassFeeModal">
                                    <i class="fa fa-cog"></i> Set Default Class Fee
                                </button>
                            </div>
                        </div><!-- /.box-header -->
                        <div class="box-body">
                            <div class="download_label"><?php echo $this->lang->line('fees_master_list') . " : " . $this->setting_model->getCurrentSessionName(); ?></div>
                            <div class="mailbox-messages">
                                <div class="table-responsive">  
                                    <table class="table table-striped table-bordered table-hover example">
                                        <thead>
                                            <tr>
                                                <th><?php echo $this->lang->line('fees_group'); ?></th>
                                                <th>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <?php echo $this->lang->line('fees_code'); ?>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <?php echo $this->lang->line('amount'); ?>
                                                        </div>
                                                    </div>
                                                </th>
                                                <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($feemasterList as $feegroup) {
                                                ?>
                                                <tr>
                                                    <td class="mailbox-name">
                                                        <a href="#" data-toggle="popover" class="detail_popover"><?php echo $feegroup->group_name; ?></a>
                                                    </td>
                                                    <td class="mailbox-name">
                                                        <ul class="liststyle1">
                                                            <?php
                                                            foreach ($feegroup->feetypes as $feetype_key => $feetype_value) {
                                                                ?>
                                                               <li> 
                                                                <div class="row">
                                                                    <div class="col-md-6"> 
                                                                        <i class="fa fa-money"></i>
                                                                      <?php 


                                                                echo $feetype_value->type."(".$feetype_value->code.")"; ?></div>
                                                                    <div class="col-md-3"> 
                                                                     <?php 


                                                                echo $currency_symbol.amountFormat($feetype_value->amount); ?></div>
                                                                    <div class="col-md-3"> <?php if ($this->rbac->hasPrivilege('fees_master', 'can_edit')) {
                                                                 ?>
                                                                    <a href="<?php echo base_url(); ?>admin/feemaster/edit/<?php echo $feetype_value->id ?>"   data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                                        <i class="fa fa-pencil"></i>
                                                                    </a>&nbsp;
                                                                    <?php
                                                                }
                                                                if ($this->rbac->hasPrivilege('fees_master', 'can_delete')) {
                                                                    ?>
                                                                    <a href="<?php echo base_url(); ?>admin/feemaster/delete/<?php echo $feetype_value->id ?>" data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');">
                                                                        <i class="fa fa-remove"></i>
                                                                    </a>
                                                                <?php } ?></div>
                                                                    
                                                                </div>
                                                             
                                                            </li>

                                                                <?php
                                                            }
                                                            ?>
                                                        </ul>
                                                    </td>
                                                    <td class="mailbox-date pull-right">
                                                        <?php if ($this->rbac->hasPrivilege('fees_group_assign', 'can_view')) { ?>
                                                            <a href="<?php echo base_url(); ?>admin/feemaster/assign/<?php echo $feegroup->id ?>" 
                                                               class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo $this->lang->line('assign_view_student'); ?>">
                                                                <i class="fa fa-tag"></i>
                                                            </a>
                                                        <?php } ?>
                                                        <?php if ($this->rbac->hasPrivilege('fees_master', 'can_delete')) { ?>
                                                            <a href="<?php echo base_url(); ?>admin/feemaster/deletegrp/<?php echo $feegroup->id ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');">
                                                                <i class="fa fa-remove"></i>
                                                            </a>
                                                        <?php } ?>

                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                        </tbody>
                                    </table><!-- /.table -->
                                </div>  
                            </div><!-- /.mail-box-messages -->
                        </div><!-- /.box-body -->
                        </form>
                    </div>
                </div><!--/.col (right) -->
            </div><!--/.col (right) -->
            <!-- left column -->
        <?php } ?>
        <!-- left column -->
</div>

<!-- Set Default Class Fee Modal -->
<div class="modal fade" id="setDefaultClassFeeModal" tabindex="-1" role="dialog" aria-labelledby="setDefaultClassFeeModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="setDefaultClassFeeModalLabel">
                    <i class="fa fa-cog"></i> Set Default Class Fee
                </h4>
            </div>
            <div class="modal-body">
                <form id="defaultClassFeeForm" method="post">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="class_id">Class <span class="text-danger">*</span></label>
                                <select class="form-control" id="class_id" name="class_id" required>
                                    <option value="">Select Class</option>
                                    <?php
                                    if (isset($classlist)) {
                                        foreach ($classlist as $class) {
                                            echo '<option value="' . $class['id'] . '">' . $class['class'] . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fee_group_id">Fee Group <span class="text-danger">*</span></label>
                                <select class="form-control" id="fee_group_id" name="fee_group_id" required>
                                    <option value="">Select Fee Group</option>
                                    <?php
                                    if (isset($feegroupList)) {
                                        foreach ($feegroupList as $feegroup) {
                                            echo '<option value="' . $feegroup['id'] . '">' . $feegroup['name'] . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Save Default Class Fee
                            </button>
                            <button type="button" class="btn btn-default" onclick="clearEditMode()" style="display: none;" id="cancelEditBtn">
                                <i class="fa fa-times"></i> Cancel Edit
                            </button>
                        </div>
                    </div>
                </form>

                <hr>

                <!-- List of Default Class Fees -->
                <div class="row">
                    <div class="col-md-12">
                        <h5><i class="fa fa-list"></i> Default Class Fee Assignments</h5>
                        <div id="defaultClassFeeList" style="max-height: 300px; overflow-y: auto;">
                            <!-- This will be populated via AJAX -->
                            <div class="text-center">
                                <i class="fa fa-spinner fa-spin"></i> Loading...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        var account_type = "<?php echo set_value('account_type', $feegroup_type->fine_type); ?>";
        load_disable(account_type);
    });

    $(document).on('change', '.finetype', function () {
        calculatefine();
    });

    $(document).on('keyup', '#amount,#fine_percentage', function () {
        calculatefine();
    });

    function load_disable(account_type) {
        if (account_type === "percentage") {
            $('#fine_amount').prop('readonly', true);
            $('#fine_percentage').prop('readonly', false);
        } else if (account_type === "fix") {
            $('#fine_amount').prop('readonly', false);
            $('#fine_percentage').prop('readonly', true);
        } else {
            $('#fine_amount').prop('readonly', true);
            $('#fine_percentage').prop('readonly', true);
        }
    }

    function calculatefine() {
        var amount = $('#amount').val();
        var fine_percentage = $('#fine_percentage').val();
        var finetype = $('input[name=account_type]:checked', '#form1').val();
        if (finetype === "percentage") {
            fine_amount = ((amount * fine_percentage) / 100).toFixed(2);
            $('#fine_amount').val(fine_amount).prop('readonly', true);
            $('#fine_percentage').prop('readonly', false);
        } else if (finetype === "fix") {
            $('#fine_amount').val("").prop('readonly', false);
            $('#fine_percentage').val("").prop('readonly', true);
        } else {
            $('#fine_amount').val("");
        }
    }

    // Modal event handlers
    $('#setDefaultClassFeeModal').on('shown.bs.modal', function () {
        loadDefaultClassFeeList();
    });

    // Clear edit mode when modal is closed
    $('#setDefaultClassFeeModal').on('hidden.bs.modal', function () {
        clearEditMode();
    });

    // Form submission handler
    $('#defaultClassFeeForm').on('submit', function(e) {
        e.preventDefault();
        saveDefaultClassFee();
    });

    function saveDefaultClassFee() {
        var classId = $('#class_id').val();
        var feeGroupId = $('#fee_group_id').val();
        var editId = $('#edit_id').val();

        if (!classId || !feeGroupId) {
            alert('Please select both Class and Fee Group');
            return;
        }

        var postData = {
            class_id: classId,
            fee_group_id: feeGroupId
        };

        // Include edit_id if it exists (for updates)
        if (editId) {
            postData.edit_id = editId;
        }

        $.ajax({
            url: '<?php echo base_url(); ?>admin/feemaster/saveDefaultClassFee',
            type: 'POST',
            data: postData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    alert('Default class fee saved successfully!');
                    clearEditMode();
                    loadDefaultClassFeeList();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while saving the default class fee.');
            }
        });
    }

    function loadDefaultClassFeeList() {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/feemaster/getDefaultClassFeeList',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    displayDefaultClassFeeList(response.data);
                } else {
                    $('#defaultClassFeeList').html('<div class="text-center text-muted">No default class fees found.</div>');
                }
            },
            error: function() {
                $('#defaultClassFeeList').html('<div class="text-center text-danger">Error loading default class fees.</div>');
            }
        });
    }

    function displayDefaultClassFeeList(data) {
        var html = '';
        if (data && data.length > 0) {
            html += '<div class="table-responsive"><table class="table table-striped table-bordered">';
            html += '<thead><tr><th>Class</th><th>Fee Group</th><th>Actions</th></tr></thead><tbody>';

            $.each(data, function(index, item) {
                html += '<tr>';
                html += '<td>' + item.class_name + '</td>';
                html += '<td>' + item.fee_group_name + '</td>';
                html += '<td>';
                html += '<button class="btn btn-xs btn-info" onclick="editDefaultClassFee(' + item.id + ')" title="Edit">';
                html += '<i class="fa fa-edit"></i></button> ';
                html += '<button class="btn btn-xs btn-danger" onclick="deleteDefaultClassFee(' + item.id + ')" title="Delete">';
                html += '<i class="fa fa-trash"></i></button>';
                html += '</td>';
                html += '</tr>';
            });

            html += '</tbody></table></div>';
        } else {
            html = '<div class="text-center text-muted">No default class fees found.</div>';
        }

        $('#defaultClassFeeList').html(html);
    }

    function editDefaultClassFee(id) {
        // Load the record for editing
        $.ajax({
            url: '<?php echo base_url(); ?>admin/feemaster/getDefaultClassFee/' + id,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#class_id').val(response.data.class_id);
                    $('#fee_group_id').val(response.data.fee_group_id);
                    // Add hidden field for update
                    if ($('#edit_id').length === 0) {
                        $('#defaultClassFeeForm').append('<input type="hidden" id="edit_id" name="edit_id">');
                    }
                    $('#edit_id').val(id);

                    // Change button text to indicate edit mode
                    $('#defaultClassFeeForm button[type="submit"]').html('<i class="fa fa-save"></i> Update Default Class Fee');
                    $('#cancelEditBtn').show();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while loading the record for editing.');
            }
        });
    }

    function clearEditMode() {
        $('#defaultClassFeeForm')[0].reset();
        $('#edit_id').remove();
        $('#defaultClassFeeForm button[type="submit"]').html('<i class="fa fa-save"></i> Save Default Class Fee');
        $('#cancelEditBtn').hide();
    }

    function deleteDefaultClassFee(id) {
        if (confirm('Are you sure you want to delete this default class fee assignment?')) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/feemaster/deleteDefaultClassFee/' + id,
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        alert('Default class fee deleted successfully!');
                        loadDefaultClassFeeList();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('An error occurred while deleting the default class fee.');
                }
            });
        }
    }
</script>