<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>

<!-- Bootstrap Datepicker CSS -->
<link rel="stylesheet" href="<?php echo base_url(); ?>backend/bootstrap/css/bootstrap-datepicker3.css"/>

<!-- jQuery and Bootstrap Datepicker JS -->
<script type="text/javascript" src="<?php echo base_url(); ?>backend/bootstrap/js/bootstrap-datepicker.js"></script>

<style>
    /* Exact CSS from typewisereport.php - Dynamic table styling */
    #headerTable {
        font-size: 12px;
        border-collapse: collapse;
        min-width: 100%;
        white-space: nowrap;
    }
    
    #headerTable th {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: center;
        vertical-align: middle;
        font-weight: bold;
        background-color: #f8f9fa;
        min-width: 80px;
    }
    
    #headerTable td {
        border: 1px solid #ddd;
        padding: 6px;
        vertical-align: middle;
        min-width: 80px;
        text-align: center;
    }
    
    #headerTable .total-bg {
        background-color: #337ab7 !important;
        color: white !important;
        font-weight: bold !important;
    }
    
    /* Responsive table */
    .table-responsive {
        overflow-x: auto;
        min-height: 400px;
        width: 100%;
    }
    
    /* Fee type header styling */
    #headerTable thead tr:first-child th {
        background-color: #2c3e50;
        color: white;
        font-weight: bold;
        min-width: 100px;
    }
    
    #headerTable thead tr:nth-child(2) th {
        background-color: #34495e;
        color: white;
        font-size: 10px;
        padding: 4px;
        min-width: 70px;
    }
    
    /* Highlight balance columns */
    #headerTable td:last-child,
    #headerTable td:nth-last-child(2),
    #headerTable td:nth-last-child(3) {
        font-weight: bold;
    }
    
    /* Fixed width columns for better visibility */
    .fee-column {
        min-width: 90px !important;
        max-width: 120px;
        padding: 5px !important;
    }
    
    .student-info {
        min-width: 120px;
        max-width: 200px;
    }
    
    /* Date picker and search date styling */
    .search_date {
        transition: all 0.3s ease;
    }
    
    .date {
        cursor: pointer;
    }
    
    .datepicker {
        z-index: 9999 !important;
    }
    
    /* Print specific styles */
    @media print {
        #headerTable {
            font-size: 8px;
        }
        
        #headerTable th,
        #headerTable td {
            padding: 2px;
            min-width: 50px;
        }
        
        .btn {
            display: none !important;
        }
    }

/* Multi-select dropdown enhancements for Finance Reports */
.SumoSelect {
    width: 100% !important;
}

.SumoSelect > .CaptionCont {
    border: 1px solid #d2d6de;
    border-radius: 3px;
    background-color: #fff;
    min-height: 34px;
    padding: 6px 12px;
}

.SumoSelect > .CaptionCont > span {
    line-height: 1.42857143;
    color: #555;
    padding-right: 20px;
}

.SumoSelect > .CaptionCont > span.placeholder {
    color: #999;
    font-style: italic;
}

.SumoSelect.open > .CaptionCont,
.SumoSelect:focus > .CaptionCont,
.SumoSelect:hover > .CaptionCont {
    border-color: #66afe9;
    box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6);
}

.SumoSelect .optWrapper {
    border: 1px solid #d2d6de;
    border-radius: 3px;
    box-shadow: 0 6px 12px rgba(0,0,0,.175);
    background-color: #fff;
    z-index: 9999;
}

.SumoSelect .optWrapper ul.options {
    max-height: 200px;
    overflow-y: auto;
}

.SumoSelect .optWrapper ul.options li {
    padding: 8px 12px;
    border-bottom: 1px solid #f4f4f4;
}

.SumoSelect .optWrapper ul.options li:hover {
    background-color: #f5f5f5;
}

.SumoSelect .optWrapper ul.options li.selected {
    background-color: #337ab7;
    color: #fff;
}

.SumoSelect .search-txt {
    border: 1px solid #d2d6de;
    border-radius: 3px;
    padding: 6px 12px;
    margin: 5px;
    width: calc(100% - 10px);
}

/* Additional table styling */
.table-container {
    border: 1px solid #ddd;
    border-radius: 5px;
    overflow-x: auto;
    overflow-y: visible;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    max-width: 100%;
}

.table-columnwise thead th {
    background: linear-gradient(to bottom, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 1px solid #ccc;
}

.table-columnwise tbody tr:nth-child(even) {
    background-color: #f9f9f9;
}

.table-columnwise tbody tr:hover {
    background-color: #e8f4fd;
}

/* Responsive design improvements */
@media (max-width: 768px) {
    .col-sm-2.col-lg-2.col-md-2 {
        margin-bottom: 15px;
    }

    .SumoSelect > .CaptionCont {
        min-height: 40px;
        padding: 8px 12px;
    }

    .form-group label {
        font-weight: 600;
        margin-bottom: 5px;
    }

    .payment-details-cell {
        min-width: 120px;
        max-width: 150px;
    }

    .payment-row {
        flex-direction: column;
        align-items: flex-start;
        padding: 3px 0;
    }

    .payment-amount, .payment-date, .payment-collector {
        margin: 1px 0;
        text-align: left;
    }

    .table-container {
        overflow-x: auto;
    }

    .table-columnwise {
        min-width: 800px;
    }
}

@media (max-width: 480px) {
    .SumoSelect > .CaptionCont {
        min-height: 44px;
        padding: 10px 12px;
    }
}

/* Form styling improvements */
.form-group label {
    margin-bottom: 5px;
    font-weight: 500;
}

/* Enhanced Excel-like table styling with borders */
.table-columnwise {
    font-size: 11px;
    border-collapse: collapse !important;
    width: 100%;
    font-family: Arial, sans-serif;
    background-color: #ffffff;
    border: 2px solid #333 !important;
}

.table-columnwise th {
    border: 1px solid #333 !important;
    padding: 8px 4px;
    background-color: #f8f9fa;
    font-weight: bold;
    text-align: center;
    vertical-align: middle;
}

.table-columnwise td {
    border: 1px solid #333 !important;
    padding: 4px;
    vertical-align: top;
}

.table-columnwise .student-info {
    background-color: #f9f9f9;
    font-weight: 500;
    border: 1px solid #333 !important;
}

.table-columnwise .total-cell {
    background-color: #e8f4fd;
    font-weight: bold;
    text-align: center;
    border: 1px solid #333 !important;
}

.table-columnwise tfoot td {
    border: 1px solid #333 !important;
    background-color: #f0f8ff;
    font-weight: bold;
    text-align: center;
}

/* Payment details cell styling */
.payment-details-cell {
    padding: 6px !important;
    vertical-align: top;
    min-width: 180px;
    max-width: 220px;
    border: 1px solid #333 !important;
}

.payment-breakdown {
    font-size: 10px;
    line-height: 1.3;
}

.payment-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 3px 0;
    border-bottom: 1px dotted #ddd;
    margin-bottom: 3px;
    background-color: #fafafa;
    padding: 2px 4px;
    border-radius: 2px;
}

.payment-row:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.payment-amount {
    font-weight: bold;
    color: #2c5aa0;
    flex: 0 0 auto;
    margin-right: 8px;
    font-size: 10px;
}

.payment-date {
    font-size: 9px;
    color: #666;
    flex: 1;
    text-align: center;
    margin: 0 4px;
}

.payment-collector {
    font-size: 9px;
    color: #333;
    flex: 1;
    text-align: right;
    font-style: italic;
}

.payment-summary {
    margin-top: 6px;
    padding: 4px;
    border-top: 1px solid #ccc;
    background-color: #f0f8ff;
    border-radius: 3px;
}

/* Grand Total Footer Styling */
.grand-total-footer {
    position: sticky;
    bottom: 0;
    z-index: 10;
}

.grand-total-row {
    font-weight: bold !important;
    font-size: 12px !important;
    border-top: 2px solid #333 !important;
}

.grand-total-row.assigned-total {
    background-color: #f8f9fa !important;
    color: #495057;
}

.grand-total-row.paid-total {
    background-color: #e8f5e8 !important;
    color: #155724;
}

.grand-total-row.remaining-total {
    background-color: #ffe8e8 !important;
    color: #721c24;
}

.grand-total-label {
    text-align: left !important;
    padding: 8px !important;
    font-weight: bold !important;
}

.grand-total-amount {
    text-align: center !important;
    padding: 8px !important;
    font-weight: bold !important;
    border: 1px solid #ccc !important;
}

.grand-total-final {
    text-align: center !important;
    padding: 8px !important;
    font-weight: bold !important;
    background-color: #e8f4fd !important;
    border: 2px solid #007bff !important;
}

/* Export Buttons Styling */
.export-buttons {
    display: inline-block;
}

.export-buttons .btn {
    margin-left: 5px;
    font-size: 12px;
    padding: 4px 8px;
    border-radius: 3px;
    transition: all 0.3s ease;
}

.export-buttons .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.export-buttons .btn i {
    margin-right: 3px;
}

.payment-total {
    text-align: center;
    font-weight: bold;
    color: #2c5aa0;
    margin-bottom: 2px;
}

.payment-remaining {
    text-align: center;
    font-weight: bold;
    color: #d9534f;
    font-size: 9px;
}

.payment-overpaid {
    text-align: center;
    font-weight: bold;
    color: #28a745;
    font-size: 9px;
}

.no-payment {
    text-align: center;
    color: #999;
    font-style: italic;
    padding: 15px 0;
    background-color: #f9f9f9;
    border-radius: 3px;
}

/* Simple table container */
.table-responsive {
    overflow-x: auto;
    overflow-y: auto;
    margin: 0;
    padding: 0;
    background-color: #ffffff;
    border: 1px solid #000000;
}

/* Simple Excel-like headers */
.table-columnwise th {
    background-color: #f0f0f0;
    border: 1px solid #000000;
    padding: 5px;
    text-align: center;
    font-weight: bold;
    font-size: 11px;
}

/* Simple Excel-like cells */
.table-columnwise td {
    border: 1px solid #000000;
    padding: 5px;
    text-align: center;
    font-size: 11px;
    background-color: #ffffff;
}













</style>
<div class="content-wrapper">
    <section class="content-header"></section>
    <!-- Main content -->
    <section class="content">
        <?php $this->load->view('financereports/_finance');?>
        <div class="row">
            <div class="col-md-12">
                <div class="box removeboxmius">
                    <div class="box-header ">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('select_criteria'); ?></h3>
                    </div>
                    <form role="form" action="<?php echo site_url('financereports/fee_collection_report_columnwise') ?>" method="post" class="">
                        <div class="box-body row">
                            <?php echo $this->customlib->getCSRF(); ?>
                            <div class="col-sm-2 col-lg-2 col-md-2">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('search_duration'); ?><small class="req"> *</small></label>
                                    <select class="form-control" name="search_type" onchange="showdate(this.value)">

                                        <?php foreach ($searchlist as $key => $search) {
    ?>
                                            <option value="<?php echo $key ?>" <?php
if ((isset($search_type)) && ($search_type == $key)) {
        echo "selected";
    }
    ?>><?php echo $search ?></option>
                                                <?php }?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('search_type'); ?></span>
                                </div>
                            </div>

                            <div class="col-sm-2 col-lg-2 col-md-2">
                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('session'); ?></label>

                                    <select id="sch_session_id" name="sch_session_id[]" class="form-control multiselect-dropdown" multiple>
                                        <?php foreach ($sessionlist as $session) {
                                            ?>
                                            <option value="<?php echo $session['id'] ?>"

                                            <?php if (set_value('sch_session_id') == $session['id']) {echo "selected=selected";}?>><?php echo $session['session'] ?></option>
                                            <?php } ?>
                                    </select>

                                    <span class="text-danger" id="error_sch_session_id"></span>
                                </div>
                            </div>

                            <div class="col-sm-2 col-lg-2 col-md-2">
                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('class'); ?></label>
                                    <select id="class_id" name="class_id[]" class="form-control multiselect-dropdown" multiple>
                                        <?php
                                            $count = 0;
                                            foreach ($classlist as $class) {
                                                ?>
                                            <option value="<?php echo $class['id'] ?>" <?php if (set_value('class_id') == $class['id']) {
                                                echo "selected=selected";
                                            }
                                            ?>><?php echo $class['class'] ?></option>
                                            <?php
                                        $count++;
                                        }
                                        ?>
                                    </select>
                                    <span class="text-danger" id="error_class_id"></span>
                                </div>
                            </div>

                            <div class="col-sm-2 col-lg-2 col-md-2">
                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('section'); ?></label>
                                    <select id="section_id" name="section_id[]" class="form-control multiselect-dropdown" multiple>
                                    </select>
                                    <span class="text-danger" id="error_section_id"></span>
                                </div>
                            </div>

                            <div class="col-sm-2 col-lg-2 col-md-2">
                               <div class="form-group">
                                            <label for="exampleInputEmail1"><?php echo $this->lang->line('fees_type'); ?></label>

                                            <select id="feetype_id" name="feetype_id[]" class="form-control multiselect-dropdown" multiple>
                                                <?php
                                                    $count = 0;
                                                    foreach ($feetypeList as $feetype) {
                                                        ?>
                                                    <option value="<?php echo $feetype['id'] ?>"<?php
                                                    if (set_value('feetype_id') == $feetype['id']) {
                                                            echo "selected =selected";
                                                        }
                                                        ?>><?php echo $feetype['type'] ?></option>

                                                    <?php
                                                $count++;
                                                }
                                                ?>
                                            </select>
                                            <span class="text-danger" id="error_feetype_id"></span>
                                        </div>
                            </div>

                            <div class="col-sm-2 col-lg-2 col-md-2">
                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('collect_by'); ?></label>

                                    <select id="collect_by" name="collect_by[]" class="form-control multiselect-dropdown" multiple>
                                        <?php
                                            $count = 0;
                                            foreach ($collect_by as $key => $value) {
                                                ?>
                                            <option value="<?php echo $key ?>"<?php
                                            if (set_value('collect_by') == $key) {
                                                    echo "selected =selected";
                                                }
                                                ?>><?php echo $value ?></option>

                                            <?php
                                        $count++;
                                        }
                                        ?>
                                    </select>
                                    <span class="text-danger" id="error_collect_by"></span>
                                </div>
                            </div>

                            <div id='date_result'>
                                <!-- Date fields for period search -->
                                <div class="col-sm-2 search_date" style="display: none;">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('date_from'); ?><small class="req"> *</small></label>
                                        <input id="date_from" name="date_from" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date_from', date($this->customlib->getSchoolDateFormat())); ?>" readonly="">
                                        <span class="text-danger"><?php echo form_error('date_from'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-2 search_date" style="display: none;">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('date_to'); ?><small class="req"> *</small></label>
                                        <input id="date_to" name="date_to" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date_to', date($this->customlib->getSchoolDateFormat())); ?>" readonly="">
                                        <span class="text-danger"><?php echo form_error('date_to'); ?></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-2 col-lg-2 col-md-2">
                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('group_by'); ?></label>

                                    <select class="form-control" name="group">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php
                                            $count = 0;
                                            foreach ($group_by as $key => $value) {
                                                ?>
                                            <option value="<?php echo $key ?>"<?php
                                            if ((isset($group_byid)) && ($group_byid == $key)) {
                                                    echo "selected";
                                                }
                                                ?>><?php echo $value ?></option>

                                            <?php
                                        $count++;
                                        }
                                        ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('group'); ?></span>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-12">
                                    <button type="submit" name="search" value="search_filter" id="search_btn" class="btn btn-primary btn-sm checkbox-toggle pull-right"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                </div>
                            </div>
                        </div>
                    </form>
 <?php
if (empty($results)) {
    ?>
<div class="box-header ptbnull">
    <div class="alert alert-info">
       <?php echo $this->lang->line('no_record_found'); ?>
    </div>
</div>
                                        <?php
} else {
    ?>
                    <div class="">
                        <div class="box-header ptbnull">
                            <div class="row">
                                <div class="col-md-8">
                                    <h3 class="box-title titlefix"><i class="fa fa-money"></i> <?php echo $this->lang->line('fee_collection_report_column_wise'); ?></h3>
                                </div>
                                <div class="col-md-4 text-right">
                                    <!-- Exact export buttons from typewisereport.php -->
                                    <a class="btn btn-default btn-xs pull-right" id="print" onclick="printDiv()" ><i class="fa fa-print"></i></a>
                                    <a class="btn btn-default btn-xs pull-right" id="btnExport" onclick="exportToExcel();"> <i class="fa fa-file-excel-o"></i> </a>
                                </div>
                            </div>
                        </div>




                        <div class="box-body table-responsive" id="transfee">
                        <?php if (isset($error_message)) { ?>
                            <div class="alert alert-danger">
                                <strong>Error:</strong> <?php echo $error_message; ?>
                            </div>
                        <?php } ?>
                        
                        <div id="printhead"><center><b><h4><?php echo $this->lang->line('fee_collection_report_column_wise') . "<br>";
    $this->customlib->get_postmessage();
    ?></h4></b></center></div>
                            <div class="download_label"><?php echo $this->lang->line('fee_collection_report_column_wise') . "<br>";
    $this->customlib->get_postmessage();
    ?></div>





                            <!-- Exact table structure from typewisereport.php -->
                            <table class="table table-striped table-bordered table-hover" id="headerTable" style="width: 100%; table-layout: auto;">
                                    <thead class="header">
                                        <tr>
                                            <th rowspan="2" class="student-info"><?php echo $this->lang->line('s_no'); ?></th>
                                            <th rowspan="2" class="student-info"><?php echo $this->lang->line('admission_no'); ?></th>
                                            <th rowspan="2" class="student-info"><?php echo $this->lang->line('name'); ?></th>
                                            <th rowspan="2" class="student-info"><?php echo $this->lang->line('phone'); ?></th>
                                            <th rowspan="2" class="student-info"><?php echo $this->lang->line('class'); ?></th>
                                            <th rowspan="2" class="student-info"><?php echo $this->lang->line('section'); ?></th>
                                            <?php
                                            $total_by_type = array();
                                            
                                            // Prepare fee types for dynamic columns
                                            $dynamic_fee_types = array();
                                            if (isset($fee_types) && !empty($fee_types)) {
                                                foreach ($fee_types as $fee_type) {
                                                    $fee_key = $fee_type['type'];
                                                    $dynamic_fee_types[$fee_key] = array(
                                                        'type' => $fee_type['type'],
                                                        'group' => isset($fee_type['group']) ? $fee_type['group'] : 'General'
                                                    );
                                                    $total_by_type[$fee_type['type']] = array(
                                                        'total_amount' => 0,
                                                        'paid_amount' => 0,
                                                        'remaining_amount' => 0
                                                    );
                                                }
                                            }
                                            
                                            if (!empty($dynamic_fee_types)) {
                                                foreach ($dynamic_fee_types as $fee_key => $fee_info): ?>
                                                    <th colspan="5" class="fee-column" style="text-align: center; background-color: #3498db; color: white; border: 2px solid #2980b9;">
                                                        <strong><?php echo htmlspecialchars($fee_info['type']); ?></strong>
                                                    </th>
                                                <?php endforeach;
                                            } else {
                                                // Show placeholder columns if no fee types found
                                                ?>
                                                <th colspan="5" class="fee-column" style="text-align: center; background-color: #95a5a6; color: white;">
                                                    <strong>No Fee Types Selected</strong>
                                                </th>
                                                <?php
                                            }
                                            ?>
                                            <th rowspan="2" style="background-color: #e74c3c; color: white; min-width: 100px;"><strong>Total Amount</strong></th>
                                            <th rowspan="2" style="background-color: #27ae60; color: white; min-width: 100px;"><strong>Total Paid</strong></th>
                                            <th rowspan="2" style="background-color: #f39c12; color: white; min-width: 100px;"><strong>Total Balance</strong></th>
                                        </tr>
                                        <tr>
                                            <?php 
                                            if (!empty($dynamic_fee_types)) {
                                                foreach ($dynamic_fee_types as $fee_key => $fee_info): ?>
                                                    <th class="fee-column" style="font-size: 11px; background-color: #ecf0f1; color: #2c3e50; border: 1px solid #bdc3c7;"><strong>Total</strong></th>
                                                    <th class="fee-column" style="font-size: 11px; background-color: #ecf0f1; color: #2c3e50; border: 1px solid #bdc3c7;"><strong>Fine</strong></th>
                                                    <th class="fee-column" style="font-size: 11px; background-color: #ecf0f1; color: #2c3e50; border: 1px solid #bdc3c7;"><strong>Discount</strong></th>
                                                    <th class="fee-column" style="font-size: 11px; background-color: #ecf0f1; color: #2c3e50; border: 1px solid #bdc3c7;"><strong>Paid</strong></th>
                                                    <th class="fee-column" style="font-size: 11px; background-color: #ecf0f1; color: #2c3e50; border: 1px solid #bdc3c7;"><strong>Balance</strong></th>
                                                <?php endforeach;
                                            } else {
                                                // Show placeholder sub-columns
                                                ?>
                                                <th class="fee-column" style="font-size: 11px; background-color: #ecf0f1; color: #2c3e50;">-</th>
                                                <th class="fee-column" style="font-size: 11px; background-color: #ecf0f1; color: #2c3e50;">-</th>
                                                <th class="fee-column" style="font-size: 11px; background-color: #ecf0f1; color: #2c3e50;">-</th>
                                                <th class="fee-column" style="font-size: 11px; background-color: #ecf0f1; color: #2c3e50;">-</th>
                                                <th class="fee-column" style="font-size: 11px; background-color: #ecf0f1; color: #2c3e50;">-</th>
                                                <?php
                                            }
                                            ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Initialize all variables to prevent undefined variable errors
                                        $grand_total = 0;
                                        $sn = 0;
                                        $students_data = array();
                                        $dynamic_grand_totals = array();
                                        $dynamic_fee_types = array();
                                        
                                        // Initialize fee types safely
                                        if (isset($fee_types) && is_array($fee_types) && !empty($fee_types)) {
                                            foreach ($fee_types as $fee_type) {
                                                if (isset($fee_type['type'])) {
                                                    $fee_key = $fee_type['type'];
                                                    $dynamic_fee_types[$fee_key] = array(
                                                        'type' => $fee_type['type'],
                                                        'group' => isset($fee_type['group']) ? $fee_type['group'] : 'General'
                                                    );
                                                }
                                            }
                                        }
                                        
                                        // Reorganize data into student-centric structure like typewise report
                                        
                                        // Initialize grand totals for each fee type
                                        if (!empty($dynamic_fee_types)) {
                                            foreach ($dynamic_fee_types as $fee_key => $fee_info) {
                                                $dynamic_grand_totals[$fee_key] = array(
                                                    'total' => 0, 'fine' => 0, 'discount' => 0, 'paid' => 0, 'balance' => 0
                                                );
                                            }
                                        }
                                        $dynamic_grand_totals['overall'] = array('total' => 0, 'paid' => 0, 'balance' => 0);
                                        
                                        // Process results to create student-centric view
                                        if (isset($results) && is_array($results)) {
                                            foreach ($results as $student) {
                                                $student_key = $student['admission_no'];
                                                
                                                if (!isset($students_data[$student_key])) {
                                                    $students_data[$student_key] = array(
                                                        'admission_no' => $student['admission_no'],
                                                        'firstname' => $student['firstname'],
                                                        'middlename' => isset($student['middlename']) ? $student['middlename'] : '',
                                                        'lastname' => isset($student['lastname']) ? $student['lastname'] : '',
                                                        'mobileno' => isset($student['mobileno']) ? $student['mobileno'] : '',
                                                        'class' => $student['class'],
                                                        'section' => $student['section'],
                                                        'fees' => array()
                                                    );
                                                }
                                                
                                                // Map fee types from the existing data structure
                                                if (isset($student['fee_types']) && is_array($student['fee_types'])) {
                                                    foreach ($student['fee_types'] as $type => $fee_data) {
                                                        // Handle both old and new data formats
                                                        if (is_numeric($fee_data)) {
                                                            $paid_amount = $fee_data;
                                                            $total_amount = $fee_data;
                                                            $remaining_amount = 0;
                                                            $fine_amount = 0;
                                                            $discount_amount = 0;
                                                        } else {
                                                            $paid_amount = isset($fee_data['paid_amount']) ? $fee_data['paid_amount'] : 0;
                                                            $total_amount = isset($fee_data['total_amount']) ? $fee_data['total_amount'] : 0;
                                                            $remaining_amount = isset($fee_data['remaining_amount']) ? $fee_data['remaining_amount'] : 0;
                                                            $fine_amount = isset($fee_data['fine_amount']) ? $fee_data['fine_amount'] : 0;
                                                            $discount_amount = isset($fee_data['discount_amount']) ? $fee_data['discount_amount'] : 0;
                                                        }
                                                        
                                                        $students_data[$student_key]['fees'][$type] = array(
                                                            'total' => $total_amount,
                                                            'fine' => $fine_amount,
                                                            'discount' => $discount_amount,
                                                            'paid' => $paid_amount,
                                                            'balance' => $remaining_amount
                                                        );
                                                    }
                                                }
                                            }
                                        }

                                        // Display students data in dynamic column format
                                        if (!empty($students_data)) {
                                            foreach ($students_data as $student) {
                                                $sn++;
                                                $row_total = 0;
                                                $row_paid = 0;
                                                $row_balance = 0;
                                        ?>
                                            <tr style="<?php echo ($sn % 2 == 0) ? 'background-color: #f8f9fa;' : ''; ?>">
                                                <td style="text-align: center; font-weight: bold;"><?php echo $sn; ?></td>
                                                <td style="text-align: center; font-weight: bold; color: #3498db;"><?php echo htmlspecialchars($student['admission_no']); ?></td>
                                                <td style="text-align: left; padding-left: 10px;"><?php echo htmlspecialchars($this->customlib->getFullName($student['firstname'], $student['middlename'], $student['lastname'], $sch_setting->middlename, $sch_setting->lastname)); ?></td>
                                                <td style="text-align: center;"><?php echo htmlspecialchars($student['mobileno']); ?></td>
                                                <td style="text-align: center; color: #8e44ad; font-weight: bold;"><?php echo htmlspecialchars($student['class']); ?></td>
                                                <td style="text-align: center; color: #8e44ad; font-weight: bold;"><?php echo htmlspecialchars($student['section']); ?></td>
                                                
                                                <?php 
                                                if (!empty($dynamic_fee_types)) {
                                                    foreach ($dynamic_fee_types as $fee_key => $fee_info): 
                                                        $fee_data = isset($student['fees'][$fee_key]) ? $student['fees'][$fee_key] : array(
                                                            'total' => 0, 'fine' => 0, 'discount' => 0, 'paid' => 0, 'balance' => 0
                                                        );
                                                        
                                                        // Add to row totals
                                                        $row_total += $fee_data['total'];
                                                        $row_paid += $fee_data['paid'];
                                                        $row_balance += $fee_data['balance'];
                                                        
                                                        // Add to grand totals
                                                        $dynamic_grand_totals[$fee_key]['total'] += $fee_data['total'];
                                                        $dynamic_grand_totals[$fee_key]['fine'] += $fee_data['fine'];
                                                        $dynamic_grand_totals[$fee_key]['discount'] += $fee_data['discount'];
                                                        $dynamic_grand_totals[$fee_key]['paid'] += $fee_data['paid'];
                                                        $dynamic_grand_totals[$fee_key]['balance'] += $fee_data['balance'];
                                                ?>
                                                        <td class="fee-column" style="text-align: right; <?php echo $fee_data['total'] > 0 ? 'background-color: #ebf3fd; color: #2980b9; font-weight: bold;' : 'color: #95a5a6;'; ?>">
                                                            <?php echo $fee_data['total'] > 0 ? number_format($fee_data['total'], 2) : '-'; ?>
                                                        </td>
                                                        <td class="fee-column" style="text-align: right; <?php echo $fee_data['fine'] > 0 ? 'background-color: #fdf2e9; color: #e67e22; font-weight: bold;' : 'color: #95a5a6;'; ?>">
                                                            <?php echo $fee_data['fine'] > 0 ? number_format($fee_data['fine'], 2) : '-'; ?>
                                                        </td>
                                                        <td class="fee-column" style="text-align: right; <?php echo $fee_data['discount'] > 0 ? 'background-color: #eafaf1; color: #27ae60; font-weight: bold;' : 'color: #95a5a6;'; ?>">
                                                            <?php echo $fee_data['discount'] > 0 ? number_format($fee_data['discount'], 2) : '-'; ?>
                                                        </td>
                                                        <td class="fee-column" style="text-align: right; <?php echo $fee_data['paid'] > 0 ? 'background-color: #e8f6f3; color: #16a085; font-weight: bold;' : 'color: #95a5a6;'; ?>">
                                                            <?php echo $fee_data['paid'] > 0 ? number_format($fee_data['paid'], 2) : '-'; ?>
                                                        </td>
                                                        <td class="fee-column" style="text-align: right; <?php echo $fee_data['balance'] > 0 ? 'background-color: #fdedec; color: #e74c3c; font-weight: bold;' : ($fee_data['balance'] == 0 && $fee_data['total'] > 0 ? 'background-color: #eafaf1; color: #27ae60; font-weight: bold;' : 'color: #95a5a6;'); ?>">
                                                            <?php echo $fee_data['balance'] != 0 ? $currency_symbol . number_format($fee_data['balance'], 2) : ($fee_data['total'] > 0 ? $currency_symbol . '0.00' : '-'); ?>
                                                        </td>
                                                <?php 
                                                    endforeach;
                                                } else {
                                                    // Show placeholder cells if no fee types
                                                    ?>
                                                    <td class="fee-column" style="text-align: center; color: #95a5a6;">-</td>
                                                    <td class="fee-column" style="text-align: center; color: #95a5a6;">-</td>
                                                    <td class="fee-column" style="text-align: center; color: #95a5a6;">-</td>
                                                    <td class="fee-column" style="text-align: center; color: #95a5a6;">-</td>
                                                    <td class="fee-column" style="text-align: center; color: #95a5a6;">-</td>
                                                    <?php
                                                }
                                                
                                                // Add to overall grand totals
                                                $dynamic_grand_totals['overall']['total'] += $row_total;
                                                $dynamic_grand_totals['overall']['paid'] += $row_paid;
                                                $dynamic_grand_totals['overall']['balance'] += $row_balance;
                                                $grand_total += $row_paid;
                                                ?>
                                                
                                                <td style="text-align: right; font-weight: bold; font-size: 14px;"><?php echo number_format($row_total, 2); ?></td>
                                                <td style="text-align: right; font-weight: bold; font-size: 14px;"><?php echo number_format($row_paid, 2); ?></td>
                                                <td style="text-align: right; font-weight: bold; font-size: 14px;"><?php echo number_format($row_balance, 2); ?></td>
                                            </tr>
                                        <?php
                                            } // end foreach students
                                        } else {
                                            // Show message when no students found
                                        ?>
                                            <tr>
                                                <td colspan="<?php echo 9 + (count($dynamic_fee_types) * 5); ?>" style="text-align: center; padding: 20px; color: #7f8c8d; font-style: italic;">
                                                    <i class="fa fa-info-circle"></i> No student data found for the selected criteria. Please adjust your filters and try again.
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                    <?php 
                                    // Grand Total Row - exactly like typewisereport.php
                                    if (!empty($students_data)) {
                                    ?>
                                        <tr class="total-bg" style="background-color: #2c3e50 !important; color: white !important; font-weight: bold !important;">
                                            <td colspan="6" style="text-align: center; font-weight: bold; font-size: 14px; padding: 10px;">
                                                <strong>GRAND TOTAL</strong>
                                            </td>
                                            <?php 
                                            if (!empty($dynamic_fee_types)) {
                                                foreach ($dynamic_fee_types as $fee_key => $fee_info): ?>
                                                    <td class="fee-column" style="text-align: right; font-weight: bold;"><?php echo number_format($dynamic_grand_totals[$fee_key]['total'], 2); ?></td>
                                                    <td class="fee-column" style="text-align: right; font-weight: bold;"><?php echo number_format($dynamic_grand_totals[$fee_key]['fine'], 2); ?></td>
                                                    <td class="fee-column" style="text-align: right; font-weight: bold;"><?php echo number_format($dynamic_grand_totals[$fee_key]['discount'], 2); ?></td>
                                                    <td class="fee-column" style="text-align: right; font-weight: bold;"><?php echo number_format($dynamic_grand_totals[$fee_key]['paid'], 2); ?></td>
                                                    <td class="fee-column" style="text-align: right; font-weight: bold;"><?php echo number_format($dynamic_grand_totals[$fee_key]['balance'], 2); ?></td>
                                                <?php endforeach;
                                            } else {
                                                // Show placeholder cells in grand total
                                                ?>
                                                <td class="fee-column" style="text-align: center;">-</td>
                                                <td class="fee-column" style="text-align: center;">-</td>
                                                <td class="fee-column" style="text-align: center;">-</td>
                                                <td class="fee-column" style="text-align: center;">-</td>
                                                <td class="fee-column" style="text-align: center;">-</td>
                                                <?php
                                            }
                                            ?>
                                            <td style="text-align: right; font-weight: bold; font-size: 14px;"><?php echo number_format($dynamic_grand_totals['overall']['total'], 2); ?></td>
                                            <td style="text-align: right; font-weight: bold; font-size: 14px;"><?php echo number_format($dynamic_grand_totals['overall']['paid'], 2); ?></td>
                                            <td style="text-align: right; font-weight: bold; font-size: 14px;"><?php echo number_format($dynamic_grand_totals['overall']['balance'], 2); ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                                        <?php
}
?>
                </div>
            </div>
        </div>
    </section>
</div>
<iframe id="txtArea1" style="display:none"></iframe>

<script>
    $(document).ready(function () {
        console.log('Document ready, jQuery version:', $.fn.jquery);
        console.log('Found multiselect dropdowns:', $('.multiselect-dropdown').length);

        // Initialize export buttons visibility
        try {
            if (document.getElementById("print")) {
                document.getElementById("print").style.display = "block";
            }
            if (document.getElementById("printhead")) {
                document.getElementById("printhead").style.display = "none";
            }
        } catch (e) {
            console.log('Export buttons not found in DOM');
        }

        // Initialize Bootstrap Datepicker
        $('.date').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            todayHighlight: true,
            orientation: "bottom auto",
            todayBtn: "linked"
        });

        // Check if SumoSelect is available
        if (typeof $.fn.SumoSelect === 'undefined') {
            console.error('SumoSelect plugin not loaded!');
            return;
        }

        // Add a small delay to ensure DOM is fully rendered
        setTimeout(function() {
            console.log('Initializing SumoSelect...');

            // Initialize SumoSelect for all multi-select dropdowns
            $('.multiselect-dropdown').each(function() {
                console.log('Initializing dropdown:', $(this).attr('id'));
                $(this).SumoSelect({
                    placeholder: 'Select Options',
                    csvDispCount: 3,
                    captionFormat: '{0} Selected',
                    captionFormatAllSelected: 'All Selected ({0})',
                    selectAll: true,
                    search: true,
                    searchText: 'Search...',
                    noMatch: 'No matches found "{0}"',
                    okCancelInMulti: true,
                    isClickAwayOk: true,
                    locale: ['OK', 'Cancel', 'Select All'],
                    up: false,
                    showTitle: true
                });
            });

            console.log('SumoSelect initialization complete');
        }, 100);

    // Initialize section dropdown on page load if class is pre-selected
    var preSelectedClass = $('#class_id').val();
    if (preSelectedClass && preSelectedClass.length > 0) {
        $('#class_id').trigger('change');
    }

    // Handle class dropdown changes for section population
    $(document).on('change', '#class_id', function (e) {
        var sectionDropdown = $('#section_id')[0];
        if (sectionDropdown && sectionDropdown.sumo) {
            sectionDropdown.sumo.removeAll();
        }

        var class_ids = $(this).val();
        var base_url = '<?php echo base_url() ?>';

        if (class_ids && class_ids.length > 0) {
            var requests = [];
            var allSections = [];
            var addedSections = {};

            // Get sections for all selected classes
            $.each(class_ids, function(index, class_id) {
                requests.push(
                    $.ajax({
                        type: "GET",
                        url: base_url + "sections/getByClass",
                        data: {'class_id': class_id},
                        dataType: "json",
                        success: function(data) {
                            if (data && Array.isArray(data)) {
                                $.each(data, function(i, obj) {
                                    // Avoid duplicate sections
                                    if (!addedSections[obj.section_id]) {
                                        allSections.push({
                                            value: obj.section_id,
                                            text: obj.section
                                        });
                                        addedSections[obj.section_id] = true;
                                    }
                                });
                            }
                        }
                    })
                );
            });

            // Wait for all requests to complete
            $.when.apply($, requests).done(function() {
                // Add sections to dropdown
                if (sectionDropdown && sectionDropdown.sumo && allSections.length > 0) {
                    $.each(allSections, function(index, section) {
                        sectionDropdown.sumo.add(section.value, section.text);
                    });
                    // Refresh the dropdown to ensure proper display
                    sectionDropdown.sumo.reload();
                }
            });
        }
    });

        // Enhanced loading state for SumoSelect dropdowns
        function showDropdownLoading(selector) {
            $(selector).prop('disabled', true);
            $(selector).next('.SumoSelect').addClass('loading');
        }

        function hideDropdownLoading(selector) {
            $(selector).prop('disabled', false);
            $(selector).next('.SumoSelect').removeClass('loading');
        }

        function showdate(value) {
            if (value == 'period') {
                $('.search_date').show();
                // Initialize datepicker when date fields are shown
                setTimeout(function() {
                    $('.date').datepicker({
                        format: 'dd/mm/yyyy',
                        autoclose: true,
                        todayHighlight: true,
                        orientation: "bottom auto",
                        todayBtn: "linked"
                    });
                }, 100);
            } else {
                $('.search_date').hide();
            }
        }

        // Ensure table is responsive and visible - Enhanced for columnwise
        const table = document.getElementById('headerTable');
        if (table) {
            table.style.display = 'table';
            table.style.width = '100%';
            table.style.tableLayout = 'auto';
            
            // Make sure parent container allows horizontal scrolling
            const tableContainer = table.closest('.table-container');
            if (tableContainer) {
                tableContainer.style.overflowX = 'auto';
                tableContainer.style.width = '100%';
            }
        }

        // Add some debug information to console
        console.log('Columnwise Table initialized:', table);
        console.log('Columnwise Table rows:', table ? table.rows.length : 0);
        console.log('Columnwise Table container:', table ? table.closest('.table-container') : null);

    // Form validation with comprehensive debugging
    $('form').on('submit', function(e) {
        console.log('=== FRONTEND: Form Submission Started ===');
        
        // Get all form values
        var search_type = $('select[name="search_type"]').val();
        var class_ids = $('#class_id').val();
        var section_ids = $('#section_id').val();
        var session_ids = $('#sch_session_id').val();
        var feetype_ids = $('#feetype_id').val();
        var collect_by_ids = $('#collect_by').val();
        var group = $('select[name="group"]').val();
        
        // Log all form values with types
        console.log('FRONTEND: Form Field Values:');
        console.log('  - search_type:', search_type, '(type:', typeof search_type, ')');
        console.log('  - class_ids:', class_ids, '(type:', typeof class_ids, ', length:', class_ids ? class_ids.length : 'null', ')');
        console.log('  - section_ids:', section_ids, '(type:', typeof section_ids, ', length:', section_ids ? section_ids.length : 'null', ')');
        console.log('  - session_ids:', session_ids, '(type:', typeof session_ids, ', length:', session_ids ? session_ids.length : 'null', ')');
        console.log('  - feetype_ids:', feetype_ids, '(type:', typeof feetype_ids, ', length:', feetype_ids ? feetype_ids.length : 'null', ')');
        console.log('  - collect_by_ids:', collect_by_ids, '(type:', typeof collect_by_ids, ', length:', collect_by_ids ? collect_by_ids.length : 'null', ')');
        console.log('  - group:', group, '(type:', typeof group, ')');
        
        // Log serialized form data
        var formData = $(this).serializeArray();
        console.log('FRONTEND: Serialized form data:', formData);
        
        // Log form data as will be sent to server
        var formDataObject = {};
        $.each(formData, function(i, field) {
            if (formDataObject[field.name]) {
                if (!Array.isArray(formDataObject[field.name])) {
                    formDataObject[field.name] = [formDataObject[field.name]];
                }
                formDataObject[field.name].push(field.value);
            } else {
                formDataObject[field.name] = field.value;
            }
        });
        console.log('FRONTEND: Form data as object:', formDataObject);
        
        // Validate search type
        if (!search_type) {
            console.log('FRONTEND: Validation failed - no search_type selected');
            e.preventDefault();
            alert('<?php echo $this->lang->line("please_select_search_duration"); ?>');
            return false;
        }
        
        // Log SumoSelect states
        ['class_id', 'section_id', 'sch_session_id', 'feetype_id', 'collect_by'].forEach(function(fieldId) {
            var element = $('#' + fieldId)[0];
            if (element && element.sumo) {
                console.log('FRONTEND: SumoSelect ' + fieldId + ' - Selected:', element.sumo.getSelected());
                console.log('FRONTEND: SumoSelect ' + fieldId + ' - String:', element.sumo.getSelStr());
            }
        });
        
        console.log('FRONTEND: Form validation passed, submitting to server...');
        console.log('=== FRONTEND: Form Submission Processing ===');
        
        return true;
    });

    // Clear error messages when user makes selections
    $('.multiselect-dropdown').on('sumo:closed', function() {
        var fieldName = $(this).attr('name');
        if (fieldName) {
            var errorElement = $('#error_' + fieldName.replace('[]', ''));
            if (errorElement.length) {
                errorElement.text('');
            }
        }
    });

    // Show/hide date fields based on search type
    var initialSearchType = $('select[name="search_type"]').val();
    console.log('Initial search type:', initialSearchType);
    showdate(initialSearchType);
    
    // Ensure datepicker is initialized for visible date fields
    setTimeout(function() {
        if ($('.search_date:visible').length > 0) {
            $('.date').datepicker({
                format: 'dd/mm/yyyy',
                autoclose: true,
                todayHighlight: true,
                orientation: "bottom auto",
                todayBtn: "linked"
            });
        }
    }, 500);

});

// Global export functions (outside document.ready)
function printDiv() {
    document.getElementById("print").style.display = "none";
    document.getElementById("btnExport").style.display = "none";
    document.getElementById("printhead").style.display = "block";
    
    // Create a print-specific version with smaller fonts
    var divElements = document.getElementById('transfee').innerHTML;
    var printContent = divElements.replace(/class="table table-striped table-bordered table-hover"/g, 'class="table table-bordered" style="font-size: 8px;"');
    
    var oldPage = document.body.innerHTML;
    document.body.innerHTML =
            "<html><head><title><?php echo $this->lang->line('fee_collection_report_column_wise'); ?></title>" +
            "<style>@page { size: landscape; margin: 0.5in; } " +
            "table { border-collapse: collapse; width: 100%; font-size: 8px; } " +
            "th, td { border: 1px solid #000; padding: 2px; text-align: center; } " +
            "th { background-color: #f0f0f0; font-weight: bold; } " +
            ".total-bg { background-color: #d9d9d9 !important; font-weight: bold; }" +
            "</style></head><body>" +
            printContent + "</body>";
    window.print();
    document.body.innerHTML = oldPage;
    document.getElementById("printhead").style.display = "none";
    location.reload(true);
}

function exportToExcel(){
    var htmls = "";
    var uri = 'data:application/vnd.ms-excel;base64,';
    var template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table border="1">{table}</table></body></html>';
    var base64 = function(s) {
        return window.btoa(unescape(encodeURIComponent(s)))
    };

    var format = function(s, c) {
        return s.replace(/{(\w+)}/g, function(m, p) {
            return c[p];
        })
    };
    
    var tab_text = "<tr>";
    var textRange;
    var j = 0;
    var val="";
    tab = document.getElementById('headerTable'); // id of table

    // Add report title
    tab_text += "<tr><td colspan='20' style='text-align:center;font-weight:bold;font-size:16px;'><?php echo $this->lang->line('fee_collection_report_column_wise'); ?></td></tr>";
    tab_text += "<tr><td colspan='20' style='text-align:center;'><?php echo date('Y-m-d H:i:s'); ?></td></tr>";
    tab_text += "<tr><td colspan='20'></td></tr>";

    for (j = 0; j < tab.rows.length; j++)
    {
        tab_text = tab_text + tab.rows[j].innerHTML + "</tr>";
    }

    var ctx = {
        worksheet : 'Fee Collection Report Columnwise',
        table : tab_text
    }

    var link = document.createElement("a");
    link.download = "fee_collection_report_columnwise_" + new Date().toISOString().slice(0,10) + ".xls";
    link.href = uri + base64(format(template, ctx));
    link.click();
}

function fnExcelReport(){
    exportToExcel();
}

<?php
if ($search_type == 'period') {
    ?>

        $(document).ready(function () {
            showdate('period');
        });

    <?php
}
?>


</script>


