<style type="text/css">
    .liststyle1 {
        margin: 0;
        list-style: none;
        line-height: 28px;
    }
</style>

<?php $currency_symbol = $this->customlib->getSchoolCurrencyFormat(); ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-money"></i> <?php echo $this->lang->line('fees_collection'); ?>
        </h1>
    </section>

    <section class="content">
        <div class="row">
            <?php if ($this->rbac->hasPrivilege('fees_master', 'can_add')) {
                ?>
                <div class="col-md-3">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">
                                <?php echo $this->lang->line('add_fees_master') . " : " . $this->setting_model->getCurrentSessionName(); ?>
                            </h3>
                        </div>
                        <form id="form1" action="<?php echo base_url() ?>admin/halltickectgeneration/subgroupcombo" id="feemasterform"
                            name="feemasterform" method="post" accept-charset="utf-8">
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


                                        <div class="form-group">
                                            <label
                                                for="exampleInputEmail1"><?php echo $this->lang->line('subject_group'); ?></label>
                                            <small class="req">*</small>
                                            <select autofocus="" id="fee_groups_id" name="fee_groups_id"
                                                class="form-control">
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                <?php
                                                foreach ($feegroupList as $feegroup) {
                                                    ?>
                                                    <option value="<?php echo $feegroup['id'] ?>" <?php
                                                       if (set_value('fee_groups_id') == $feegroup['id']) {
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
                                            <label
                                                for="exampleInputEmail1"><?php echo $this->lang->line('subjects'); ?></label><small
                                                class="req"> *</small>
                                            <select id="feetype_id" name="feetype_id" class="form-control">
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                <?php
                                                foreach ($feetypeList as $feetype) {
                                                    ?>
                                                    <option value="<?php echo $feetype['id'] ?>" <?php
                                                       if (set_value('feetype_id') == $feetype['id']) {
                                                           echo "selected =selected";
                                                       }
                                                       ?>><?php echo $feetype['name'] ?></option>

                                                    <?php
                                                    $count++;
                                                }
                                                ?>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('feetype_id'); ?></span>
                                        </div>

                                        <div class="form-group">
                                            <label
                                                for="exampleInputEmail1"><?php echo $this->lang->line('due_date'); ?></label><small
                                                class="req" id="due_date_error"> </small>
                                            <input id="due_date date" name="due_date" placeholder="" type="text"
                                                class="form-control date" value="<?php echo set_value('due_date'); ?>" />
                                            <span class="text-danger"><?php echo form_error('due_date'); ?></span>
                                        </div>

                                        <div class="form-group">
                                            <label
                                                for="exampleInputEmail1"><?php echo $this->lang->line('start_time'); ?></label><small
                                                class="req" id="start_time_error"> </small>
                                            <div class="input-group"> 
                                                <input type="text" name="start_time" class="form-control start_time time" id="start_time" aria-invalid="false">
                                                <div class="input-group-addon"><i class="fa fa-clock-o"></i></div>
                                            </div>
                                            <span class="text-danger"><?php echo form_error('start_time'); ?></span>
                                        </div>

                                        <div class="form-group">
                                            <label
                                                for="exampleInputEmail1"><?php echo $this->lang->line('end_time'); ?></label><small
                                                class="req" id="end_time_error"> </small>
                                            <div class="input-group"> 
                                                <input type="text" name="end_time" class="form-control end_time time" id="end_time" aria-invalid="false">
                                                <div class="input-group-addon"><i class="fa fa-clock-o"></i></div>
                                            </div>
                                            <span class="text-danger"><?php echo form_error('end_time'); ?></span>
                                        </div>

                                        <div class="form-group">
                                            <label for="exampleInputEmail1"><?php echo $this->lang->line('max_marks'); ?>
                                                </label><small class="req" id="max_marks_error"> *</small>
                                            <input id="max_marks" name="max_marks" placeholder="" type="number"
                                                class="form-control" value="<?php echo set_value('max_marks'); ?>" />
                                            <span class="text-danger"><?php echo form_error('max_marks'); ?></span>
                                        </div>

                                        <div class="form-group">
                                            <label for="exampleInputEmail1"><?php echo $this->lang->line('min_marks'); ?></label><small class="req" id="min_marks_error"> *</small>
                                            <input id="min_marks" name="min_marks" placeholder="" type="number"
                                                class="form-control" value="<?php echo set_value('min_marks'); ?>" />
                                            <span class="text-danger"><?php echo form_error('min_marks'); ?></span>
                                        </div>


                                    </div>

                                </div>
                            </div>

                            <div class="box-footer">
                                <button type="submit"
                                    class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                            </div>
                        
                        </form>
                    </div>
                </div>
            <?php } ?>
            <div class="col-md-<?php
            if ($this->rbac->hasPrivilege('fees_master', 'can_add')) {
                echo "9";
            } else {
                echo "12";
            }
            ?>">
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix">
                            <?php echo $this->lang->line('fees_master_list') . " : " . $this->setting_model->getCurrentSessionName(); ?>
                        </h3>
                    </div>
                    <div class="box-body">
                        <div class="download_label">
                            <?php echo $this->lang->line('fees_master_list') . " : " . $this->setting_model->getCurrentSessionName(); ?>
                        </div>
                        <div class="mailbox-messages">
                            <div class="">



                            <table class="table table-striped table-bordered table-hover example">
                                <thead>
                                    <tr>
                                       
                                        <th><?php echo $this->lang->line('subject_group'); ?></th>
                                        <th>
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <?php echo $this->lang->line('subjects'); ?>
                                                </div>
                                                <div class="col-md-2">
                                                    <?php echo $this->lang->line('max_marks'); ?>
                                                </div>
                                                <div class="col-md-2">
                                                    <?php echo $this->lang->line('min_marks'); ?>
                                                </div>
                                                <div class="col-md-2">
                                                    <?php echo $this->lang->line('start_time'); ?>
                                                </div>
                                                <div class="col-md-2">
                                                    <?php echo $this->lang->line('end_time'); ?>
                                                </div>
                                                <div class="col-md-2">
                                                    <?php echo $this->lang->line('due_date'); ?>
                                                </div>
                                            </div>
                                        </th>
                                        <th class="text-right noExport"><?php echo $this->lang->line('action'); ?>
                                        </th>


                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($feemasterList as $group): ?>
                                        
                                        <tr>
                                            <td><?php echo $group['subjectgrp_name']; ?></td>
                                            
                                            <td class="mailbox-name">
                                                <ul class="liststyle1">
                                                    <?php
                                                    foreach ($group['subjects'] as $subject) {
                                                        ?>
                                                        <li>
                                                            <div class="row">
                                                                <div class="col-md-2">
                                                                    <?php echo $subject['subject_name']; ?>
                                                                </div>

                                                                <div class="col-md-2">
                                                                    <?php echo $subject['maxmark']; ?>
                                                                </div>

                                                                <div class="col-md-2">
                                                                    <?php echo $subject['minmark']; ?>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <?php echo $subject['starttime']; ?>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <?php echo $subject['endtime']; ?>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <?php echo $subject['date']; ?>
                                                                </div>


                                                                <div class="col-md-3"> <?php if ($this->rbac->hasPrivilege('fees_master', 'can_edit')) {
                                                                    ?>
                                                                        <a href="<?php echo base_url(); ?>admin/halltickectgeneration/subgroupcomboedit/<?php echo $subject['id']; ?>"
                                                                            data-toggle="tooltip"
                                                                            title="<?php echo $this->lang->line('edit'); ?>">
                                                                            <i class="fa fa-pencil"></i>
                                                                        </a>&nbsp;
                                                                        <?php
                                                                    }
                                                                    if ($this->rbac->hasPrivilege('fees_master', 'can_delete')) {
                                                                        ?>
                                                                        <a href="<?php echo base_url(); ?>admin/halltickectgeneration/deletecomboitem/<?php echo $subject['id'];?>"
                                                                            data-toggle="tooltip"
                                                                            title="<?php echo $this->lang->line('delete'); ?>"
                                                                            onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');">
                                                                            <i class="fa fa-remove"></i>
                                                                        </a>
                                                                    <?php } ?>

                                                                </div>


                                                            </div>

                                                        </li>
                                                        <?php
                                                    }
                                                    ?>
                                                </ul>
                                            </td>

                                            <td class="mailbox-date pull-right">
                                                <?php if ($this->rbac->hasPrivilege('fees_master', 'can_delete')) { ?>
                                                    <a data-placement="top"
                                                        href="<?php echo base_url(); ?>admin/halltickectgeneration/deletecombogrp/<?php echo $group['subjectgrp_id']; ?>"
                                                        class="btn btn-default btn-xs" data-toggle="tooltip"
                                                        title="<?php echo $this->lang->line('delete'); ?>"
                                                        onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');">
                                                        <i class="fa fa-remove"></i>
                                                    </a>
                                                <?php } ?>
                                            </td>

                                        </tr>
                                        
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                                
                            </div>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<script type="text/javascript">

    $(document).on('focus', '.time', function () {
        var $this = $(this);
        $this.datetimepicker({
            format: 'LT'
        });
    });
    
   
    
    
    $(document).ready(function () {
        $('.detail_popover').popover({
            placement: 'right',
            trigger: 'hover',
            container: 'body',
            html: true,
            content: function () {
                return $(this).closest('td').find('.fee_detail_popover').html();
            }
        });
    });
</script>