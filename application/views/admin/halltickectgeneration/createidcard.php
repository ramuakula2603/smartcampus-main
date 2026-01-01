<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>     <!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1><i class="fa fa-newspaper-o"></i> <?php //echo $this->lang->line('certificate'); ?></h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            
            
           
                <div class="col-md-4">
                    <!-- Horizontal Form -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><?php echo $this->lang->line('tc_template_add'); ?></h3>
                        </div><!-- /.box-header -->
                        <!-- form start -->
                        <form id="form1" enctype="multipart/form-data" action="<?php echo site_url('admin/halltickectgeneration/create') ?>"  id="certificateform" name="certificateform" method="post" accept-charset="utf-8">
                            
                        
                            <div class="box-body">

                                <?php if ($this->session->flashdata('msg')) { ?>
                                    <?php 
                                        echo $this->session->flashdata('msg');
                                        $this->session->unset_userdata('msg');
                                    ?>
                                <?php } ?>

                                <?php
                                if (isset($error_message)) {
                                    echo "<div class='alert alert-danger'>" . $error_message . "</div>";
                                }
                                ?>

                                <div class="form-group">
                                    <!-- <label><?php echo $this->lang->line('halltickect_name'); ?></label><small class="req"> *</small> -->
                                    <label><?php echo $this->lang->line('halltickect_name'); ?></label><small class="req"> *</small>

                                    <input autofocus="" id="halltickect_name" name="halltickect_name" placeholder="" type="text" class="form-control" value="<?php echo set_value('halltickect_name'); ?>" />
                                    <span class="text-danger"><?php echo form_error('halltickect_name'); ?></span>
                                </div>

                                
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('logo'); ?></label>
                                    <input id="logo_img" placeholder="" type="file" class="filestyle form-control" data-height="40"  name="logo_img">
                                    <span class="text-danger"><?php echo form_error('logo_img'); ?></span>
                                </div>


                                <!-- school name -->
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('school_name'); ?></label><small class="req"> *</small>
                                    <input autofocus="" id="school_name" name="school_name" placeholder="" type="text" class="form-control" value="<?php echo set_value('school_name'); ?>" />
                                    <span class="text-danger"><?php echo form_error('school_name'); ?></span>
                                </div>


                                <!-- header address -->
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('hall_address'); ?></label><small class="req"> *</small>
                                    <!-- <input id="hall_address" name="hall_address" placeholder="" type="text" class="form-control" value="<?php echo set_value('hall_address'); ?>" /> -->
                                    <textarea class="form-control" id="hall_address" name="hall_address" placeholder="" rows="2" placeholder=""><?php echo set_value('hall_address'); ?></textarea>
                                    <span class="text-danger"><?php echo form_error('hall_address'); ?></span>
                                </div>
                                
            
                                <!-- Halltickect email -->
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('hall_email'); ?></label><small class="req"> *</small>
                                    <textarea class="form-control" id="hall_email" name="hall_email" placeholder="" rows="2" placeholder=""><?php echo set_value('hall_email'); ?></textarea>
                                    <span class="text-danger"><?php echo form_error('hall_email'); ?></span>
                                </div>


                                <!-- Phone number -->
                                <div class="form-group">
                                    <!-- <label><?php echo $this->lang->line('hall_phone'); ?></label><small class="req"> *</small> -->
                                    <label><?php echo $this->lang->line('hall_phone'); ?></label><small class="req"> *</small>

                                    <input autofocus="" id="hall_phone" name="hall_phone" placeholder="" type="text" class="form-control" value="<?php echo set_value('hall_phone'); ?>" />
                                    <span class="text-danger"><?php echo form_error('hall_phone'); ?></span>
                                </div>


                                <!-- Hall Exam Heading -->
                                <div class="form-group">
                                    <!-- <label><?php echo $this->lang->line('hall_exam_heading'); ?></label><small class="req"> *</small> -->
                                    <label><?php echo $this->lang->line('hall_exam_heading'); ?></label><small class="req"> *</small>

                                    <input autofocus="" id="hall_exam_heading" name="hall_exam_heading" placeholder="" type="text" class="form-control" value="<?php echo set_value('hall_exam_heading'); ?>" />
                                    <span class="text-danger"><?php echo form_error('hall_exam_heading'); ?></span>
                                </div>

                                
                                
                                <!-- Hall Top Left Heading -->
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('top_left_text'); ?></label><small class="req"> *</small>
                                    <input autofocus="" id="top_left_text" name="top_left_text" placeholder="" type="text" class="form-control" value="<?php echo set_value('top_left_text'); ?>" />
                                    <span class="text-danger"><?php echo form_error('top_left_text'); ?></span>
                                </div>


                                <!-- Hall Top Middle Heading -->
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('top_middle_text'); ?></label><small class="req"> *</small>
                                    <input autofocus="" id="top_middle_text" name="top_middle_text" placeholder="" type="text" class="form-control" value="<?php echo set_value('top_middle_text'); ?>" />
                                    <span class="text-danger"><?php echo form_error('top_middle_text'); ?></span>
                                </div>


                                <!-- Hall Top Right Heading -->
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('top_right_text'); ?></label><small class="req"> *</small>
                                    <input autofocus="" id="top_right_text" name="top_right_text" placeholder="" type="text" class="form-control" value="<?php echo set_value('top_right_text'); ?>" />
                                    <span class="text-danger"><?php echo form_error('top_right_text'); ?></span>
                                </div>


                                <!-- Hall Bottpm Left Heading -->
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('bottom_left_text'); ?></label><small class="req"> *</small>
                                    <input autofocus="" id="bottom_left_text" name="bottom_left_text" placeholder="" type="text" class="form-control" value="<?php echo set_value('bottom_left_text'); ?>" />
                                    <span class="text-danger"><?php echo form_error('bottom_left_text'); ?></span>
                                </div>


                                <!-- Hall Bottpm Middle Heading -->
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('bottom_middle_text'); ?></label><small class="req"> *</small>
                                    <input autofocus="" id="bottom_middle_text" name="bottom_middle_text" placeholder="" type="text" class="form-control" value="<?php echo set_value('bottom_middle_text'); ?>" />
                                    <span class="text-danger"><?php echo form_error('bottom_middle_text'); ?></span>
                                </div>


                                <!-- Hall Bottpm Right Heading -->
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('bottom_right_text'); ?></label><small class="req"> *</small>
                                    <input autofocus="" id="bottom_right_text" name="bottom_right_text" placeholder="" type="text" class="form-control" value="<?php echo set_value('bottom_right_text'); ?>" />
                                    <span class="text-danger"><?php echo form_error('bottom_right_text'); ?></span>
                                </div>

                                
                            </div>
                            


                            <div class="box-footer">
                                <button type="submit" id="submitbtn" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                            </div>



                        </form>
                    </div>
                </div><!--/.col (right) -->
                <!-- left column -->
            
            
            
            
            
            <div class="col-md-8">
                <!-- general form elements -->
                <div class="box box-primary" id="hroom">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('tc_template_list'); ?></h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="mailbox-messages table-responsive overflow-visible">
                            <div class="download_label"><?php echo $this->lang->line('tc_template_list'); ?></div>
                            <table class="table table-striped table-bordered table-hover example">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('halltickect_name'); ?></th>
                                        <th><?php echo $this->lang->line('background_image'); ?></th>
                                        <th class="text text-center"> <?php echo $this->lang->line('design_type'); ?> </th>
                                        <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($idcardlist)) {
                                        ?>

                                        <?php
                                    } else {
                                        $count = 1;
                                        foreach ($idcardlist as $idcard) {
                                            ?>
                                            <tr>
                                                <td class="mailbox-name">                                               
                                                    
                                                    <a data-id="<?php echo $idcard->id ?>" class="btn btn-default btn-xs view_data"  data-toggle="tooltip" ><?php echo $idcard->halltickect_name; ?></a>                             
                                                </td>
                                                <td class="mailbox-name">
                                                    <?php if ($idcard->background != '' && !is_null($idcard->background)) { ?>
                                                        <img src="<?php echo $this->media_storage->getImageURL('uploads/student_id_card/background/'.$idcard->background) ?>" width="40">
                                                    <?php } else { ?>
                                                        <i class="fa fa-picture-o fa-3x" aria-hidden="true"></i>
                                                    <?php } ?>
                                                </td>
                                                <td class="mailbox-name text text-center">
                                                    <?php echo $this->lang->line('vertical');  ?>
                                                </td>
                                                <td class="mailbox-date pull-right no-print">
                                                    <a data-id="<?php echo $idcard->id ?>" class="btn btn-default btn-xs view_data"  data-toggle="tooltip" title="<?php echo $this->lang->line('view'); ?>">
                                                        <i class="fa fa-reorder"></i>
                                                    </a>
                                                   
                                                        <a href="<?php echo base_url(); ?>admin/halltickectgeneration/edit/<?php echo $idcard->id ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                       
                                                    
                                                        <a href="<?php echo base_url(); ?>admin/halltickectgeneration/delete/<?php echo $idcard->id ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');">
                                                            <i class="fa fa-remove"></i>
                                                        </a>
                                                    
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        $count++;
                                    }
                                    ?>
                                </tbody>
                            </table><!-- /.table -->
                        </div><!-- /.mail-box-messages -->
                    </div><!-- /.box-body -->
                </div>
            </div><!--/.col (left) -->
            <!-- right column -->
        </div>
        <div class="row">
            <div class="col-md-12">
            </div><!--/.col (right) -->
        </div>   <!-- /.row -->
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

<!-- Modal -->
<!-- <div class="modal fade" id="certificateModal" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('view_id_card'); ?></h4>
            </div>
            <div class="modal-body" id="certificate_detail">
            <div class="modal-inner-loader"></div>
            <div class="modal-inner-content">
          
            </div> 
            </div>
        </div>
    </div>
</div> -->

<div class="modal fade" id="certificateModal" role="dialog" style="width: 100%;" >
    <div class="modal-dialog modal-lg" style="width: 65%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('view_certificate'); ?></h4>
            </div>
            <div class="modal-body" id="certificate_detail">
                <div class="modal-inner-loader"></div>
                <div class="modal-inner-content"></div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#header_color").colorpicker();
        $(document).on('click','.view_data',function(){
    
            $('#certificateModal').modal("show");
            var certificateid = $(this).data('id');
            $.ajax({
                url: "<?php echo base_url('admin/halltickectgeneration/view') ?>",
                method: "post",
                data: {certificateid: certificateid},
                 beforeSend: function() {
      
                  },
                success: function (data) {
                 $('#certificateModal .modal-inner-content').html(data);
                 $('#certificateModal .modal-inner-loader').addClass('displaynone');

                 },
                error: function(xhr) { // if error occured
                 alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                },
                complete: function() {
                 
                }
            });
        });       
    });

    $('#certificateModal').on('hidden.bs.modal', function (e) {
        $('#certificateModal .modal-inner-content').html("");
        $('#certificateModal .modal-inner-loader').removeClass('displaynone');
     });
</script>
<script type="text/javascript">
    function valueChanged()
    {
        if ($('#enable_student_img').is(":checked"))
            $("#enableImageDiv").show();       
        else
            $("#enableImageDiv").hide();       
    }
</script>
<script>
    $(function(){
        $('#form1'). submit( function() {
            $("#submitbtn").button('loading');
        });
    })
</script>