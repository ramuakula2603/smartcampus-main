<div class="content-wrapper change-password-page">
    <section class="content-header">
        <h1>
            <i class="fa fa-key"></i> <?php echo $this->lang->line('change_password'); ?><small><?php //echo $this->lang->line('setting1'); ?></small>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <form action="<?php echo site_url('admin/admin/changepass') ?>"  id="passwordform" name="passwordform" method="post" data-parsley-validate="" class="form-horizontal form-label-left change-password-form" novalidate="">
                        <div class="change-password-card-header">
                            <h2 class="change-password-title"><?php echo $this->lang->line('change_password'); ?></h2>
                            <p class="change-password-subtitle"><?php echo $this->lang->line('update_your_password_for_enhanced_security') ?: 'Update your password for enhanced security.'; ?></p>
                        </div>

                        <?php 
                            if ($this->session->flashdata('msg')) {
                                echo $this->session->flashdata('msg');
                                $this->session->unset_userdata('msg'); 
                            } 
                        ?>                      
                        <?php
                        if (isset($error_message)) {
                            echo $error_message;
                        }
                        ?>
                        <?php echo $this->customlib->getCSRF(); ?>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12"><?php echo $this->lang->line('current_password'); ?><span class="required"></span>
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="password-input-wrapper">
                                    <input id="current_pass" name="current_pass" required="required" class="form-control col-md-7 col-xs-12" type="password" placeholder="<?php echo $this->lang->line('enter_your_current_password') ?: 'Enter your current password'; ?>" value="<?php echo set_value('currentr_password'); ?>">
                                    <button type="button" class="password-toggle" aria-label="Toggle current password visibility"><i class="fa fa-eye-slash"></i></button>
                                </div>
                                <span class="text-danger"><?php echo form_error('current_pass'); ?></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12"><?php echo $this->lang->line('new_password'); ?><span class="required"></span>
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="password-input-wrapper">
                                    <input id="new_pass" required="required" class="form-control col-md-7 col-xs-12" name="new_pass" type="password" placeholder="<?php echo $this->lang->line('enter_your_new_password') ?: 'Enter your new password'; ?>" value="<?php echo set_value('new_password'); ?>">
                                    <button type="button" class="password-toggle" aria-label="Toggle new password visibility"><i class="fa fa-eye-slash"></i></button>
                                </div>
                                <span class="text-danger"><?php echo form_error('new_pass'); ?></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12"><?php echo $this->lang->line('confirm_password'); ?><span class="required"></span>
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="password-input-wrapper">
                                    <input id="confirm_pass" name="confirm_pass" type="password" class="form-control col-md-7 col-xs-12" placeholder="<?php echo $this->lang->line('confirm_your_new_password') ?: 'Confirm your new password'; ?>" value="<?php echo set_value('confirm_password'); ?>">
                                    <button type="button" class="password-toggle" aria-label="Toggle confirm password visibility"><i class="fa fa-eye-slash"></i></button>
                                </div>
                                <span class="text-danger"><?php echo form_error('confirm_pass'); ?></span>
                            </div>
                        </div>

                        <div class="box-footer">
                            <div class="form-group">
                                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                    <button type="submit" class="btn btn-info"><?php echo $this->lang->line('change_password'); ?></button>
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
    (function ($) {
        function bindPasswordToggles() {
            $('.change-password-page .password-toggle').off('click').on('click', function () {
                var $btn = $(this);
                var $input = $btn.closest('.password-input-wrapper').find('input');
                var type = $input.attr('type') === 'password' ? 'text' : 'password';
                $input.attr('type', type);
                $btn.find('i').toggleClass('fa-eye').toggleClass('fa-eye-slash');
            });
        }

        $(document).ready(function () {
            bindPasswordToggles();
        });

    })(jQuery);
</script>