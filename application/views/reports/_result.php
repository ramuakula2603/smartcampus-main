<div class="row">
    <div class="col-md-12">
        <div class="box box-primary border0 mb0 margesection">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-search"></i> Result</h3>
            </div>
            <div class="">
                <ul class="reportlists">
                    <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('Reports/result/internal_result'); ?>">
                        <a href="<?php echo base_url(); ?>report/internal_result">
                            <i class="fa fa-file-text-o"></i> Internal Results
                        </a>
                    </li>
                    <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('Reports/result/external_result'); ?>">
                        <a href="<?php echo base_url(); ?>report/external_result">
                            <i class="fa fa-file-text-o"></i> External Results
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
