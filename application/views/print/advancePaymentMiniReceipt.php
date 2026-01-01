<?php $currency_symbol = $this->customlib->getSchoolCurrencyFormat(); ?>
<!DOCTYPE html>
<html>
<head>
<style type="text/css">
    .receipt {
        width: 350px;
        border: 1px solid #000;
        padding: 12px;
        background-color: white;
        margin: 0 auto;
    }
    
    .header {
        position: relative;
        text-align: center;
        margin-bottom: 12px;
        padding-top: 12px;
    }
    
    .logo {
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 38px;
        height: 38px;
    }
    
    .college-info {
        margin: 0 auto;
        padding: 0 40px;
    }
    
    .college-name {
        font-weight: bold;
        font-size: 12px;
        margin-bottom: 4px;
        text-transform: uppercase;
    }
    
    .college-address {
        font-size: 9px;
        margin: 0 auto;
        max-width: 90%;
        line-height: 1.3;
    }
    
    .receipt-title {
        text-align: center;
        font-weight: bold;
        border-top: 1px solid #000;
        border-bottom: 1px solid #000;
        padding: 5px 0;
        margin: 12px 0;
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 11px;
        background-color: #f8f9fa;
    }
    
    .info-row {
        display: flex;
        justify-content: space-between;
        margin: 6px 0;
        font-size: 10px;
        flex-wrap: wrap;
        padding: 0;
    }
    
    .info-row span {
        max-width: 50%;
        white-space: normal;
        overflow: visible;
        text-overflow: clip;
        line-height: 1.3;
        padding-right: 0;
    }

    .info-row .student-name {
        max-width: 48%;
        text-align: right;
        margin-left: auto;
        word-wrap: break-word;
        height: auto;
        min-height: fit-content;
    }

    .info-row + .info-row {
        margin-top: 8px;
    }
    
    .advance-details {
        background-color: #e8f4fd;
        border: 1px solid #bee5eb;
        border-radius: 4px;
        padding: 10px;
        margin: 12px 0;
        font-size: 10px;
    }
    
    .advance-row {
        display: flex;
        justify-content: space-between;
        margin: 6px 0;
        font-size: 10px;
    }
    
    .advance-row.total {
        font-weight: bold;
        border-top: 1px solid #000;
        padding-top: 6px;
        margin-top: 10px;
        font-size: 11px;
    }
    
    .payment-info {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 8px;
        margin: 10px 0;
        font-size: 10px;
    }
    
    .signature-section {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
        font-size: 9px;
    }
    
    .signature-container {
        margin-top: 25px;
        text-align: center;
        font-size: 9px;
        width: 45%;
    }
    
    .footer {
        font-style: italic;
        font-size: 9px;
        margin-top: 15px;
        text-align: center;
        width: 100%;
        border-top: 1px solid #000;
        padding-top: 8px;
    }
    
    hr {
        border: none;
        border-top: 1px solid #000;
        margin: 10px 0;
    }

    .invoice-details {
        background-color: #fff3cd;
        border: 1px solid #ffeaa7;
        border-radius: 4px;
        padding: 8px;
        margin: 10px 0;
        font-size: 10px;
        text-align: center;
    }

    .note-section {
        background-color: #d1ecf1;
        border: 1px solid #bee5eb;
        border-radius: 4px;
        padding: 8px;
        margin: 10px 0;
        font-size: 9px;
    }

    .amount-in-words {
        font-style: italic;
        font-size: 9px;
        margin: 5px 0;
        text-align: center;
    }
</style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <img src="<?php echo $this->media_storage->getImageURL('uploads/school_content/logo/'.$sch_setting->image); ?>" alt="College Logo" class="logo">
            <div class="college-info">
                <div class="college-name"><?php echo $sch_setting->name;?></div>
                <div class="college-address"><?php echo $sch_setting->address;?></div>
            </div>
        </div>

        <div class="receipt-title">
            ADVANCE PAYMENT RECEIPT
        </div>

        <div class="invoice-details">
            <strong>Receipt No: <?php echo $advance_payment->invoice_id; ?></strong>
        </div>

        <div class="info-row">
            <span><b>Date: </b><?php echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($advance_payment->payment_date)); ?></span>
            <span><b>Academic: </b><?php echo $sch_setting->session;?></span>
        </div>
        
        <hr>
        
        <div class="info-row">
            <span><b>Admission No: </b><?php echo $student_data->admission_no; ?></span>
            <span class="student-name"><b>Student Name:</b> <?php echo $this->customlib->getFullName($student_data->firstname, $student_data->middlename, $student_data->lastname, $sch_setting->middlename, $sch_setting->lastname); ?></span>
        </div>
        
        <div class="info-row">
            <span><b>Class: </b><?php echo $student_data->class; ?></span>
            <span><b>Section: </b><?php echo $student_data->section; ?></span>
        </div>

        <div class="info-row">
            <span><b>Father Name: </b><?php echo $student_data->father_name; ?></span>
            <span><b>Mobile: </b><?php echo $student_data->mobileno; ?></span>
        </div>

        <hr>

        <div class="advance-details">
            <div class="advance-row">
                <span><b>Advance Amount Paid:</b></span>
                <span><b><?php echo $currency_symbol . number_format($advance_payment->amount, 2); ?></b></span>
            </div>
            
            <div class="advance-row">
                <span>Payment Mode:</span>
                <span><?php echo ucfirst(str_replace('_', ' ', $advance_payment->payment_mode)); ?></span>
            </div>
            
            <?php if (!empty($advance_payment->reference_no)): ?>
            <div class="advance-row">
                <span>Reference No:</span>
                <span><?php echo $advance_payment->reference_no; ?></span>
            </div>
            <?php endif; ?>
            
            <div class="advance-row">
                <span>Collected By:</span>
                <span><?php echo $advance_payment->collected_by; ?></span>
            </div>

            <?php if (!empty($advance_payment->description)): ?>
            <div class="advance-row">
                <span>Description:</span>
                <span><?php echo $advance_payment->description; ?></span>
            </div>
            <?php endif; ?>
        </div>

        <div class="payment-info">
            <div class="advance-row total">
                <span>Total Advance Amount:</span>
                <span><?php echo $currency_symbol . number_format($advance_payment->amount, 2); ?></span>
            </div>
            
            <div class="advance-row">
                <span>Current Balance:</span>
                <span><?php echo $currency_symbol . number_format($advance_payment->balance, 2); ?></span>
            </div>
        </div>

        <div class="amount-in-words">
            <strong>Amount in Words: </strong>
            <?php 
            // Simple amount display - you can enhance this later with a number-to-words converter
            echo "Rupees " . number_format($advance_payment->amount, 2) . " Only";
            ?>
        </div>

        <div class="note-section">
            <strong>Note:</strong> This advance payment will be automatically adjusted against future fee collections. 
            Please keep this receipt for your records.
        </div>

        <hr>
        
        <div class="signature-section">
            <div class="signature-container">Student/Parent Signature</div>
            <div class="signature-container">Authorised Signatory / Cashier</div>
        </div>

        <div class="footer">
            <?php echo $this->setting_model->get_receiptfooter(); ?>
            <br><small>Generated on: <?php echo date('d-m-Y H:i:s'); ?></small>
        </div>
    </div>
</body>
</html>
