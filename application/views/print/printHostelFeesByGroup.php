<?php $currency_symbol = $this->customlib->getSchoolCurrencyFormat(); ?>
<style type="text/css">
    .page-break	{ display: block; page-break-before: always; }
    @media print {
        .page-break	{ display: block; page-break-before: always; }
        .col-sm-1, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-sm-10, .col-sm-11, .col-sm-12 {
            float: left;
        }
        .col-sm-12 {
            width: 100%;
        }
        .col-sm-11 {
            width: 91.66666667%;
        }
        .col-sm-10 {
            width: 83.33333333%;
        }
        .col-sm-9 {
            width: 75%;
        }
        .col-sm-8 {
            width: 66.66666667%;
        }
        .col-sm-7 {
            width: 58.33333333%;
        }
        .col-sm-6 {
            width: 50%;
        }
        .col-sm-5 {
            width: 41.66666667%;
        }
        .col-sm-4 {
            width: 33.33333333%;
        }
        .col-sm-3 {
            width: 25%;
        }
        .col-sm-2 {
            width: 16.66666667%;
        }
        .col-sm-1 {
            width: 8.33333333%;
        }
    }
</style>

<html lang="en">
    <head>
        <title><?php echo $this->lang->line('fees_receipt'); ?></title>
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/bootstrap/css/bootstrap.min.css"> 
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/AdminLTE.min.css">
    </head>
    <body>       
        <div class="container"> 
              <?php 
$print_copy=explode(',', $settinglist[0]['is_duplicate_fees_invoice']);
         ?>

           <?php 
if(in_array('0', $print_copy)){
    ?>

            <div class="row">
                <div id="content" class="col-lg-12 col-sm-12 ">
                    <div class="invoice">
                        <div class="row header ">
                            <div class="col-sm-12">
                                <img  src="<?php echo $this->media_storage->getImageURL('/uploads/print_headerfooter/student_receipt/'.$this->setting_model->get_receiptheader()); ?>" style="height: 100px;width: 100%;">
                            </div>
                        </div> 
                        
                        <div class="row">
                            <div class="col-md-12 text text-center">
                                <?php echo $this->lang->line('office_copy'); ?>
                            </div>
                        </div>
                          
                        <div class="row">                           
                            <div class="col-xs-6 text-left">
                                <br/>
                                <address>
                                    <strong><?php
                                   echo $this->customlib->getFullName($feeList->firstname,$feeList->middlename,$feeList->lastname,$sch_setting->middlename,$sch_setting->lastname);
                                      ?></strong><?php echo " (".$feeList->admission_no.")"; ?> <br>

                                    <?php echo $this->lang->line('father_name'); ?>: <?php echo $feeList->father_name; ?><br>
                                    <?php echo $this->lang->line('class'); ?>: <?php echo $feeList->class . " (" . $feeList->section . ")"; ?>
                                </address>
                            </div>
                            <div class="col-xs-6 text-right">
                                <br/>
                                <address>
                                    <strong><?php echo $this->lang->line('date') ; ?>: <?php
                                        $date = date('d-m-Y');
                                        echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($date));
                                        ?></strong><br/>
                                </address>                               
                            </div>
                        </div>
                        <hr style="margin-top: 0px;margin-bottom: 0px;" />
                        <div class="row">
                            <?php
                            // Debug: Show what data we received
                            error_log("HOSTEL PRINT TEMPLATE DEBUG - feeList: " . print_r($feeList, true));
                            
                            // Check if we have feeList data
                            if (!empty($feeList) && isset($feeList->firstname)) {
                                ?>

                                <table class="table table-striped table-responsive" style="font-size: 8pt;">
                                    <thead>
                                    <th><?php echo $this->lang->line('fees_group'); ?></th>
                                    <th><?php echo $this->lang->line('fees_code'); ?></th>
                                    <th  class=""><?php echo $this->lang->line('due_date'); ?></th>
                                    <th class=""><?php echo $this->lang->line('status'); ?></th>
                                    <th  class="text text-right"><?php echo $this->lang->line('amount'); ?></th>
                                    <th  class="text text-center"><?php echo $this->lang->line('payment_id'); ?></th>
                                    <th  class="text text-center"><?php echo $this->lang->line('mode'); ?></th>
                                    <th  class=""><?php echo $this->lang->line('date'); ?></th>
                                    <th  class="text text-right"><?php echo $this->lang->line('paid'); ?></th>
                                    <th  class="text text-right"><?php echo $this->lang->line('fine'); ?></th>
                                    <th class="text text-right" ><?php echo $this->lang->line('discount'); ?></th>
                                    <th  class="text text-right"><?php echo $this->lang->line('balance'); ?></th>
                                    <th></th>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $fee_discount = 0;
                                        $fee_paid = 0;
                                        $fee_fine = 0;
                                      
                                        if (!empty($feeList->amount_detail) && $feeList->amount_detail != '0') {
                                            $fee_deposits = json_decode(($feeList->amount_detail));

                                            if (is_array($fee_deposits) || is_object($fee_deposits)) {
                                                foreach ($fee_deposits as $fee_deposits_key => $fee_deposits_value) {
                                                    $fee_paid = $fee_paid + $fee_deposits_value->amount;
                                                    $fee_discount = $fee_discount + $fee_deposits_value->amount_discount;
                                                    $fee_fine = $fee_fine + $fee_deposits_value->amount_fine;
                                                }
                                            }
                                        }
                                        $feetype_balance = $feeList->fees - ($fee_paid + $fee_discount);
                                        ?>
                                        <tr  class="dark-gray">
                                            <td><?php echo $this->lang->line("hostel_fees"); ?></td>
                                            <td><?php echo isset($feeList->month) ? $this->lang->line(strtolower($feeList->month)) : ''; ?></td>
                                            <td class="">
                                                <?php
                                                if (isset($feeList->due_date) && $feeList->due_date != "0000-00-00") {
                                                    echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($feeList->due_date));
                                                }
                                                ?>
                                            </td>
                                            <td class="">
                                                <?php
                                                if ($feetype_balance == 0) {
                                                    echo $this->lang->line('paid');
                                                } else if (!empty($feeList->amount_detail) && $feeList->amount_detail != '0') {
                                                    echo $this->lang->line('partial');
                                                } else {
                                                    echo $this->lang->line('unpaid');
                                                }
                                                ?>
                                            </td>
                                            <td class="text text-right"><?php 
                                            echo $currency_symbol . (isset($feeList->fees) ? $feeList->fees : '0'); 

                                            if (isset($feeList->due_date) && ($feeList->due_date != "0000-00-00" && $feeList->due_date != null) && (strtotime($feeList->due_date) < strtotime(date('Y-m-d')))) {
                                                $hr_fine_amount = isset($feeList->fine_amount) ? $feeList->fine_amount : 0;
                                                if (isset($feeList->fine_type) && $feeList->fine_type != "" && $feeList->fine_type == "percentage") {
                                                    $hr_fine_amount = percentageAmount($feeList->fees, $feeList->fine_percentage);
                                                }
                                                if ($hr_fine_amount > 0) {
                                                    ?>
                                                    <span  class="text text-danger"><?php echo " + " . $currency_symbol.amountFormat($hr_fine_amount); ?></span>
                                                    <?php
                                                }
                                            }
                                            ?></td>

                                            <td colspan="3"></td>
                                            <td class="text text-right"><?php
                                                echo ($currency_symbol . number_format($fee_paid, 2, '.', ''));
                                                ?></td>
                                            <td class="text text-right"><?php
                                                echo ($currency_symbol . number_format($fee_fine, 2, '.', ''));
                                                ?></td>
                                            <td class="text text-right"><?php
                                                echo ($currency_symbol . number_format($fee_discount, 2, '.', ''));
                                                ?></td>
                                            <td class="text text-right"><?php
                                                if ($feetype_balance > 0) {
                                                    echo ($currency_symbol . number_format($feetype_balance, 2, '.', ''));
                                                }
                                                ?>
                                            </td>
                                        </tr>

                                        <?php
                                        if (!empty($feeList->amount_detail) && $feeList->amount_detail != '0') {
                                            $fee_deposits = json_decode(($feeList->amount_detail));
                                            if (is_array($fee_deposits) || is_object($fee_deposits)) {
                                                foreach ($fee_deposits as $fee_deposits_key => $fee_deposits_value) {
                                                    ?>
                                                    <tr class="white-td">
                                                        <td colspan="5" class="text-right"><img src="<?php echo $this->media_storage->getImageURL('backend/images/table-arrow.png');?>" alt="" /></td>
                                                        <td class="text text-center">
                                                            <?php echo (isset($feeList->student_fees_deposite_id) ? $feeList->student_fees_deposite_id : '0') . "/" . (isset($fee_deposits_value->inv_no) ? $fee_deposits_value->inv_no : ''); ?>
                                                        </td>
                                                        <td class="text text-center"><?php echo isset($fee_deposits_value->payment_mode) ? $this->lang->line(strtolower($fee_deposits_value->payment_mode)) : ''; ?></td>
                                                        <td class="text text-center">
                                                            <?php echo isset($fee_deposits_value->date) ? date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($fee_deposits_value->date)) : ''; ?>
                                                        </td>
                                                        <td class="text text-right"><?php echo ($currency_symbol . number_format((isset($fee_deposits_value->amount) ? $fee_deposits_value->amount : 0), 2, '.', '')); ?></td>
                                                        <td class="text text-right"><?php echo ($currency_symbol . number_format((isset($fee_deposits_value->amount_fine) ? $fee_deposits_value->amount_fine : 0), 2, '.', '')); ?></td>
                                                        <td class="text text-right"><?php echo ($currency_symbol . number_format((isset($fee_deposits_value->amount_discount) ? $fee_deposits_value->amount_discount : 0), 2, '.', '')); ?></td>
                                                    </tr>
                                                    <?php
                                                }
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                                <?php
                            } else {
                                ?>
                                <div class="alert alert-warning">
                                    <h4>Debug Information:</h4>
                                    <p><strong>feeList is empty or invalid:</strong></p>
                                    <pre><?php echo print_r($feeList, true); ?></pre>
                                </div>
                                <table class="table table-striped table-responsive" style="font-size: 8pt;">
                                    <tbody>
                                        <tr>
                                            <td colspan="11" class="text-danger text-center">
                                                <?php echo $this->lang->line('no_transaction_found'); ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="row header ">
                    <div class="col-sm-12">
                        <?php echo $this->setting_model->get_receiptfooter(); ?>
                    </div>
                </div>  
            </div>
    <?php
}
?>

<?php
if(in_array('1', $print_copy)){ 
    if (!$sch_setting->single_page_print) {   
        ?>
        <div class="page-break"></div>
        <?php 
    } else { 
        echo "<br><br><hr style='width:100%;'>"; 
    } ?>
    <div class="row">
        <div id="content" class="col-lg-12 col-sm-12 ">
            <div class="invoice">
                <div class="row header ">
                    <div class="col-sm-12">
                        <img  src="<?php echo $this->media_storage->getImageURL('/uploads/print_headerfooter/student_receipt/'.$this->setting_model->get_receiptheader()); ?>" style="height: 100px;width: 100%;">
                    </div>
                </div>
               
                <div class="row">
                    <div class="col-md-12 text text-center">
                        <?php echo $this->lang->line('student_copy'); ?>
                    </div>
                </div>
                
                <div class="row">                           
                    <div class="col-xs-6">
                        <br/>
                        <address>
                            <strong><?php echo $this->customlib->getFullName($feeList->firstname, $feeList->middlename, $feeList->lastname, $sch_setting->middlename, $sch_setting->lastname); ?></strong><?php echo " (" . $feeList->admission_no . ")"; ?> <br>
                            <?php echo $this->lang->line('father_name'); ?>: <?php echo $feeList->father_name; ?><br>
                            <?php echo $this->lang->line('class'); ?>: <?php echo $feeList->class . " (" . $feeList->section . ")"; ?>
                        </address>
                    </div>
                    <div class="col-xs-6 text-right">
                        <br/>
                        <address>
                            <strong>Date: <?php
                                $date = date('d-m-Y');
                                echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($date));
                                ?></strong><br/>
                        </address>                               
                    </div>
                </div>
                <hr style="margin-top: 0px;margin-bottom: 0px;" />
                <div class="row">
                    <?php
                    if (!empty($feeList)) {
                        ?>
                        <table class="table table-striped table-responsive" style="font-size: 8pt;">
                            <thead>
                            <th><?php echo $this->lang->line('fees_group'); ?></th>
                            <th><?php echo $this->lang->line('fees_code'); ?></th>
                            <th  class=""><?php echo $this->lang->line('due_date'); ?></th>
                            <th class=""><?php echo $this->lang->line('status'); ?></th>
                            <th  class="text text-right"><?php echo $this->lang->line('amount'); ?></th>
                            <th  class="text text-center"><?php echo $this->lang->line('payment_id'); ?></th>
                            <th  class="text text-center"><?php echo $this->lang->line('mode'); ?></th>
                            <th  class=""><?php echo $this->lang->line('date'); ?></th>
                            <th  class="text text-right"><?php echo $this->lang->line('paid'); ?></th>
                            <th  class="text text-right"><?php echo $this->lang->line('fine'); ?></th>
                            <th class="text text-right" ><?php echo $this->lang->line('discount'); ?></th>
                            <th  class="text text-right"><?php echo $this->lang->line('balance'); ?></th>
                            <th></th>
                            </thead>
                            <tbody>
                                <?php
                                if (empty($feeList)) {
                                    ?>
                                    <tr>
                                        <td colspan="11" class="text-danger text-center">
                                            <?php echo $this->lang->line('no_transaction_found'); ?>
                                        </td>
                                    </tr>
                                    <?php
                                } else {
                                    $fee_discount = 0;
                                    $fee_paid = 0;
                                    $fee_fine = 0;
                                   
                                    if (!empty($feeList->amount_detail)) {
                                        $fee_deposits = json_decode(($feeList->amount_detail));
                                        foreach ($fee_deposits as $fee_deposits_key => $fee_deposits_value) {
                                            $fee_paid = $fee_paid + $fee_deposits_value->amount;
                                            $fee_discount = $fee_discount + $fee_deposits_value->amount_discount;
                                            $fee_fine = $fee_fine + $fee_deposits_value->amount_fine;
                                        }
                                    }
                                    $feetype_balance = $feeList->fees - ($fee_paid + $fee_discount);
                                    ?>
                                    <tr  class="dark-gray">
                                        <td><?php echo $this->lang->line("hostel_fees"); ?></td>
                                        <td><?php echo $this->lang->line(strtolower($feeList->month)); ?></td>
                                        <td class="">
                                            <?php
                                            if ($feeList->due_date == "0000-00-00") {
                                                
                                            } else {
                                                echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($feeList->due_date));
                                            }
                                            ?>
                                        </td>
                                        <td class="">
                                            <?php
                                            if ($feetype_balance == 0) {
                                                echo $this->lang->line('paid');
                                            } else if (!empty($feeList->amount_detail)) {
                                                echo $this->lang->line('partial');
                                            } else {
                                                echo $this->lang->line('unpaid');
                                            }
                                            ?>
                                        </td>
                                        <td class="text text-right"><?php 
                                        echo $currency_symbol . $feeList->fees; 

                                        if (($feeList->due_date != "0000-00-00" && $feeList->due_date != null) && (strtotime($feeList->due_date) < strtotime(date('Y-m-d')))) {
                                            $hr_fine_amount = $feeList->fine_amount;
                                            if ($feeList->fine_type != "" && $feeList->fine_type == "percentage") {
                                                $hr_fine_amount = percentageAmount($feeList->fees, $feeList->fine_percentage);
                                            }
                                            ?>
                                            <span  class="text text-danger"><?php echo " + " . $currency_symbol.amountFormat($hr_fine_amount); ?></span>
                                            <?php
                                        }
                                        ?></td>

                                        <td colspan="3"></td>
                                        <td class="text text-right"><?php
                                            echo ($currency_symbol . number_format($fee_paid, 2, '.', ''));
                                            ?></td>
                                        <td class="text text-right"><?php
                                            echo ($currency_symbol . number_format($fee_fine, 2, '.', ''));
                                            ?></td>
                                        <td class="text text-right"><?php
                                            echo ($currency_symbol . number_format($fee_discount, 2, '.', ''));
                                            ?></td>
                                        <td class="text text-right"><?php
                                            if ($feetype_balance > 0) {
                                                echo ($currency_symbol . number_format($feetype_balance, 2, '.', ''));
                                            }
                                            ?>
                                        </td>
                                    </tr>

                                    <?php
                                    $fee_deposits = json_decode(($feeList->amount_detail));
                                    if (is_object($fee_deposits)) {
                                        foreach ($fee_deposits as $fee_deposits_key => $fee_deposits_value) {
                                            ?>
                                            <tr class="white-td">
                                                <td colspan="5" class="text-right"><img src="<?php echo $this->media_storage->getImageURL('backend/images/table-arrow.png');?>" alt="" /></td>
                                                <td class="text text-center">
                                                    <?php echo $feeList->student_fees_deposite_id . "/" . $fee_deposits_value->inv_no; ?>
                                                </td>
                                                <td class="text text-center"><?php echo $this->lang->line(strtolower($fee_deposits_value->payment_mode)); ?></td>
                                                <td class="text text-center">
                                                    <?php echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($fee_deposits_value->date)); ?>
                                                </td>
                                                <td class="text text-right"><?php echo ($currency_symbol . number_format($fee_deposits_value->amount, 2, '.', '')); ?></td>
                                                <td class="text text-right"><?php echo ($currency_symbol . number_format($fee_deposits_value->amount_fine, 2, '.', '')); ?></td>
                                                <td class="text text-right"><?php echo ($currency_symbol . number_format($fee_deposits_value->amount_discount, 2, '.', '')); ?></td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="row header ">
            <div class="col-sm-12">
                <?php echo $this->setting_model->get_receiptfooter(); ?>
            </div>
        </div>  
    </div>
    <?php
}
?>
        </div>
    </body>
</html>
