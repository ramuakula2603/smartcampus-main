<style type="text/css">
    .checkbox-inline+.checkbox-inline,
    .radio-inline+.radio-inline {
        margin-left: 8px;
    }

    /* Session header styling */
    .session-header {
        border: none !important;
        cursor: pointer;
    }

    .session-heading {
        background: linear-gradient(to right, #3c8dbc, #67a8ce);
        color: white;
        padding: 8px 15px;
        border-radius: 4px;
        margin: 5px 0;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        font-size: 14px;
        text-align: left;
        transition: all 0.3s ease;
    }

    .session-header:hover .session-heading {
        background: linear-gradient(to right, #2d7baa, #5596bc);
        box-shadow: 0 3px 7px rgba(0,0,0,0.15);
    }

    .session-heading i {
        margin-right: 8px;
    }

    .session-heading strong {
        font-weight: 600;
    }

    .session-toggle {
        transition: transform 0.3s ease;
    }

    .session-toggle.collapsed i {
        transform: rotate(180deg);
    }

    .session-fees-container {
        transition: all 0.3s ease;
    }

    /* Improve table row styling */
    .table > tbody > tr.dark-gray > td {
        background-color: #f9f9f9;
    }

    /* Add a subtle hover effect to fee rows */
    .table > tbody > tr:not(.session-header):hover > td {
        background-color: #f5f5f5;
    }
</style>
<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
$language = $this->customlib->getLanguage();
$language_name = $language["short_code"];
?>
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12">
            <section class="content-header">

            </section>
        </div>
        <div>
            <a id="sidebarCollapse" class="studentsideopen"><i class="fa fa-navicon"></i></a>
            <aside class="studentsidebar">
                <div class="stutop" id="">
                    <!-- Create the tabs -->
                    <div class="studentsidetopfixed">
                        <p class="classtap">
                            <?php echo $student["class"]; ?> <a href="#" data-toggle="control-sidebar"
                                class="studentsideclose"><i class="fa fa-times"></i></a>
                        </p>
                        <ul class="nav nav-justified studenttaps">
                            <?php foreach ($class_section as $skey => $svalue) {
                                ?>
                                <li <?php
                                if ($student["section_id"] == $svalue["section_id"]) {
                                    echo "class='active'";
                                }
                                ?>>
                                    <a href="#section<?php echo $svalue["section_id"] ?>" data-toggle="tab">
                                        <?php print_r($svalue["section"]); ?>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>

                    <!-- Tab panes -->
                    <div class="tab-content">
                        <?php foreach ($class_section as $skey => $snvalue) {
                            ?>
                            <div class="tab-pane <?php
                            if ($student["section_id"] == $snvalue["section_id"]) {
                                echo "active";
                            }
                            ?>" id="section<?php echo $snvalue["section_id"]; ?>">
                                <?php
                                foreach ($studentlistbysection as $stkey => $stvalue) {
                                    if ($stvalue['section_id'] == $snvalue["section_id"]) {
                                        ?>
                                        <div class="studentname">
                                            <a class=""
                                                href="<?php echo base_url() . "studentfee/addfee/" . $stvalue["student_session_id"] ?>">
                                                <div class="icon"><img
                                                        src="<?php echo base_url() . $stvalue["image"] . img_time(); ?>"
                                                        alt="User Image"></div>
                                                <div class="student-tittle">
                                                    <?php echo $stvalue["firstname"] . " " . $stvalue["lastname"]; ?>
                                                </div>
                                            </a>
                                        </div>
                                        <?php
                                    }
                                }
                                ?>
                            </div>
                        <?php } ?>
                        <div class="tab-pane" id="sectionB">
                            <h3 class="control-sidebar-heading">Recent Activity 2</h3>
                        </div>
                        <div class="tab-pane" id="sectionC">
                            <h3 class="control-sidebar-heading">Recent Activity 3</h3>
                        </div>
                        <div class="tab-pane" id="sectionD">
                            <h3 class="control-sidebar-heading">Recent Activity 3</h3>
                        </div>
                        <!-- /.tab-pane -->
                    </div>
                </div>
            </aside>
        </div>
    </div>
    <!-- /.control-sidebar -->
    <section class="content">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-md-4">
                                <h3 class="box-title">
                                    <?php echo $this->lang->line('student_fees'); ?>
                                </h3>
                            </div>
                            <div class="col-md-8">
                                <div class="btn-group pull-right">
                                    <a href="<?php echo base_url() ?>studentfee" type="button"
                                        class="btn btn-primary btn-xs">
                                        <i class="fa fa-arrow-left"></i>
                                        <?php echo $this->lang->line('back'); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div><!--./box-header-->
                    <div class="box-body" style="padding-top:0;">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="sfborder-top-border">
                                    <div class="col-md-2">
                                        <img width="115" height="115"
                                            class="mt5 mb10 sfborder-img-shadow img-responsive img-rounded" src="<?php
                                            if (!empty($student["image"])) {
                                                echo $this->media_storage->getImageURL($student["image"]);
                                            } else {
                                                if ($student['gender'] == 'Female') {
                                                    echo $this->media_storage->getImageURL("uploads/student_images/default_female.jpg");
                                                } elseif ($student['gender'] == 'Male') {
                                                    echo $this->media_storage->getImageURL("uploads/student_images/default_male.jpg");
                                                }
                                            }
                                            ?>
                                        " alt="No Image">
                                    </div>
                                    <div class="col-md-10">
                                        <div class="row">
                                            <table class="table table-striped mb0 font13">
                                                <tbody>
                                                    <tr>
                                                        <th class="bozero">
                                                            <?php echo $this->lang->line('name'); ?>
                                                        </th>
                                                        <td class="bozero">
                                                            <?php echo $this->customlib->getFullName($student['firstname'], $student['middlename'], $student['lastname'], $sch_setting->middlename, $sch_setting->lastname); ?>
                                                        </td>
                                                        <th class="bozero">
                                                            <?php echo $this->lang->line('class_section'); ?>
                                                        </th>
                                                        <td class="bozero">
                                                            <?php echo $student['class'] . " (" . $student['section'] . ")" ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>
                                                            <?php echo $this->lang->line('father_name'); ?>
                                                        </th>
                                                        <td>
                                                            <?php echo $student['father_name']; ?>
                                                        </td>
                                                        <th>
                                                            <?php echo $this->lang->line('admission_no'); ?>
                                                        </th>
                                                        <td>
                                                            <?php echo $student['admission_no']; ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>
                                                            <?php echo $this->lang->line('mobile_number'); ?>
                                                        </th>
                                                        <td>
                                                            <?php echo $student['mobileno']; ?>
                                                        </td>
                                                        <th>
                                                            <?php echo $this->lang->line('roll_number'); ?>
                                                        </th>
                                                        <td>
                                                            <?php echo $student['roll_no']; ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>
                                                            <?php echo $this->lang->line('category'); ?>
                                                        </th>
                                                        <td>
                                                            <?php
                                                            foreach ($categorylist as $value) {
                                                                if ($student['category_id'] == $value['id']) {
                                                                    echo $value['category'];
                                                                }
                                                            }
                                                            ?>
                                                        </td>
                                                        <?php if ($sch_setting->rte) { ?>
                                                            <th>
                                                                <?php echo $this->lang->line('rte'); ?>
                                                            </th>
                                                            <td><b class="text-danger">
                                                                    <?php echo $student['rte']; ?>
                                                                </b>
                                                            </td>
                                                        <?php } ?>
                                                    </tr>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Advance Payment Information Section -->
                            <div class="col-md-12">
                                <div class="box box-info" style="margin-bottom: 10px;">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">
                                            <i class="fa fa-credit-card"></i> <?php echo $this->lang->line('advance_payment_information'); ?>
                                        </h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-primary btn-sm" onclick="openAdvancePaymentModal('<?php echo $student_session_id; ?>', '<?php echo addslashes($this->customlib->getFullName($student['firstname'], $student['middlename'], $student['lastname'], $sch_setting->middlename, $sch_setting->lastname)); ?>', '<?php echo $student['admission_no']; ?>', '<?php echo $student['class'] . ' (' . $student['section'] . ')'; ?>', '<?php echo addslashes($student['father_name']); ?>')">
                                                <i class="fa fa-plus"></i> <?php echo $this->lang->line('add_advance_payment'); ?>
                                            </button>
                                            <?php if (isset($advance_balance) && $advance_balance > 0) { ?>
                                                <button type="button" class="btn btn-info btn-sm" onclick="viewAdvanceHistory('<?php echo $student_session_id; ?>')">
                                                    <i class="fa fa-history"></i> <?php echo $this->lang->line('view_history'); ?>
                                                </button>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="info-box bg-green">
                                                    <span class="info-box-icon"><i class="fa fa-money"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text"><?php echo $this->lang->line('available_advance_balance'); ?></span>
                                                        <span class="info-box-number" id="advance-balance-display">
                                                            <?php echo $currency_symbol . amountFormat(isset($advance_balance) ? $advance_balance : 0); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="info-box bg-blue">
                                                    <span class="info-box-icon"><i class="fa fa-list"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text"><?php echo $this->lang->line('total_advance_payments'); ?></span>
                                                        <span class="info-box-number">
                                                            <?php echo isset($advance_payments) ? count($advance_payments) : 0; ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="info-box bg-yellow">
                                                    <span class="info-box-icon"><i class="fa fa-exchange"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text"><?php echo $this->lang->line('usage_transactions'); ?></span>
                                                        <span class="info-box-number">
                                                            <?php echo isset($advance_usage_history) ? count($advance_usage_history) : 0; ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if (isset($advance_balance) && $advance_balance > 0) { ?>
                                            <div class="alert alert-info">
                                                <i class="fa fa-info-circle"></i>
                                                <strong><?php echo $this->lang->line('note'); ?>:</strong>
                                                <?php echo $this->lang->line('advance_payment_auto_apply_note'); ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div
                                    style="background: #dadada; height: 1px; width: 100%; clear: both; margin-bottom: 10px;">
                                </div>
                            </div>
                        </div>
                        <div class="row no-print mb10">
                            <div class="col-md-12 mDMb10">


                                <div class="float-rtl-right float-left">
                                    <button type="button" class="btn btn-sm btn-info printSelected" id="load"
                                        data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('please_wait') ?>"><i
                                            class="fa fa-print"></i>
                                        <?php echo $this->lang->line('print_selected'); ?>
                                    </button>

                                    <?php if ($this->rbac->hasPrivilege('collect_fees', 'can_add')) { ?>
                                        <button type="button" class="btn btn-sm btn-danger ministatement" id="load"
                                            data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('please_wait') ?>"><i
                                                class="fa fa-print"></i>
                                            <?php echo $this->lang->line('mini_statement'); ?>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-warning collectSelected" id="load"
                                            data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('please_wait') ?>"><i
                                                class="fa fa-money"></i>
                                            <?php echo $this->lang->line('collect_selected'); ?>
                                        </button>
                                        
                                        <button type="button" class="btn btn-sm btn-success viewAdvanceTransfers" 
                                            data-student-session-id="<?php echo $student_session_id; ?>"
                                            title="View Advance Payment Transfers">
                                            <i class="fa fa-exchange"></i>
                                            Advance Transfers
                                        </button>
                                    <?php } ?>

                                    <?php
                                    if ($student_processing_fee) { ?>
                                        <a href="javascript:void(0)" class="btn btn-sm btn-info getProcessingfees"
                                            data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('please_wait') ?>"><i
                                                class="fa fa-money"></i>
                                            <?php echo $this->lang->line('processing_fees') ?>
                                        </a>
                                        <?php
                                    }
                                    ?>
                                </div>


                                <span class="pull-right pt5">
                                    <?php echo $this->lang->line('date'); ?>:
                                    <?php echo date($this->customlib->getSchoolDateFormat()); ?>
                                </span>
                            </div>
                        </div>
                        <?php
                        $student_admission_no = '';
                        if ($student['admission_no'] != '') {
                            $student_admission_no = ' (' . $student['admission_no'] . ')';
                        }
                        ?>
                        <!-- Session headers will be displayed within the table -->
                        <div class="table-responsive">
                            <div class="download_label">
                                <?php echo $this->lang->line('student_fees') . ": " . $student['firstname'] . " " . $student['lastname'] . $student_admission_no; ?>
                            </div>
                            <table class="table table-striped table-bordered table-hover example table-fixed-header">
                                <thead class="header">
                                    <tr>
                                        <th style="width: 10px"><input type="checkbox" id="select_all" /></th>
                                        <th align="left">
                                            <?php echo $this->lang->line('fees_group'); ?>
                                        </th>
                                        <th align="left">
                                            <?php echo $this->lang->line('fees_code'); ?>
                                        </th>
                                        <th align="left" class="text text-left">
                                            <?php echo $this->lang->line('due_date'); ?>
                                        </th>
                                        <th align="left" class="text text-left">
                                            <?php echo $this->lang->line('status'); ?>
                                        </th>
                                        <th class="text text-right">
                                            <?php echo $this->lang->line('amount') ?> <span>
                                                <?php echo "(" . $currency_symbol . ")"; ?>
                                            </span>
                                        </th>
                                        <th class="text text-left">
                                            <?php echo $this->lang->line('payment_id'); ?>
                                        </th>
                                        <th class="text text-left">
                                            <?php echo $this->lang->line('mode'); ?>
                                        </th>
                                        <th class="text text-left">
                                            <?php echo $this->lang->line('date'); ?>
                                        </th>
                                        <th class="text text-right">
                                            <?php echo $this->lang->line('discount'); ?> <span>
                                                <?php echo "(" . $currency_symbol . ")"; ?>
                                            </span>
                                        </th>
                                        <th class="text text-right">
                                            <?php echo $this->lang->line('fine'); ?> <span>
                                                <?php echo "(" . $currency_symbol . ")"; ?>
                                            </span>
                                        </th>
                                        <th class="text text-right">
                                            <?php echo $this->lang->line('paid'); ?> <span>
                                                <?php echo "(" . $currency_symbol . ")"; ?>
                                            </span>
                                        </th>
                                        <th class="text text-right">
                                            <?php echo $this->lang->line('balance'); ?> <span>
                                                <?php echo "(" . $currency_symbol . ")"; ?>
                                            </span>
                                        </th>
                                        <th class="text text-right noExport">
                                            <?php echo $this->lang->line('action'); ?>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php

                                    $total_amount = 0;
                                    $total_deposite_amount = 0;
                                    $total_fine_amount = 0;
                                    $total_fees_fine_amount = 0;

                                    $total_discount_amount = 0;
                                    $total_balance_amount = 0;
                                    $alot_fee_discount = 0;

                                    // Initialize the displayed_sessions array if it doesn't exist
                                    if(!isset($displayed_sessions)) {
                                        $displayed_sessions = array(); // Track which sessions we've already displayed
                                    }

                                    // Display fees grouped by session
                                    if(isset($fees_by_session) && !empty($fees_by_session)) {
                                        foreach($sessions as $session_id => $session_name) {
                                            if(isset($fees_by_session[$session_id]) && !empty($fees_by_session[$session_id])) {
                                                // Mark this session as displayed for regular fees only
                                                $displayed_sessions['regular_' . $session_id] = true;
                                                // Display session header
                                                ?>
                                                <tr class="session-header" data-session-id="<?php echo $session_id; ?>">
                                                    <td colspan="14" class="text-center">
                                                        <div class="session-heading">
                                                            <i class="fa fa-calendar-check-o"></i>
                                                            <strong><?php echo $session_name; ?> Session:</strong> Fees for <?php echo $session_name; ?> session
                                                            <span class="session-toggle pull-right">
                                                                <i class="fa fa-chevron-up"></i>
                                                            </span>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php

                                                // Add a container for the session fees
                                                ?>
                                                <tbody class="session-fees-container" data-session-id="<?php echo $session_id; ?>">
                                                <?php
                                                // Display fees for this session
                                                foreach($fees_by_session[$session_id] as $key => $fee) {
                                                    foreach ($fee->fees as $fee_key => $fee_value) {
                                            $fee_paid = 0;
                                            $fee_discount = 0;
                                            $fee_fine = 0;
                                            $fees_fine_amount = 0;
                                            $feetype_balance = 0;
                                            if (!empty($fee_value->amount_detail)) {
                                                $fee_deposits = json_decode(($fee_value->amount_detail));

                                                foreach ($fee_deposits as $fee_deposits_key => $fee_deposits_value) {
                                                    $fee_paid = $fee_paid + $fee_deposits_value->amount;
                                                    $fee_discount = $fee_discount + $fee_deposits_value->amount_discount;
                                                    $fee_fine = $fee_fine + $fee_deposits_value->amount_fine;
                                                }
                                            }
                                            if (($fee_value->due_date != "0000-00-00" && $fee_value->due_date != null) && (strtotime($fee_value->due_date) < strtotime(date('Y-m-d')))) {
                                                $fees_fine_amount = $fee_value->fine_amount;
                                                $total_fees_fine_amount = $total_fees_fine_amount + $fee_value->fine_amount;
                                            }

                                            $total_amount += $fee_value->amount;
                                            $total_discount_amount += $fee_discount;
                                            $total_deposite_amount += $fee_paid;
                                            $total_fine_amount += $fee_fine;
                                            $feetype_balance = $fee_value->amount - ($fee_paid + $fee_discount);
                                            $total_balance_amount += $feetype_balance;
                                            ?>
                                            <?php
                                            if ($feetype_balance > 0 && strtotime($fee_value->due_date) < strtotime(date('Y-m-d'))) {
                                                ?>
                                                <tr class="danger font12">
                                                    <?php
                                            } else {
                                                ?>
                                                <tr class="dark-gray">
                                                    <?php
                                            }
                                            ?>
                                                <td>
                                                    <input class="checkbox" type="checkbox" name="fee_checkbox"
                                                        data-fee_master_id="<?php echo $fee_value->id ?>" data-otherfeecat=""
                                                        data-fee_session_group_id="<?php echo $fee_value->fee_session_group_id ?>"
                                                        data-fee_groups_feetype_id="<?php echo $fee_value->fee_groups_feetype_id ?>"
                                                        data-fee_category="fees" data-trans_fee_id="0">
                                                </td>
                                                <td align="left" class="text-rtl-right">
                                                    <?php
                                                    if ($fee_value->is_system) {
                                                        echo $fee_value->name;
                                                        echo $this->lang->line($fee_value->name) . " (" . $this->lang->line($fee_value->type) . ")";
                                                    } else {
                                                        echo $fee_value->name . " (" . $fee_value->type . ")";
                                                    }
                                                    ?>
                                                </td>
                                                <td align="left" class="text-rtl-right">
                                                    <?php
                                                    if ($fee_value->is_system) {
                                                        echo $this->lang->line($fee_value->code);
                                                    } else {
                                                        echo $fee_value->code;
                                                    }

                                                    ?>
                                                </td>
                                                <td align="left" class="text text-left">
                                                    <?php
                                                    if ($fee_value->due_date == "0000-00-00") {

                                                    } else {

                                                        if ($fee_value->due_date) {
                                                            echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($fee_value->due_date));
                                                        }
                                                    }
                                                    ?>
                                                </td>
                                                <td align="left" class="text text-left width85">
                                                    <?php
                                                    if ($feetype_balance == 0) {
                                                        ?><span class="label label-success">
                                                            <?php echo $this->lang->line('paid'); ?>
                                                        </span>
                                                        <?php
                                                    } else if (!empty($fee_value->amount_detail)) {
                                                        ?><span class="label label-warning">
                                                            <?php echo $this->lang->line('partial'); ?>
                                                            </span>
                                                        <?php
                                                    } else {
                                                        ?><span class="label label-danger">
                                                            <?php echo $this->lang->line('unpaid'); ?>
                                                            </span>
                                                        <?php
                                                    }
                                                    ?>
                                                </td>
                                                <td class="text text-right">
                                                    <?php echo amountFormat($fee_value->amount);
                                                    if (($fee_value->due_date != "0000-00-00" && $fee_value->due_date != null) && (strtotime($fee_value->due_date) < strtotime(date('Y-m-d')))) {
                                                        ?>
                                                        <span data-toggle="popover" class="text text-danger detail_popover">
                                                            <?php echo " + " . amountFormat($fee_value->fine_amount); ?>
                                                        </span>

                                                        <div class="fee_detail_popover" style="display: none">
                                                            <?php
                                                            if ($fee_value->fine_amount != "") {
                                                                ?>
                                                                <p class="text text-danger">
                                                                    <?php echo $this->lang->line('fine'); ?>
                                                                </p>
                                                                <?php
                                                            }
                                                            ?>
                                                        </div>
                                                        <?php
                                                    }
                                                    ?>
                                                </td>
                                                <td class="text text-left"></td>
                                                <td class="text text-left"></td>
                                                <td class="text text-left"></td>
                                                <td class="text text-right">
                                                    <?php
                                                    echo amountFormat($fee_discount);
                                                    ?>
                                                </td>
                                                <td class="text text-right">
                                                    <?php
                                                    echo amountFormat($fee_fine);
                                                    ?>
                                                </td>
                                                <td class="text text-right">
                                                    <?php
                                                    echo amountFormat($fee_paid);
                                                    ?>
                                                </td>
                                                <td class="text text-right">
                                                    <?php
                                                    // Separate display logic for payment collection buttons vs other buttons
                                                    $payment_buttons_display = "ss-none"; // Hide payment buttons for fully paid fees
                                                    if ($feetype_balance > 0) {
                                                        $payment_buttons_display = "";
                                                        echo amountFormat($feetype_balance);
                                                    }
                                                    ?>
                                                </td>
                                                <td width="100">
                                                    <div class="btn-group">
                                                        <div class="pull-right">

                                                            <?php if ($this->rbac->hasPrivilege('collect_fees', 'can_add')) { ?>
                                                                <?php
                                                                // Check if there's a pending discount request for this specific fee
                                                                $pending_key = $fee_value->fee_groups_feetype_id . '_' . $fee->id;
                                                                $has_pending_discount = isset($pending_discount_lookup) && isset($pending_discount_lookup[$pending_key]);
                                                                $discount_button_class = $has_pending_discount ? 'btn btn-xs btn-warning disabled' : 'btn btn-xs btn-default myCollectFeeBtn';
                                                                $discount_button_title = $has_pending_discount ? $this->lang->line('discount_req') . ' - Pending Approval' : $this->lang->line('discount_req');
                                                                $discount_button_disabled = $has_pending_discount ? 'disabled="disabled"' : '';
                                                                $discount_modal_target = $has_pending_discount ? '' : 'data-toggle="modal" data-target="#myFeesdiscountModal"';
                                                                ?>
                                                                <button type="button"
                                                                    data-student_session_id="<?php echo $fee->student_session_id; ?>"
                                                                    data-student_fees_master_id="<?php echo $fee->id; ?>"
                                                                    data-fee_groups_feetype_id="<?php echo $fee_value->fee_groups_feetype_id; ?>"
                                                                    data-group="<?php echo ($fee_value->is_system) ? $this->lang->line($fee_value->name) . " (" . $this->lang->line($fee_value->type) . ")" : $fee_value->name . " (" . $fee_value->type . ")"; ?>"
                                                                    data-type="<?php echo ($fee_value->is_system) ? $this->lang->line($fee_value->type) : $fee_value->code; ?>"
                                                                    class="<?php echo $discount_button_class; ?> <?php echo $payment_buttons_display; ?>"
                                                                    title="<?php echo $discount_button_title; ?>"
                                                                    <?php echo $discount_modal_target; ?>
                                                                    <?php echo $discount_button_disabled; ?>
                                                                    data-fee-category="fees" data-trans_fee_id="0">
                                                                    <i class="fa fa-percent"></i>
                                                                    <?php if ($has_pending_discount): ?>
                                                                        <small style="font-size: 8px; display: block; line-height: 1;">PENDING</small>
                                                                    <?php endif; ?>
                                                                </button>

                                                                <button type="button"
                                                                    data-student_session_id="<?php echo $fee->student_session_id; ?>"
                                                                    data-student_fees_master_id="<?php echo $fee->id; ?>"
                                                                    data-fee_groups_feetype_id="<?php echo $fee_value->fee_groups_feetype_id; ?>"
                                                                    data-group="<?php echo ($fee_value->is_system) ? $this->lang->line($fee_value->name) . " (" . $this->lang->line($fee_value->type) . ")" : $fee_value->name . " (" . $fee_value->type . ")"; ?>"
                                                                    data-type="<?php echo ($fee_value->is_system) ? $this->lang->line($fee_value->type) : $fee_value->code; ?>"
                                                                    class="btn btn-xs btn-default myCollectFeeBtn <?php echo $payment_buttons_display; ?>"
                                                                    title="<?php echo $this->lang->line('add_fees'); ?>"
                                                                    data-toggle="modal" data-target="#myFeesModal"
                                                                    data-fee-category="fees" data-trans_fee_id="0"><i
                                                                        class="fa fa-plus"></i></button>
                                                            <?php } ?>

                                                            <?php
                                                            // Add View History button for fees that have payment history
                                                            if (!empty($fee_value->amount_detail)) { ?>
                                                                <button type="button" class="btn btn-xs btn-info viewFeeHistory"
                                                                    data-student_session_id="<?php echo $fee->student_session_id; ?>"
                                                                    data-student_fees_master_id="<?php echo $fee->id; ?>"
                                                                    data-fee_groups_feetype_id="<?php echo $fee_value->fee_groups_feetype_id; ?>"
                                                                    data-fee_session_group_id="<?php echo $fee_value->fee_session_group_id; ?>"
                                                                    data-group="<?php echo ($fee_value->is_system) ? $this->lang->line($fee_value->name) . " (" . $this->lang->line($fee_value->type) . ")" : $fee_value->name . " (" . $fee_value->type . ")"; ?>"
                                                                    title="<?php echo $this->lang->line('view_history'); ?>"
                                                                    data-toggle="modal" data-target="#feeHistoryModal">
                                                                    <i class="fa fa-history"></i>
                                                                </button>
                                                            <?php } ?>

                                                            <button class="btn btn-xs btn-default printInv"
                                                                data-student_session_id="<?php echo $fee->student_session_id; ?>"
                                                                data-fee_master_id="<?php echo $fee_value->id ?>"
                                                                data-fee_session_group_id="<?php echo $fee_value->fee_session_group_id ?>"
                                                                data-fee_groups_feetype_id="<?php echo $fee_value->fee_groups_feetype_id ?>"
                                                                data-fee-category="fees" data-trans_fee_id="0"
                                                                title="<?php echo $this->lang->line('print'); ?>"
                                                                data-loading-text="<i class='fa fa-spinner fa-spin '></i>"><i
                                                                    class="fa fa-print"></i> </button>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
                                            if (!empty($fee_value->amount_detail)) {

                                                $fee_deposits = json_decode(($fee_value->amount_detail));
                                                foreach ($fee_deposits as $fee_deposits_key => $fee_deposits_value) {
                                                    ?>
                                                    <tr class="white-td">
                                                        <td align="left"></td>
                                                        <td align="left"></td>
                                                        <td align="left"></td>
                                                        <td align="left"></td>
                                                        <td align="left"><p class="text text-info">
                                                                        <?php echo $fee_deposits_value->description; ?>
                                                                    </p></td>
                                                        <td class="text-right"><img
                                                                src="<?php echo $this->media_storage->getImageURL('backend/images/table-arrow.png'); ?>"
                                                                alt="" /></td>
                                                        <td class="text text-left"> <a href="#" data-toggle="popover"
                                                                class="detail_popover">
                                                                <?php echo $fee_value->student_fees_deposite_id . "/" . $fee_deposits_value->inv_no; ?>
                                                            </a>
                                                            <div class="fee_detail_popover" style="display: none">
                                                                <?php
                                                                if ($fee_deposits_value->description == "") {
                                                                    ?>
                                                                    <p class="text text-danger">
                                                                        <?php echo $this->lang->line('no_description'); ?>
                                                                    </p>
                                                                    <?php
                                                                } else {
                                                                    ?>
                                                                    <p class="text text-info">
                                                                        <?php echo $fee_deposits_value->description; ?>
                                                                    </p>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </div>
                                                        </td>
                                                        <td class="text text-left">
                                                            <?php echo $this->lang->line(strtolower($fee_deposits_value->payment_mode)); ?>
                                                        </td>
                                                        <td class="text text-left">
                                                            <?php if ($fee_deposits_value->date) {
                                                                echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($fee_deposits_value->date));
                                                            } ?>
                                                        </td>
                                                        <td class="text text-right">
                                                            <?php echo amountFormat($fee_deposits_value->amount_discount); ?>
                                                        </td>
                                                        <td class="text text-right">
                                                            <?php echo amountFormat($fee_deposits_value->amount_fine); ?>
                                                        </td>
                                                        <td class="text text-right">
                                                            <?php echo amountFormat($fee_deposits_value->amount); ?>
                                                        </td>
                                                        <td></td>
                                                        <td class="text text-right">
                                                            <div class="btn-group">
                                                                <div class="pull-right">
                                                                    <?php if ($this->rbac->hasPrivilege('collect_fees', 'can_delete')) { ?>
                                                                        <button class="btn btn-default btn-xs"
                                                                            data-invoiceno="<?php echo $fee_value->student_fees_deposite_id . "/" . $fee_deposits_value->inv_no; ?>"
                                                                            data-main_invoice="<?php echo $fee_value->student_fees_deposite_id ?>"
                                                                            data-sub_invoice="<?php echo $fee_deposits_value->inv_no ?>"
                                                                            data-toggle="modal" data-target="#confirm-delete"
                                                                            title="<?php echo $this->lang->line('revert'); ?>">
                                                                            <i class="fa fa-undo"> </i>
                                                                        </button>
                                                                    <?php } ?>
                                                                    <button class="btn btn-xs btn-default printDoc"
                                                                        data-student_session_id="<?php echo $fee->student_session_id; ?>"
                                                                        data-fee_master_id="<?php echo $fee_value->id ?>"
                                                                        data-fee_session_group_id="<?php echo $fee_value->fee_session_group_id ?>"
                                                                        data-fee_groups_feetype_id="<?php echo $fee_value->fee_groups_feetype_id ?>"

                                                                        data-main_invoice="<?php echo $fee_value->student_fees_deposite_id ?>"
                                                                        data-sub_invoice="<?php echo $fee_deposits_value->inv_no ?>"
                                                                        title="<?php echo $this->lang->line('print'); ?>"><i
                                                                            class="fa fa-print"></i> </button>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
                                            }
                                            ?>
                                            <?php
                                        }
                                    }
                                    ?>

                                    <?php

                                    if (!empty($transport_fees)) {
                                        foreach ($transport_fees as $transport_fee_key => $transport_fee_value) {

                                            $fee_paid = 0;
                                            $fee_discount = 0;
                                            $fee_fine = 0;
                                            $fees_fine_amount = 0;
                                            $feetype_balance = 0;

                                            if (!empty($transport_fee_value->amount_detail)) {
                                                $fee_deposits = json_decode(($transport_fee_value->amount_detail));
                                                foreach ($fee_deposits as $fee_deposits_key => $fee_deposits_value) {
                                                    $fee_paid = $fee_paid + $fee_deposits_value->amount;
                                                    $fee_discount = $fee_discount + $fee_deposits_value->amount_discount;
                                                    $fee_fine = $fee_fine + $fee_deposits_value->amount_fine;
                                                }
                                            }

                                            $feetype_balance = $transport_fee_value->fees - ($fee_paid + $fee_discount);

                                            if (($transport_fee_value->due_date != "0000-00-00" && $transport_fee_value->due_date != null) && (strtotime($transport_fee_value->due_date) < strtotime(date('Y-m-d')))) {
                                                $fees_fine_amount = is_null($transport_fee_value->fine_percentage) ? $transport_fee_value->fine_amount : percentageAmount($transport_fee_value->fees, $transport_fee_value->fine_percentage);
                                                $total_fees_fine_amount = $total_fees_fine_amount + $fees_fine_amount;
                                            }

                                            $total_amount += $transport_fee_value->fees;
                                            $total_discount_amount += $fee_discount;
                                            $total_deposite_amount += $fee_paid;
                                            $total_fine_amount += $fee_fine;
                                            $total_balance_amount += $feetype_balance;

                                            if (strtotime($transport_fee_value->due_date) < strtotime(date('Y-m-d'))) {
                                                ?>
                                                <tr class="danger font12">
                                                    <?php
                                            } else {
                                                ?>
                                                <tr class="dark-gray">
                                                    <?php
                                            }
                                            ?>
                                                <td>
                                                    <input class="checkbox" type="checkbox" name="fee_checkbox"
                                                        data-fee_master_id="0" data-fee_session_group_id="0"
                                                        data-fee_groups_feetype_id="0" data-fee_category="transport"
                                                        data-otherfeecat=""
                                                        data-trans_fee_id="<?php echo $transport_fee_value->id; ?>">

                                                </td>
                                                <td align="left" class="text-rtl-right">
                                                    <?php echo $this->lang->line('transport_fees'); ?>
                                                </td>
                                                <td align="left" class="text-rtl-right">
                                                    <?php echo $transport_fee_value->month; ?>
                                                </td>
                                                <td align="left" class="text text-left">
                                                    <?php echo $this->customlib->dateformat($transport_fee_value->due_date); ?>
                                                </td>
                                                <td align="left" class="text text-left width85">
                                                    <?php
                                                    if ($feetype_balance == 0) {
                                                        ?><span class="label label-success">
                                                            <?php echo $this->lang->line('paid'); ?>
                                                        </span>
                                                        <?php
                                                    } else if (!empty($transport_fee_value->amount_detail)) {
                                                        ?><span class="label label-warning">
                                                            <?php echo $this->lang->line('partial'); ?>
                                                            </span>
                                                        <?php
                                                    } else {
                                                        ?><span class="label label-danger">
                                                            <?php echo $this->lang->line('unpaid'); ?>
                                                            </span>
                                                        <?php
                                                    }
                                                    ?>
                                                </td>
                                                <td class="text text-right">
                                                    <?php

                                                    echo amountFormat($transport_fee_value->fees);

                                                    if (($transport_fee_value->due_date != "0000-00-00" && $transport_fee_value->due_date != null) && (strtotime($transport_fee_value->due_date) < strtotime(date('Y-m-d')))) {
                                                        $tr_fine_amount = $transport_fee_value->fine_amount;
                                                        if ($transport_fee_value->fine_type != "" && $transport_fee_value->fine_type == "percentage") {

                                                            $tr_fine_amount = percentageAmount($transport_fee_value->fees, $transport_fee_value->fine_percentage);
                                                        }
                                                        ?>

                                                        <span data-toggle="popover" class="text text-danger detail_popover">
                                                            <?php echo " + " . amountFormat($tr_fine_amount); ?>
                                                        </span>
                                                        <div class="fee_detail_popover" style="display: none">
                                                            <?php
                                                            if ($tr_fine_amount != "") {
                                                                ?>
                                                                <p class="text text-danger">
                                                                    <?php echo $this->lang->line('fine'); ?>
                                                                </p>
                                                                <?php
                                                            }
                                                            ?>
                                                        </div>
                                                        <?php
                                                    }
                                                    ?>
                                                </td>
                                                <td class="text text-left"></td>
                                                <td class="text text-left"></td>
                                                <td class="text text-left"></td>
                                                <td class="text text-right">
                                                    <?php
                                                    echo amountFormat($fee_discount);
                                                    ?>
                                                </td>
                                                <td class="text text-right">
                                                    <?php
                                                    echo amountFormat($fee_fine);
                                                    ?>
                                                </td>
                                                <td class="text text-right">
                                                    <?php
                                                    echo amountFormat($fee_paid);
                                                    ?>
                                                </td>
                                                <td class="text text-right">
                                                    <?php
                                                    // Separate display logic for payment collection buttons vs other buttons
                                                    $payment_buttons_display = "ss-none"; // Hide payment buttons for fully paid fees
                                                    if ($feetype_balance > 0) {
                                                        $payment_buttons_display = "";
                                                        echo amountFormat($feetype_balance);
                                                    }
                                                    ?>
                                                </td>
                                                <td width="100">

                                                    <?php if ($this->rbac->hasPrivilege('collect_fees', 'can_add')) { ?>
                                                        <button type="button"
                                                            data-student_session_id="<?php echo $transport_fee_value->student_session_id; ?>"
                                                            data-student_fees_master_id="0" data-fee_groups_feetype_id="0"
                                                            data-group="<?php echo $this->lang->line('transport_fees'); ?>"
                                                            data-type="<?php echo $transport_fee_value->month; ?>"
                                                            class="btn btn-xs btn-default myCollectFeeBtn <?php echo $payment_buttons_display; ?>"
                                                            title="<?php echo $this->lang->line('add_fees'); ?>" data-toggle="modal"
                                                            data-target="#myFeesModal" data-fee-category="transport"
                                                            data-trans_fee_id="<?php echo $transport_fee_value->id; ?>"><i
                                                                class="fa fa-plus"></i></button>
                                                    <?php } ?>

                                                    <?php
                                                    // Add View History button for transport fees that have payment history
                                                    if (!empty($transport_fee_value->amount_detail)) { ?>
                                                        <button type="button" class="btn btn-xs btn-info viewTransportFeeHistory"
                                                            data-student_session_id="<?php echo $transport_fee_value->student_session_id; ?>"
                                                            data-trans_fee_id="<?php echo $transport_fee_value->id; ?>"
                                                            data-group="<?php echo $this->lang->line('transport_fees'); ?>"
                                                            data-type="<?php echo $transport_fee_value->month; ?>"
                                                            title="<?php echo $this->lang->line('view_history'); ?>"
                                                            data-toggle="modal" data-target="#transportFeeHistoryModal">
                                                            <i class="fa fa-history"></i>
                                                        </button>
                                                    <?php } ?>

                                                    <button class="btn btn-xs btn-default printInv"
                                                        data-student_session_id="<?php echo $transport_fee_value->student_session_id; ?>"
                                                        data-fee_master_id="0" data-fee_session_group_id="0"
                                                        data-fee_groups_feetype_id="0" data-fee-category="transport"
                                                        data-trans_fee_id="<?php echo $transport_fee_value->id; ?>"
                                                        title="<?php echo $this->lang->line('print'); ?>"
                                                        data-loading-text="<i class='fa fa-spinner fa-spin '></i>"><i
                                                            class="fa fa-print"></i> </button>

                                                </td>
                                            </tr>

                                            <?php
                                            if (!empty($transport_fee_value->amount_detail)) {

                                                $fee_deposits = json_decode(($transport_fee_value->amount_detail));

                                                foreach ($fee_deposits as $fee_deposits_key => $fee_deposits_value) {
                                                    ?>
                                                    <tr class="white-td">
                                                        <td align="left"></td>
                                                        <td align="left"></td>
                                                        <td align="left"></td>
                                                        <td align="left"></td>
                                                        <td align="left"></td>
                                                        <td class="text-right"><img
                                                                src="<?php echo base_url(); ?>backend/images/table-arrow.png" alt="" />
                                                        </td>
                                                        <td class="text text-left">

                                                            <a href="#" data-toggle="popover" class="detail_popover">
                                                                <?php echo $transport_fee_value->student_fees_deposite_id . "/" . $fee_deposits_value->inv_no; ?>
                                                            </a>
                                                            <div class="fee_detail_popover" style="display: none">
                                                                <?php
                                                                if ($fee_deposits_value->description == "") {
                                                                    ?>
                                                                    <p class="text text-danger">
                                                                        <?php echo $this->lang->line('no_description'); ?>
                                                                    </p>
                                                                    <?php
                                                                } else {
                                                                    ?>
                                                                    <p class="text text-info">
                                                                        <?php echo $fee_deposits_value->description; ?>
                                                                    </p>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </div>
                                                        </td>
                                                        <td class="text text-left">
                                                            <?php echo $this->lang->line(strtolower($fee_deposits_value->payment_mode)); ?>
                                                        </td>
                                                        <td class="text text-left">
                                                            <?php echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($fee_deposits_value->date)); ?>
                                                        </td>
                                                        <td class="text text-right">
                                                            <?php echo amountFormat($fee_deposits_value->amount_discount); ?>
                                                        </td>
                                                        <td class="text text-right">
                                                            <?php echo amountFormat($fee_deposits_value->amount_fine); ?>
                                                        </td>
                                                        <td class="text text-right">
                                                            <?php echo amountFormat($fee_deposits_value->amount); ?>
                                                        </td>
                                                        <td></td>
                                                        <td class="text text-right">
                                                            <div class="btn-group ">
                                                                <div class="pull-right">
                                                                    <?php if ($this->rbac->hasPrivilege('collect_fees', 'can_delete')) { ?>
                                                                        <button class="btn btn-default btn-xs"
                                                                            data-invoiceno="<?php echo $transport_fee_value->student_fees_deposite_id . "/" . $fee_deposits_value->inv_no; ?>"
                                                                            data-main_invoice="<?php echo $transport_fee_value->student_fees_deposite_id ?>"
                                                                            data-sub_invoice="<?php echo $fee_deposits_value->inv_no ?>"
                                                                            data-toggle="modal" data-target="#confirm-delete"
                                                                            title="<?php echo $this->lang->line('revert'); ?>">
                                                                            <i class="fa fa-undo"> </i>
                                                                        </button>
                                                                    <?php } ?>
                                                                    <button class="btn btn-xs btn-default printDoc"
                                                                        data-student_session_id="<?php echo $fee->student_session_id; ?>"
                                                                        data-fee_master_id="<?php echo $fee_value->id ?>"
                                                                        data-fee_session_group_id="<?php echo $fee_value->fee_session_group_id ?>"
                                                                        data-fee_groups_feetype_id="<?php echo $fee_value->fee_groups_feetype_id ?>"

                                                                        data-fee-category="transport"
                                                                        data-main_invoice="<?php echo $transport_fee_value->student_fees_deposite_id ?>"
                                                                        data-sub_invoice="<?php echo $fee_deposits_value->inv_no ?>"
                                                                        title="<?php echo $this->lang->line('print'); ?>"><i
                                                                            class="fa fa-print"></i> </button>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
                                            }
                                            ?>

                                            <?php
                                        }
                                    }

                                    ?>

                                    <?php
                                    // HOSTEL FEES SECTION - DO NOT DUPLICATE
                                    // This section should only appear ONCE on the page
                                    // Use a static flag to prevent duplicate execution

                                    static $hostel_fees_displayed = false;

                                    // Display hostel fees grouped by session (only once)
                                    if(!$hostel_fees_displayed && isset($hostel_fees_by_session) && !empty($hostel_fees_by_session)) {
                                        $hostel_fees_displayed = true; // Set flag to prevent duplicate display
                                        foreach($hostel_fees_by_session as $session_id => $hostel_fees_for_session) {
                                            if(!empty($hostel_fees_for_session)) {
                                                // Get session name
                                                $session_name = isset($sessions[$session_id]) ? $sessions[$session_id] : "Session " . $session_id;

                                                // Display session header for hostel fees
                                                ?>
                                                <tr class="session-header hostel-fees-header-unique" data-session-id="hostel-<?php echo $session_id; ?>">
                                                    <td colspan="14" class="text-center">
                                                        <div class="session-heading">
                                                            <i class="fa fa-calendar-check-o"></i>
                                                            <strong><?php echo $session_name; ?> Session:</strong> Hostel Fees for <?php echo $session_name; ?> session
                                                            <span class="session-toggle pull-right">
                                                                <i class="fa fa-chevron-up"></i>
                                                            </span>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php

                                                // Add a container for the session hostel fees
                                                ?>
                                                <tbody class="session-fees-container" data-session-id="hostel-<?php echo $session_id; ?>">
                                                <?php
                                                // Display hostel fees for this session
                                                foreach($hostel_fees_for_session as $hostel_fee_key => $hostel_fee_value) {

                                            $fee_paid = 0;
                                            $fee_discount = 0;
                                            $fee_fine = 0;
                                            $fees_fine_amount = 0;
                                            $feetype_balance = 0;

                                            if (!empty($hostel_fee_value->amount_detail)) {
                                                $fee_deposits = json_decode(($hostel_fee_value->amount_detail));
                                                foreach ($fee_deposits as $fee_deposits_key => $fee_deposits_value) {
                                                    $fee_paid = $fee_paid + $fee_deposits_value->amount;
                                                    $fee_discount = $fee_discount + $fee_deposits_value->amount_discount;
                                                    $fee_fine = $fee_fine + $fee_deposits_value->amount_fine;
                                                }
                                            }

                                            $feetype_balance = $hostel_fee_value->fees - ($fee_paid + $fee_discount);

                                            if (($hostel_fee_value->due_date != "0000-00-00" && $hostel_fee_value->due_date != null) && (strtotime($hostel_fee_value->due_date) < strtotime(date('Y-m-d')))) {
                                                $fees_fine_amount = is_null($hostel_fee_value->fine_percentage) ? $hostel_fee_value->fine_amount : percentageAmount($hostel_fee_value->fees, $hostel_fee_value->fine_percentage);
                                                $total_fees_fine_amount = $total_fees_fine_amount + $fees_fine_amount;
                                            }

                                            $total_amount += $hostel_fee_value->fees;
                                            $total_discount_amount += $fee_discount;
                                            $total_deposite_amount += $fee_paid;
                                            $total_fine_amount += $fee_fine;
                                            $total_balance_amount += $feetype_balance;

                                            if (strtotime($hostel_fee_value->due_date) < strtotime(date('Y-m-d'))) {
                                                ?>
                                                <tr class="danger font12">
                                                    <?php
                                            } else {
                                                ?>
                                                <tr class="dark-gray">
                                                    <?php
                                            }
                                            ?>
                                                <td>
                                                    <input class="checkbox" type="checkbox" name="fee_checkbox"
                                                        data-fee_master_id="0" data-fee_session_group_id="0"
                                                        data-fee_groups_feetype_id="0" data-fee_category="hostel"
                                                        data-otherfeecat=""
                                                        data-hostel_fee_id="<?php echo $hostel_fee_value->id; ?>">

                                                </td>
                                                <td align="left" class="text-rtl-right">
                                                    <?php echo $this->lang->line('hostel_fees'); ?>
                                                </td>
                                                <td align="left" class="text-rtl-right">
                                                    <?php echo $hostel_fee_value->month; ?>
                                                </td>
                                                <td align="left" class="text text-left">
                                                    <?php echo $this->customlib->dateformat($hostel_fee_value->due_date); ?>
                                                </td>
                                                <td align="left" class="text text-left width85">
                                                    <?php
                                                    if ($feetype_balance == 0) {
                                                        ?><span class="label label-success">
                                                            <?php echo $this->lang->line('paid'); ?>
                                                        </span>
                                                        <?php
                                                    } else if (!empty($hostel_fee_value->amount_detail)) {
                                                        ?><span class="label label-warning">
                                                            <?php echo $this->lang->line('partial'); ?>
                                                            </span>
                                                        <?php
                                                    } else {
                                                        ?><span class="label label-danger">
                                                            <?php echo $this->lang->line('unpaid'); ?>
                                                        </span>
                                                        <?php
                                                    }
                                                    ?>
                                                </td>
                                                <td class="text text-right">
                                                    <?php

                                                    echo amountFormat($hostel_fee_value->fees);

                                                    if (($hostel_fee_value->due_date != "0000-00-00" && $hostel_fee_value->due_date != null) && (strtotime($hostel_fee_value->due_date) < strtotime(date('Y-m-d')))) {
                                                        $hr_fine_amount = $hostel_fee_value->fine_amount;
                                                        if ($hostel_fee_value->fine_type != "" && $hostel_fee_value->fine_type == "percentage") {

                                                            $hr_fine_amount = percentageAmount($hostel_fee_value->fees, $hostel_fee_value->fine_percentage);
                                                        }
                                                        ?>

                                                        <span data-toggle="popover" class="text text-danger detail_popover">
                                                            <?php echo " + " . amountFormat($hr_fine_amount); ?>
                                                        </span>

                                                        <div class="fee_detail_popover" style="display: none">
                                                            <?php
                                                            if ($hostel_fee_value->fine_type == "none") {
                                                                $fine_title = $this->lang->line('fine');
                                                            } else {
                                                                $fine_title = $this->lang->line('fine') . " @" . $hostel_fee_value->fine_percentage . "%";
                                                            }
                                                            ?>
                                                            <p class="text text-danger"><?php echo $fine_title . " : " . amountFormat($hr_fine_amount); ?></p>
                                                        </div>
                                                        <?php
                                                    }
                                                    ?>
                                                </td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td class="text text-right"><?php echo amountFormat($fee_discount); ?></td>
                                                <td class="text text-right"><?php echo amountFormat($fee_fine); ?></td>
                                                <td class="text text-right"><?php echo amountFormat($fee_paid); ?></td>
                                                <td class="text text-right">
                                                    <?php
                                                    // Separate display logic for payment collection buttons vs other buttons
                                                    $payment_buttons_display = "ss-none"; // Hide payment buttons for fully paid fees
                                                    if ($feetype_balance > 0) {
                                                        $payment_buttons_display = "";
                                                        echo amountFormat($feetype_balance);
                                                    }
                                                    ?>
                                                </td>
                                                <td width="100">

                                                    <?php if ($this->rbac->hasPrivilege('collect_fees', 'can_add')) { ?>
                                                        <button type="button"
                                                            data-student_session_id="<?php echo $hostel_fee_value->student_session_id; ?>"
                                                            data-student_fees_master_id="0" data-fee_groups_feetype_id="0"
                                                            data-group="<?php echo $this->lang->line('hostel_fees'); ?>"
                                                            data-type="<?php echo $hostel_fee_value->month; ?>"
                                                            class="btn btn-xs btn-default myCollectFeeBtn <?php echo $payment_buttons_display; ?>"
                                                            title="<?php echo $this->lang->line('add_fees'); ?>" data-toggle="modal"
                                                            data-target="#myFeesModal" data-fee-category="hostel"
                                                            data-hostel_fee_id="<?php echo $hostel_fee_value->id; ?>"><i
                                                                class="fa fa-plus"></i></button>
                                                    <?php } ?>

                                                    <?php
                                                    // Add View History button for hostel fees that have payment history
                                                    if (!empty($hostel_fee_value->amount_detail)) { ?>
                                                        <button type="button" class="btn btn-xs btn-info viewHostelFeeHistory"
                                                            data-student_session_id="<?php echo $hostel_fee_value->student_session_id; ?>"
                                                            data-trans_fee_id="<?php echo $hostel_fee_value->id; ?>"
                                                            data-group="<?php echo $this->lang->line('hostel_fees'); ?>"
                                                            data-type="<?php echo $hostel_fee_value->month; ?>"
                                                            title="<?php echo $this->lang->line('view_history'); ?>"
                                                            data-toggle="modal" data-target="#hostelFeeHistoryModal">
                                                            <i class="fa fa-history"></i>
                                                        </button>
                                                    <?php } ?>

                                                    <button class="btn btn-xs btn-default printInv"
                                                        data-student_session_id="<?php echo $hostel_fee_value->student_session_id; ?>"
                                                        data-fee_master_id="0" data-fee_session_group_id="0"
                                                        data-fee_groups_feetype_id="0" data-fee-category="hostel"
                                                        data-trans_fee_id="<?php echo $hostel_fee_value->id; ?>"
                                                        title="<?php echo $this->lang->line('print'); ?>"
                                                        data-loading-text="<i class='fa fa-spinner fa-spin '></i>"><i
                                                            class="fa fa-print"></i> </button>

                                                </td>
                                            </tr>

                                            <?php
                                            if (!empty($hostel_fee_value->amount_detail)) {

                                                $fee_deposits = json_decode(($hostel_fee_value->amount_detail));

                                                foreach ($fee_deposits as $fee_deposits_key => $fee_deposits_value) {
                                                    ?>
                                                    <tr class="dark-gray">
                                                        <td align="left"></td>
                                                        <td align="left"></td>
                                                        <td align="left"></td>
                                                        <td align="left"></td>
                                                        <td align="left"></td>
                                                        <td class="text-right"><img
                                                                src="<?php echo $this->media_storage->getImageURL('backend/images/table-arrow.png'); ?>"
                                                                alt="" /></td>
                                                        <td class="text text-right">

                                                            <a href="#" data-toggle="popover" class="detail_popover">
                                                                <?php echo $hostel_fee_value->student_fees_deposite_id . "/" . $fee_deposits_value->inv_no; ?>
                                                            </a>
                                                            <div class="fee_detail_popover" style="display: none">
                                                                <?php
                                                                if ($fee_deposits_value->description == "") {
                                                                    ?>
                                                                    <p class="text text-info"><?php echo $this->lang->line('description') . ": " . $this->lang->line('no_description'); ?></p>
                                                                    <?php
                                                                } else {
                                                                    ?>
                                                                    <p class="text text-info"><?php echo $this->lang->line('description') . ": " . $fee_deposits_value->description; ?></p>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </div>

                                                        </td>
                                                        <td class="text text-left"><?php echo $this->lang->line(strtolower($fee_deposits_value->payment_mode)); ?></td>
                                                        <td class="text text-left"><?php echo $this->customlib->dateformat($fee_deposits_value->date); ?></td>
                                                        <td class="text text-right"><?php echo amountFormat($fee_deposits_value->amount_discount); ?></td>
                                                        <td class="text text-right"><?php echo amountFormat($fee_deposits_value->amount_fine); ?></td>
                                                        <td class="text text-right"><?php echo amountFormat($fee_deposits_value->amount); ?></td>
                                                        <td class="text text-right"></td>
                                                        <td class="text text-right">
                                                            <div class="btn-group ">
                                                                <div class="pull-right">
                                                                    <?php if ($this->rbac->hasPrivilege('collect_fees', 'can_delete')) { ?>
                                                                        <button class="btn btn-default btn-xs"
                                                                            data-invoiceno="<?php echo $hostel_fee_value->student_fees_deposite_id . "/" . $fee_deposits_value->inv_no; ?>"
                                                                            data-main_invoice="<?php echo $hostel_fee_value->student_fees_deposite_id ?>"
                                                                            data-sub_invoice="<?php echo $fee_deposits_value->inv_no ?>"
                                                                            data-toggle="modal" data-target="#confirm-delete"
                                                                            title="<?php echo $this->lang->line('revert'); ?>">
                                                                            <i class="fa fa-undo"> </i>
                                                                        </button>
                                                                    <?php } ?>
                                                                    <button class="btn btn-default btn-xs printDoc"
                                                                        data-student_session_id="<?php echo $fee->student_session_id; ?>"
                                                                        data-fee_master_id="<?php echo $fee_value->id ?>"
                                                                        data-fee_session_group_id="<?php echo $fee_value->fee_session_group_id ?>"
                                                                        data-fee_groups_feetype_id="<?php echo $fee_value->fee_groups_feetype_id ?>"

                                                                        data-fee-category="hostel"
                                                                        data-main_invoice="<?php echo $hostel_fee_value->student_fees_deposite_id ?>"
                                                                        data-sub_invoice="<?php echo $fee_deposits_value->inv_no ?>"
                                                                        title="<?php echo $this->lang->line('print'); ?>"><i
                                                                            class="fa fa-print"></i> </button>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
                                            }

                                            ?>

                                            <?php
                                                }
                                                // Close session container
                                                ?>
                                                </tbody>
                                                <?php
                                            }
                                        }
                                    }

                                    ?>


                                    <?php
                                    if (!empty($student_discount_fee)) {

                                        foreach ($student_discount_fee as $discount_key => $discount_value) {
                                            ?>
                                            <tr class="dark-light">
                                                <td></td>
                                                <td align="left" class="text-rtl-right">
                                                    <?php echo $this->lang->line('discount'); ?>
                                                </td>
                                                <td align="left" class="text-rtl-right">
                                                    <?php echo $discount_value['code']; ?>
                                                </td>
                                                <td align="left"></td>
                                                <td align="left" class="text text-left">
                                                    <?php

                                                    if ($discount_value['status'] == "applied") {
                                                        ?>
                                                        <a href="#" data-toggle="popover" class="detail_popover">

                                                            <?php
                                                            if ($discount_value['type'] == "percentage") {
                                                                echo "<p class='text text-success'>" . $this->lang->line('discount_of') . " " . $discount_value['percentage'] . "% " . $this->lang->line($discount_value['status']) . " : " . $discount_value['payment_id'] . "</p>";
                                                            } else {
                                                                echo "<p class='text text-success'>" . $this->lang->line('discount_of') . " " . $currency_symbol . amountFormat($discount_value['amount']) . " " . $this->lang->line($discount_value['status']) . " : " . $discount_value['payment_id'] . "</p>";
                                                            }
                                                            ?>
                                                        </a>
                                                        <div class="fee_detail_popover" style="display: none">
                                                            <?php
                                                            if ($discount_value['student_fees_discount_description'] == "") {
                                                                ?>
                                                                <p class="text text-danger">
                                                                    <?php echo $this->lang->line('no_description'); ?>
                                                                </p>
                                                                <?php
                                                            } else {
                                                                ?>
                                                                <p class="text text-danger">
                                                                    <?php echo $discount_value['student_fees_discount_description'] ?>
                                                                </p>
                                                                <?php
                                                            }
                                                            ?>

                                                        </div>
                                                        <?php
                                                    } else {
                                                        if ($discount_value['type'] == "" || $discount_value['type'] == "fix") {
                                                            echo '<p class="text text-danger">' . $this->lang->line('discount_of') . " " . $currency_symbol . amountFormat($discount_value['amount']) . " " . $this->lang->line($discount_value['status']);
                                                        } else {
                                                            echo '<p class="text text-danger">' . $this->lang->line('discount_of') . " " . $discount_value['percentage'] . "% " . $this->lang->line($discount_value['status']);
                                                        }

                                                    }
                                                    ?>
                                                </td>
                                                <td></td>
                                                <td class="text text-left"></td>
                                                <td class="text text-left"></td>
                                                <td class="text text-left"></td>
                                                <td class="text text-right">
                                                    <?php
                                                    $alot_fee_discount = $alot_fee_discount;
                                                    ?>
                                                </td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td>
                                                    <div class="btn-group ">
                                                        <div class="pull-right">
                                                            <?php
                                                            if ($discount_value['status'] == "applied") {
                                                                ?>
                                                                <?php if ($this->rbac->hasPrivilege('collect_fees', 'can_delete')) { ?>
                                                                    <button class="btn btn-default btn-xs"
                                                                        data-discounttitle="<?php echo $discount_value['code']; ?>"
                                                                        data-discountid="<?php echo $discount_value['id']; ?>"
                                                                        data-toggle="modal" data-target="#confirm-discountdelete"
                                                                        title="<?php echo $this->lang->line('revert'); ?>">
                                                                        <i class="fa fa-undo"> </i>
                                                                    </button>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                            <button type="button"
                                                                data-modal_title="<?php echo $this->lang->line('discount') . " : " . $discount_value['code']; ?>"
                                                                data-student_fees_discount_id="<?php echo $discount_value['id']; ?>"
                                                                class="btn btn-xs btn-default applydiscount"
                                                                title="<?php echo $this->lang->line('apply_discount'); ?>"><i
                                                                    class="fa fa-check"></i>
                                                            </button>

                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>























                                    <?php
                                    // Initialize the displayed_sessions array if it doesn't exist
                                    if(!isset($displayed_sessions)) {
                                        $displayed_sessions = array(); // Track which sessions we've already displayed
                                    }

                                    // Display additional fees grouped by session
                                    if(isset($additional_fees_by_session) && !empty($additional_fees_by_session)) {
                                        foreach($sessions as $session_id => $session_name) {
                                            if(isset($additional_fees_by_session[$session_id]) && !empty($additional_fees_by_session[$session_id])) {
                                                            // For additional fees, we want to show them even if the regular fees for this session have been displayed
                                                // We'll use a different tracking key to avoid conflicts
                                                // Skip if this additional fee session has already been displayed
                                                if(isset($displayed_sessions['additional_' . $session_id])) {
                                                    continue;
                                                }
                                                // Mark this session as displayed for additional fees
                                                $displayed_sessions['additional_' . $session_id] = true;
                                                // Display session header
                                                ?>
                                                <tr class="session-header" data-session-id="additional-<?php echo $session_id; ?>">
                                                    <td colspan="14" class="text-center">
                                                        <div class="session-heading">
                                                            <i class="fa fa-calendar-check-o"></i>
                                                            <strong><?php echo $session_name; ?> Session:</strong> Additional Fees for <?php echo $session_name; ?> session
                                                            <span class="session-toggle pull-right">
                                                                <i class="fa fa-chevron-up"></i>
                                                            </span>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php

                                                // Add a container for the session fees
                                                ?>
                                                <tbody class="session-fees-container" data-session-id="additional-<?php echo $session_id; ?>">
                                                <?php
                                                // Display fees for this session
                                                foreach($additional_fees_by_session[$session_id] as $key => $fee) {
                                                    foreach ($fee->fees as $fee_key => $fee_value) {
                                            $fee_paid = 0;
                                            $fee_discount = 0;
                                            $fee_fine = 0;
                                            $fees_fine_amount = 0;
                                            $feetype_balance = 0;
                                            if (!empty($fee_value->amount_detail)) {
                                                $fee_deposits = json_decode(($fee_value->amount_detail));

                                                foreach ($fee_deposits as $fee_deposits_key => $fee_deposits_value) {
                                                    $fee_paid = $fee_paid + $fee_deposits_value->amount;
                                                    $fee_discount = $fee_discount + $fee_deposits_value->amount_discount;
                                                    $fee_fine = $fee_fine + $fee_deposits_value->amount_fine;
                                                }
                                            }
                                            if (($fee_value->due_date != "0000-00-00" && $fee_value->due_date != null) && (strtotime($fee_value->due_date) < strtotime(date('Y-m-d')))) {
                                                $fees_fine_amount = $fee_value->fine_amount;
                                                $total_fees_fine_amount = $total_fees_fine_amount + $fee_value->fine_amount;
                                            }

                                            $total_amount += $fee_value->amount;
                                            $total_discount_amount += $fee_discount;
                                            $total_deposite_amount += $fee_paid;
                                            $total_fine_amount += $fee_fine;
                                            $feetype_balance = $fee_value->amount - ($fee_paid + $fee_discount);
                                            $total_balance_amount += $feetype_balance;
                                            ?>
                                            <?php
                                            if ($feetype_balance > 0 && strtotime($fee_value->due_date) < strtotime(date('Y-m-d'))) {
                                                ?>
                                                <tr class="danger font12">
                                                    <?php
                                            } else {
                                                ?>
                                                <tr class="dark-gray">
                                                    <?php
                                            }
                                            ?>
                                                <td>
                                                    <input class="checkbox" type="checkbox" name="fee_checkbox"
                                                        data-fee_master_id="<?php echo $fee_value->id ?>"
                                                        data-fee_session_group_id="<?php echo $fee_value->fee_session_group_id ?>"
                                                        data-fee_groups_feetype_id="<?php echo $fee_value->fee_groups_feetype_id ?>"
                                                        data-student_session_id="<?php echo $fee->student_session_id; ?>"
                                                        data-fee_category="fees" data-trans_fee_id="0"
                                                        data-otherfeecat="otherfee">
                                                </td>
                                                <td align="left" class="text-rtl-right">
                                                    <?php
                                                    if ($fee_value->is_system) {
                                                        echo $fee_value->name;
                                                        echo $this->lang->line($fee_value->name) . " (" . $this->lang->line($fee_value->type) . ")";
                                                    } else {
                                                        echo $fee_value->name . " (" . $fee_value->type . ")";
                                                    }
                                                    ?>
                                                </td>
                                                <td align="left" class="text-rtl-right">
                                                    <?php
                                                    if ($fee_value->is_system) {
                                                        echo $this->lang->line($fee_value->code);
                                                    } else {
                                                        echo $fee_value->code;
                                                    }

                                                    ?>
                                                </td>
                                                <td align="left" class="text text-left">
                                                    <?php
                                                    if ($fee_value->due_date == "0000-00-00") {

                                                    } else {

                                                        if ($fee_value->due_date) {
                                                            echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($fee_value->due_date));
                                                        }
                                                    }
                                                    ?>
                                                </td>
                                                <td align="left" class="text text-left width85">
                                                    <?php
                                                    if ($feetype_balance == 0) {
                                                        ?><span class="label label-success">
                                                            <?php echo $this->lang->line('paid'); ?>
                                                        </span>
                                                        <?php
                                                    } else if (!empty($fee_value->amount_detail)) {
                                                        ?><span class="label label-warning">
                                                            <?php echo $this->lang->line('partial'); ?>
                                                            </span>
                                                        <?php
                                                    } else {
                                                        ?><span class="label label-danger">
                                                            <?php echo $this->lang->line('unpaid'); ?>
                                                            </span>
                                                        <?php
                                                    }
                                                    ?>
                                                </td>
                                                <td class="text text-right">
                                                    <?php echo amountFormat($fee_value->amount);
                                                    if (($fee_value->due_date != "0000-00-00" && $fee_value->due_date != null) && (strtotime($fee_value->due_date) < strtotime(date('Y-m-d')))) {
                                                        ?>
                                                        <span data-toggle="popover" class="text text-danger detail_popover">
                                                            <?php echo " + " . amountFormat($fee_value->fine_amount); ?>
                                                        </span>

                                                        <div class="fee_detail_popover" style="display: none">
                                                            <?php
                                                            if ($fee_value->fine_amount != "") {
                                                                ?>
                                                                <p class="text text-danger">
                                                                    <?php echo $this->lang->line('fine'); ?>
                                                                </p>
                                                                <?php
                                                            }
                                                            ?>
                                                        </div>
                                                        <?php
                                                    }
                                                    ?>
                                                </td>
                                                <td class="text text-left"></td>
                                                <td class="text text-left"></td>
                                                <td class="text text-left"></td>
                                                <td class="text text-right">
                                                    <?php
                                                    echo amountFormat($fee_discount);
                                                    ?>
                                                </td>
                                                <td class="text text-right">
                                                    <?php
                                                    echo amountFormat($fee_fine);
                                                    ?>
                                                </td>
                                                <td class="text text-right">
                                                    <?php
                                                    echo amountFormat($fee_paid);
                                                    ?>
                                                </td>
                                                <td class="text text-right">
                                                    <?php
                                                    // Separate display logic for payment collection buttons vs other buttons
                                                    $payment_buttons_display = "ss-none"; // Hide payment buttons for fully paid fees
                                                    if ($feetype_balance > 0) {
                                                        $payment_buttons_display = "";
                                                        echo amountFormat($feetype_balance);
                                                    }
                                                    ?>
                                                </td>
                                                <td width="100">
                                                    <div class="btn-group">
                                                        <div class="pull-right">

                                                            <?php if ($this->rbac->hasPrivilege('collect_fees', 'can_add')) { ?>
                                                                <button type="button"
                                                                    data-student_session_id="<?php echo $fee->student_session_id; ?>"
                                                                    data-student_fees_master_id="<?php echo $fee->id; ?>"
                                                                    data-fee_groups_feetype_id="<?php echo $fee_value->fee_groups_feetype_id; ?>"
                                                                    data-group="<?php echo ($fee_value->is_system) ? $this->lang->line($fee_value->name) . " (" . $this->lang->line($fee_value->type) . ")" : $fee_value->name . " (" . $fee_value->type . ")"; ?>"
                                                                    data-type="<?php echo ($fee_value->is_system) ? $this->lang->line($fee_value->type) : $fee_value->code; ?>"
                                                                    class="btn btn-xs btn-default myCollectFeeBtn <?php echo $payment_buttons_display; ?>"
                                                                    title="<?php echo $this->lang->line('add_fees'); ?>"
                                                                    data-toggle="modal" data-target="#myAdditionalFeesModal"
                                                                    data-fee-category="fees" data-trans_fee_id="0"><i
                                                                        class="fa fa-plus"></i></button>
                                                            <?php } ?>

                                                            <?php
                                                            // Add View History button for additional fees that have payment history
                                                            if (!empty($fee_value->amount_detail)) { ?>
                                                                <button type="button" class="btn btn-xs btn-info viewAdditionalFeeHistory"
                                                                    data-student_session_id="<?php echo $fee->student_session_id; ?>"
                                                                    data-student_fees_master_id="<?php echo $fee->id; ?>"
                                                                    data-fee_groups_feetype_id="<?php echo $fee_value->fee_groups_feetype_id; ?>"
                                                                    data-fee_session_group_id="<?php echo $fee_value->fee_session_group_id; ?>"
                                                                    data-group="<?php echo ($fee_value->is_system) ? $this->lang->line($fee_value->name) . " (" . $this->lang->line($fee_value->type) . ")" : $fee_value->name . " (" . $fee_value->type . ")"; ?>"
                                                                    title="<?php echo $this->lang->line('view_history'); ?>"
                                                                    data-toggle="modal" data-target="#additionalFeeHistoryModal">
                                                                    <i class="fa fa-history"></i>
                                                                </button>
                                                            <?php } ?>

                                                            <button class="btn btn-xs btn-default adding_printInv"
                                                                data-student_session_id="<?php echo $fee->student_session_id; ?>"
                                                                data-fee_master_id="<?php echo $fee_value->id ?>"
                                                                data-fee_session_group_id="<?php echo $fee_value->fee_session_group_id ?>"
                                                                data-fee_groups_feetype_id="<?php echo $fee_value->fee_groups_feetype_id ?>"
                                                                data-fee-category="fees" data-trans_fee_id="0"
                                                                title="<?php echo $this->lang->line('print'); ?>"
                                                                data-loading-text="<i class='fa fa-spinner fa-spin '></i>"><i
                                                                    class="fa fa-print"></i> </button>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
                                            if (!empty($fee_value->amount_detail)) {

                                                $fee_deposits = json_decode(($fee_value->amount_detail));
                                                foreach ($fee_deposits as $fee_deposits_key => $fee_deposits_value) {
                                                    ?>
                                                    <tr class="white-td">
                                                        <td align="left"></td>
                                                        <td align="left"></td>
                                                        <td align="left"></td>
                                                        <td align="left"></td>
                                                        <td align="left"></td>
                                                        <td class="text-right"><img
                                                                src="<?php echo $this->media_storage->getImageURL('backend/images/table-arrow.png'); ?>"
                                                                alt="" /></td>
                                                        <td class="text text-left"> <a href="#" data-toggle="popover"
                                                                class="detail_popover">
                                                                <?php echo $fee_value->student_fees_deposite_id . "/" . $fee_deposits_value->inv_no; ?>
                                                            </a>
                                                            <div class="fee_detail_popover" style="display: none">
                                                                <?php
                                                                if ($fee_deposits_value->description == "") {
                                                                    ?>
                                                                    <p class="text text-danger">
                                                                        <?php echo $this->lang->line('no_description'); ?>
                                                                    </p>
                                                                    <?php
                                                                } else {
                                                                    ?>
                                                                    <p class="text text-info">
                                                                        <?php echo $fee_deposits_value->description; ?>
                                                                    </p>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </div>
                                                        </td>
                                                        <td class="text text-left">
                                                            <?php echo $this->lang->line(strtolower($fee_deposits_value->payment_mode)); ?>
                                                        </td>
                                                        <td class="text text-left">
                                                            <?php if ($fee_deposits_value->date) {
                                                                echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($fee_deposits_value->date));
                                                            } ?>
                                                        </td>
                                                        <td class="text text-right">
                                                            <?php echo amountFormat($fee_deposits_value->amount_discount); ?>
                                                        </td>
                                                        <td class="text text-right">
                                                            <?php echo amountFormat($fee_deposits_value->amount_fine); ?>
                                                        </td>
                                                        <td class="text text-right">
                                                            <?php echo amountFormat($fee_deposits_value->amount); ?>
                                                        </td>
                                                        <td></td>

                                                        <td class="text text-right">
                                                            <div class="btn-group">
                                                                <div class="pull-right">
                                                                    <?php if ($this->rbac->hasPrivilege('collect_fees', 'can_delete')) { ?>
                                                                        <button class="btn btn-default btn-xs"
                                                                            data-adding_invoiceno="<?php echo $fee_value->student_fees_deposite_id . "/" . $fee_deposits_value->inv_no; ?>"
                                                                            data-adding_main_invoice="<?php echo $fee_value->student_fees_deposite_id ?>"
                                                                            data-adding_sub_invoice="<?php echo $fee_deposits_value->inv_no ?>"
                                                                            data-toggle="modal" data-target="#adding-confirm-delete"
                                                                            title="<?php echo $this->lang->line('revert'); ?>">
                                                                            <i class="fa fa-undo"> </i>
                                                                        </button>
                                                                    <?php } ?>
                                                                    <button class="btn btn-xs btn-default adding_printDoc"
                                                                        data-student_session_id="<?php echo $fee->student_session_id; ?>"
                                                                        data-fee_master_id="<?php echo $fee_value->id ?>"
                                                                        data-fee_session_group_id="<?php echo $fee_value->fee_session_group_id ?>"
                                                                        data-fee_groups_feetype_id="<?php echo $fee_value->fee_groups_feetype_id ?>"

                                                                        data-adding_main_invoice="<?php echo $fee_value->student_fees_deposite_id ?>"
                                                                        data-adding_sub_invoice="<?php echo $fee_deposits_value->inv_no ?>"
                                                                        data-fee-category="fees"
                                                                        title="<?php echo $this->lang->line('print'); ?>"><i
                                                                            class="fa fa-print"></i> </button>
                                                                </div>
                                                            </div>
                                                        </td>

                                                    </tr>
                                                    <?php
                                                            }
                                                        }
                                                        ?>
                                                        </tbody>
                                                        <?php
                                                    }
                                                }
                                            }
                                            ?>
                                            <?php
                                                    }
                                                }
                                                ?>
                                                </tbody>
                                                <?php
                                            }
                                        }
                                    }
                                    ?>

                                    <tr class="box box-solid total-bg">
                                        <td align="left"></td>
                                        <td align="left"></td>
                                        <td align="left"></td>
                                        <td align="left"></td>
                                        <td align="left" class="text text-left">
                                            <?php echo $this->lang->line('grand_total'); ?>
                                        </td>
                                        <td class="text text-right">

                                            <?php
                                            echo $currency_symbol . (amountFormat($total_amount));
                                            ?>

                                            <span data-toggle="popover" class="text text-danger detail_popover">
                                                <?php echo " + " . (amountFormat($total_fees_fine_amount)); ?>
                                            </span>

                                            <div class="fee_detail_popover" style="display: none">

                                                <p class="text text-danger">
                                                    <?php echo $this->lang->line('fine'); ?>
                                                </p>

                                            </div>
                                        </td>
                                        <td class="text text-left"></td>
                                        <td class="text text-left"></td>
                                        <td class="text text-left"></td>
                                        <td class="text text-right">
                                            <?php
                                            echo $currency_symbol . amountFormat(($total_discount_amount + $alot_fee_discount));
                                            ?>
                                        </td>
                                        <td class="text text-right">
                                            <?php
                                            echo $currency_symbol . amountFormat(($total_fine_amount));
                                            ?>
                                        </td>
                                        <td class="text text-right">
                                            <?php
                                            echo $currency_symbol . amountFormat(($total_deposite_amount));
                                            ?>
                                        </td>
                                        <td class="text text-right">
                                            <?php
                                            echo $currency_symbol . amountFormat(($total_balance_amount - $alot_fee_discount));
                                            ?>
                                        </td>
                                        <td class="text text-right"></td>
                                    </tr>



                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
            </div>
            <!--/.col (left) -->
        </div>
    </section>
</div>








<div class="modal fade" id="myFeesdiscountModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title title text-center fees_title"></h4>
            </div>
            <div class="modal-body pb0">
                <div class="form-horizontal balanceformpopup">
                    <div class="box-body">

                        <input type="hidden" class="form-control" id="std_id"
                            value="<?php echo $student["student_session_id"]; ?>" readonly="readonly" />
                        <input type="hidden" class="form-control" id="parent_app_key"
                            value="<?php echo $student['parent_app_key'] ?>" readonly="readonly" />
                        <input type="hidden" class="form-control" id="guardian_phone"
                            value="<?php echo $student['guardian_phone'] ?>" readonly="readonly" />
                        <input type="hidden" class="form-control" id="guardian_email"
                            value="<?php echo $student['guardian_email'] ?>" readonly="readonly" />
                        <input type="hidden" class="form-control" id="student_fees_master_id" value="0"
                            readonly="readonly" />
                        <input type="hidden" class="form-control" id="fee_groups_feetype_id" value="0"
                            readonly="readonly" />
                        <input type="hidden" class="form-control" id="transport_fees_id" value="0"
                            readonly="readonly" />
                        <input type="hidden" class="form-control" id="hostel_fees_id" value="0"
                            readonly="readonly" />
                        <input type="hidden" class="form-control" id="fee_category" value="" readonly="readonly" />
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">
                                <?php echo $this->lang->line('date'); ?><small class="req"> *</small>
                            </label>
                            <div class="col-sm-9">
                                <input id="datee" name="admission_date" placeholder="" type="text"
                                    class="form-control date_fee"
                                    value="<?php echo date($this->customlib->getSchoolDateFormat()); ?>"
                                    readonly="readonly" />
                                <span class="text-danger" id="date_error"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-3 control-label">
                                <?php echo $this->lang->line('discount_amountt'); ?> (
                                <?php echo $currency_symbol; ?>)<small class="req"> *</small>
                            </label>
                            <div class="col-sm-9">
                                <input type="text" autofocus="" class="form-control modal_amount" id="amounttt" value="0">
                                <span class="text-danger" id="amount_error"></span>
                            </div>
                        </div>


                        <!--
                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-3 control-label">
                                <?php echo $this->lang->line('discount_group'); ?>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control modal_discount_group" id="discount_group">
                                    <option value="">
                                        <?php echo $this->lang->line('select'); ?>
                                    </option>
                                </select>
                                <span class="text-danger" id="amount_error"></span>
                            </div>
                        </div> -->

                        <!-- <div class="form-group">
                            <label for="inputPassword3" class="col-sm-3 control-label">
                                <?php echo $this->lang->line('discount'); ?> (
                                <?php echo $currency_symbol; ?>)<small class="req"> *</small>
                            </label>
                            <div class="col-sm-9">
                                <div class="row">
                                    <div class="col-md-5 col-sm-5">
                                        <div class="">
                                            <input type="text" class="form-control" id="amount_discount" value="0">

                                            <span class="text-danger" id="amount_discount_error"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-sm-2 ltextright">
                                        <label for="inputPassword3" class="control-label">
                                            <?php echo $this->lang->line('fine'); ?> (
                                            <?php echo $currency_symbol; ?>)<small class="req">*</small>
                                        </label>
                                    </div>
                                    <div class="col-md-5 col-sm-5">
                                        <div class="">
                                            <input type="text" class="form-control" id="amount_fine" value="0">
                                            <span class="text-danger" id="amount_fine_error"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->


                        <!-- <div class="form-group">
                            <label for="inputPassword3" class="col-sm-3 control-label">
                                <?php echo $this->lang->line('payment_mode'); ?>
                            </label>
                            <div class="col-sm-9">
                                <label class="radio-inline">
                                    <input type="radio" name="payment_mode_fee" value="Cash" checked="checked">
                                    <?php echo $this->lang->line('cash'); ?>
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="payment_mode_fee" value="Cheque">
                                    <?php echo $this->lang->line('cheque'); ?>
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="payment_mode_fee" value="DD">
                                    <?php echo $this->lang->line('dd'); ?>
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="payment_mode_fee" value="bank_transfer">
                                    <?php echo $this->lang->line('bank_transfer'); ?>
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="payment_mode_fee" value="upi">
                                    <?php echo $this->lang->line('upi'); ?>
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="payment_mode_fee" value="card">
                                    <?php echo $this->lang->line('card'); ?>
                                </label>
                                <span class="text-danger" id="payment_mode_error"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-3 control-label">
                                <?php echo $this->lang->line('accountname'); ?><small class="req"> *</small>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control modal_discount_group" id="accountname">
                                    <option value="">
                                        <?php echo $this->lang->line('select'); ?>
                                    </option>
                                </select>
                                <span class="text-danger" id="accountname_error"></span>
                            </div>
                        </div> -->



                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-3 control-label">
                                <?php echo $this->lang->line('note'); ?>
                            </label>
                            <div class="col-sm-9">
                                <textarea class="form-control" rows="3" id="description" placeholder=""></textarea>
                            </div>
                        </div>



                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">
                    <?php echo $this->lang->line('cancel'); ?>
                </button>
                <button type="button" class="btn cfees discount_save_button" id="load" data-action="collect"
                    data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> <?php echo $this->lang->line('processing'); ?>">
                    <?php echo $currency_symbol; ?>
                    <?php echo $this->lang->line('discount_req'); ?>
                </button>
                <!-- <button type="button" class="btn cfees save_button" id="load" data-action="print"
                    data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> <?php echo $this->lang->line('processing'); ?>">
                    <?php echo $currency_symbol; ?>
                    <?php echo $this->lang->line('collect_print'); ?>
                </button> -->
            </div>
        </div>
    </div>
</div>














<div class="modal fade" id="myFeesModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title title text-center fees_title"></h4>
            </div>
            <div class="modal-body pb0">
                <div class="form-horizontal balanceformpopup">
                    <div class="box-body">

                        <input type="hidden" class="form-control" id="std_id"
                            value="<?php echo $student["student_session_id"]; ?>" readonly="readonly" />
                        <input type="hidden" class="form-control" id="parent_app_key"
                            value="<?php echo $student['parent_app_key'] ?>" readonly="readonly" />
                        <input type="hidden" class="form-control" id="guardian_phone"
                            value="<?php echo $student['guardian_phone'] ?>" readonly="readonly" />
                        <input type="hidden" class="form-control" id="guardian_email"
                            value="<?php echo $student['guardian_email'] ?>" readonly="readonly" />
                        <input type="hidden" class="form-control" id="student_fees_master_id" value="0"
                            readonly="readonly" />
                        <input type="hidden" class="form-control" id="fee_groups_feetype_id" value="0"
                            readonly="readonly" />
                        <input type="hidden" class="form-control" id="transport_fees_id" value="0"
                            readonly="readonly" />
                        <input type="hidden" class="form-control" id="hostel_fees_id" value="0"
                            readonly="readonly" />
                        <input type="hidden" class="form-control" id="fee_category" value="" readonly="readonly" />
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">
                                <?php echo $this->lang->line('date'); ?><small class="req"> *</small>
                            </label>
                            <div class="col-sm-9">
                                <input id="date" name="admission_date" placeholder="" type="text"
                                    class="form-control date_fee"
                                    value="<?php echo date($this->customlib->getSchoolDateFormat()); ?>" />
                                <span class="text-danger" id="date_error"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-3 control-label">
                                <?php echo $this->lang->line('amount'); ?> (
                                <?php echo $currency_symbol; ?>)<small class="req"> *</small>
                            </label>
                            <div class="col-sm-9">
                                <input type="text" autofocus="" class="form-control modal_amount" id="amount" value="0">
                                <span class="text-danger" id="amount_error"></span>
                            </div>
                        </div>
                        
                        <!-- Advance Payment Collection Option -->
                        <div class="form-group" id="advance_payment_option" style="display: none;">
                            <label for="inputPassword3" class="col-sm-3 control-label">
                                <?php echo $this->lang->line('payment_source'); ?>
                            </label>
                            <div class="col-sm-9">
                                <div class="checkbox" style="display: flex; align-items: center;">
                                    <label style="margin-bottom: 0; display: flex; align-items: center;">
                                        <input type="checkbox" id="collect_from_advance" name="collect_from_advance" value="1" style="margin-right: 6px;">
                                        <strong><?php echo $this->lang->line('collect_from_advance_payment'); ?></strong>
                                        <span class="text-muted" style="font-style: italic; margin-left: 10px; display: flex; align-items: center;">
                                            <i class="fa fa-info-circle" style="margin-right: 4px;"></i>
                                            If you want to pay the amount from the advance payment balance, check this option
                                        </span>
                                    </label>
                                </div>
                                <div id="advance_payment_info" style="display: none; margin-top: 10px;">
                                    <div class="alert alert-info">
                                        <i class="fa fa-info-circle"></i>
                                        <strong><?php echo $this->lang->line('available_advance_balance'); ?>:</strong>
                                        <span id="modal_advance_balance"><?php echo $currency_symbol; ?>0.00</span>
                                    </div>
                                </div>
                                <span class="text-danger" id="advance_payment_error"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-3 control-label">
                                <?php echo $this->lang->line('discount_group'); ?>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control modal_discount_group" id="discount_group">
                                    <option value="">
                                        <?php echo $this->lang->line('select'); ?>
                                    </option>
                                </select>
                                <span class="text-danger" id="amount_error"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-3 control-label">
                                <?php echo $this->lang->line('discount'); ?> (
                                <?php echo $currency_symbol; ?>)<small class="req"> *</small>
                            </label>
                            <div class="col-sm-9">
                                <div class="row">
                                    <div class="col-md-5 col-sm-5">
                                        <div class="">
                                            <input type="text" class="form-control" disabled id="amount_discount" value="0">

                                            <span class="text-danger" id="amount_discount_error"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-sm-2 ltextright">
                                        <label for="inputPassword3" class="control-label">
                                            <?php echo $this->lang->line('fine'); ?> (
                                            <?php echo $currency_symbol; ?>)<small class="req">*</small>
                                        </label>
                                    </div>
                                    <div class="col-md-5 col-sm-5">
                                        <div class="">
                                            <input type="text" class="form-control" id="amount_fine" value="0">
                                            <span class="text-danger" id="amount_fine_error"></span>
                                        </div>
                                    </div>
                                </div>
                            </div><!--./col-sm-9-->
                        </div>
                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-3 control-label">
                                <?php echo $this->lang->line('payment_mode'); ?>
                            </label>
                            <div class="col-sm-9">
                                <label class="radio-inline">
                                    <input type="radio" name="payment_mode_fee" value="Cash" checked="checked">
                                    <?php echo $this->lang->line('cash'); ?>
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="payment_mode_fee" value="Cheque">
                                    <?php echo $this->lang->line('cheque'); ?>
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="payment_mode_fee" value="DD">
                                    <?php echo $this->lang->line('dd'); ?>
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="payment_mode_fee" value="bank_transfer">
                                    <?php echo $this->lang->line('bank_transfer'); ?>
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="payment_mode_fee" value="upi">
                                    <?php echo $this->lang->line('upi'); ?>
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="payment_mode_fee" value="card">
                                    <?php echo $this->lang->line('card'); ?>
                                </label>
                                <span class="text-danger" id="payment_mode_error"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-3 control-label">
                                <?php echo $this->lang->line('accountname'); ?><small class="req"> *</small>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control modal_discount_group" id="accountname">
                                    <option value="">
                                        <?php echo $this->lang->line('select'); ?>
                                    </option>
                                </select>
                                <span class="text-danger" id="accountname_error"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-3 control-label">
                                <?php echo $this->lang->line('note'); ?>
                            </label>
                            <div class="col-sm-9">
                                <textarea class="form-control" rows="3" id="description1" placeholder=""></textarea>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">
                    <?php echo $this->lang->line('cancel'); ?>
                </button>
                <button type="button" class="btn cfees save_button" id="load" data-action="collect"
                    data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> <?php echo $this->lang->line('processing'); ?>">
                    <?php echo $currency_symbol; ?>
                    <?php echo $this->lang->line('collect_fees'); ?>
                </button>
                <button type="button" class="btn cfees save_button" id="load" data-action="print"
                    data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> <?php echo $this->lang->line('processing'); ?>">
                    <?php echo $currency_symbol; ?>
                    <?php echo $this->lang->line('collect_print'); ?>
                </button>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="myAdditionalFeesModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title title text-center adding_fees_title"></h4>
            </div>
            <div class="modal-body pb0">
                <div class="form-horizontal balanceformpopup">
                    <div class="box-body">

                        <input type="hidden" class="form-control" id="adding_std_id"
                            value="<?php echo $student["student_session_id"]; ?>" readonly="readonly" />
                        <input type="hidden" class="form-control" id="adding_parent_app_key"
                            value="<?php echo $student['parent_app_key'] ?>" readonly="readonly" />
                        <input type="hidden" class="form-control" id="adding_guardian_phone"
                            value="<?php echo $student['guardian_phone'] ?>" readonly="readonly" />
                        <input type="hidden" class="form-control" id="adding_guardian_email"
                            value="<?php echo $student['guardian_email'] ?>" readonly="readonly" />
                        <input type="hidden" class="form-control" id="adding_student_fees_master_id" value="0"
                            readonly="readonly" />
                        <input type="hidden" class="form-control" id="adding_fee_groups_feetype_id" value="0"
                            readonly="readonly" />
                        <input type="hidden" class="form-control" id="adding_transport_fees_id" value="0"
                            readonly="readonly" />
                        <input type="hidden" class="form-control" id="adding_fee_category" value=""
                            readonly="readonly" />
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">
                                <?php echo $this->lang->line('date'); ?><small class="req"> *</small>
                            </label>
                            <div class="col-sm-9">
                                <input id="adding_date" name="admission_date" placeholder="" type="text"
                                    class="form-control date_fee"
                                    value="<?php echo date($this->customlib->getSchoolDateFormat()); ?>"
                                    readonly="readonly" />
                                <span class="text-danger" id="adding_date_error"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-3 control-label">
                                <?php echo $this->lang->line('amount'); ?> (
                                <?php echo $currency_symbol; ?>)<small class="req"> *</small>
                            </label>
                            <div class="col-sm-9">
                                <input type="number" autofocus="" class="form-control modal_amount" id="adding_amount"
                                    value="0">
                                <span class="text-danger" id="adding_amount_error"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-3 control-label">
                                <?php echo $this->lang->line('discount_group'); ?>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control modal_discount_group" id="adding_discount_group">
                                    <option value="">
                                        <?php echo $this->lang->line('select'); ?>
                                    </option>
                                </select>
                                <span class="text-danger" id="adding_amount_error"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-3 control-label">
                                <?php echo $this->lang->line('discount'); ?> (
                                <?php echo $currency_symbol; ?>)<small class="req"> *</small>
                            </label>
                            <div class="col-sm-9">
                                <div class="row">
                                    <div class="col-md-5 col-sm-5">
                                        <div class="">
                                            <input type="text" class="form-control" id="adding_amount_discount"
                                                value="0">

                                            <span class="text-danger" id="adding_amount_discount_error"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-sm-2 ltextright">
                                        <label for="inputPassword3" class="control-label">
                                            <?php echo $this->lang->line('fine'); ?> (
                                            <?php echo $currency_symbol; ?>)<small class="req">*</small>
                                        </label>
                                    </div>
                                    <div class="col-md-5 col-sm-5">
                                        <div class="">
                                            <input type="text" class="form-control" id="adding_amount_fine" value="0">
                                            <span class="text-danger" id="adding_amount_fine_error"></span>
                                        </div>
                                    </div>
                                </div>
                            </div><!--./col-sm-9-->
                        </div>
                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-3 control-label">
                                <?php echo $this->lang->line('payment_mode'); ?>
                            </label>
                            <div class="col-sm-9">
                                <label class="radio-inline">
                                    <input type="radio" name="adding_payment_mode_fee" value="Cash" checked="checked">
                                    <?php echo $this->lang->line('cash'); ?>
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="adding_payment_mode_fee" value="Cheque">
                                    <?php echo $this->lang->line('cheque'); ?>
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="adding_payment_mode_fee" value="DD">
                                    <?php echo $this->lang->line('dd'); ?>
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="adding_payment_mode_fee" value="bank_transfer">
                                    <?php echo $this->lang->line('bank_transfer'); ?>
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="adding_payment_mode_fee" value="upi">
                                    <?php echo $this->lang->line('upi'); ?>
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="adding_payment_mode_fee" value="card">
                                    <?php echo $this->lang->line('card'); ?>
                                </label>
                                <span class="text-danger" id="adding_payment_mode_error"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-3 control-label">
                                <?php echo $this->lang->line('accountname'); ?><small class="req"> *</small>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control modal_discount_group" id="addingaccountname">
                                    <option value="">
                                        <?php echo $this->lang->line('select'); ?>
                                    </option>
                                </select>
                                <span class="text-danger" id="addingaccountname_error"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-3 control-label">
                                <?php echo $this->lang->line('note'); ?>
                            </label>
                            <div class="col-sm-9">
                                <textarea class="form-control" rows="3" id="adding_description"
                                    placeholder=""></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">
                    <?php echo $this->lang->line('cancel'); ?>
                </button>
                <button type="button" class="btn cfees adding_save_button" id="load" data-action="collect"
                    data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> <?php echo $this->lang->line('processing'); ?>">
                    <?php echo $currency_symbol; ?>
                    <?php echo $this->lang->line('collect_fees'); ?>
                </button>
                <button type="button" class="btn cfees adding_save_button" id="load" data-action="print"
                    data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> <?php echo $this->lang->line('processing'); ?>">
                    <?php echo $currency_symbol; ?>
                    <?php echo $this->lang->line('collect_print'); ?>
                </button>
            </div>
        </div>
    </div>
</div>













<div class="delmodal modal fade" id="adding-confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo $this->lang->line('confirmation'); ?>
                </h4>
            </div>
            <div class="modal-body">
                <p>
                    <?php echo $this->lang->line('are_you_sure_want_to_delete'); ?> <b class="adding_invoice_no"></b>
                    <?php echo $this->lang->line('invoice_this_action_is_irreversible') ?>
                </p>
                <p>
                    <?php echo $this->lang->line('do_you_want_to_proceed') ?>
                </p>
                <p class="debug-url"></p>
                <input type="hidden" name="adding_main_invoice" id="adding_main_invoice" value="">
                <input type="hidden" name="adding_sub_invoice" id="adding_sub_invoice" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('cancel'); ?>
                </button>
                <a class="btn btn-danger btn-ok">
                    <?php echo $this->lang->line('revert'); ?>
                </a>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="myDisApplyModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title title text-center discount_title"></h4>
            </div>
            <div class="modal-body pb0">
                <div class="form-horizontal">
                    <div class="box-body">
                        <input type="hidden" class="form-control" id="student_fees_discount_id" value="" />
                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-3 control-label">
                                <?php echo $this->lang->line('payment_id'); ?> <small class="req">*</small>
                            </label>
                            <div class="col-sm-9">

                                <input type="text" class="form-control" id="discount_payment_id">

                                <span class="text-danger" id="discount_payment_id_error"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-3 control-label">
                                <?php echo $this->lang->line('description'); ?>
                            </label>

                            <div class="col-sm-9">
                                <textarea class="form-control" rows="3" id="dis_description" placeholder=""></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">
                    <?php echo $this->lang->line('cancel'); ?>
                </button>
                <button type="button" class="btn cfees dis_apply_button" id="load"
                    data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> <?php echo $this->lang->line('processing'); ?>">
                    <?php echo $this->lang->line('apply_discount'); ?>
                </button>
            </div>
        </div>
    </div>
</div>


<div class="delmodal modal fade" id="confirm-discountdelete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo $this->lang->line('confirmation'); ?>
                </h4>
            </div>
            <div class="modal-body">
                <p>
                    <?php echo $this->lang->line('are_you_sure_want_to_revert'); ?> <b class="discount_title"></b>
                    <?php echo $this->lang->line('discount_this_action_is_irreversible'); ?>
                </p>
                <p>
                    <?php echo $this->lang->line('do_you_want_to_proceed') ?>
                </p>
                <p class="debug-url"></p>
                <input type="hidden" name="discount_id" id="discount_id" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('cancel'); ?>
                </button>
                <a class="btn btn-danger btn-discountdel">
                    <?php echo $this->lang->line('revert'); ?>
                </a>
            </div>
        </div>
    </div>
</div>


<div class="delmodal modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo $this->lang->line('confirmation'); ?>
                </h4>
            </div>
            <div class="modal-body">
                <p>
                    <?php echo $this->lang->line('are_you_sure_want_to_delete'); ?> <b class="invoice_no"></b>
                    <?php echo $this->lang->line('invoice_this_action_is_irreversible') ?>
                </p>
                <p>
                    <?php echo $this->lang->line('do_you_want_to_proceed') ?>
                </p>
                <p class="debug-url"></p>
                <input type="hidden" name="main_invoice" id="main_invoice" value="">
                <input type="hidden" name="sub_invoice" id="sub_invoice" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('cancel'); ?>
                </button>
                <a class="btn btn-danger btn-ok">
                    <?php echo $this->lang->line('revert'); ?>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="norecord modal fade" id="confirm-norecord" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <p>
                    <?php echo $this->lang->line('no_record_found'); ?>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('cancel'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<div id="listCollectionModal" class="modal fade">
    <div class="modal-dialog">
        <form action="<?php echo site_url('studentfee/addfeegrp'); ?>" method="POST" id="collect_fee_group">
            <div class="modal-content">
                <!-- //================ -->
                <input type="hidden" class="form-control" id="group_std_id" name="student_session_id"
                    value="<?php echo $student["student_session_id"]; ?>" readonly="readonly" />
                <input type="hidden" class="form-control" id="group_parent_app_key" name="parent_app_key"
                    value="<?php echo $student['parent_app_key'] ?>" readonly="readonly" />
                <input type="hidden" class="form-control" id="group_guardian_phone" name="guardian_phone"
                    value="<?php echo $student['guardian_phone'] ?>" readonly="readonly" />
                <input type="hidden" class="form-control" id="group_guardian_email" name="guardian_email"
                    value="<?php echo $student['guardian_email'] ?>" readonly="readonly" />
                <!-- //================ -->
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">
                        <?php echo $this->lang->line('collect_fees'); ?>
                    </h4>
                </div>
                <div class="modal-body">

                </div>
            </div>
        </form>
    </div>
</div>

<div id="processing_fess_modal" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">
                    <?php echo $this->lang->line('processing_fees'); ?>
                </h4>
            </div>
            <div class="modal-body">

            </div>
        </div>
    </div>
</div>








<script type="text/javascript">
    // Debug: Check if required variables are available (they should be defined in footer.php)
    console.log('Date picker variables check:', {
        date_format: typeof date_format !== 'undefined' ? date_format : 'UNDEFINED',
        start_week: typeof start_week !== 'undefined' ? start_week : 'UNDEFINED',
        language_name: '<?php echo $language_name; ?>',
        base_url: typeof base_url !== 'undefined' ? base_url : '<?php echo base_url(); ?>',
        feesinbackdate: '<?php echo isset($feesinbackdate) ? $feesinbackdate : "NOT_SET"; ?>',
        datepicker_available: typeof $.fn.datepicker !== 'undefined'
    });

    // Define missing variables if they're not available from footer.php
    if (typeof date_format === 'undefined') {
        var date_format = '<?php echo $result = strtr($this->customlib->getSchoolDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy', 'M' => 'M']) ?>';
    }
    if (typeof start_week === 'undefined') {
        var start_week = <?php echo $this->customlib->getStartWeek(); ?>;
    }
    if (typeof base_url === 'undefined') {
        var base_url = '<?php echo base_url(); ?>';
    }

    $(document).on('click', '.discount_save_button', function (e) {
        var $this = $(this);
        var action = $this.data('action');
        $this.button('loading');
        
        // Get values from the discount modal fields (using correct IDs)
        var date = $('#datee').val(); // Discount modal uses 'datee' not 'date'
        var student_session_id = $('#std_id').val();
        var amount = $('#amounttt').val(); // Discount modal uses 'amounttt' not 'amount'
        var description = $('#description').val(); // Discount modal uses 'description' not 'description1'
        var student_fees_master_id = $('#student_fees_master_id').val();
        var fee_groups_feetype_id = $('#fee_groups_feetype_id').val();
        var fee_category = $('#fee_category').val();
        
        // Validate required fields
        if (!amount || amount <= 0) {
            alert('Please enter a valid discount amount');
            $this.button('reset');
            return;
        }
        
        if (!student_fees_master_id || !fee_groups_feetype_id) {
            alert('Fee information is missing. Please try again.');
            $this.button('reset');
            return;
        }
        
        $.ajax({
            url: '<?php echo site_url("studentfee/adddiscountstudentfee") ?>',
            type: 'post',
            data: { 
                student_session_id: student_session_id, 
                date: date, 
                amount: amount,
                description: description, 
                student_fees_master_id: student_fees_master_id, 
                fee_groups_feetype_id: fee_groups_feetype_id
            },
            dataType: 'json',
            success: function (response) {
                $this.button('reset');
                if (response.status == "success") {
                    // Close the modal
                    $('#myFeesdiscountModal').modal('hide');
                    // Show success message
                    alert('Discount request submitted successfully! It will be reviewed by the admin.');
                    // Reload the page to show updated discount status
                    location.reload(true);
                } else if (response.status === "fail") {
                    // Show validation errors
                    var errorMessage = 'Validation errors:\n';
                    $.each(response.error, function (index, value) {
                        errorMessage += '- ' + value + '\n';
                        var errorDiv = '#' + index + '_error';
                        $(errorDiv).empty().append(value);
                    });
                    alert(errorMessage);
                } else {
                    alert('An error occurred while submitting the discount request. Please try again.');
                }
            },
            error: function(xhr, status, error) {
                $this.button('reset');
                console.error('AJAX Error:', status, error);
                alert('Network error occurred. Please check your connection and try again.');
            }
        });
    });
</script>


<script type="text/javascript">
    $("#myFeesdiscountModal").on('shown.bs.modal', function (e) {
        e.stopPropagation();
        var discount_group_dropdown = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
        var data = $(e.relatedTarget).data();
        console.log(data);

        var modal = $(this);
        var type = data.type;
        var amount = data.amount;
        var group = data.group;
        var fee_groups_feetype_id = data.fee_groups_feetype_id;
        var student_fees_master_id = data.student_fees_master_id;
        var student_session_id = data.student_session_id;
        var fee_category = data.feeCategory;
        var trans_fee_id = data.trans_fee_id;

        $('.fees_title').html("");
        $('.fees_title').html("<b>" + group + ":</b> " + type);
        $('#fee_groups_feetype_id').val(fee_groups_feetype_id);
        $('#student_fees_master_id').val(student_fees_master_id);
        $('#transport_fees_id').val(trans_fee_id);
        $('#hostel_fees_id').val(hostel_fee_id); // Fixed: Use hostel_fee_id for hostel fees
        $('#fee_category').val(fee_category);

        $.ajax({
            type: "post",
            url: '<?php echo site_url("studentfee/geBalanceFee") ?>',
            dataType: 'JSON',
            data: {
                'fee_groups_feetype_id': fee_groups_feetype_id,
                'student_fees_master_id': student_fees_master_id,
                'student_session_id': student_session_id,
                'fee_category': fee_category,
                'trans_fee_id': trans_fee_id
            },
            beforeSend: function () {
                $('#discount_group').html("");
                $("span[id$='_error']").html("");
                $('#amounttt').val("");
                $('#amount_discount').val("0");
                $('#amount_fine').val("0");
            },
            success: function (data) {

                if (data.status === "success") {
                    fee_amount = data.balance;
                    fee_type_amount = data.student_fees;
                    $('#amounttt').val(data.balance);
                    $('#amount_fine').val(data.remain_amount_fine);
                    $.each(data.discount_not_applied, function (i, obj) {
                        discount_group_dropdown += "<option value=" + obj.student_fees_discount_id + " data-disamount=" + obj.amount + " data-type=" + obj.type + " data-percentage=" + obj.percentage + ">" + obj.code + "</option>";
                    });
                    $('#discount_group').append(discount_group_dropdown);

                }
            },
            error: function (xhr) { // if error occured
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");

            },
            complete: function () {
            }
        });
    });
</script>













<script type="text/javascript">
    $(document).ready(function () {
        $('#listCollectionModal,#processing_fess_modal,#confirm-norecord,#myFeesModal,#myFeesdiscountModal,#myAdditionalFeesModal').modal({
            backdrop: 'static',
            keyboard: false,
            show: false
        });
    });

    $(document).ready(function () {

        $(document).on('click', '.printDoc', function () {
            var main_invoice = $(this).data('main_invoice');
            var sub_invoice = $(this).data('sub_invoice');
            var fee_category = $(this).data('fee-category');
            var student_session_id = '<?php echo $student['student_session_id'] ?>';

            var fee_master_id = $(this).data('fee_master_id');
            var student_session_id = $(this).data('student_session_id');
            var fee_session_group_id = $(this).data('fee_session_group_id');
            var fee_groups_feetype_id = $(this).data('fee_groups_feetype_id');


            $.ajax({
                url: '<?php echo site_url("studentfee/printFeesByName") ?>',
                type: 'post',
                dataType: "JSON",
                data: { 'fee_groups_feetype_id':fee_groups_feetype_id,'fee_session_group_id':fee_session_group_id,'fee_master_id':fee_master_id, 'fee_category': fee_category, 'student_session_id': student_session_id, 'main_invoice': main_invoice, 'sub_invoice': sub_invoice },
                // data: { 'fee_category': fee_category, 'student_session_id': student_session_id, 'main_invoice': main_invoice, 'sub_invoice': sub_invoice },
                success: function (response) {
                    Popup(response.page);
                }
            });
        });

        $(document).on('click', '.printInv', function () {
            var $this = $(this);
            var fee_master_id = $(this).data('fee_master_id');
            var student_session_id = $(this).data('student_session_id');
            var fee_session_group_id = $(this).data('fee_session_group_id');
            var fee_groups_feetype_id = $(this).data('fee_groups_feetype_id');
            var trans_fee_id = $(this).data('trans_fee_id');
            var fee_category = $(this).data('fee-category');
            $.ajax({
                url: '<?php echo site_url("studentfee/printFeesByGroup") ?>',
                type: 'post',
                dataType: "JSON",
                data: { 'trans_fee_id': trans_fee_id, 'fee_category': fee_category, 'fee_groups_feetype_id': fee_groups_feetype_id, 'fee_master_id': fee_master_id, 'fee_session_group_id': fee_session_group_id, 'student_session_id': student_session_id },
                beforeSend: function () {
                    $this.button('loading');
                },

                success: function (response) {

                    Popup(response.page);
                    $this.button('reset');
                },
                error: function (xhr) { // if error occured
                    alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                    $this.button('reset');
                },
                complete: function () {
                    $this.button('reset');
                }

            });
        });
    });

    $(document).on('click', '.getProcessingfees', function () {
        var $this = $(this);
        var student_session_id = '<?php echo $student['student_session_id'] ?>';
        $.ajax({
            type: 'POST',
            url: base_url + "studentfee/getProcessingfees/" + student_session_id,

            dataType: "JSON",
            beforeSend: function () {
                $this.button('loading');
            },
            success: function (data) {
                $("#processing_fess_modal .modal-body").html(data.view);
                $("#processing_fess_modal").modal('show');
                $this.button('reset');
            },
            error: function (xhr) { // if error occured
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");

            },
            complete: function () {
                $this.button('reset');
            }
        });
    });

</script>

<script type="text/javascript">
    $(document).on('click', '.save_button', function (e) {
        console.log('🔴 MAIN SAVE HANDLER - Starting fee collection process...');
        
        var $this = $(this);
        var action = $this.data('action');
        console.log('🔴 Action:', action);
        
        $this.button('loading');
        var form = $(this).attr('frm');
        var feetype = $('#feetype_').val();
        var date = $('#date').val();
        var accountname = $('#accountname').val();
        var student_session_id = $('#std_id').val();
        var amount = $('#amount').val();
        var amount_discount = $('#amount_discount').val();
        var amount_fine = $('#amount_fine').val();
        var description = $('#description1').val();
        var parent_app_key = $('#parent_app_key').val();
        var guardian_phone = $('#guardian_phone').val();
        var guardian_email = $('#guardian_email').val();
        var student_fees_master_id = $('#student_fees_master_id').val();
        var fee_groups_feetype_id = $('#fee_groups_feetype_id').val();
        var transport_fees_id = $('#transport_fees_id').val();
        var hostel_fees_id = $('#hostel_fees_id').val();
        var fee_category = $('#fee_category').val();
        var payment_mode = $('input[name="payment_mode_fee"]:checked').val();
        var student_fees_discount_id = $('#discount_group').val();
        var collect_from_advance = $('#collect_from_advance').is(':checked') ? 1 : 0;
        
        // CRITICAL DEBUG: Log all form values before submission
        console.log('🔴 Form data collected:', {
            action: action,
            student_session_id: student_session_id,
            date: date,
            amount: amount,
            amount_discount: amount_discount,
            amount_fine: amount_fine,
            fee_category: fee_category,
            payment_mode: payment_mode,
            accountname: accountname,
            collect_from_advance: collect_from_advance
        });
        
        // CRITICAL: Check if amount is being modified by advance payment logic
        console.log('🔴 AMOUNT DEBUG:');
        console.log('Amount field value:', $('#amount').val());
        console.log('Amount variable:', amount);
        console.log('Amount type:', typeof amount);
        console.log('Amount parsed as float:', parseFloat(amount));
        console.log('Collect from advance:', collect_from_advance);
        
        // Check if amount is zero or empty
        if (!amount || amount === '0' || amount === 0) {
            console.log('❌ WARNING: Amount is zero or empty!');
            console.log('Amount field element:', $('#amount')[0]);
            console.log('Amount field properties:', {
                value: $('#amount')[0].value,
                innerHTML: $('#amount')[0].innerHTML,
                type: $('#amount')[0].type
            });
        }
        
        // Debug logging for hostel fees
        if (fee_category === 'hostel') {
            console.log('HOSTEL FEE DEBUG - Save button clicked:');
            console.log('hostel_fees_id:', hostel_fees_id);
            console.log('student_session_id:', student_session_id);
            console.log('fee_category:', fee_category);
            console.log('amount:', amount);
        }
        
        // Additional validation for advance payment
        if (collect_from_advance === 1) {
            var advanceBalanceText = $('#modal_advance_balance').text();
            var advanceBalance = parseFloat(advanceBalanceText.replace(/[^0-9.]/g, ''));
            var currentAmount = parseFloat(amount) || 0;
            
            if (currentAmount > advanceBalance) {
                alert('Amount cannot exceed available advance balance of ' + advanceBalanceText);
                $this.button('reset');
                return false;
            }
        }
        
        console.log('🔴 Making AJAX request to:', '<?php echo site_url("studentfee/addstudentfee") ?>');
        
        $.ajax({
            url: '<?php echo site_url("studentfee/addstudentfee") ?>',
            type: 'post',
            data: { 
                action: action, 
                accountname: accountname,
                student_session_id: student_session_id, 
                date: date, 
                type: feetype, 
                amount: amount, 
                amount_discount: amount_discount, 
                amount_fine: amount_fine, 
                description: description, 
                student_fees_master_id: student_fees_master_id, 
                fee_groups_feetype_id: fee_groups_feetype_id, 
                fee_category: fee_category, 
                transport_fees_id: transport_fees_id, 
                hostel_fees_id: hostel_fees_id, 
                payment_mode: payment_mode, 
                guardian_phone: guardian_phone, 
                guardian_email: guardian_email, 
                student_fees_discount_id: student_fees_discount_id, 
                parent_app_key: parent_app_key,
                collect_from_advance: collect_from_advance
            },
            dataType: 'json',
            beforeSend: function() {
                console.log('🔴 AJAX beforeSend triggered');
            },
            success: function (response) {
                console.log('🔴 AJAX Success:', response);
                $this.button('reset');
                if (response.status == "success") {
                    if (action == "collect") {
                        console.log('🔴 Reloading page after successful collection');
                        location.reload(true);
                    } else if (action === "print") {
                        console.log('🔴 Opening print popup');
                        Popup(response.print, true);
                    }
                } else if (response.status === "fail") {
                    console.log('🔴 AJAX returned failure:', response.error);
                    $.each(response.error, function (index, value) {
                        var errorDiv = '#' + index + '_error';
                        $(errorDiv).empty().append(value);
                    });
                }
            },
            error: function(xhr, status, error) {
                console.log('🔴 AJAX Error:', error);
                console.log('🔴 XHR:', xhr);
                console.log('🔴 Status:', status);
                $this.button('reset');
                alert('Error occurred: ' + error);
            }
        });
    });
</script>


<script>
    var base_url = '<?php echo base_url() ?>';

    function Popup(data, winload = false) {
        var frame1 = $('<iframe />').attr("id", "printDiv");
        frame1[0].name = "frame1";
        frame1.css({ "position": "absolute", "top": "-1000000px" });
        $("body").append(frame1);
        var frameDoc = frame1[0].contentWindow ? frame1[0].contentWindow : frame1[0].contentDocument.document ? frame1[0].contentDocument.document : frame1[0].contentDocument;
        frameDoc.document.open();
        //Create a new HTML document.
        frameDoc.document.write('<html>');
        frameDoc.document.write('<head>');
        frameDoc.document.write('<title></title>');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/bootstrap/css/bootstrap.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/dist/css/font-awesome.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/dist/css/ionicons.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/dist/css/AdminLTE.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/dist/css/skins/_all-skins.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/plugins/iCheck/flat/blue.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/plugins/morris/morris.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/plugins/jvectormap/jquery-jvectormap-1.2.2.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/plugins/datepicker/datepicker3.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/plugins/daterangepicker/daterangepicker-bs3.css">');
        frameDoc.document.write('</head>');
        frameDoc.document.write('<body>');
        frameDoc.document.write(data);
        frameDoc.document.write('</body>');
        frameDoc.document.write('</html>');
        frameDoc.document.close();
        setTimeout(function () {
            document.getElementById('printDiv').contentWindow.focus();
            document.getElementById('printDiv').contentWindow.print();
            $("#printDiv", top.document).remove();
            if (winload) {
                window.location.reload(true);
            }
        }, 500);

        return true;
    }
    $(document).ready(function () {
        $('.delmodal').modal({
            backdrop: 'static',
            keyboard: false,
            show: false
        });
        $('#listCollectionModal').modal({
            backdrop: 'static',
            keyboard: false,
            show: false
        });

        $('#confirm-delete').on('show.bs.modal', function (e) {
            $('.invoice_no', this).text("");
            $('#main_invoice', this).val("");
            $('#sub_invoice', this).val("");
            $('.invoice_no', this).text($(e.relatedTarget).data('invoiceno'));
            $('#main_invoice', this).val($(e.relatedTarget).data('main_invoice'));
            $('#sub_invoice', this).val($(e.relatedTarget).data('sub_invoice'));
        });

        $('#confirm-discountdelete').on('show.bs.modal', function (e) {
            $('.discount_title', this).text("");
            $('#discount_id', this).val("");
            $('.discount_title', this).text($(e.relatedTarget).data('discounttitle'));
            $('#discount_id', this).val($(e.relatedTarget).data('discountid'));
        });

        $('#confirm-delete').on('click', '.btn-ok', function (e) {
            var $modalDiv = $(e.delegateTarget);
            var main_invoice = $('#main_invoice').val();
            var sub_invoice = $('#sub_invoice').val();

            $modalDiv.addClass('modalloading');
            $.ajax({
                type: "post",
                url: '<?php echo site_url("studentfee/deleteFee") ?>',
                dataType: 'JSON',
                data: { 'main_invoice': main_invoice, 'sub_invoice': sub_invoice },
                success: function (data) {
                    $modalDiv.modal('hide').removeClass('modalloading');
                    location.reload(true);
                }
            });
        });

        $('#confirm-discountdelete').on('click', '.btn-discountdel', function (e) {
            var $modalDiv = $(e.delegateTarget);
            var discount_id = $('#discount_id').val();

            $modalDiv.addClass('modalloading');
            $.ajax({
                type: "post",
                url: '<?php echo site_url("studentfee/deleteStudentDiscount") ?>',
                dataType: 'JSON',
                data: { 'discount_id': discount_id },
                success: function (data) {
                    $modalDiv.modal('hide').removeClass('modalloading');
                    location.reload(true);
                }
            });
        });

        $(document).on('click', '.btn-ok', function (e) {
            var $modalDiv = $(e.delegateTarget);
            var main_invoice = $('#main_invoice').val();
            var sub_invoice = $('#sub_invoice').val();

            $modalDiv.addClass('modalloading');
            $.ajax({
                type: "post",
                url: '<?php echo site_url("studentfee/deleteFee") ?>',
                dataType: 'JSON',
                data: { 'main_invoice': main_invoice, 'sub_invoice': sub_invoice },
                success: function (data) {
                    $modalDiv.modal('hide').removeClass('modalloading');
                    location.reload(true);
                }
            });

        });
        $('.detail_popover').popover({
            placement: 'right',
            title: '',
            trigger: 'hover',
            container: 'body',
            html: true,
            content: function () {
                return $(this).closest('td').find('.fee_detail_popover').html();
            }
        });
    });
    var fee_amount = 0;
    var fee_type_amount = 0;
</script>

<script type="text/javascript">
    $("#myFeesModal").on('shown.bs.modal', function (e) {
        e.stopPropagation();
        var discount_group_dropdown = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
        var data = $(e.relatedTarget).data();
        console.log(data);

        var modal = $(this);
        var type = data.type;
        var amount = data.amount;
        var group = data.group;
        var fee_groups_feetype_id = data.fee_groups_feetype_id;
        var student_fees_master_id = data.student_fees_master_id;
        var student_session_id = data.student_session_id;
        var fee_category = data.feeCategory;
        var trans_fee_id = data.trans_fee_id;
        var hostel_fee_id = data.hostel_fee_id;

        $('.fees_title').html("");
        $('.fees_title').html("<b>" + group + ":</b> " + type);
        $('#fee_groups_feetype_id').val(fee_groups_feetype_id);
        $('#student_fees_master_id').val(student_fees_master_id);
        $('#transport_fees_id').val(trans_fee_id);
        $('#hostel_fees_id').val(hostel_fee_id);
        $('#fee_category').val(fee_category);

        $.ajax({
            type: "post",
            url: '<?php echo site_url("studentfee/geBalanceFee") ?>',
            dataType: 'JSON',
            data: {
                'fee_groups_feetype_id': fee_groups_feetype_id,
                'student_fees_master_id': student_fees_master_id,
                'student_session_id': student_session_id,
                'fee_category': fee_category,
                'trans_fee_id': fee_category === 'hostel' ? hostel_fee_id : trans_fee_id,
                'hostel_fee_id': hostel_fee_id
            },
            beforeSend: function () {
                $('#discount_group').html("");
                $("span[id$='_error']").html("");
                $('#amount').val("");
                $('#amount_discount').val("0");
                $('#amount_fine').val("0");
            },
            success: function (data) {

                if (data.status === "success") {
                    fee_amount = data.balance;
                    fee_type_amount = data.student_fees;
                    $('#amount').val(data.balance);
                    $('#amount_fine').val(data.remain_amount_fine);
                    
                    // CRITICAL DEBUG: Log when amount field is set
                    console.log('🔴 AMOUNT FIELD SET BY AJAX:');
                    console.log('Setting amount field to:', data.balance);
                    console.log('data.balance type:', typeof data.balance);
                    console.log('Amount field value after setting:', $('#amount').val());
                    
                    // Set up a watcher to detect any changes to the amount field
                    $('#amount').off('input.debug').on('input.debug', function() {
                        console.log('🔴 AMOUNT FIELD CHANGED!');
                        console.log('New value:', $(this).val());
                        console.log('Stack trace:', new Error().stack);
                    });
                    $.each(data.discount_not_applied, function (i, obj) {
                        discount_group_dropdown += "<option value=" + obj.student_fees_discount_id + " data-disamount=" + obj.amount + " data-type=" + obj.type + " data-percentage=" + obj.percentage + ">" + obj.code + "</option>";
                    });
                    $('#discount_group').append(discount_group_dropdown);

                    // Load advance payment balance for the student
                    loadAdvanceBalanceForModal(student_session_id);
                }
            },
            error: function (xhr) { // if error occured
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");

            },
            complete: function () {
            }
        });
    });
</script>

<script type="text/javascript">
    $(document).ready(function () {
        $.extend($.fn.dataTable.defaults, {
            searching: false,
            ordering: false,
            paging: false,
            bSort: false,
            info: false
        });
    });

    $(document).ready(function () {
        $('.table-fixed-header').fixedHeader();
    });

    (function ($) {

        $.fn.fixedHeader = function (options) {
            var config = {
                topOffset: 50
                //bgColor: 'white'
            };
            if (options) {
                $.extend(config, options);
            }

            return this.each(function () {
                var o = $(this);

                var $win = $(window);
                var $head = $('thead.header', o);
                var isFixed = 0;
                var headTop = $head.length && $head.offset().top - config.topOffset;

                function processScroll() {
                    if (!o.is(':visible')) {
                        return;
                    }
                    if ($('thead.header-copy').size()) {
                        $('thead.header-copy').width($('thead.header').width());
                    }
                    var i;
                    var scrollTop = $win.scrollTop();
                    var t = $head.length && $head.offset().top - config.topOffset;
                    if (!isFixed && headTop !== t) {
                        headTop = t;
                    }
                    if (scrollTop >= headTop && !isFixed) {
                        isFixed = 1;
                    } else if (scrollTop <= headTop && isFixed) {
                        isFixed = 0;
                    }
                    isFixed ? $('thead.header-copy', o).offset({
                        left: $head.offset().left
                    }).removeClass('hide') : $('thead.header-copy', o).addClass('hide');
                }
                $win.on('scroll', processScroll);

                // hack sad times - holdover until rewrite for 2.1
                $head.on('click', function () {
                    if (!isFixed) {
                        setTimeout(function () {
                            $win.scrollTop($win.scrollTop() - 47);
                        }, 10);
                    }
                });

                $head.clone().removeClass('header').addClass('header-copy header-fixed').appendTo(o);
                var header_width = $head.width();
                o.find('thead.header-copy').width(header_width);
                o.find('thead.header > tr:first > th').each(function (i, h) {
                    var w = $(h).width();
                    o.find('thead.header-copy> tr > th:eq(' + i + ')').width(w);
                });
                $head.css({
                    margin: '0 auto',
                    width: o.width(),
                    'background-color': config.bgColor
                });
                processScroll();
            });
        };

    })(jQuery);

    $(".applydiscount").click(function () {
        $("span[id$='_error']").html("");
        $('.discount_title').html("");
        $('#student_fees_discount_id').val("");
        var student_fees_discount_id = $(this).data("student_fees_discount_id");
        var modal_title = $(this).data("modal_title");
        $('.discount_title').html("<b>" + modal_title + "</b>");
        $('#student_fees_discount_id').val(student_fees_discount_id);
        $('#myDisApplyModal').modal({
            backdrop: 'static',
            keyboard: false,
            show: true
        });
    });

    $(document).on('click', '.dis_apply_button', function (e) {
        var $this = $(this);
        $this.button('loading');
        var discount_payment_id = $('#discount_payment_id').val();
        var student_fees_discount_id = $('#student_fees_discount_id').val();
        var dis_description = $('#dis_description').val();

        $.ajax({
            url: '<?php echo site_url("admin/feediscount/applydiscount") ?>',
            type: 'post',
            data: {
                discount_payment_id: discount_payment_id,
                student_fees_discount_id: student_fees_discount_id,
                dis_description: dis_description
            },
            dataType: 'json',
            success: function (response) {
                $this.button('reset');
                if (response.status === "success") {
                    location.reload(true);
                } else if (response.status === "fail") {
                    $.each(response.error, function (index, value) {
                        var errorDiv = '#' + index + '_error';
                        $(errorDiv).empty().append(value);
                    });
                }
            }
        });
    });

</script>

<script type="text/javascript">

    $(document).ready(function () {
        $(document).on('click', '.printSelected', function () {
            var array_to_print = [];
            var $this = $(this);
            $.each($("input[name='fee_checkbox']:checked"), function () {
                var trans_fee_id = $(this).data('trans_fee_id');
                var fee_category = $(this).data('fee_category');
                var fee_session_group_id = $(this).data('fee_session_group_id');
                var fee_master_id = $(this).data('fee_master_id');
                var fee_groups_feetype_id = $(this).data('fee_groups_feetype_id');
                var otherfeecat = $(this).data('otherfeecat');
                var student_session_id = $(this).data('student_session_id');
                var hostel_fee_id = $(this).data('hostel_fee_id');
                
                item = {};
                item["fee_category"] = fee_category;
                item["trans_fee_id"] = trans_fee_id;
                item["fee_session_group_id"] = fee_session_group_id;
                item["fee_master_id"] = fee_master_id;
                item["fee_groups_feetype_id"] = fee_groups_feetype_id;
                item["otherfeecat"] = otherfeecat;
                item["student_session_id"] = student_session_id;
                item["hostel_fee_id"] = hostel_fee_id;

                array_to_print.push(item);
            });
            if (array_to_print.length === 0) {
                alert("<?php echo $this->lang->line('no_record_selected'); ?>");
            } else {
                $.ajax({
                    url: '<?php echo site_url("studentfee/printFeesByGroupArray") ?>',
                    type: 'post',
                    data: { 'data': JSON.stringify(array_to_print) },
                    beforeSend: function () {
                        $this.button('loading');
                    },
                    success: function (response) {
                        Popup(response);
                        $this.button('reset');
                    },
                    error: function (xhr) { // if error occured
                        alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");

                    },
                    complete: function () {
                        $this.button('reset');
                    }
                });
            }
        });

        $(document).on('click', '.collectSelected', function () {
            var $this = $(this);
            var array_to_collect_fees = [];
            $.each($("input[name='fee_checkbox']:checked"), function () {

                var trans_fee_id = $(this).data('trans_fee_id');
                var fee_category = $(this).data('fee_category');
                var fee_session_group_id = $(this).data('fee_session_group_id');
                var fee_master_id = $(this).data('fee_master_id');
                var fee_groups_feetype_id = $(this).data('fee_groups_feetype_id');
                var otherfeecat = $(this).data('otherfeecat');
                var student_session_id = $(this).data('student_session_id');
                item = {};
                item["fee_category"] = fee_category;
                item["trans_fee_id"] = trans_fee_id;
                item["fee_session_group_id"] = fee_session_group_id;
                item["fee_master_id"] = fee_master_id;
                item["fee_groups_feetype_id"] = fee_groups_feetype_id;
                item["otherfeecat"] = otherfeecat;
                item["student_session_id"] = student_session_id;

                array_to_collect_fees.push(item);
            });

            $.ajax({
                type: 'POST',
                url: base_url + "studentfee/getcollectfee",
                data: { 'data': JSON.stringify(array_to_collect_fees) },
                dataType: "JSON",
                beforeSend: function () {
                    $this.button('loading');
                },
                success: function (data) {

                    $("#listCollectionModal .modal-body").html(data.view);
                    $("#listCollectionModal").modal('show');
                    $this.button('reset');
                },
                error: function (xhr) { // if error occured
                    alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");

                },
                complete: function () {
                    $this.button('reset');
                }
            });
        });

        $(document).on('click', '.ministatement', function () {
            var array_to_print = [];
            var $this = $(this);
            $.each($("input[name='fee_checkbox']:checked"), function () {
                var trans_fee_id = $(this).data('trans_fee_id');
                var fee_master_id = $(this).data('fee_master_id');
                var fee_session_group_id = $(this).data('fee_session_group_id');
                var fee_groups_feetype_id = $(this).data('fee_groups_feetype_id');
                var fee_category = $(this).data('fee_category');
                var otherfeecat = $(this).data('otherfeecat');
                var student_session_id = $(this).data('student_session_id');
                var hostel_fee_id = $(this).data('hostel_fee_id');
                var item = {};
                item["trans_fee_id"] = trans_fee_id;
                item["fee_master_id"] = fee_master_id;
                item["fee_session_group_id"] = fee_session_group_id;
                item["fee_groups_feetype_id"] = fee_groups_feetype_id;
                item["fee_category"] = fee_category;
                item["otherfeecat"] = otherfeecat;
                item["student_session_id"] = student_session_id;
                item["hostel_fee_id"] = hostel_fee_id;

                array_to_print.push(item);
            });
            if (array_to_print.length === 0) {
                alert("<?php echo $this->lang->line('no_record_selected'); ?>");
            } else {
                $.ajax({
                    url: '<?php echo site_url("studentfee/printMiniFeesByGroupArray") ?>',
                    type: 'post',
                    data: { 'data': JSON.stringify(array_to_print) },
                    beforeSend: function () {
                        $this.button('loading');
                    },
                    success: function (response) {
                        Popup(response);
                        $this.button('reset');
                    },
                    error: function (xhr) { // if error occured
                        alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");

                    },
                    complete: function () {
                        $this.button('reset');
                    }
                });
            }
        });
    });

    $(function () {
        $(document).on('change', "#discount_group", function () {
            var amount = $('option:selected', this).data('disamount');
            var type = $('option:selected', this).data('type');
            var percentage = $('option:selected', this).data('percentage');
            let balance_amount = 0;
            if (type == null || type == "fix") {

                balance_amount = (parseFloat(fee_amount) - parseFloat(amount)).toFixed(2);
            } else if (type == "percentage") {
                var per_amount = ((parseFloat(fee_type_amount) * parseFloat(percentage)) / 100).toFixed(2);
                balance_amount = (parseFloat(fee_amount) - per_amount).toFixed(2);
            }

            if (typeof amount !== typeof undefined && amount !== false) {
                $('div#myFeesModal').find('input#amount_discount').prop('readonly', true).val((type == "percentage") ? per_amount : amount);
                $('div#myFeesModal').find('input#amount').val(balance_amount);

            } else {
                $('div#myFeesModal').find('input#amount').val(fee_amount);
                $('div#myFeesModal').find('input#amount_discount').prop('readonly', false).val(0);
            }
        });
    });

    $("#collect_fee_group").submit(function (e) {
        var form = $(this);
        var url = form.attr('action');
        var smt_btn = $(this).find("button[type=submit]");
        $.ajax({
            type: "POST",
            url: url,
            dataType: 'JSON',
            data: form.serialize(), // serializes the form's elements.
            beforeSend: function () {
                smt_btn.button('loading');
            },
            success: function (response) {
                if (response.status === 1) {

                    location.reload(true);
                } else if (response.status === 0) {
                    $.each(response.error, function (index, value) {
                        var errorDiv = '#form_collection_' + index + '_error';
                        $(errorDiv).empty().append(value);
                    });
                }
            },
            error: function (xhr) { // if error occured

                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");

            },
            complete: function () {
                smt_btn.button('reset');
            }
        });

        e.preventDefault(); // avoid to execute the actual submit of the form.
    });


    $(document).on('change', '#select_all', function () {
        console.log("sdfsfs");
        $('input:checkbox').not(this).prop('checked', this.checked);
    });

</script>














<script type="text/javascript">
    $("#myAdditionalFeesModal").on('shown.bs.modal', function (e) {
        e.stopPropagation();
        var discount_group_dropdown = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
        var data = $(e.relatedTarget).data();
        console.log("Modal Data:", data);
        var modal = $(this);
        var adding_type = data.type;
        var adding_amount = data.amount;
        var adding_group = data.group;
        var adding_fee_groups_feetype_id = data.fee_groups_feetype_id;
        var adding_student_fees_master_id = data.student_fees_master_id;
        var adding_student_session_id = data.student_session_id;
        var adding_fee_category = data.feeCategory;
        var adding_trans_fee_id = data.trans_fee_id;

        $('.adding_fees_title').html("<b>" + adding_group + ":</b> " + adding_type);
        $('#adding_fee_groups_feetype_id').val(adding_fee_groups_feetype_id);
        $('#adding_student_fees_master_id').val(adding_student_fees_master_id);
        $('#adding_transport_fees_id').val(adding_trans_fee_id);
        $('#adding_fee_category').val(adding_fee_category);

        $.ajax({
            type: "post",
            url: '<?php echo site_url("studentfee/geBalanceFeeadding") ?>',
            dataType: 'json',
            data: {
                'fee_groups_feetype_id': adding_fee_groups_feetype_id,
                'student_fees_master_id': adding_student_fees_master_id,
                'student_session_id': adding_student_session_id,
                'fee_category': adding_fee_category,
                'trans_fee_id': adding_trans_fee_id
            },
            beforeSend: function () {
                $('#adding_discount_group').html("");
                $("span[id$='_error']").html("");
                $('#adding_amount').val("");
                $('#adding_amount_discount').val("0");
                $('#adding_amount_fine').val("0");
            },
            success: function (data) {
                console.log("AJAX Success Data:", data);
                if (data.status === "success") {
                    $('#adding_amount').val(data.balance);
                    $('#adding_amount_fine').val(data.remain_amount_fine);
                    $.each(data.discount_not_applied, function (i, obj) {
                        discount_group_dropdown += "<option value=" + obj.student_fees_discount_id + " data-disamount=" + obj.amount + " data-type=" + obj.type + " data-percentage=" + obj.percentage + ">" + obj.code + "</option>";
                    });
                    $('#adding_discount_group').append(discount_group_dropdown);
                }
            },
            error: function (xhr, status, error) {
                console.log("AJAX Error:", status, error);
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
            },
            complete: function () { }
        });
    });

    $(document).on('click', '.adding_save_button', function (e) {
        var $this = $(this);
        var adding_action = $this.data('action');
        $this.button('loading');
        var form = $(this).attr('frm');
        var feetype = $('#feetype_').val();
        var adding_date = $('#adding_date').val();
        var addingaccountname = $('#addingaccountname').val();
        var adding_student_session_id = $('#adding_std_id').val();
        var adding_amount = $('#adding_amount').val();
        var adding_amount_discount = $('#adding_amount_discount').val();
        var adding_amount_fine = $('#adding_amount_fine').val();
        var adding_description = $('#adding_description').val();
        var adding_parent_app_key = $('#adding_parent_app_key').val();
        var adding_guardian_phone = $('#adding_guardian_phone').val();
        var adding_guardian_email = $('#adding_guardian_email').val();
        var adding_student_fees_master_id = $('#adding_student_fees_master_id').val();
        var adding_fee_groups_feetype_id = $('#adding_fee_groups_feetype_id').val();
        var adding_transport_fees_id = $('#adding_transport_fees_id').val();
        var adding_fee_category = $('#adding_fee_category').val();
        var adding_payment_mode = $('input[name="adding_payment_mode_fee"]:checked').val();
        var adding_student_fees_discount_id = $('#adding_discount_group').val();


        $.ajax({
            url: '<?php echo site_url("studentfee/addstudentadditionalfee") ?>',
            type: 'post',
            data: {
                adding_action: adding_action,
                adding_student_session_id: adding_student_session_id,
                adding_date: adding_date,
                addingaccountname : addingaccountname,
                adding_amount: adding_amount,
                adding_amount_discount: adding_amount_discount,
                adding_amount_fine: adding_amount_fine,
                adding_description: adding_description,
                adding_parent_app_key: adding_parent_app_key,
                adding_guardian_phone: adding_guardian_phone,
                adding_guardian_email: adding_guardian_email,
                adding_student_fees_master_id: adding_student_fees_master_id,
                adding_fee_groups_feetype_id: adding_fee_groups_feetype_id,
                adding_transport_fees_id: adding_transport_fees_id,
                adding_fee_category: adding_fee_category,
                adding_payment_mode: adding_payment_mode,
                adding_student_fees_discount_id: adding_student_fees_discount_id
            },
            dataType: 'json',
            success: function (response) {

                $this.button('reset');
                if (response.status === "success") {
                    if (adding_action === "collect") {
                        location.reload(true);
                    } else if (adding_action === "print") {
                        Popup(response.print, true);
                    }
                } else if (response.status === "fail") {

                    $.each(response.error, function (index, value) {
                        var errorDiv = '#' + index + '_error';
                        $(errorDiv).empty().append(value);
                    });
                }
            },
            error: function (xhr, status, error) {
                console.log("AJAX Error:", status, error);
                alert("AJAX Error: " + status + " - " + error);
            }
        });
    });



    $('#adding-confirm-delete').on('show.bs.modal', function (e) {
        $('.adding_invoice_no', this).text("");
        $('#adding_main_invoice', this).val("");
        $('#adding_sub_invoice', this).val("");
        $('.adding_invoice_no', this).text($(e.relatedTarget).data('adding_invoiceno'));
        $('#adding_main_invoice', this).val($(e.relatedTarget).data('adding_main_invoice'));
        $('#adding_sub_invoice', this).val($(e.relatedTarget).data('adding_sub_invoice'));
    });


    $('#adding-confirm-delete').on('click', '.btn-ok', function (e) {
        var $modalDiv = $(e.delegateTarget);
        var adding_main_invoice = $('#adding_main_invoice').val();
        var adding_sub_invoice = $('#adding_sub_invoice').val();

        $modalDiv.addClass('modalloading');
        $.ajax({
            type: "post",
            url: '<?php echo site_url("studentfee/deleteaddingFee") ?>',
            dataType: 'JSON',
            data: { 'main_invoice': adding_main_invoice, 'sub_invoice': adding_sub_invoice },
            success: function (data) {
                $modalDiv.modal('hide').removeClass('modalloading');
                location.reload(true);
            }
        });
    });

    $(document).on('click', '.adding_printDoc', function () {

        var adding_main_invoice = $(this).data('adding_main_invoice');


        var adding_sub_invoice = $(this).data('adding_sub_invoice');
        var fee_category = $(this).data('fee-category');
        var student_session_id = '<?php echo $student['student_session_id'] ?>';

        var fee_master_id = $(this).data('fee_master_id');
        var student_session_id = $(this).data('student_session_id');
        var fee_session_group_id = $(this).data('fee_session_group_id');
        var fee_groups_feetype_id = $(this).data('fee_groups_feetype_id');

        $.ajax({
            url: '<?php echo site_url("studentfee/printaddingFeesByName") ?>',
            type: 'post',
            dataType: "JSON",
            data: { 'fee_groups_feetype_id':fee_groups_feetype_id,'fee_session_group_id':fee_session_group_id,'fee_master_id':fee_master_id, 'fee_category': fee_category, 'student_session_id': student_session_id, 'main_invoice': main_invoice, 'sub_invoice': sub_invoice },
            // data: { 'fee_category': fee_category, 'student_session_id': student_session_id, 'main_invoice': adding_main_invoice, 'sub_invoice': adding_sub_invoice },
            success: function (response) {
                Popup(response.page);
            }
        });
    });

    $(document).on('click', '.adding_printInv', function () {
        var $this = $(this);
        var adding_fee_master_id = $(this).data('fee_master_id');
        var adding_student_session_id = $(this).data('student_session_id');
        var adding_fee_session_group_id = $(this).data('fee_session_group_id');
        var adding_fee_groups_feetype_id = $(this).data('fee_groups_feetype_id');
        var adding_trans_fee_id = $(this).data('trans_fee_id');
        var adding_fee_category = $(this).data('feeCategory');
        // alert(fee_category);
        $.ajax({
            url: '<?php echo site_url("studentfee/printaddingFeesByGroup") ?>',
            type: 'post',
            dataType: "JSON",
            data: { 'trans_fee_id': adding_trans_fee_id, 'fee_category': adding_fee_category, 'fee_groups_feetype_id': adding_fee_groups_feetype_id, 'fee_master_id': adding_fee_master_id, 'fee_session_group_id': adding_fee_session_group_id, 'student_session_id': adding_student_session_id },
            beforeSend: function () {
                $this.button('loading');
            },

            success: function (response) {

                Popup(response.page);
                $this.button('reset');
            },
            error: function (xhr) { // if error occured
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                $this.button('reset');
            },
            complete: function () {
                $this.button('reset');
            }

        });
    });








    $(document).ready(function() {
        // Session header collapsible functionality
        $('.session-header').on('click', function() {
            var sessionId = $(this).data('session-id');
            var $container = $('.session-fees-container[data-session-id="' + sessionId + '"]');
            var $toggle = $(this).find('.session-toggle');

            $container.toggle();
            $toggle.toggleClass('collapsed');
        });

        // Function to fetch account types based on the selected payment mode
        function fetchAccountTypes(selectedValue) {
            $.ajax({
                type: "GET",
                url: base_url + "admin/addaccount/getaccounts",
                data: { 'accountcategory_id': selectedValue },
                dataType: "json",
                success: function(data) {
                    var options = '';
                    // $.each(data, function(i, obj) {
                    //     options += "<option value='" + obj.id + "'>" + obj.name + "</option>";
                    // });
                    if (data && data.length > 0) {
                        $.each(data, function(i, obj) {
                            options += "<option value='" + obj.id + "'>" + obj.name + "</option>";
                        });
                    } else {
                        options = "<option value='' selected>Select an option</option>";
                    }
                    $('#accountname').html(options);
                }
            });
        }

        // Fetch account types when the page loads
        var selectedValue = $('input[name="payment_mode_fee"]:checked').val();
        fetchAccountTypes(selectedValue);

        // Event listener for radio buttons
        $('input[name="payment_mode_fee"]').change(function() {
            var selectedValue = $(this).val();
            fetchAccountTypes(selectedValue);
        });
    });


    $(document).ready(function() {
        // Function to fetch account types based on the selected payment mode
        function fetchAccountTypes(selectedValue) {
            $.ajax({
                type: "GET",
                url: base_url + "admin/addaccount/getaccounts",
                data: { 'accountcategory_id': selectedValue },
                dataType: "json",
                success: function(data) {
                    var options = '';
                    // $.each(data, function(i, obj) {
                    //     options += "<option value='" + obj.id + "'>" + obj.name + "</option>";
                    // });
                    if (data && data.length > 0) {
                        $.each(data, function(i, obj) {
                            options += "<option value='" + obj.id + "'>" + obj.name + "</option>";
                        });
                    } else {
                        options = "<option value='' selected>Select an option</option>";
                    }
                    $('#addingaccountname').html(options);
                }
            });
        }

        // Fetch account types when the page loads
        var selectedValue = $('input[name="adding_payment_mode_fee"]:checked').val();
        fetchAccountTypes(selectedValue);

        // Event listener for radio buttons
        $('input[name="adding_payment_mode_fee"]').change(function() {
            var selectedValue = $(this).val();
            fetchAccountTypes(selectedValue);
        });
    });

    // Session header collapsible functionality
    $('.session-header').on('click', function() {
        var sessionId = $(this).data('session-id');
        var $container = $('.session-fees-container[data-session-id="' + sessionId + '"]');
        var $toggle = $(this).find('.session-toggle');

        $container.toggle();
        $toggle.toggleClass('collapsed');
    });


    // Add CSS for proper date picker alignment and positioning
    $('<style>')
        .prop('type', 'text/css')
        .html(`
            /* Date picker input styling */
            .date_fee {
                position: relative;
                z-index: 1;
                cursor: pointer;
            }

            /* Datepicker dropdown container - use absolute positioning for better control */
            .datepicker.dropdown-menu {
                position: absolute !important;
                z-index: 1060 !important;
                margin-top: 2px;
                padding: 4px;
                border: 1px solid rgba(0,0,0,.15);
                border-radius: 4px;
                box-shadow: 0 6px 12px rgba(0,0,0,.175);
                background-color: #fff;
                min-width: 250px;
                font-size: 14px;
                display: block;
            }

            /* Ensure datepicker appears above modals */
            .modal-open .datepicker.dropdown-menu {
                z-index: 1070 !important;
            }

            /* Datepicker positioning in modals */
            .modal .datepicker.dropdown-menu {
                position: absolute !important;
                z-index: 1070 !important;
            }

            /* Input field styling */
            .date_fee.form-control {
                background-color: #fff;
            }

            .date_fee.form-control:focus {
                border-color: #66afe9;
                outline: 0;
                box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102,175,233,.6);
            }

            /* Table styling within datepicker */
            .datepicker table {
                width: 100%;
                margin: 0;
            }

            .datepicker td, .datepicker th {
                text-align: center;
                width: 30px;
                height: 30px;
                border-radius: 4px;
                border: none;
            }

            /* Hover and active states */
            .datepicker table tr td.active:hover,
            .datepicker table tr td.active:hover:hover,
            .datepicker table tr td.active.active {
                background-color: #3c8dbc !important;
                border-color: #367fa9;
            }

            /* Ensure proper positioning relative to input */
            .datepicker-container {
                position: relative;
            }
        `)
        .appendTo('head');

    // Initialize date pickers with fixed positioning
    $(document).ready(function() {
        function initializeDatePickers() {
            if (!$.fn.datepicker) {
                console.error('Datepicker plugin not loaded');
                return;
            }

            $('.date_fee').each(function() {
                var $input = $(this);

                // Destroy existing instance if any
                if ($input.data('datepicker')) {
                    $input.datepicker('destroy');
                }

                // Initialize datepicker with proper positioning
                $input.datepicker({
                    format: 'dd/mm/yyyy',
                    autoclose: true,
                    todayHighlight: true,
                    orientation: 'bottom left',
                    container: 'body',
                    zIndexOffset: 1050
                });
            });
        }

        // Function to position datepicker correctly
        function positionDatepicker($input) {
            setTimeout(function() {
                var $datepicker = $('.datepicker.dropdown-menu:visible');
                if ($datepicker.length && $input.length) {
                    var inputOffset = $input.offset();
                    var inputHeight = $input.outerHeight();
                    var inputWidth = $input.outerWidth();

                    // Check if we're in a modal
                    var $modal = $input.closest('.modal');

                    if ($modal.length) {
                        // For modals, use absolute positioning relative to the modal content
                        var $modalContent = $modal.find('.modal-content');
                        var modalContentOffset = $modalContent.offset();

                        // Calculate position relative to modal content
                        var relativeTop = inputOffset.top - modalContentOffset.top + inputHeight + 2;
                        var relativeLeft = inputOffset.left - modalContentOffset.left;

                        // Ensure the datepicker stays within modal bounds
                        var modalWidth = $modalContent.outerWidth();
                        var datepickerWidth = Math.max(inputWidth, 250);

                        if (relativeLeft + datepickerWidth > modalWidth) {
                            relativeLeft = modalWidth - datepickerWidth - 10;
                        }

                        $datepicker.css({
                            'position': 'absolute',
                            'top': relativeTop + 'px',
                            'left': relativeLeft + 'px',
                            'z-index': '1070',
                            'min-width': datepickerWidth + 'px'
                        });

                        // Append to modal content to ensure proper positioning
                        if ($datepicker.parent()[0] !== $modalContent[0]) {
                            $datepicker.appendTo($modalContent);
                        }
                    } else {
                        // For non-modal elements, use absolute positioning relative to document
                        $datepicker.css({
                            'position': 'absolute',
                            'top': (inputOffset.top + inputHeight + 2) + 'px',
                            'left': inputOffset.left + 'px',
                            'z-index': '1060',
                            'min-width': Math.max(inputWidth, 250) + 'px'
                        });
                    }

                    console.log('📍 Datepicker positioned correctly');
                }
            }, 10);
        }

        // Initialize datepickers
        initializeDatePickers();

        // Reinitialize after modal content loads
        setTimeout(initializeDatePickers, 500);

        // Event handlers for datepicker positioning
        $(document).on('show', '.date_fee', function(e) {
            var $input = $(this);
            positionDatepicker($input);
        });

        $(document).on('click focus', '.date_fee', function(e) {
            var $this = $(this);

            // Initialize if not already done
            if (!$this.data('datepicker')) {
                $this.datepicker({
                    format: 'dd/mm/yyyy',
                    autoclose: true,
                    todayHighlight: true,
                    orientation: 'bottom left',
                    container: 'body',
                    zIndexOffset: 1050
                });
            }

            // Show and position the datepicker
            setTimeout(function() {
                $this.datepicker('show');
                positionDatepicker($this);
            }, 10);
        });

        // Additional event handler for when datepicker is shown
        $(document).on('changeDate show', '.date_fee', function(e) {
            var $input = $(this);
            positionDatepicker($input);
        });

        // Handle modal events to reposition datepickers
        $(document).on('shown.bs.modal', '.modal', function() {
            setTimeout(function() {
                initializeDatePickers();
                // Hide any visible datepickers when modal opens
                $('.datepicker.dropdown-menu:visible').hide();
            }, 100);
        });

        // Specific handler for myFeesModal
        $("#myFeesModal").on('shown.bs.modal', function() {
            setTimeout(function() {
                initializeDatePickers();
            }, 150);
        });

        // Handle window resize to reposition visible datepickers
        $(window).on('resize', function() {
            var $visibleDatepicker = $('.datepicker.dropdown-menu:visible');
            if ($visibleDatepicker.length) {
                var $input = $('.date_fee:focus');
                if ($input.length) {
                    positionDatepicker($input);
                }
            }
        });

        console.log('🎯 Date picker solution loaded');
    });
</script>

<!-- Advance Payment Modal -->
<div class="modal fade" id="advancePaymentModal" tabindex="-1" role="dialog" aria-labelledby="advancePaymentModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="advancePaymentModalLabel">
                    <i class="fa fa-plus"></i> <?php echo $this->lang->line('add_advance_payment'); ?>
                </h4>
            </div>
            <form id="advancePaymentForm" method="post" action="<?php echo site_url('studentfee/createAdvancePayment'); ?>">
                <div class="modal-body">
                    <?php echo $this->customlib->getCSRF(); ?>
                    <input type="hidden" id="modal_student_session_id" name="student_session_id" value="">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('student_name'); ?> <span class="req">*</span></label>
                                <input type="text" id="modal_student_name" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('admission_no'); ?></label>
                                <input type="text" id="modal_admission_no" class="form-control" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('class'); ?></label>
                                <input type="text" id="modal_class_section" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('father_name'); ?></label>
                                <input type="text" id="modal_father_name" class="form-control" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('amount'); ?> <span class="req">*</span></label>
                                <input type="number" step="0.01" name="amount" id="advance_amount" class="form-control" placeholder="0.00" required>
                                <span class="text-danger" id="error_amount"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('date'); ?> <span class="req">*</span></label>
                                <input type="text" name="date" id="advance_date" class="form-control date" value="<?php echo date($this->customlib->getSchoolDateFormat()); ?>" required>
                                <span class="text-danger" id="error_date"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('payment_mode'); ?> <span class="req">*</span></label>
                                <select name="payment_mode" id="advance_payment_mode" class="form-control" required>
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    <option value="cash"><?php echo $this->lang->line('cash'); ?></option>
                                    <option value="cheque"><?php echo $this->lang->line('cheque'); ?></option>
                                    <option value="dd"><?php echo $this->lang->line('dd'); ?></option>
                                    <option value="bank_transfer"><?php echo $this->lang->line('bank_transfer'); ?></option>
                                    <option value="upi"><?php echo $this->lang->line('upi'); ?></option>
                                    <option value="card"><?php echo $this->lang->line('card'); ?></option>
                                </select>
                                <span class="text-danger" id="error_payment_mode"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('reference_no'); ?></label>
                                <input type="text" name="reference_no" id="advance_reference_no" class="form-control">
                                <span class="text-danger" id="error_reference_no"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('accountname'); ?></label>
                                <select name="accountname" id="advance_accountname" class="form-control">
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                </select>
                                <span class="text-danger" id="error_accountname"></span>
                                <small class="text-muted"><?php echo $this->lang->line('optional'); ?> - Account transactions will be handled when advance is used</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('description'); ?></label>
                                <textarea name="description" id="advance_description" class="form-control" rows="3" placeholder="<?php echo $this->lang->line('description'); ?>"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" class="btn btn-primary" id="advancePaymentSubmitBtn" data-action="collect">
                        <i class="fa fa-save"></i> <?php echo $this->lang->line('save'); ?>
                    </button>
                    <button type="button" class="btn btn-success" id="advancePaymentPrintBtn" data-action="print">
                        <i class="fa fa-print"></i> <?php echo $this->lang->line('save_print'); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Advance History Modal -->
<div class="modal fade" id="advanceHistoryModal" tabindex="-1" role="dialog" aria-labelledby="advanceHistoryModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="advanceHistoryModalLabel">
                    <i class="fa fa-history"></i> <?php echo $this->lang->line('advance_payment_history'); ?>
                </h4>
            </div>
            <div class="modal-body" id="advanceHistoryContent">
                <!-- Content will be loaded via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<!-- Revert Confirmation Modal -->
<div class="modal fade" id="revertConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="revertConfirmationModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="revertConfirmationModalLabel">
                    <i class="fa fa-exclamation-triangle text-warning"></i> <?php echo $this->lang->line('confirm_revert'); ?>
                </h4>
            </div>
            <form id="revertForm">
                <div class="modal-body">
                    <input type="hidden" id="revert_usage_id" name="usage_id">
                    <div class="alert alert-warning">
                        <i class="fa fa-warning"></i>
                        <strong><?php echo $this->lang->line('warning'); ?>:</strong>
                        <?php echo $this->lang->line('revert_advance_payment_warning'); ?>
                    </div>
                    <div class="form-group">
                        <label><?php echo $this->lang->line('reason_for_revert'); ?> <span class="req">*</span></label>
                        <textarea name="reason" id="revert_reason" class="form-control" rows="3" placeholder="<?php echo $this->lang->line('enter_reason_for_reverting'); ?>" required></textarea>
                    </div>
                    <div id="revert_details"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" class="btn btn-danger" id="confirmRevertBtn">
                        <i class="fa fa-undo"></i> <?php echo $this->lang->line('confirm_revert'); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Fee History Modal -->
<div class="modal fade" id="feeHistoryModal" tabindex="-1" role="dialog" aria-labelledby="feeHistoryModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="feeHistoryModalLabel">
                    <i class="fa fa-history"></i> <?php echo $this->lang->line('payment_history'); ?>
                </h4>
            </div>
            <div class="modal-body" id="feeHistoryContent">
                <!-- Content will be loaded via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<!-- Transport Fee History Modal -->
<div class="modal fade" id="transportFeeHistoryModal" tabindex="-1" role="dialog" aria-labelledby="transportFeeHistoryModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="transportFeeHistoryModalLabel">
                    <i class="fa fa-history"></i> <?php echo $this->lang->line('transport_fee_payment_history'); ?>
                </h4>
            </div>
            <div class="modal-body" id="transportFeeHistoryContent">
                <!-- Content will be loaded via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<!-- Hostel Fee History Modal -->
<div class="modal fade" id="hostelFeeHistoryModal" tabindex="-1" role="dialog" aria-labelledby="hostelFeeHistoryModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="hostelFeeHistoryModalLabel">
                    <i class="fa fa-history"></i> <?php echo $this->lang->line('hostel_fee_payment_history'); ?>
                </h4>
            </div>
            <div class="modal-body" id="hostelFeeHistoryContent">
                <!-- Content will be loaded via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<!-- Additional Fee History Modal -->
<div class="modal fade" id="additionalFeeHistoryModal" tabindex="-1" role="dialog" aria-labelledby="additionalFeeHistoryModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="additionalFeeHistoryModalLabel">
                    <i class="fa fa-history"></i> <?php echo $this->lang->line('additional_fee_payment_history'); ?>
                </h4>
            </div>
            <div class="modal-body" id="additionalFeeHistoryContent">
                <!-- Content will be loaded via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<!-- Advance Payment Transfers Modal -->
<div class="modal fade" id="advanceTransfersModal" tabindex="-1" role="dialog" aria-labelledby="advanceTransfersModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="advanceTransfersModalLabel">
                    <i class="fa fa-exchange"></i> Advance Payment Transfers History
                </h4>
            </div>
            <div class="modal-body" id="advanceTransfersContent">
                <div class="text-center">
                    <i class="fa fa-spinner fa-spin fa-2x"></i>
                    <p>Loading advance payment transfers...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
                <button type="button" class="btn btn-primary" onclick="refreshAdvanceTransfers()">
                    <i class="fa fa-refresh"></i> Refresh
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Advance Payment Management Functions
function openAdvancePaymentModal(studentSessionId, studentName, admissionNo, classSection, fatherName) {
    console.log('Opening advance payment modal for:', studentName);

    // Clear previous form data and errors
    $('#advancePaymentForm')[0].reset();
    $('[id^=error_]').html('');

    // Set student information
    $('#modal_student_session_id').val(studentSessionId);
    $('#modal_student_name').val(studentName);
    $('#modal_admission_no').val(admissionNo);
    $('#modal_class_section').val(classSection);
    $('#modal_father_name').val(fatherName);

    // Set default date
    $('#advance_date').val('<?php echo date($this->customlib->getSchoolDateFormat()); ?>');

    // Load account names for advance payment
    loadAdvanceAccountNames();

    // Show modal
    $('#advancePaymentModal').modal('show');
}

// Function to load account names for advance payment
function loadAdvanceAccountNames() {
    $.ajax({
        url: '<?php echo site_url("studentfee/getAccounts") ?>',
        type: 'POST',
        dataType: 'json',
        success: function(data) {
            var advance_account_dropdown = '<option value=""><?php echo $this->lang->line("select"); ?></option>';
            $.each(data, function(i, obj) {
                advance_account_dropdown += "<option value=" + obj.id + ">" + obj.account_name + "</option>";
            });
            $('#advance_accountname').html(advance_account_dropdown);
        },
        error: function() {
            console.log('Failed to load account names for advance payment');
        }
    });
}

// Function to load account names for regular fees
function loadAccountNames() {
    $.ajax({
        url: '<?php echo site_url("studentfee/getAccounts") ?>',
        type: 'POST',
        dataType: 'json',
        success: function(data) {
            var account_dropdown = '<option value=""><?php echo $this->lang->line("select"); ?></option>';
            $.each(data, function(i, obj) {
                account_dropdown += "<option value=" + obj.id + ">" + obj.account_name + "</option>";
            });
            $('#accountname').html(account_dropdown);
        },
        error: function() {
            console.log('Failed to load account names');
        }
    });
}

// Handle advance payment mode change for auto-account selection
$(document).on('change', '#advance_payment_mode', function() {
    var selectedMode = $(this).val();
    console.log('Advance payment mode changed to:', selectedMode);
    
    // Clear payment mode error
    $('#error_payment_mode').text('');
    
    // Auto-select corresponding account
    setTimeout(function() {
        $('#advance_accountname option').each(function() {
            var optionText = $(this).text().toLowerCase();
            if ((selectedMode === 'cash' && optionText.includes('cash')) ||
                (selectedMode === 'cheque' && optionText.includes('bank')) ||
                (selectedMode === 'dd' && optionText.includes('bank')) ||
                (selectedMode === 'bank_transfer' && optionText.includes('bank')) ||
                (selectedMode === 'upi' && optionText.includes('bank')) ||
                (selectedMode === 'card' && optionText.includes('bank'))) {
                $(this).prop('selected', true);
                $('#error_accountname').text(''); // Clear account error
                return false;
            }
        });
    }, 100);
});

// Print advance payment receipt function
function printAdvanceReceipt(advancePaymentId) {
    console.log('Printing advance receipt for ID:', advancePaymentId);

    $.ajax({
        url: '<?php echo site_url("studentfee/printAdvancePaymentMiniReceipt"); ?>',
        type: 'POST',
        data: {
            advance_id: advancePaymentId,
            '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
        },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                Popup(response.page, true);
            } else {
                showErrorMessage(response.error || 'Failed to generate receipt');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error printing advance receipt:', error);
            showErrorMessage('Network error occurred while generating receipt');
        }
    });
}

function viewAdvanceHistory(studentSessionId) {
    console.log('Viewing advance history for student session:', studentSessionId);

    $('#advanceHistoryContent').html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</div>');
    $('#advanceHistoryModal').data('student-session-id', studentSessionId).modal('show');

    $.ajax({
        url: '<?php echo site_url("studentfee/getAdvanceHistory"); ?>',
        type: 'POST',
        data: {
            student_session_id: studentSessionId,
            '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
        },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                let content = '<div class="table-responsive">';
                content += '<table class="table table-striped table-bordered">';
                content += '<thead><tr>';
                content += '<th><?php echo $this->lang->line("date"); ?></th>';
                content += '<th><?php echo $this->lang->line("amount"); ?></th>';
                content += '<th><?php echo $this->lang->line("balance"); ?></th>';
                content += '<th><?php echo $this->lang->line("payment_mode"); ?></th>';
                content += '<th><?php echo $this->lang->line("description"); ?></th>';
                content += '<th><?php echo $this->lang->line("action"); ?></th>';
                content += '</tr></thead><tbody>';

                if (response.advance_payments && response.advance_payments.length > 0) {
                    $.each(response.advance_payments, function(index, payment) {
                        content += '<tr>';
                        content += '<td>' + payment.payment_date + '</td>';
                        content += '<td><?php echo $currency_symbol; ?>' + parseFloat(payment.amount).toFixed(2) + '</td>';
                        content += '<td><?php echo $currency_symbol; ?>' + parseFloat(payment.balance).toFixed(2) + '</td>';
                        content += '<td>' + payment.payment_mode + '</td>';
                        content += '<td>' + (payment.description || '') + '</td>';
                        content += '<td class="text-center">';
                        content += '<button type="button" class="btn btn-info btn-xs" onclick="printAdvanceReceipt(' + payment.id + ')" title="<?php echo $this->lang->line("print_receipt"); ?>">';
                        content += '<i class="fa fa-print"></i> <?php echo $this->lang->line("print"); ?>';
                        content += '</button> ';
                        content += '<button type="button" class="btn btn-danger btn-xs" onclick="confirmRevertAdvancePayment(' + payment.id + ', \'' + payment.payment_date + '\', ' + parseFloat(payment.amount).toFixed(2) + ')" title="<?php echo $this->lang->line("revert"); ?>">';
                        content += '<i class="fa fa-undo"></i> <?php echo $this->lang->line("revert"); ?>';
                        content += '</button>';
                        content += '</td>';
                        content += '</tr>';
                    });
                } else {
                    content += '<tr><td colspan="6" class="text-center"><?php echo $this->lang->line("no_record_found"); ?></td></tr>';
                }

                content += '</tbody></table></div>';
                $('#advanceHistoryContent').html(content);
            } else {
                $('#advanceHistoryContent').html('<div class="alert alert-danger">' + (response.error || 'Error loading history') + '</div>');
            }
        },
        error: function() {
            $('#advanceHistoryContent').html('<div class="alert alert-danger">Error loading advance payment history</div>');
        }
    });
}

// Advance Payment Revert Functions
function confirmRevertAdvancePayment(advancePaymentId, paymentDate, amount) {
    console.log('Confirming revert for advance payment:', advancePaymentId);

    // Create confirmation dialog
    var confirmMessage = 'Are you sure you want to revert this advance payment?\n\n';
    confirmMessage += 'Date: ' + paymentDate + '\n';
    confirmMessage += 'Amount: <?php echo $currency_symbol; ?>' + amount + '\n\n';
    confirmMessage += 'This action will:\n';
    confirmMessage += '• Delete the advance payment if it is not assigned to any fees\n';
    confirmMessage += '• Show an error if the advance payment is currently assigned to fees\n\n';
    confirmMessage += 'This action cannot be undone. Do you want to proceed?';

    if (confirm(confirmMessage)) {
        revertAdvancePayment(advancePaymentId);
    }
}

function revertAdvancePayment(advancePaymentId) {
    console.log('Reverting advance payment:', advancePaymentId);

    // Show loading state
    $('button[onclick*="' + advancePaymentId + '"]').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');

    $.ajax({
        url: '<?php echo site_url("studentfee/deleteAdvancePayment"); ?>',
        type: 'POST',
        data: {
            advance_payment_id: advancePaymentId,
            '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
        },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                // Show success message
                showSuccessMessage(response.message || 'Advance payment reverted successfully');

                // Refresh all advance payment information
                refreshAllAdvancePaymentData();

            } else {
                // Show error message
                showErrorMessage(response.message || 'Failed to revert advance payment');

                // Reset button state
                $('button[onclick*="' + advancePaymentId + '"]').prop('disabled', false).html('<i class="fa fa-undo"></i> <?php echo $this->lang->line("revert"); ?>');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX error:', error);
            showErrorMessage('Network error occurred. Please try again.');

            // Reset button state
            $('button[onclick*="' + advancePaymentId + '"]').prop('disabled', false).html('<i class="fa fa-undo"></i> <?php echo $this->lang->line("revert"); ?>');
        }
    });
}

function refreshAdvanceBalance() {
    // Get current student session ID from the page
    var studentSessionId = '<?php echo isset($student_session_id) ? $student_session_id : ""; ?>';

    if (studentSessionId) {
        $.ajax({
            url: '<?php echo site_url("studentfee/getAdvancePaymentDetails"); ?>',
            type: 'POST',
            data: {
                student_session_id: studentSessionId,
                '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Update the balance display
                    $('#advance-balance-display').text(response.formatted_balance || '<?php echo $currency_symbol; ?>0.00');

                    // Update the payment count
                    var paymentCount = response.advance_payments ? response.advance_payments.length : 0;
                    $('.info-box-number').eq(1).text(paymentCount);
                }
            },
            error: function() {
                console.log('Failed to refresh advance balance');
            }
        });
    }
}

function confirmRevertAdvance(usageId, amount, date) {
    $('#revert_usage_id').val(usageId);
    $('#revert_reason').val('');
    $('#revert_details').html(
        '<div class="alert alert-info">' +
        '<strong>Usage Details:</strong><br>' +
        'Amount: <?php echo $currency_symbol; ?>' + parseFloat(amount).toFixed(2) + '<br>' +
        'Date: ' + date +
        '</div>'
    );
    $('#revertConfirmationModal').modal('show');
}

// Document Ready Functions
$(document).ready(function() {
    // Handle advance payment form submission
    $('#advancePaymentForm').on('submit', function(e) {
        e.preventDefault();

        var form = $(this);
        var submitBtn = $('#advancePaymentSubmitBtn');
        var printBtn = $('#advancePaymentPrintBtn');
        var formData = form.serialize();

        // Determine which button was clicked
        var action = form.find('input[name="action"]').val() || 'collect';

        // Clear previous errors
        $('[id^=error_]').html('');

        // Prevent double submission by disabling both buttons
        submitBtn.prop('disabled', true);
        printBtn.prop('disabled', true);

        // Show loading state on the appropriate button
        if (action === 'print') {
            printBtn.html('<i class="fa fa-spinner fa-spin"></i> <?php echo $this->lang->line("processing"); ?>');
        } else {
            submitBtn.html('<i class="fa fa-spinner fa-spin"></i> <?php echo $this->lang->line("processing"); ?>');
        }

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Show success message
                    showSuccessMessage(response.message || '<?php echo $this->lang->line("advance_payment_added_successfully"); ?>');

                    // Handle print action
                    if (action === 'print' && response.print) {
                        Popup(response.print, true);
                    }

                    // Close modal
                    $('#advancePaymentModal').modal('hide');

                    // Refresh all advance payment information
                    refreshAllAdvancePaymentData();

                    // Reset form
                    form[0].reset();

                } else if (response.status === 'fail') {
                    // Show validation errors
                    if (response.error) {
                        $.each(response.error, function(key, value) {
                            $('#error_' + key).html(value);
                        });
                    }
                } else {
                    showErrorMessage(response.message || '<?php echo $this->lang->line("something_went_wrong"); ?>');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                showErrorMessage('<?php echo $this->lang->line("network_error"); ?>');
            },
            complete: function() {
                // Reset button states
                submitBtn.prop('disabled', false).html('<i class="fa fa-save"></i> <?php echo $this->lang->line("save"); ?>');
                printBtn.prop('disabled', false).html('<i class="fa fa-print"></i> <?php echo $this->lang->line("save_print"); ?>');
                
                // Remove action input
                form.find('input[name="action"]').remove();
            }
        });
    });

    // Handle print button click
    $(document).on('click', '#advancePaymentPrintBtn', function(e) {
        e.preventDefault();
        
        // Add action input for print
        var form = $('#advancePaymentForm');
        form.find('input[name="action"]').remove();
        form.append('<input type="hidden" name="action" value="print">');
        
        // Submit form
        form.trigger('submit');
    });

    // Handle save button click  
    $(document).on('click', '#advancePaymentSubmitBtn', function(e) {
        e.preventDefault();
        
        // Add action input for collect
        var form = $('#advancePaymentForm');
        form.find('input[name="action"]').remove();
        form.append('<input type="hidden" name="action" value="collect">');
        
        // Submit form
        form.trigger('submit');
    });

    // Handle revert form submission
    $('#revertForm').on('submit', function(e) {
        e.preventDefault();

        var form = $(this);
        var submitBtn = $('#confirmRevertBtn');
        var usageId = $('#revert_usage_id').val();
        var reason = $('#revert_reason').val();

        // Validate required fields
        if (!usageId) {
            showErrorMessage('Usage ID is required');
            return;
        }

        if (!reason.trim()) {
            showErrorMessage('Reason for revert is required');
            return;
        }

        // Show loading state
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> <?php echo $this->lang->line("processing"); ?>');

        // Prepare data
        var postData = {
            usage_id: usageId,
            reason: reason,
            student_session_id: '<?php echo isset($student_session_id) ? $student_session_id : ""; ?>',
            '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
        };

        console.log('Sending revert request:', postData);

        $.ajax({
            url: '<?php echo site_url("studentfee/revertAdvancePayment"); ?>',
            type: 'POST',
            data: postData,
            dataType: 'json',
            success: function(response) {
                console.log('Revert response:', response);

                if (response.status === 'success') {
                    // Show success message
                    showSuccessMessage(response.message || '<?php echo $this->lang->line("advance_payment_reverted_successfully"); ?>');

                    // Close modal
                    $('#revertConfirmationModal').modal('hide');

                    // Refresh all advance payment information
                    refreshAllAdvancePaymentData();

                    // Reset form
                    form[0].reset();

                } else {
                    showErrorMessage(response.error || response.message || '<?php echo $this->lang->line("something_went_wrong"); ?>');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                console.error('Response Text:', xhr.responseText);

                try {
                    var errorResponse = JSON.parse(xhr.responseText);
                    showErrorMessage(errorResponse.error || errorResponse.message || '<?php echo $this->lang->line("network_error"); ?>');
                } catch(e) {
                    showErrorMessage('<?php echo $this->lang->line("network_error"); ?>: ' + error);
                }
            },
            complete: function() {
                // Reset button state
                submitBtn.prop('disabled', false).html('<i class="fa fa-undo"></i> <?php echo $this->lang->line("confirm_revert"); ?>');
            }
        });
    });
});

// Helper function to refresh advance payment information
function refreshAdvancePaymentInfo() {
    var studentSessionId = '<?php echo isset($student_session_id) ? $student_session_id : ""; ?>';

    if (!studentSessionId) return;

    $.ajax({
        url: '<?php echo site_url("studentfee/getAdvancePaymentDetails"); ?>',
        type: 'POST',
        data: {
            student_session_id: studentSessionId,
            '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
        },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                // Update balance display
                $('#advance-balance-display').html('<?php echo $currency_symbol; ?>' + response.formatted_balance);

                // Refresh advance history modal if it's open
                if ($('#advanceHistoryModal').hasClass('in') || $('#advanceHistoryModal').is(':visible')) {
                    var currentStudentSessionId = $('#advanceHistoryModal').data('student-session-id');
                    if (currentStudentSessionId && currentStudentSessionId === studentSessionId) {
                        viewAdvanceHistory(currentStudentSessionId);
                    }
                }

                // Optionally reload the page to refresh all fee information
                // location.reload();
            }
        },
        error: function() {
            console.error('Failed to refresh advance payment information');
        }
    });
}

// Comprehensive function to refresh all advance payment related data
function refreshAllAdvancePaymentData(studentSessionId) {
    if (!studentSessionId) {
        studentSessionId = '<?php echo isset($student_session_id) ? $student_session_id : ""; ?>';
    }

    if (!studentSessionId) return;

    // Refresh the main advance payment info
    refreshAdvancePaymentInfo();

    // If advance history modal is open, refresh it
    if ($('#advanceHistoryModal').hasClass('in') || $('#advanceHistoryModal').is(':visible')) {
        var modalStudentSessionId = $('#advanceHistoryModal').data('student-session-id');
        if (modalStudentSessionId && modalStudentSessionId === studentSessionId) {
            console.log('Refreshing advance history modal for student session:', modalStudentSessionId);
            viewAdvanceHistory(modalStudentSessionId);
        }
    }

    console.log('All advance payment data refreshed for student session:', studentSessionId);
}

// Helper functions for showing messages
function showSuccessMessage(message) {
    if (typeof successMsg === 'function') {
        successMsg(message);
    } else {
        alert(message);
    }
}

function showErrorMessage(message) {
    if (typeof errorMsg === 'function') {
        errorMsg(message);
    } else {
        alert(message);
    }
}

// Fee History Functions
$(document).on('click', '.viewFeeHistory', function() {
    var studentSessionId = $(this).data('student_session_id');
    var studentFeesMasterId = $(this).data('student_fees_master_id');
    var feeGroupsFeetypeId = $(this).data('fee_groups_feetype_id');
    var feeSessionGroupId = $(this).data('fee_session_group_id');
    var group = $(this).data('group');

    $('#feeHistoryModalLabel').html('<i class="fa fa-history"></i> Payment History - ' + group);
    $('#feeHistoryContent').html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</div>');
    $('#feeHistoryModal').modal('show');

    $.ajax({
        url: '<?php echo site_url("studentfee/getFeeHistory"); ?>',
        type: 'POST',
        data: {
            student_session_id: studentSessionId,
            student_fees_master_id: studentFeesMasterId,
            fee_groups_feetype_id: feeGroupsFeetypeId,
            fee_session_group_id: feeSessionGroupId,
            '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
        },
        success: function(response) {
            $('#feeHistoryContent').html(response);
        },
        error: function() {
            $('#feeHistoryContent').html('<div class="alert alert-danger">Error loading payment history</div>');
        }
    });
});

$(document).on('click', '.viewTransportFeeHistory', function() {
    var studentSessionId = $(this).data('student_session_id');
    var transFeeId = $(this).data('trans_fee_id');
    var group = $(this).data('group');
    var type = $(this).data('type');

    $('#transportFeeHistoryModalLabel').html('<i class="fa fa-history"></i> Payment History - ' + group + ' (' + type + ')');
    $('#transportFeeHistoryContent').html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</div>');
    $('#transportFeeHistoryModal').modal('show');

    $.ajax({
        url: '<?php echo site_url("studentfee/getTransportFeeHistory"); ?>',
        type: 'POST',
        data: {
            student_session_id: studentSessionId,
            trans_fee_id: transFeeId,
            '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
        },
        success: function(response) {
            $('#transportFeeHistoryContent').html(response);
        },
        error: function() {
            $('#transportFeeHistoryContent').html('<div class="alert alert-danger">Error loading transport fee payment history</div>');
        }
    });
});

$(document).on('click', '.viewHostelFeeHistory', function() {
    var studentSessionId = $(this).data('student_session_id');
    var transFeeId = $(this).data('trans_fee_id');
    var group = $(this).data('group');
    var type = $(this).data('type');

    $('#hostelFeeHistoryModalLabel').html('<i class="fa fa-history"></i> Payment History - ' + group + ' (' + type + ')');
    $('#hostelFeeHistoryContent').html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</div>');
    $('#hostelFeeHistoryModal').modal('show');

    $.ajax({
        url: '<?php echo site_url("studentfee/getHostelFeeHistory"); ?>',
        type: 'POST',
        data: {
            student_session_id: studentSessionId,
            trans_fee_id: transFeeId,
            '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
        },
        success: function(response) {
            $('#hostelFeeHistoryContent').html(response);
        },
        error: function() {
            $('#hostelFeeHistoryContent').html('<div class="alert alert-danger">Error loading hostel fee payment history</div>');
        }
    });
});

$(document).on('click', '.viewAdditionalFeeHistory', function() {
    var studentSessionId = $(this).data('student_session_id');
    var studentFeesMasterId = $(this).data('student_fees_master_id');
    var feeGroupsFeetypeId = $(this).data('fee_groups_feetype_id');
    var feeSessionGroupId = $(this).data('fee_session_group_id');
    var group = $(this).data('group');

    $('#additionalFeeHistoryModalLabel').html('<i class="fa fa-history"></i> Payment History - ' + group);
    $('#additionalFeeHistoryContent').html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</div>');
    $('#additionalFeeHistoryModal').modal('show');

    $.ajax({
        url: '<?php echo site_url("studentfee/getAdditionalFeeHistory"); ?>',
        type: 'POST',
        data: {
            student_session_id: studentSessionId,
            student_fees_master_id: studentFeesMasterId,
            fee_groups_feetype_id: feeGroupsFeetypeId,
            fee_session_group_id: feeSessionGroupId,
            '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
        },
        success: function(response) {
            $('#additionalFeeHistoryContent').html(response);
        },
        error: function() {
            $('#additionalFeeHistoryContent').html('<div class="alert alert-danger">Error loading additional fee payment history</div>');
        }
    });
});

// Fix for hostel fee collection
$(document).on('click', '.myCollectFeeBtn[data-fee-category="hostel"]', function(e) {
    var studentSessionId = $(this).data('student_session_id');
    var hostelFeeId = $(this).data('hostel-fee-id'); // Fixed: Use hyphenated version for data attributes
    var group = $(this).data('group');
    var type = $(this).data('type');
    
    console.log('Hostel fee button clicked:', {
        studentSessionId: studentSessionId,
        hostelFeeId: hostelFeeId,
        group: group,
        type: type
    });
    
    // Clear previous data and errors
    $('#myFeesModal').find('input[type="text"], input[type="number"], select, textarea').val('');
    $('#myFeesModal').find('.text-danger').text('');
    $("span[id$='_error']").html("");
    
    // Set current date
    $('#date').val('<?php echo date($this->customlib->getSchoolDateFormat()); ?>');
    
    // Set default payment mode to Cash and trigger change
    $('input[name="payment_mode_fee"][value="Cash"]').prop('checked', true).trigger('change');
    
    // Set modal data for hostel fees
    $('#myFeesModal').find('#std_id').val(studentSessionId);
    $('#myFeesModal').find('#fee_category').val('hostel');
    $('#myFeesModal').find('#hostel_fees_id').val(hostelFeeId);
    $('#myFeesModal').find('#transport_fees_id').val(0);
    $('#myFeesModal').find('#student_fees_master_id').val(0);
    $('#myFeesModal').find('#fee_groups_feetype_id').val(0);
    
    // Update modal title
    $('.fees_title').html("<b>" + group + ":</b> " + type);
    
    // Load hostel fee balance
    $.ajax({
        type: "post",
        url: '<?php echo site_url("studentfee/geBalanceFee") ?>',
        dataType: 'JSON',
        data: {
            'fee_groups_feetype_id': 0,
            'student_fees_master_id': 0,
            'student_session_id': studentSessionId,
            'fee_category': 'hostel',
            'trans_fee_id': hostelFeeId,
            'hostel_fee_id': hostelFeeId
        },
        beforeSend: function () {
            $('#discount_group').html('<option value=""><?php echo $this->lang->line('select'); ?></option>');
            $('#amount').val("");
            $('#amount_discount').val("0");
            $('#amount_fine').val("0");
        },
        success: function(data) {
            console.log('Hostel fee balance loaded:', data);
            $('#amount').val(data.balance);
            $('#amount_fine').val(data.remain_amount_fine || 0);
            $('#amount_discount').val(data.amount_discount || 0);
            
            // Load account names based on payment mode
            var selectedPaymentMode = $('input[name="payment_mode_fee"]:checked').val();
            fetchAccountTypes(selectedPaymentMode);
            
            // Load advance payment balance for the student
            loadAdvanceBalanceForModal(studentSessionId);
            
            // Auto-select Cash account if Cash payment mode is selected
            setTimeout(function() {
                if ($('input[name="payment_mode_fee"]:checked').val() === 'Cash') {
                    $('#accountname option').each(function() {
                        if ($(this).text().toLowerCase().includes('cash')) {
                            $(this).prop('selected', true);
                            return false;
                        }
                    });
                }
            }, 500);
        },
        error: function (xhr) {
            console.error('AJAX error loading hostel fee balance:', xhr);
            alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
        },
        complete: function () {
            // Show the modal after data is loaded
            $('#myFeesModal').modal('show');
        }
    });
});

// Add payment mode change handler for auto-selecting account
$(document).on('change', 'input[name="payment_mode_fee"]', function() {
    var selectedMode = $(this).val();
    console.log('Payment mode changed to:', selectedMode);
    
    // Clear payment mode error
    $('#payment_mode_error').text('');
    
    // Auto-select corresponding account
    setTimeout(function() {
        $('#accountname option').each(function() {
            var optionText = $(this).text().toLowerCase();
            if ((selectedMode === 'Cash' && optionText.includes('cash')) ||
                (selectedMode === 'Cheque' && optionText.includes('bank')) ||
                (selectedMode === 'DD' && optionText.includes('bank')) ||
                (selectedMode === 'bank_transfer' && optionText.includes('bank')) ||
                (selectedMode === 'upi' && optionText.includes('bank')) ||
                (selectedMode === 'card' && optionText.includes('bank'))) {
                $(this).prop('selected', true);
                $('#accountname_error').text(''); // Clear account error
                return false;
            }
        });
    }, 100);
});

// Fix form validation to not show payment mode error when mode is selected
$(document).on('click', '.save_button', function(e) {
    console.log('🟡 VALIDATION HANDLER - Running validation checks...');
    
    // Clear previous errors
    $("span[id$='_error']").html("");
    
    // Check if payment mode is selected
    var paymentMode = $('input[name="payment_mode_fee"]:checked').val();
    console.log('🟡 Payment mode:', paymentMode);
    if (!paymentMode) {
        console.log('❌ Payment mode validation failed');
        $('#payment_mode_error').text('Payment mode is required');
        e.preventDefault();
        return false;
    }
    
    // Check if account is selected
    var accountName = $('#accountname').val();
    console.log('🟡 Account name:', accountName);
    if (!accountName) {
        console.log('❌ Account name validation failed');
        $('#accountname_error').text('Account name is required');
        e.preventDefault();
        return false;
    }
    
    // Check if date is filled
    var date = $('#date').val();
    console.log('🟡 Date:', date);
    if (!date) {
        console.log('❌ Date validation failed');
        $('#date_error').text('Date is required');
        e.preventDefault();
        return false;
    }
    
    // Check if amount is filled and valid
    var amount = $('#amount').val();
    if (!amount || parseFloat(amount) <= 0) {
        $('#amount_error').text('Valid amount is required');
        e.preventDefault();
        return false;
    }
});

// Advance Payment Fee Collection Functions
// Function to load advance balance for the fee collection modal
function loadAdvanceBalanceForModal(studentSessionId) {
    console.log('Loading advance balance for modal:', studentSessionId);
    
    $.ajax({
        url: '<?php echo site_url("studentfee/getAdvancePaymentDetails"); ?>',
        type: 'POST',
        data: {
            student_session_id: studentSessionId,
            '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
        },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success' && parseFloat(response.balance) > 0) {
                // Show advance payment option if balance exists
                $('#advance_payment_option').show();
                $('#modal_advance_balance').text('<?php echo $currency_symbol; ?>' + response.formatted_balance);
            } else {
                // Hide advance payment option if no balance
                $('#advance_payment_option').hide();
                $('#collect_from_advance').prop('checked', false);
                $('#advance_payment_info').hide();
            }
        },
        error: function() {
            console.log('Failed to load advance balance for modal');
            $('#advance_payment_option').hide();
        }
    });
}

// Handle advance payment checkbox change
$(document).on('change', '#collect_from_advance', function() {
    var isChecked = $(this).is(':checked');
    var advanceBalanceText = $('#modal_advance_balance').text();
    var advanceBalance = parseFloat(advanceBalanceText.replace(/[^0-9.]/g, ''));
    var currentAmount = parseFloat($('#amount').val()) || 0;
    
    if (isChecked) {
        // Show advance balance info
        $('#advance_payment_info').show();
        
        // Validate amount against advance balance
        if (currentAmount > 0 && currentAmount > advanceBalance) {
            alert('Amount cannot exceed available advance balance of ' + advanceBalanceText);
            $(this).prop('checked', false);
            $('#advance_payment_info').hide();
            return;
        }
        
        // Set payment mode to Cash when using advance payment
        $('input[name="payment_mode_fee"][value="Cash"]').prop('checked', true);
        
        // Auto-select cash account
        setTimeout(function() {
            $('#accountname option').each(function() {
                if ($(this).text().toLowerCase().includes('cash')) {
                    $(this).prop('selected', true);
                    return false;
                }
            });
        }, 100);
        
    } else {
        // Hide advance balance info
        $('#advance_payment_info').hide();
    }
});

// Handle amount change to validate against advance balance when checkbox is checked
$(document).on('keyup change', '#amount', function() {
    var isAdvanceChecked = $('#collect_from_advance').is(':checked');
    
    if (isAdvanceChecked) {
        var advanceBalanceText = $('#modal_advance_balance').text();
        var advanceBalance = parseFloat(advanceBalanceText.replace(/[^0-9.]/g, ''));
        var currentAmount = parseFloat($(this).val()) || 0;
        
        // Clear previous errors
        $('#advance_payment_error').html('');
        
        if (currentAmount > advanceBalance) {
            $('#advance_payment_error').html('<div class="text-danger">Amount cannot exceed available advance balance of ' + advanceBalanceText + '</div>');
            $(this).addClass('error');
        } else {
            $(this).removeClass('error');
        }
    }
});

// Reset advance payment fields when modal is hidden
$('#myFeesModal').on('hidden.bs.modal', function() {
    $('#collect_from_advance').prop('checked', false);
    $('#advance_payment_info').hide();
    $('#advance_payment_option').hide();
    $('#advance_payment_error').html('');
});

// Handle Advance Transfers button click
$(document).on('click', '.viewAdvanceTransfers', function() {
    var studentSessionId = $(this).data('student-session-id');
    loadAdvanceTransfersHistory(studentSessionId);
    $('#advanceTransfersModal').modal('show');
});

// Function to load advance transfers history
function loadAdvanceTransfersHistory(studentSessionId) {
    $('#advanceTransfersContent').html(`
        <div class="text-center">
            <i class="fa fa-spinner fa-spin fa-2x"></i>
            <p>Loading advance payment transfers...</p>
        </div>
    `);
    
    $.ajax({
        url: '<?php echo site_url("studentfee/getAdvanceTransfersHistory"); ?>',
        type: 'POST',
        data: {
            student_session_id: studentSessionId
        },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                $('#advanceTransfersContent').html(response.html);
            } else {
                $('#advanceTransfersContent').html(`
                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle"></i> ${response.message || 'No transfer records found'}
                    </div>
                `);
            }
        },
        error: function(xhr, status, error) {
            $('#advanceTransfersContent').html(`
                <div class="alert alert-danger">
                    <i class="fa fa-error"></i> Error loading transfer history: ${error}
                </div>
            `);
        }
    });
}

// Function to refresh advance transfers
function refreshAdvanceTransfers() {
    var studentSessionId = $('.viewAdvanceTransfers').data('student-session-id');
    loadAdvanceTransfersHistory(studentSessionId);
}

// Debug: Test if JavaScript is working and buttons are clickable
$(document).ready(function() {
    console.log('Document ready - JavaScript is working');
    console.log('Number of .myCollectFeeBtn buttons found:', $('.myCollectFeeBtn').length);
    console.log('Number of .collectSelected buttons found:', $('.collectSelected').length);
    console.log('Number of .printSelected buttons found:', $('.printSelected').length);
    
    // Check if any buttons are hidden
    $('.myCollectFeeBtn').each(function(index) {
        var $btn = $(this);
        var isVisible = $btn.is(':visible');
        var hasHiddenClass = $btn.hasClass('ss-none');
        console.log('Button ' + index + ':', {
            visible: isVisible,
            hasHiddenClass: hasHiddenClass,
            classes: $btn.attr('class'),
            data: $btn.data()
        });
    });
    
    // Add click handler for all myCollectFeeBtn buttons
    $(document).on('click', '.myCollectFeeBtn', function(e) {
        console.log('myCollectFeeBtn clicked!', this);
        console.log('Button data:', $(this).data());
        // Don't prevent default as we want the modal to open
    });
    
    // Test basic button clicks
    $(document).on('click', '.collectSelected', function(e) {
        console.log('collectSelected clicked!');
    });
    
    $(document).on('click', '.printSelected', function(e) {
        console.log('printSelected clicked!');
    });
});

</script>