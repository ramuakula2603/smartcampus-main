<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<style>
    .box-body {
        padding: 20px;
    }
    .download-buttons {
        margin-bottom: 20px;
    }
    .download-btn {
        margin-right: 10px;
        padding: 6px 12px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }
    th {
        background-color: #f5f5f5;
    }
    .total-row {
        font-weight: bold;
        background-color: #f9f9f9;
    }
    .daily-section {
        margin-bottom: 30px;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 15px;
        background-color: #fff;
    }
    .date-header {
        background-color: #f8f9fa;
        padding: 10px;
        margin: -15px -15px 15px -15px;
        border-bottom: 1px solid #ddd;
        border-radius: 4px 4px 0 0;
        font-weight: bold;
        color: #333;
    }
    .daily-balance-info {
        margin-bottom: 15px;
    }
    .daily-balance-info table {
        margin-bottom: 0;
    }
    .daily-balance-info th {
        background-color: #f5f5f5;
        text-align: center;
    }
    .daily-balance-info td {
        text-align: center;
        font-weight: bold;
    }
    .report-header {
        margin-bottom: 20px;
        padding: 15px;
        background-color: #f8f9fa;
        border-radius: 4px;
    }
    .report-header h3 {
        margin: 0 0 10px 0;
        color: #333;
        font-weight: bold;
    }
    .report-header p {
        margin: 0;
        color: #666;
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
                            <button type="button" class="btn btn-primary btn-sm" onclick="printDiv()"><i class="fa fa-print"></i> <?php echo $this->lang->line('print'); ?></button>
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
                            <div class="row">
                                <div class="col-md-12">
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
                                        <p style="font-size: 14px; color: #666;">
                                            <strong>Date Range:</strong> <?php echo set_value('date_from'); ?> - <?php echo set_value('date_to'); ?>
                                        </p>
                                        <hr style="margin: 15px 0; border-color: #ddd;">
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <tr>
                                                <th colspan="2"><?php echo $this->lang->line('account_details'); ?></th>
                                            </tr>
                                            <tr>
                                                <td><?php echo $this->lang->line('opening_balance'); ?></td>
                                                <td><?php echo $currency_symbol . number_format($openaccountbalance, 2); ?></td>
                                            </tr>
                                            <tr>
                                                <td><?php echo $this->lang->line('closing_balance'); ?></td>
                                                <td><?php echo $currency_symbol . number_format($closeaccountblance, 2); ?></td>
                                            </tr>
                                        </table>
                                        
                                        <?php foreach ($daily_data as $date => $transactions) { 
                                            $daily_opening = isset($transactions['opening_balance']) ? $transactions['opening_balance'] : 0;
                                            $daily_closing = isset($transactions['closing_balance']) ? $transactions['closing_balance'] : 0;
                                        ?>
                                        <div class="daily-section">
                                            <h4 class="date-header"><?php echo date('d M Y', strtotime($date)); ?></h4>
                                            <div class="daily-balance-info">
                                                <table class="table table-bordered">
                                                    <tr>
                                                        <th width="50%"><?php echo $this->lang->line('opening_balance'); ?></th>
                                                        <th width="50%"><?php echo $this->lang->line('closing_balance'); ?></th>
                                                    </tr>
                                                    <tr>
                                                        <td><?php echo $currency_symbol . number_format($daily_opening, 2); ?></td>
                                                        <td><?php echo $currency_symbol . number_format($daily_closing, 2); ?></td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th><?php echo $this->lang->line('date'); ?></th>
                                                        <th><?php echo $this->lang->line('type'); ?></th>
                                                        <th><?php echo $this->lang->line('receipt_no'); ?></th>
                                                        <th><?php echo $this->lang->line('description'); ?></th>
                                                        <th><?php echo $this->lang->line('credit'); ?></th>
                                                        <th><?php echo $this->lang->line('debit'); ?></th>
                                                        <th><?php echo $this->lang->line('balance'); ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $total_credit = 0;
                                                    $total_debit = 0;
                                                    foreach ($transactions['transactions'] as $transaction) {
                                                        $amount = $transaction['amount'];
                                                        if ($transaction['status'] == 'credit') {
                                                            $total_credit += $amount;
                                                        } else {
                                                            $total_debit += $amount;
                                                        }
                                                    ?>
                                                    <tr>
                                                        <td><?php echo date('d-m-Y', strtotime($date)); ?></td>
                                                        <td><?php echo $transaction['type']; ?></td>
                                                        <td><?php echo $transaction['receiptid']; ?></td>
                                                        <td><?php echo $transaction['description']; ?></td>
                                                        <td><?php echo ($transaction['status'] == 'credit') ? $currency_symbol . number_format($amount, 2) : ''; ?></td>
                                                        <td><?php echo ($transaction['status'] == 'debit') ? $currency_symbol . number_format($amount, 2) : ''; ?></td>
                                                        <td><?php echo $currency_symbol . number_format($transaction['balance'], 2); ?></td>
                                                    </tr>
                                                    <?php } ?>
                                                    <tr class="total-row">
                                                        <td colspan="4" class="text-right"><strong><?php echo $this->lang->line('total'); ?></strong></td>
                                                        <td><?php echo $currency_symbol . number_format($total_credit, 2); ?></td>
                                                        <td><?php echo $currency_symbol . number_format($total_debit, 2); ?></td>
                                                        <td><?php echo $currency_symbol . number_format($total_credit - $total_debit, 2); ?></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
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
    newWin.document.write('<html><head><link rel="stylesheet" href="<?php echo base_url(); ?>backend/bootstrap/css/bootstrap.min.css"><link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/style-main.css"></head><body>');
    newWin.document.write(divToPrint.innerHTML);
    newWin.document.write('</body></html>');
    newWin.document.close();
    setTimeout(function(){
        newWin.print();
        newWin.close();
    }, 500);
}
</script>
