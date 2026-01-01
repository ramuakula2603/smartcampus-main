<?php if (!empty($student_info)) { ?>
<div class="row mb-3">
    <div class="col-md-12">
        <div class="alert alert-info">
            <div class="row">
                <div class="col-md-6">
                    <strong><i class="fa fa-user"></i> Student:</strong> <?php echo $student_info['firstname'] . ' ' . $student_info['lastname']; ?><br>
                    <strong><i class="fa fa-id-card"></i> Admission No:</strong> <?php echo $student_info['admission_no']; ?>
                </div>
                <div class="col-md-6">
                    <strong><i class="fa fa-graduation-cap"></i> Class:</strong> <?php echo $student_info['class'] . ' (' . $student_info['section'] . ')'; ?><br>
                    <strong><i class="fa fa-calendar"></i> Session:</strong> <?php echo $student_info['session']; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<?php if (!empty($transfers)) { ?>
<div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead class="bg-primary text-white">
            <tr>
                <th><i class="fa fa-calendar"></i> Transfer Date</th>
                <th><i class="fa fa-money"></i> Amount Transferred</th>
                <th><i class="fa fa-receipt"></i> Fee Receipt</th>
                <th><i class="fa fa-tag"></i> Fee Category</th>
                <th><i class="fa fa-exchange"></i> Transfer Type</th>
                <th><i class="fa fa-balance-scale"></i> Balance Impact</th>
                <th><i class="fa fa-file-text"></i> Details</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $totalTransferred = 0;
            foreach ($transfers as $transfer) { 
                $totalTransferred += isset($transfer->transfer_amount) ? $transfer->transfer_amount : $transfer->amount_used;
                $transferAmount = isset($transfer->transfer_amount) ? $transfer->transfer_amount : $transfer->amount_used;
                $feeReceipt = isset($transfer->fee_receipt_id) ? $transfer->fee_receipt_id : (isset($transfer->invoice_id) ? $transfer->invoice_id : 'N/A');
                $feeCategory = isset($transfer->fee_category) ? $transfer->fee_category : 'Fee Payment';
                $transferType = isset($transfer->transfer_type) ? $transfer->transfer_type : 'Partial';
                $transferDate = isset($transfer->created_at) ? $transfer->created_at : $transfer->usage_date;
                $balanceBefore = isset($transfer->advance_balance_before) ? $transfer->advance_balance_before : 'N/A';
                $balanceAfter = isset($transfer->advance_balance_after) ? $transfer->advance_balance_after : 'N/A';
                $originalDate = isset($transfer->original_date) ? $transfer->original_date : (isset($transfer->payment_date) ? $transfer->payment_date : 'N/A');
            ?>
            <tr>
                <td>
                    <span class="text-muted"><?php echo date('d M Y', strtotime($transferDate)); ?></span><br>
                    <small class="text-muted"><?php echo date('h:i A', strtotime($transferDate)); ?></small>
                </td>
                <td>
                    <strong class="text-success"><?php echo $currency_symbol . number_format($transferAmount, 2); ?></strong>
                </td>
                <td>
                    <span class="badge badge-info"><?php echo $feeReceipt; ?></span>
                </td>
                <td>
                    <span class="label label-primary"><?php echo ucfirst($feeCategory); ?></span>
                </td>
                <td>
                    <?php if ($transferType == 'Complete') { ?>
                        <span class="label label-danger">Complete Transfer</span>
                    <?php } else { ?>
                        <span class="label label-warning">Partial Transfer</span>
                    <?php } ?>
                </td>
                <td>
                    <?php if ($balanceBefore !== 'N/A' && $balanceAfter !== 'N/A') { ?>
                        <small>
                            <strong>Before:</strong> <?php echo $currency_symbol . number_format($balanceBefore, 2); ?><br>
                            <strong>After:</strong> <?php echo $currency_symbol . number_format($balanceAfter, 2); ?>
                        </small>
                    <?php } else { ?>
                        <span class="text-muted">Zero Cash Entry</span>
                    <?php } ?>
                </td>
                <td>
                    <button type="button" class="btn btn-xs btn-info" onclick="showTransferDetails(<?php echo htmlspecialchars(json_encode($transfer)); ?>)">
                        <i class="fa fa-eye"></i> View
                    </button>
                </td>
            </tr>
            <?php } ?>
        </tbody>
        <tfoot class="bg-light">
            <tr>
                <th>Total Transferred:</th>
                <th class="text-success">
                    <strong><?php echo $currency_symbol . number_format($totalTransferred, 2); ?></strong>
                </th>
                <th colspan="5" class="text-muted">
                    <?php echo count($transfers); ?> transfer<?php echo count($transfers) > 1 ? 's' : ''; ?> found
                </th>
            </tr>
        </tfoot>
    </table>
</div>

<div class="row mt-3">
    <div class="col-md-12">
        <div class="alert alert-success">
            <h5><i class="fa fa-info-circle"></i> Transfer Summary</h5>
            <div class="row">
                <div class="col-md-3">
                    <strong>Total Transfers:</strong><br>
                    <span class="text-primary"><?php echo count($transfers); ?></span>
                </div>
                <div class="col-md-3">
                    <strong>Total Amount:</strong><br>
                    <span class="text-success"><?php echo $currency_symbol . number_format($totalTransferred, 2); ?></span>
                </div>
                <div class="col-md-3">
                    <strong>Transfer Method:</strong><br>
                    <span class="text-info">Direct Advance Utilization</span>
                </div>
                <div class="col-md-3">
                    <strong>Account Impact:</strong><br>
                    <span class="text-warning">Zero Cash Entry</span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php } else { ?>
<div class="alert alert-warning text-center">
    <i class="fa fa-exclamation-triangle fa-3x"></i>
    <h4>No Advance Payment Transfers Found</h4>
    <p>This student has not made any advance payment transfers to fee collections yet.</p>
    <hr>
    <p class="text-muted">
        <i class="fa fa-lightbulb-o"></i> 
        <strong>Note:</strong> When a student uses advance payment to pay fees, the transfer details will appear here with complete audit trail.
    </p>
</div>
<?php } ?>

<!-- Transfer Details Modal -->
<div class="modal fade" id="transferDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-info-circle"></i> Transfer Details</h4>
            </div>
            <div class="modal-body" id="transferDetailsContent">
                <!-- Content will be populated by JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function showTransferDetails(transfer) {
    var currencySymbol = '<?php echo $currency_symbol; ?>';
    var content = `
        <div class="row">
            <div class="col-md-6">
                <strong>Transfer Amount:</strong><br>
                <span class="text-success h4">${currencySymbol}${parseFloat(transfer.transfer_amount || transfer.amount_used).toFixed(2)}</span>
            </div>
            <div class="col-md-6">
                <strong>Transfer Date:</strong><br>
                <span class="text-muted">${new Date(transfer.created_at || transfer.usage_date).toLocaleString()}</span>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-6">
                <strong>Fee Receipt ID:</strong><br>
                <span class="badge badge-info">${transfer.fee_receipt_id || transfer.invoice_id}</span>
            </div>
            <div class="col-md-6">
                <strong>Fee Category:</strong><br>
                <span class="label label-primary">${transfer.fee_category || 'Fee Payment'}</span>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-6">
                <strong>Original Advance Amount:</strong><br>
                <span class="text-info">${currencySymbol}${parseFloat(transfer.original_advance_amount || transfer.original_amount || 0).toFixed(2)}</span>
            </div>
            <div class="col-md-6">
                <strong>Original Advance Date:</strong><br>
                <span class="text-muted">${transfer.original_date || transfer.payment_date || 'N/A'}</span>
            </div>
        </div>
    `;
    
    if (transfer.advance_balance_before && transfer.advance_balance_after) {
        content += `
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <strong>Balance Before Transfer:</strong><br>
                    <span class="text-warning">${currencySymbol}${parseFloat(transfer.advance_balance_before).toFixed(2)}</span>
                </div>
                <div class="col-md-6">
                    <strong>Balance After Transfer:</strong><br>
                    <span class="text-success">${currencySymbol}${parseFloat(transfer.advance_balance_after).toFixed(2)}</span>
                </div>
            </div>
        `;
    }
    
    if (transfer.transfer_description || transfer.description) {
        content += `
            <hr>
            <div class="row">
                <div class="col-md-12">
                    <strong>Description:</strong><br>
                    <p class="text-muted">${transfer.transfer_description || transfer.description || 'N/A'}</p>
                </div>
            </div>
        `;
    }
    
    $('#transferDetailsContent').html(content);
    $('#transferDetailsModal').modal('show');
}
</script>
