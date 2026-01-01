<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<style>
    /* General Styles */
    .box-body { padding: 15px; }
    .table { width: 100%; border-collapse: collapse; margin-bottom: 0; }
    .table th, .table td { 
        border: 1px solid #e3e3e3; 
        padding: 8px; 
        font-size: 12px;
    }
    .table th { 
        background-color: #f8f9fa; 
        font-weight: 600;
    }
    .text-right { text-align: right; }
    
    /* Report Header */
    .report-header {
        margin-bottom: 15px;
        padding: 10px;
        background: #fff;
        border: 1px solid #e3e3e3;
    }
    .report-header h3 {
        color: #2c3e50;
        font-size: 18px;
        margin: 0 0 5px 0;
        font-weight: bold;
    }
    .report-header p {
        color: #666;
        font-size: 12px;
        margin: 0;
    }

    /* Account Summary */
    .account-summary {
        margin: 10px 0;
        border: 1px solid #e3e3e3;
    }
    .account-summary th, .account-summary td {
        padding: 8px;
        font-size: 12px;
    }

    /* Daily Section */
    .daily-section {
        border: 1px solid #e3e3e3;
        margin-bottom: 10px;
        page-break-inside: avoid;
    }
    .date-header {
        background: #2c3e50;
        color: #fff;
        padding: 4px 8px;
        font-size: 12px;
        font-weight: 600;
        margin: 0;
    }
    .daily-balance-info {
        padding: 4px;
    }
    .daily-balance-info .table {
        margin: 0;
    }
    .side-by-side-tables {
        display: flex;
        gap: 4px;
        padding: 4px;
        overflow-x: auto;
        scrollbar-width: thin;
        scrollbar-color: #c1c1c1 #f1f1f1;
    }
    .table-container {
        flex: 1;
    }
    .table-header {
        background: #f8f9fa;
        padding: 4px;
        font-size: 12px;
        font-weight: 600;
        text-align: center;
        border-bottom: 1px solid #e3e3e3;
    }

    /* Transaction Tables */
    .table {
        margin: 0;
    }
    .table th, .table td {
        padding: 4px;
        font-size: 12px;
        border: 1px solid #e3e3e3;
    }
    .total-row {
        background: #f8f9fa;
        font-weight: 600;
    }

    /* Print Styles */
    @media print {
        @page {
            margin: 3mm;
            size: A4;
        }
        body {
            margin: 0;
            padding: 0;
            background: none;
        }
        .content-wrapper, .box, .box-body {
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
            background: none !important;
        }
        .daily-section {
            page-break-inside: avoid;
            margin: 2px 0 !important;
            padding: 0 !important;
            border: 1px solid #000 !important;
        }
        .date-header {
            background: #2c3e50 !important;
            color: #fff !important;
            padding: 2px 4px !important;
            font-size: 11px !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .daily-balance-info {
            padding: 2px !important;
        }
        .side-by-side-tables {
            gap: 2px !important;
            padding: 2px !important;
        }
        .table th, .table td {
            padding: 2px 4px !important;
            font-size: 10px !important;
            border: 1px solid #000 !important;
            line-height: 1.2 !important;
        }
        .table-header {
            background: #f8f9fa !important;
            padding: 2px !important;
            font-size: 11px !important;
            border-bottom: 1px solid #000 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        /* Remove all margins between elements */
        * {
            margin: 0 !important;
        }
        /* Force table layouts */
        .table {
            width: 100% !important;
            margin: 0 !important;
            border-collapse: collapse !important;
        }
        /* Ensure consistent cell heights */
        .table td, .table th {
            line-height: 1.2 !important;
        }
    }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-money"></i> <?php echo $this->lang->line('accountreport'); ?></h1>
    </section>
    <section class="content">
        <?php if ($this->session->flashdata('msg')) { ?>
            <?php echo $this->session->flashdata('msg'); $this->session->unset_userdata('msg'); ?>
        <?php } ?>
        
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><i class="fa fa-money"></i> <?php echo $this->lang->line('accounts') . " " . $this->lang->line('report'); ?></h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn print-btn" onclick="printDiv()">
                                <i class="fa fa-print"></i> <?php echo $this->lang->line('print'); ?>
                            </button>
                        </div>
                    </div>
                    
                    <div class="box-body">
                        <form role="form" action="<?php echo site_url('admin/accountreport/search') ?>" method="post">
                            <?php echo $this->customlib->getCSRF(); ?>
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('accountname'); ?></label><small class="req"> *</small>
                                        <select id="accountname_id" name="accountname_id" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($classlist as $class) { ?>
                                                <option value="<?php echo $class['id'] ?>" <?php if (set_value('accountname_id') == $class['id']) echo "selected=selected"; ?>><?php echo $class['name'] ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('accountname_id'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="date_from"><?php echo $this->lang->line('date_from'); ?></label><small class="req"> *</small>
                                        <input id="date_from" name="date_from" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date_from'); ?>" readonly="readonly"/>
                                        <span class="text-danger"><?php echo form_error('date_from'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="date_to"><?php echo $this->lang->line('date_to'); ?></label><small class="req"> *</small>
                                        <input id="date_to" name="date_to" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date_to'); ?>" readonly="readonly"/>
                                        <span class="text-danger"><?php echo form_error('date_to'); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="submit" name="search" value="search_filter" class="btn btn-primary btn-sm pull-right"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                            </div>
                        </form>
                    </div>

                    <?php if (isset($daily_data)) { ?>
                    <div class="box-body">
                        <div id="accountsReport">
                            <!-- Account Summary Header -->
                            <div class="report-header text-center">
                                <h3 style="margin-bottom: 15px; font-weight: bold;">
                                    <?php 
                                        foreach ($classlist as $class) {
                                            if (set_value('accountname_id') == $class['id']) {
                                                echo $class['name'];
                                                break;
                                            }
                                        }
                                    ?>
                                </h3>
                                <p style="font-size: 14px;">
                                    <strong><?php echo $this->lang->line('date_range'); ?>:</strong> 
                                    <?php 
                                    if (isset($startdate) && isset($enddate)) {
                                        echo $startdate . " - " . $enddate;
                                    }
                                    ?>
                                </p>
                            </div>

                            <!-- Overall Account Balance -->
                            <div class="table-responsive" style="margin: 20px 0;">
                                <table class="table table-bordered account-summary">
                                    <tr>
                                        <th colspan="4" class="text-center" style="background-color: #f5f5f5;">
                                            <?php echo $this->lang->line('account_summary'); ?>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th><?php echo $this->lang->line('opening_balance'); ?></th>
                                        <td class="text-right"><?php echo $currency_symbol . number_format($openaccountbalance, 2); ?></td>
                                        <th><?php echo $this->lang->line('closing_balance'); ?></th>
                                        <td class="text-right"><?php echo $currency_symbol . number_format($closeaccountblance, 2); ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $this->lang->line('total_credit'); ?></th>
                                        <td class="text-right"><?php echo $currency_symbol . number_format($total_credit, 2); ?></td>
                                        <th><?php echo $this->lang->line('total_debit'); ?></th>
                                        <td class="text-right"><?php echo $currency_symbol . number_format($total_debit, 2); ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $this->lang->line('transaction_balance'); ?></th>
                                        <td class="text-right"><?php 
                                            $transaction_balance = $total_credit - $total_debit;
                                            echo $currency_symbol . number_format($transaction_balance, 2); 
                                        ?></td>
                                        <th><?php echo $this->lang->line('balance_difference'); ?></th>
                                        <td class="text-right"><?php 
                                            $expected_closing = $openaccountbalance + $transaction_balance;
                                            $balance_difference = $closeaccountblance - $expected_closing;
                                            $color = ($balance_difference != 0) ? 'color: red;' : '';
                                            echo '<span style="' . $color . '">' . $currency_symbol . number_format($balance_difference, 2) . '</span>';
                                        ?></td>
                                    </tr>
                                </table>
                                <?php if ($balance_difference != 0) { ?>
                                <div class="alert alert-warning" style="margin-top: 10px; font-size: 12px;">
                                    <i class="fa fa-info-circle"></i> <?php echo $this->lang->line('balance_note'); ?>:
                                    <ul style="margin-top: 5px; margin-bottom: 0;">
                                        <li><?php echo $this->lang->line('opening_balance'); ?>: <?php echo $currency_symbol . number_format($openaccountbalance, 2); ?></li>
                                        <li><?php echo $this->lang->line('transaction_balance'); ?> (<?php echo $this->lang->line('credit'); ?> - <?php echo $this->lang->line('debit'); ?>): <?php echo $currency_symbol . number_format($transaction_balance, 2); ?></li>
                                        <li><?php echo $this->lang->line('expected_closing'); ?>: <?php echo $currency_symbol . number_format($expected_closing, 2); ?></li>
                                        <li><?php echo $this->lang->line('actual_closing'); ?>: <?php echo $currency_symbol . number_format($closeaccountblance, 2); ?></li>
                                        <li style="color: red;"><?php echo $this->lang->line('difference'); ?>: <?php echo $currency_symbol . number_format($balance_difference, 2); ?></li>
                                    </ul>
                                </div>
                                <?php } ?>
                            </div>

                            <?php 
                            $first_day = true;
                            foreach ($daily_data as $date => $day_data) { 
                            ?>
                            <div class="daily-section">
                                <div class="date-header"><?php echo date('d M Y', strtotime($date)); ?></div>
                                <div class="daily-balance-info">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="25%"><?php echo $this->lang->line('opening_balance'); ?></th>
                                            <td width="25%" class="text-right"><?php echo $currency_symbol . number_format($day_data['opening_balance'], 2); ?></td>
                                            <th width="25%"><?php echo $this->lang->line('closing_balance'); ?></th>
                                            <td width="25%" class="text-right"><?php echo $currency_symbol . number_format($day_data['closing_balance'], 2); ?></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="side-by-side-tables">
                                    <div class="table-container">
                                        <div class="table-header"><?php echo $this->lang->line('credit'); ?></div>
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th><?php echo $this->lang->line('type'); ?></th>
                                                    <th><?php echo $this->lang->line('receipt_no'); ?></th>
                                                    <th><?php echo $this->lang->line('description'); ?></th>
                                                    <th class="text-right"><?php echo $this->lang->line('amount'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $total_credit = 0;
                                                if (isset($day_data['transactions']) && !empty($day_data['transactions'])) {
                                                    foreach ($day_data['transactions'] as $transaction) {
                                                        if ($transaction['status'] == 'credit') {
                                                            $total_credit += $transaction['amount'];
                                                ?>
                                                <tr>
                                                    <td><?php echo $transaction['type']; ?></td>
                                                    <td><?php echo $transaction['receiptid']; ?></td>
                                                    <td><?php echo $transaction['description']; ?></td>
                                                    <td class="text-right"><?php echo $currency_symbol . number_format($transaction['amount'], 2); ?></td>
                                                </tr>
                                                <?php 
                                                        }
                                                    } 
                                                }
                                                ?>
                                                <tr class="total-row">
                                                    <td colspan="3" class="text-right"><strong><?php echo $this->lang->line('total'); ?></strong></td>
                                                    <td class="text-right"><strong><?php echo $currency_symbol . number_format($total_credit, 2); ?></strong></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="table-container">
                                        <div class="table-header"><?php echo $this->lang->line('debit'); ?></div>
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th><?php echo $this->lang->line('type'); ?></th>
                                                    <th><?php echo $this->lang->line('receipt_no'); ?></th>
                                                    <th><?php echo $this->lang->line('description'); ?></th>
                                                    <th class="text-right"><?php echo $this->lang->line('amount'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $total_debit = 0;
                                                if (isset($day_data['transactions']) && !empty($day_data['transactions'])) {
                                                    foreach ($day_data['transactions'] as $transaction) {
                                                        if ($transaction['status'] == 'debit') {
                                                            $total_debit += $transaction['amount'];
                                                ?>
                                                <tr>
                                                    <td><?php echo $transaction['type']; ?></td>
                                                    <td><?php echo $transaction['receiptid']; ?></td>
                                                    <td><?php echo $transaction['description']; ?></td>
                                                    <td class="text-right"><?php echo $currency_symbol . number_format($transaction['amount'], 2); ?></td>
                                                </tr>
                                                <?php 
                                                        }
                                                    }
                                                }
                                                ?>
                                                <tr class="total-row">
                                                    <td colspan="3" class="text-right"><strong><?php echo $this->lang->line('total'); ?></strong></td>
                                                    <td class="text-right"><strong><?php echo $currency_symbol . number_format($total_debit, 2); ?></strong></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <?php 
                            $first_day = false;
                            } 
                            ?>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
function printDiv() {
    var divToPrint = document.getElementById('accountsReport');
    var newWin = window.open('', 'Print-Window');
    newWin.document.open();
    newWin.document.write('<html><head><title>Account Report</title>');
    newWin.document.write('<link rel="stylesheet" href="<?php echo base_url(); ?>backend/bootstrap/css/bootstrap.min.css">');
    newWin.document.write('<style>');
    newWin.document.write(`
        @page {
            margin: 3mm;
            size: A4;
        }
        body {
            margin: 0;
            padding: 0;
            background: none;
        }
        * {
            margin: 0 !important;
        }
        .daily-section {
            page-break-inside: avoid;
            margin: 2px 0 !important;
            padding: 0 !important;
            border: 1px solid #000 !important;
        }
        .date-header {
            background: #2c3e50 !important;
            color: #fff !important;
            padding: 2px 4px !important;
            font-size: 11px !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .daily-balance-info {
            padding: 2px !important;
        }
        .side-by-side-tables {
            display: flex;
            gap: 2px !important;
            padding: 2px !important;
        }
        .table-container {
            flex: 1;
        }
        .table {
            width: 100% !important;
            margin: 0 !important;
            border-collapse: collapse !important;
        }
        .table th, .table td {
            padding: 2px 4px !important;
            font-size: 10px !important;
            border: 1px solid #000 !important;
            line-height: 1.2 !important;
        }
        .table-header {
            background: #f8f9fa !important;
            padding: 2px !important;
            font-size: 11px !important;
            border-bottom: 1px solid #000 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    `);
    newWin.document.write('</style></head><body>');
    newWin.document.write(divToPrint.innerHTML);
    newWin.document.write('</body></html>');
    newWin.document.close();
    setTimeout(function(){
        newWin.print();
        newWin.close();
    }, 500);
}
</script>
