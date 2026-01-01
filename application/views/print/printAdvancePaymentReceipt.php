<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>

<html lang="en">

<head>
    <title><?php echo $this->lang->line('advance_payment_receipt'); ?></title>
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/AdminLTE.min.css">
    <style>
        .advance-receipt-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .advance-amount {
            font-size: 2em;
            font-weight: bold;
            text-align: center;
            margin: 10px 0;
        }
        
        .receipt-details {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        
        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <?php
    $print_copy = explode(',', $settinglist[0]['is_duplicate_fees_invoice']);
    ?>
    <div class="container">
        <div class="row">
            <div id="content" class="col-lg-12 col-sm-12">

                <?php
                if (in_array('0', $print_copy)) {
                    ?>
                    <div class="invoice">
                        <div class="row header">
                            <div class="col-sm-12">
                                <img src="<?php echo $this->media_storage->getImageURL('/uploads/print_headerfooter/student_receipt/' . $this->setting_model->get_receiptheader()); ?>"
                                    style="height: 100px;width: 100%;">
                            </div>
                        </div>

                        <div class="advance-receipt-header">
                            <div class="row">
                                <div class="col-md-8">
                                    <h3><?php echo $this->lang->line('advance_payment_receipt'); ?></h3>
                                    <p><?php echo $this->lang->line('office_copy'); ?></p>
                                </div>
                                <div class="col-md-4 text-right">
                                    <h4><?php echo $this->lang->line('receipt_no'); ?>: <?php echo $advance_payment->invoice_id; ?></h4>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-6 text-left">
                                <address>
                                    <strong><?php echo $this->customlib->getFullName($advance_payment->firstname, $advance_payment->middlename, $advance_payment->lastname, $sch_setting->middlename, $sch_setting->lastname); ?></strong>
                                    <?php echo " (" . $advance_payment->admission_no . ")"; ?>
                                    <br>
                                    <?php echo $this->lang->line('class'); ?>: <?php echo $advance_payment->class . " (" . $advance_payment->section . ")"; ?>
                                    <br>
                                    <?php echo $this->lang->line('father_name'); ?>: <?php echo $advance_payment->father_name; ?>
                                </address>
                            </div>
                            <div class="col-xs-6 text-right">
                                <address>
                                    <strong><?php echo $sch_setting->name; ?></strong><br>
                                    <?php echo $sch_setting->address; ?><br>
                                    <?php echo $sch_setting->phone; ?>
                                </address>
                            </div>
                        </div>

                        <div class="receipt-details">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-bordered">
                                        <tr>
                                            <td><strong><?php echo $this->lang->line('payment_date'); ?>:</strong></td>
                                            <td><?php echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($advance_payment->payment_date)); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong><?php echo $this->lang->line('payment_mode'); ?>:</strong></td>
                                            <td><?php echo ucfirst($advance_payment->payment_mode); ?></td>
                                        </tr>
                                        <?php if (!empty($advance_payment->reference_no)) { ?>
                                        <tr>
                                            <td><strong><?php echo $this->lang->line('reference_no'); ?>:</strong></td>
                                            <td><?php echo $advance_payment->reference_no; ?></td>
                                        </tr>
                                        <?php } ?>
                                        <tr>
                                            <td><strong><?php echo $this->lang->line('collected_by'); ?>:</strong></td>
                                            <td><?php echo $advance_payment->collected_by; ?></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <div class="advance-amount">
                                        <?php echo $currency_symbol . amountFormat($advance_payment->amount); ?>
                                    </div>
                                    <p class="text-center"><strong><?php echo $this->lang->line('advance_amount_paid'); ?></strong></p>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($advance_payment->description)) { ?>
                        <div class="row">
                            <div class="col-sm-12">
                                <strong><?php echo $this->lang->line('note'); ?>:</strong> <?php echo $advance_payment->description; ?>
                            </div>
                        </div>
                        <?php } ?>

                        <div class="row header">
                            <div class="col-sm-12">
                                <?php echo $this->setting_model->get_receiptfooter(); ?>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>

                <?php
                if (in_array('1', $print_copy)) {
                    if (!$sch_setting->single_page_print) {
                        echo '<div class="page-break"></div>';
                    } else {
                        echo "<br><br><hr style='width:100%;'>";
                    }
                    ?>

                    <div class="invoice">
                        <div class="row header">
                            <div class="col-sm-12">
                                <img src="<?php echo $this->media_storage->getImageURL('/uploads/print_headerfooter/student_receipt/' . $this->setting_model->get_receiptheader()); ?>"
                                    style="height: 100px;width: 100%;">
                            </div>
                        </div>

                        <div class="advance-receipt-header">
                            <div class="row">
                                <div class="col-md-8">
                                    <h3><?php echo $this->lang->line('advance_payment_receipt'); ?></h3>
                                    <p><?php echo $this->lang->line('student_copy'); ?></p>
                                </div>
                                <div class="col-md-4 text-right">
                                    <h4><?php echo $this->lang->line('receipt_no'); ?>: <?php echo $advance_payment->invoice_id; ?></h4>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-6 text-left">
                                <address>
                                    <strong><?php echo $this->customlib->getFullName($advance_payment->firstname, $advance_payment->middlename, $advance_payment->lastname, $sch_setting->middlename, $sch_setting->lastname); ?></strong>
                                    <?php echo " (" . $advance_payment->admission_no . ")"; ?>
                                    <br>
                                    <?php echo $this->lang->line('class'); ?>: <?php echo $advance_payment->class . " (" . $advance_payment->section . ")"; ?>
                                    <br>
                                    <?php echo $this->lang->line('father_name'); ?>: <?php echo $advance_payment->father_name; ?>
                                </address>
                            </div>
                            <div class="col-xs-6 text-right">
                                <address>
                                    <strong><?php echo $sch_setting->name; ?></strong><br>
                                    <?php echo $sch_setting->address; ?><br>
                                    <?php echo $sch_setting->phone; ?>
                                </address>
                            </div>
                        </div>

                        <div class="receipt-details">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-bordered">
                                        <tr>
                                            <td><strong><?php echo $this->lang->line('payment_date'); ?>:</strong></td>
                                            <td><?php echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($advance_payment->payment_date)); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong><?php echo $this->lang->line('payment_mode'); ?>:</strong></td>
                                            <td><?php echo ucfirst($advance_payment->payment_mode); ?></td>
                                        </tr>
                                        <?php if (!empty($advance_payment->reference_no)) { ?>
                                        <tr>
                                            <td><strong><?php echo $this->lang->line('reference_no'); ?>:</strong></td>
                                            <td><?php echo $advance_payment->reference_no; ?></td>
                                        </tr>
                                        <?php } ?>
                                        <tr>
                                            <td><strong><?php echo $this->lang->line('collected_by'); ?>:</strong></td>
                                            <td><?php echo $advance_payment->collected_by; ?></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <div class="advance-amount">
                                        <?php echo $currency_symbol . amountFormat($advance_payment->amount); ?>
                                    </div>
                                    <p class="text-center"><strong><?php echo $this->lang->line('advance_amount_paid'); ?></strong></p>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($advance_payment->description)) { ?>
                        <div class="row">
                            <div class="col-sm-12">
                                <strong><?php echo $this->lang->line('note'); ?>:</strong> <?php echo $advance_payment->description; ?>
                            </div>
                        </div>
                        <?php } ?>

                        <div class="row header">
                            <div class="col-sm-12">
                                <?php echo $this->setting_model->get_receiptfooter(); ?>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>

            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <footer>
    </footer>
</body>

</html>
