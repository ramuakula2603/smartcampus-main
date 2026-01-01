<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<div class="table-responsive">
    <?php if (!empty($additional_fee_history)) { ?>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th><?php echo $this->lang->line('date'); ?></th>
                    <th><?php echo $this->lang->line('payment_id'); ?></th>
                    <th><?php echo $this->lang->line('mode'); ?></th>
                    <th><?php echo $this->lang->line('description'); ?></th>
                    <th class="text-right"><?php echo $this->lang->line('amount'); ?> (<?php echo $currency_symbol; ?>)</th>
                    <th class="text-right"><?php echo $this->lang->line('discount'); ?> (<?php echo $currency_symbol; ?>)</th>
                    <th class="text-right"><?php echo $this->lang->line('fine'); ?> (<?php echo $currency_symbol; ?>)</th>
                    <th class="text-center"><?php echo $this->lang->line('action'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($additional_fee_history as $history) { ?>
                    <tr>
                        <td><?php echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($history->date)); ?></td>
                        <td><?php echo $history->student_fees_deposite_id . "/" . $history->inv_no; ?></td>
                        <td><?php echo $this->lang->line(strtolower($history->payment_mode)); ?></td>
                        <td><?php echo $history->description ? $history->description : $this->lang->line('no_description'); ?></td>
                        <td class="text-right"><?php echo amountFormat($history->amount); ?></td>
                        <td class="text-right"><?php echo amountFormat($history->amount_discount); ?></td>
                        <td class="text-right"><?php echo amountFormat($history->amount_fine); ?></td>
                        <td class="text-center">
                            <div class="btn-group">
                                <?php if ($this->rbac->hasPrivilege('collect_fees', 'can_delete')) { ?>
                                    <button class="btn btn-xs btn-warning" 
                                        onclick="revertAdditionalFeePayment('<?php echo $history->student_fees_deposite_id; ?>', '<?php echo $history->inv_no; ?>')"
                                        title="<?php echo $this->lang->line('revert'); ?>">
                                        <i class="fa fa-undo"></i>
                                    </button>
                                <?php } ?>
                                <button class="btn btn-xs btn-default printAdditionalFeeReceipt"
                                    data-main_invoice="<?php echo $history->student_fees_deposite_id; ?>"
                                    data-sub_invoice="<?php echo $history->inv_no; ?>"
                                    title="<?php echo $this->lang->line('print'); ?>">
                                    <i class="fa fa-print"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <div class="alert alert-info">
            <i class="fa fa-info-circle"></i> <?php echo $this->lang->line('no_payment_history_found'); ?>
        </div>
    <?php } ?>
</div>

<script>
function revertAdditionalFeePayment(mainInvoice, subInvoice) {
    if (confirm('<?php echo $this->lang->line('are_you_sure_to_revert_this_payment'); ?>')) {
        $.ajax({
            url: '<?php echo site_url("studentfee/deleteaddingFee"); ?>',
            type: 'POST',
            data: {
                main_invoice: mainInvoice,
                sub_invoice: subInvoice,
                '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
            },
            success: function(response) {
                if (response.status === 'success') {
                    location.reload();
                } else {
                    alert('Error reverting payment');
                }
            },
            error: function() {
                alert('Error reverting payment');
            }
        });
    }
}

$(document).on('click', '.printAdditionalFeeReceipt', function() {
    var mainInvoice = $(this).data('main_invoice');
    var subInvoice = $(this).data('sub_invoice');
    
    $.ajax({
        url: '<?php echo site_url("studentfee/printaddingFeesByName"); ?>',
        type: 'POST',
        dataType: "JSON",
        data: {
            'fee_category': 'fees',
            'student_session_id': '<?php echo isset($student_session_id) ? $student_session_id : ""; ?>',
            'main_invoice': mainInvoice,
            'sub_invoice': subInvoice
        },
        success: function(response) {
            if (response.status === 1) {
                Popup(response.page);
            }
        }
    });
});
</script>
