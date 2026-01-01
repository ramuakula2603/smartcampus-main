<?php $currency_symbol = $this->customlib->getSchoolCurrencyFormat(); ?>
<style type="text/css">
    .borderwhite {
        border-top-color: #fff !important;
    }

    .box-header>.box-tools {
        display: none;
    }

    .sidebar-collapse #barChart {
        height: 100% !important;
    }

    .sidebar-collapse #lineChart {
        height: 100% !important;
    }

    /*.fc-day-grid-container{overflow: visible !important;}*/
    .tooltip-inner {
        max-width: 135px;
    }

    /* Financial Summary Cards Styling */
    .hover-expand-effect {
        transition: all 0.3s ease;
        cursor: pointer;
        margin-bottom: 20px;
    }

    .hover-expand-effect:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .info-box {
        border-radius: 8px;
        overflow: hidden;
    }

    .info-box-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }

    .info-box-number {
        font-size: 22px;
        font-weight: bold;
        line-height: 1.2;
    }

    .info-box-text {
        font-size: 14px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .progress-description {
        font-size: 12px;
        opacity: 0.8;
        margin-top: 5px;
    }



    .filter-controls {
        display: flex;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
    }

    .filter-group {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .filter-group label {
        margin: 0;
        font-weight: normal;
        white-space: nowrap;
    }

    .filter-group select,
    .filter-group input {
        min-width: 120px;
    }

    .btn-apply-filter {
        background-color: #3c8dbc;
        color: white;
        border: none;
        padding: 6px 15px;
        border-radius: 3px;
        cursor: pointer;
    }

    .btn-apply-filter:hover {
        background-color: #2e6da4;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .info-box-number {
            font-size: 18px;
        }

        .info-box-text {
            font-size: 12px;
        }

        .filter-controls {
            flex-direction: column;
            align-items: stretch;
        }

        .filter-group {
            justify-content: space-between;
        }
    }

    .roles-carousel-box .roles-carousel-inner {
        position: relative;
        min-height: 40px;
    }

    .roles-carousel-box .role-item {
        display: none;
    }

    .roles-carousel-box .roles-carousel-controls {
        margin-top: 8px;
        text-align: right;
    }

    .roles-carousel-box .roles-carousel-controls .btn {
        padding: 2px 6px;
    }

    @media (max-width: 991px) {
        .monthly-widgets-row > .col-md-3 {
            flex: 0 0 50%;
            max-width: 50%;
        }
    }

    @media (max-width: 767px) {
        .monthly-widgets-row > .col-md-3 {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }
</style>

<div class="content-wrapper">
    <section class="content">
        <div class="">

            <?php if ($mysqlVersion && $sqlMode && strpos($sqlMode->mode, 'ONLY_FULL_GROUP_BY') !== false) { ?>
                <div class="alert alert-danger">
                    Smart School may not work properly because ONLY_FULL_GROUP_BY is enabled, consult with your hosting provider to disable ONLY_FULL_GROUP_BY in sql_mode configuration.
                </div>
            <?php } ?>

            <?php
            $show    = false;
            $role    = $this->customlib->getStaffRole();
            $role_id = json_decode($role)->id;
            foreach ($notifications as $notice_key => $notice_value) {

                if ($role_id == 7) {
                    $show = true;
                } elseif (date($this->customlib->getSchoolDateFormat()) >= date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($notice_value->publish_date))) {
                    $show = true;
                }
                if ($show) {
            ?>
                    <div class="dashalert alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="alertclose close close_notice" data-dismiss="alert" aria-label="Close" data-noticeid="<?php echo $notice_value->id; ?>"><span aria-hidden="true">&times;</span></button>
                        <a href="<?php echo site_url('admin/notification') ?>"><?php echo $notice_value->title; ?></a>
                    </div>
            <?php
                }
            }
            ?>
        </div>
        <!-- Top Summary Section Title -->
        <div class="row">
            <div class="col-md-12">
                <h3 class="dashboard-summary-title">Dashboard</h3>
            </div>
        </div>
        <div class="row">
            <?php
            if ($this->module_lib->hasActive('fees_collection')) {
                if ($this->rbac->hasPrivilege('fees_awaiting_payment_widegts', 'can_view')) {
            ?>
                    <div class="<?php echo $std_graphclass; ?>">
                        <div class="topprograssstart top-summary-card">
                            <p class="text-uppercase mt5 clearfix"><i class="fa fa-money ftlayer"></i><?php echo $this->lang->line('fees_awaiting_payment'); ?><span class="pull-right"><?php echo $total_paid; ?>/<?php echo $total_fees ?></span>
                            </p>
                            <div class="progress-group">
                                <div class="progress progress-minibar">
                                    <div class="progress-bar progress-bar-aqua" style="width: <?php echo $fessprogressbar; ?>%"></div>
                                </div>
                            </div>
                        </div><!--./topprograssstart-->
                    </div><!--./col-md-3-->
            <?php
                }
            }
            ?>

            <?php
            if ($this->module_lib->hasActive('front_office')) {
                if ($this->rbac->hasPrivilege('conveted_leads_widegts', 'can_view')) {
            ?>
                    <div class="<?php echo $std_graphclass; ?>">
                        <div class="topprograssstart top-summary-card">
                            <p class="text-uppercase mt5 clearfix"><i class="fa fa-ioxhost ftlayer"></i> <?php echo $this->lang->line('converted_leads'); ?><span class="pull-right"><?php echo $total_complete + 0; ?>/<?php echo $total_enquiry; ?></span>
                            </p>
                            <div class="progress-group">
                                <div class="progress progress-minibar">
                                    <div class="progress-bar progress-bar-red" style="width: <?php echo $fenquiryprogressbar; ?>%"></div>
                                </div>
                            </div>
                        </div><!--./topprograssstart-->
                    </div><!--./col-md-3-->
                <?php
                }
            }
            if ($this->rbac->hasPrivilege('staff_present_today_widegts', 'can_view')) {
                ?>
                <div class="<?php echo $std_graphclass; ?>">
                    <div class="topprograssstart top-summary-card">
                        <p class="text-uppercase mt5 clearfix"><i class="fa fa-calendar-check-o ftlayer"></i><?php echo $this->lang->line('staff_present_today'); ?><span class="pull-right"><?php echo $Staffattendence_data + 0; ?>/<?php echo $getTotalStaff_data; ?></span>
                        </p>
                        <div class="progress-group">
                            <div class="progress progress-minibar">
                                <div class="progress-bar progress-bar-green" style="width: <?php echo $percentTotalStaff_data; ?>%"></div>
                            </div>
                        </div>
                    </div><!--./topprograssstart-->
                </div><!--./col-md-3-->
                <?php
            }
            if ($this->module_lib->hasActive('student_attendance') && $sch_setting->attendence_type == 0) {
                if ($this->rbac->hasPrivilege('student_present_today_widegts', 'can_view')) {
                ?>
                    <div class="<?php echo $std_graphclass; ?>">
                        <div class="topprograssstart top-summary-card">
                            <p class="text-uppercase mt5 clearfix"><i class="fa fa-calendar-check-o ftlayer"></i><?php echo $this->lang->line('student_present_today'); ?><span class="pull-right"> <?php echo 0 + $attendence_data['total_half_day'] + $attendence_data['total_late'] + $attendence_data['total_present']; ?>/<?php echo $total_students; ?></span>
                            </p>
                            <div class="progress-group">
                                <div class="progress progress-minibar">
                                    <div class="progress-bar progress-bar-yellow" style="width: <?php if ($total_students > 0) {
                                                                                                    echo (0 + $attendence_data['total_half_day'] + $attendence_data['total_late'] + $attendence_data['total_present'] / $total_students * 100);
                                                                                                } ?>%"></div>
                                </div>
                            </div>
                        </div><!--./topprograssstart-->
                    </div><!--./col-md-3-->
            <?php }
            }
            ?>
        </div><!--./row-->

        <!-- Date Filter Section -->
        <?php if (($this->module_lib->hasActive('income')) || ($this->module_lib->hasActive('expense'))) { ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="date-filter-section">
                        <div class="filter-controls">
                            <div class="filter-group">
                                <label for="filter_type">Filter:</label>
                                <select id="filter_type" class="form-control">
                                    <option value="current">Current Month</option>
                                    <option value="today">Today</option>
                                    <option value="weekly">Weekly (Last 7 Days)</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="yearly">Yearly</option>
                                    <option value="custom">Custom Range</option>
                                </select>
                            </div>

                            <div class="filter-group" id="monthly_filter" style="display: none;">
                                <label for="month_select">Month:</label>
                                <select id="month_select" class="form-control">
                                    <option value="1">January</option>
                                    <option value="2">February</option>
                                    <option value="3">March</option>
                                    <option value="4">April</option>
                                    <option value="5">May</option>
                                    <option value="6">June</option>
                                    <option value="7">July</option>
                                    <option value="8">August</option>
                                    <option value="9">September</option>
                                    <option value="10">October</option>
                                    <option value="11">November</option>
                                    <option value="12">December</option>
                                </select>
                                <select id="year_select" class="form-control">
                                    <option value="2023">2023</option>
                                    <option value="2024">2024</option>
                                    <option value="2025" selected>2025</option>
                                    <option value="2026">2026</option>
                                </select>
                            </div>

                            <div class="filter-group" id="yearly_filter" style="display: none;">
                                <label for="year_only_select">Year:</label>
                                <select id="year_only_select" class="form-control">
                                    <option value="2023">2023</option>
                                    <option value="2024">2024</option>
                                    <option value="2025" selected>2025</option>
                                    <option value="2026">2026</option>
                                </select>
                            </div>

                            <div class="filter-group" id="custom_filter" style="display: none;">
                                <label for="start_date">From:</label>
                                <input type="date" id="start_date" class="form-control" value="<?php echo date('Y-m-01'); ?>">
                                <label for="end_date">To:</label>
                                <input type="date" id="end_date" class="form-control" value="<?php echo date('Y-m-t'); ?>">
                            </div>

                            <button type="button" id="apply_filter" class="btn-apply-filter">
                                <i class="fa fa-refresh"></i> Apply Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Summary Cards -->
            <div class="row" id="summary_cards">

                <div class="col-md-12">
                    <div class="row">

                        <!-- Total Income Card -->
                        <?php if ($can_view_income) { ?>
                            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                <div class="info-box bg-green hover-expand-effect">
                                    <span class="info-box-icon">
                                        <i class="fa fa-arrow-up"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Income</span>
                                        <span class="info-box-number" id="total_income_display">
                                            <?php echo $currency_symbol . number_format($total_income, 2); ?>
                                        </span>
                                        <span class="progress-description" id="income_period">
                                            <?php echo $current_month; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <!-- Total Expenses Card -->
                        <?php if ($can_view_expense) { ?>
                            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                <div class="info-box bg-red hover-expand-effect">
                                    <span class="info-box-icon">
                                        <i class="fa fa-arrow-down"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Expenses</span>
                                        <span class="info-box-number" id="total_expense_display">
                                            <?php echo $currency_symbol . number_format($total_expense, 2); ?>
                                        </span>
                                        <span class="progress-description" id="expense_period">
                                            <?php echo $current_month; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <!-- Fee Collection Card -->
                        <?php if ($can_view_fees) { ?>
                            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                <div class="info-box bg-blue hover-expand-effect">
                                    <span class="info-box-icon">
                                        <i class="fa fa-graduation-cap"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Fee Collection</span>
                                        <span class="info-box-number" id="total_fee_collection_display">
                                            <?php echo $currency_symbol . number_format($total_fee_collection, 2); ?>
                                        </span>
                                        <span class="progress-description" id="fee_period">
                                            <?php echo $current_month; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <!-- Net Profit/Loss Card -->
                        <?php if ($can_view_income && $can_view_expense) { ?>
                            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                <div class="info-box <?php echo ($net_profit >= 0) ? 'bg-green' : 'bg-red'; ?> hover-expand-effect" id="net_profit_card">
                                    <span class="info-box-icon">
                                        <i class="fa <?php echo ($net_profit >= 0) ? 'fa-line-chart' : 'fa-exclamation-triangle'; ?>"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text" id="net_profit_label">
                                            <?php echo ($net_profit >= 0) ? 'Net Profit' : 'Net Loss'; ?>
                                        </span>
                                        <span class="info-box-number" id="net_profit_display">
                                            <?php echo $currency_symbol . number_format(abs($net_profit), 2); ?>
                                        </span>
                                        <span class="progress-description" id="profit_period">
                                            <?php echo $current_month; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div><!--./row-->
        <?php } ?>

        <div class="row">
            <?php
            $bar_chart = true;

            if (($this->module_lib->hasActive('fees_collection')) || ($this->module_lib->hasActive('expense'))) {
                if ($this->rbac->hasPrivilege('fees_collection_and_expense_monthly_chart', 'can_view')) {

                    $div_rol  = 3;
                    $userdata = $this->customlib->getUserData();
            ?>
                    <div class="col-lg-7 col-md-7 col-sm-12 col60">
                        <div class="box box-primary borderwhite fees-chart-card">
                            <div class="box-header with-border">
                                <h3 class="box-title"><?php echo $this->lang->line('fees_collection_expenses_for'); ?> <?php echo $this->lang->line(strtolower(date('F'))) . " " . date('Y');

                                                                                                                        ?></h3>

                            </div>
                            <div class="box-body">
                                <div class="chart">
                                    <canvas id="barChart" height="95"></canvas>
                                </div>
                            </div>
                        </div>
                    </div><!--./col-lg-7-->
            <?php }
            }
            ?>
            <?php
            if ($this->module_lib->hasActive('income')) {
                if ($this->rbac->hasPrivilege('income_donut_graph', 'can_view')) {
            ?>
                    <div class="col-lg-5 col-md-5 col-sm-12 col40">
                        <div class="box box-primary borderwhite fees-chart-card">
                            <div class="box-header with-border">
                                <h3 class="box-title"><?php echo $this->lang->line('income') . " - " . $this->lang->line(strtolower(date('F'))) . " " . date('Y');  ?></h3>
                            </div>
                            <div class="box-body">
                                <div class="chart-responsive">
                                    <canvas id="doughnut-chart" class="" height="148"></canvas>
                                </div>
                            </div>
                        </div><!--./col-md-6-->
                    </div><!--./col-lg-5-->
            <?php
                }
            }
            ?>
        </div><!--./row-->
        <div class="row">
            <?php
            $line_chart = true;
            if (($this->module_lib->hasActive('fees_collection')) || ($this->module_lib->hasActive('expense'))) {
                if ($this->rbac->hasPrivilege('fees_collection_and_expense_yearly_chart', 'can_view')) {
                    $div_rol = 3;
            ?>
                    <div class="col-lg-7 col-md-7 col-sm-12 col60">
                        <div class="box box-info borderwhite fees-chart-card">
                            <div class="box-header with-border">
                                <h3 class="box-title"><?php echo $this->lang->line('fees_collection_expenses_for_session'); ?> <?php echo $this->setting_model->getCurrentSessionName(); ?></h3>
                                <div class="box-tools pull-right">
                                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                    <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="chart">
                                    <canvas id="lineChart" height="95"></canvas>
                                </div>
                            </div>
                        </div>
                    </div><!--./col-lg-7-->
                <?php
                }
            }
            if ($this->module_lib->hasActive('expense')) {
                ?>
                <?php if ($this->rbac->hasPrivilege('expense_donut_graph', 'can_view')) {
                ?>
                    <div class="col-lg-5 col-md-5 col-sm-12 col40">
                        <div class="box box-primary borderwhite fees-chart-card">
                            <div class="box-header with-border">
                                <h3 class="box-title"><?php echo $this->lang->line('expense') . " - " . $this->lang->line(strtolower(date('F'))) . " " . date('Y');  ?></h3>
                            </div><!--./info-box-->
                            <div class="box-body">
                                <div class="chart-responsive">
                                    <canvas id="doughnut-chart1" class="" height="148"></canvas>
                                </div>
                            </div>
                        </div>
                    </div><!--./col-lg-5-->
            <?php }
            }
            ?>
        </div><!--./row-->
        <div class="row">

            <?php
            if ($this->module_lib->hasActive('fees_collection')) {
                if ($this->rbac->hasPrivilege('fees_overview_widegts', 'can_view')) {
            ?>
                    <div class="col-md-3 col-sm-6">
                        <div class="topprograssstart overview-card overview-fees-card">
                            <h5 class="pro-border pb10"><?php echo $this->lang->line('fees_overview'); ?></h5>
                            <p class="text-uppercase mt10 clearfix"><?php echo $fees_overview['total_unpaid']; ?> <?php echo $this->lang->line('unpaid'); ?><span class="pull-right"><?php echo round($fees_overview['unpaid_progress'], 2); ?>%</span>
                            </p>
                            <div class="progress-group">
                                <div class="progress progress-minibar">
                                    <div class="progress-bar" style="width: <?php echo $fees_overview['unpaid_progress']; ?>%"></div>
                                </div>
                            </div>
                            <p class="text-uppercase mt10 clearfix"><?php echo $fees_overview['total_partial']; ?> <?php echo $this->lang->line('partial'); ?><span class="pull-right"><?php echo round($fees_overview['partial_progress'], 2); ?>%</span>
                            </p>
                            <div class="progress-group">
                                <div class="progress progress-minibar">
                                    <div class="progress-bar progress-bar-aqua" style="width: <?php echo $fees_overview['partial_progress']; ?>%"></div>
                                </div>
                            </div>
                            <p class="text-uppercase mt10 clearfix"><?php echo $fees_overview['total_paid']; ?> <?php echo $this->lang->line('paid'); ?><span class="pull-right"><?php echo round($fees_overview['paid_progress'], 2); ?>%</span>
                            </p>
                            <div class="progress-group">
                                <div class="progress progress-minibar">
                                    <div class="progress-bar progress-bar-aqua" style="width: <?php echo $fees_overview['paid_progress']; ?>%"></div>
                                </div>
                            </div>
                        </div><!--./topprograssstart-->
                    </div><!--./col-md-3-->
                <?php
                }
            }
            if ($this->module_lib->hasActive('front_office')) {
                if ($this->rbac->hasPrivilege('enquiry_overview_widegts', 'can_view')) {
                ?>
                    <div class="col-md-3 col-sm-6">
                        <div class="topprograssstart overview-card overview-enquiry-card">
                            <h5 class="pro-border pb10"> <?php echo $this->lang->line('enquiry_overview'); ?></h5>
                            <p class="text-uppercase mt10 clearfix"><?php echo $enquiry_overview['active']; ?> <?php echo $this->lang->line('active') ?><span class="pull-right"><?php echo round($enquiry_overview['active_progress'], 2); ?>%</span>
                            </p>
                            <div class="progress-group">
                                <div class="progress progress-minibar">
                                    <div class="progress-bar progress-bar-red" style="width: <?php echo $enquiry_overview['active_progress']; ?>%"></div>
                                </div>
                            </div>
                            <p class="text-uppercase mt10 clearfix"><?php echo $enquiry_overview['won']; ?> <?php echo $this->lang->line('won') ?><span class="pull-right"><?php echo round($enquiry_overview['won_progress'], 2); ?>%</span>
                            </p>
                            <div class="progress-group">
                                <div class="progress progress-minibar">
                                    <div class="progress-bar progress-bar-yellow" style="width: <?php echo $enquiry_overview['won_progress']; ?>%"></div>
                                </div>
                            </div>
                            <p class="text-uppercase mt10 clearfix"><?php echo $enquiry_overview['passive']; ?> <?php echo $this->lang->line('passive') ?><span class="pull-right"><?php echo round($enquiry_overview['passive_progress'], 2); ?>%</span>
                            </p>
                            <div class="progress-group">
                                <div class="progress progress-minibar">
                                    <div class="progress-bar progress-bar-yellow" style="width: <?php echo $enquiry_overview['passive_progress']; ?>%"></div>
                                </div>
                            </div>
                            <p class="text-uppercase mt10 clearfix"><?php echo $enquiry_overview['lost']; ?> <?php echo $this->lang->line('lost') ?><span class="pull-right"><?php echo round($enquiry_overview['lost_progress'], 2); ?>%</span>
                            </p>
                            <div class="progress-group">
                                <div class="progress progress-minibar">
                                    <div class="progress-bar progress-bar-yellow" style="width: <?php echo $enquiry_overview['lost_progress']; ?>%"></div>
                                </div>
                            </div>
                            <p class="text-uppercase mt10 clearfix"><?php echo $enquiry_overview['dead']; ?> <?php echo $this->lang->line('dead') ?><span class="pull-right"><?php echo round($enquiry_overview['dead_progress'], 2); ?>%</span>
                            </p>
                            <div class="progress-group">
                                <div class="progress progress-minibar">
                                    <div class="progress-bar progress-bar-yellow" style="width: <?php echo $enquiry_overview['dead_progress']; ?>%"></div>
                                </div>
                            </div>
                        </div><!--./topprograssstart-->
                    </div><!--./col-md-3-->
                <?php
                }
            }

            if ($this->module_lib->hasActive('library')) {
                if ($this->rbac->hasPrivilege('book_overview_widegts', 'can_view')) {
                ?>
                    <div class="col-md-3 col-sm-6">
                        <div class="topprograssstart overview-card overview-library-card">
                            <h5 class="pro-border pb10"> <?php echo $this->lang->line('library_overview'); ?></h5>
                            <p class="text-uppercase mt10 clearfix"><?php echo $book_overview['dueforreturn']; ?> <?php echo $this->lang->line('due_for_return'); ?><span class="pull-right"></span>
                            </p>
                            <div class="progress-group">
                                <div class="progress progress-minibar">
                                    <div class="progress-bar progress-bar-green" style="width: <?php echo $book_overview['dueforreturn']; ?>%"></div>
                                </div>
                            </div>
                            <p class="text-uppercase mt10 clearfix"><?php echo $book_overview['forreturn']; ?> <?php echo $this->lang->line('returned') ?><span class="pull-right"></span>
                            </p>
                            <div class="progress-group">
                                <div class="progress progress-minibar">
                                    <div class="progress-bar progress-bar-green" style="width: <?php echo $book_overview['forreturn']; ?>%"></div>
                                </div>
                            </div>
                            <p class="text-uppercase mt10 clearfix"><?php echo $book_overview['total_issued']; ?> <?php echo $this->lang->line('issued_out_of'); ?> <?php echo $book_overview['total'] ?><span class="pull-right"><?php echo $book_overview['issued_progress']; ?>%</span>
                            </p>
                            <div class="progress-group">
                                <div class="progress progress-minibar">
                                    <div class="progress-bar progress-bar-green" style="width: <?php echo $book_overview['issued_progress']; ?>%"></div>
                                </div>
                            </div>
                            <p class="text-uppercase mt10 clearfix"><?php echo $book_overview['availble']; ?> <?php echo $this->lang->line('available_out_of') ?> <?php echo $book_overview['total']; ?><span class="pull-right"><?php echo $book_overview['availble_progress']; ?>%</span>
                            </p>
                            <div class="progress-group">
                                <div class="progress progress-minibar">
                                    <div class="progress-bar progress-bar-green" style="width: <?php echo $book_overview['availble_progress']; ?>%"></div>
                                </div>
                            </div>
                        </div><!--./topprograssstart-->
                    </div><!--./col-md-3-->
                <?php
                }
            }
            if ($this->module_lib->hasActive('student_attendance')) {
                if ($this->rbac->hasPrivilege('today_attendance_widegts', 'can_view')) {
                ?>
                    <div class="col-md-3 col-sm-6">
                        <div class="topprograssstart overview-card overview-attendance-card">
                            <h5 class="pro-border pb10"> <?php echo $this->lang->line('student_today_attendance'); ?></h5>
                            <p class="text-uppercase mt10 clearfix"><?php echo $attendence_data['total_present']; ?> <?php echo $this->lang->line('present'); ?><span class="pull-right"><?php echo $attendence_data['present']; ?></span>
                            </p>
                            <div class="progress-group">
                                <div class="progress progress-minibar">
                                    <div class="progress-bar" style="width: <?php echo $attendence_data['present']; ?>"></div>
                                </div>
                            </div>
                            <p class="text-uppercase mt10 clearfix"><?php echo $attendence_data['total_late']; ?> <?php echo $this->lang->line('late') ?><span class="pull-right"><?php echo $attendence_data['late']; ?></span>
                            </p>
                            <div class="progress-group">
                                <div class="progress progress-minibar">
                                    <div class="progress-bar" style="width: <?php echo $attendence_data['late']; ?>"></div>
                                </div>
                            </div>
                            <p class="text-uppercase mt10 clearfix"><?php echo $attendence_data['total_absent']; ?> <?php echo $this->lang->line('absent'); ?><span class="pull-right"><?php echo $attendence_data['absent']; ?></span>
                            </p>
                            <div class="progress-group">
                                <div class="progress progress-minibar">
                                    <div class="progress-bar" style="width: <?php echo $attendence_data['absent']; ?>"></div>
                                </div>
                            </div>
                            <p class="text-uppercase mt10 clearfix"><?php echo $attendence_data['total_half_day']; ?> <?php echo $this->lang->line('half_day'); ?><span class="pull-right"><?php echo $attendence_data['half_day']; ?></span>
                            </p>
                            <div class="progress-group">
                                <div class="progress progress-minibar">
                                    <div class="progress-bar" style="width: <?php echo $attendence_data['half_day']; ?>"></div>
                                </div>
                            </div>
                        </div><!--./topprograssstart-->
                    </div><!--./col-md-3-->
            <?php
                }
            }

            $currency_symbol = $this->customlib->getSchoolCurrencyFormat();

            $div_col    = 12;
            $div_rol    = 12;
            $bar_chart  = true;
            $line_chart = true;
            if ($this->rbac->hasPrivilege('staff_role_count_widget', 'can_view')) {
                $div_col = 9;
                $div_rol = 12;
            }

            $widget_col = array();
            if ($this->rbac->hasPrivilege('Monthly fees_collection_widget', 'can_view')) {
                $widget_col[0] = 1;
                $div_rol       = 3;
            }

            if ($this->rbac->hasPrivilege('monthly_expense_widget', 'can_view')) {
                $widget_col[1] = 2;
                $div_rol       = 3;
            }

            if ($this->rbac->hasPrivilege('student_count_widget', 'can_view')) {
                $widget_col[2] = 3;
                $div_rol       = 3;
            }
            $div = sizeof($widget_col);
            if (!empty($widget_col)) {
                $widget = 3;
            } else {

                $widget = 12;
            }
            ?>
            <div class="row monthly-widgets-row">
                <?php
                if ($this->module_lib->hasActive('fees_collection')) {
                    if ($this->rbac->hasPrivilege('Monthly fees_collection_widget', 'can_view')) {
                ?>
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box monthly-fees-card">
                                <a href="<?php echo site_url('studentfee') ?>">
                                    <span class="info-box-icon bg-green"><i class="fa fa-money"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text"><?php echo $this->lang->line('monthly_fees_collection'); ?></span>
                                        <span class="info-box-number"><?php if ($month_collection) {
                                                                            echo $currency_symbol . amountFormat($month_collection);
                                                                        } ?></span>
                                    </div>
                                </a>
                            </div>
                        </div>
                <?php
                    }
                }
                ?>

                <?php
                if ($this->module_lib->hasActive('expense')) {
                    if ($this->rbac->hasPrivilege('monthly_expense_widget', 'can_view')) {
                ?>
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box monthly-expenses-card">
                                <a href="<?php echo site_url('admin/expense') ?>">
                                    <span class="info-box-icon bg-red"><i class="fa fa-credit-card"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text"><?php echo $this->lang->line('monthly_expenses'); ?></span>
                                        <span class="info-box-number"><?php if ($month_expense) {
                                                                            echo $currency_symbol . amountFormat($month_expense);
                                                                        } ?></span>
                                    </div>
                                </a>
                            </div>
                        </div>
                <?php
                    }
                }
                ?>

                <?php
                if ($this->rbac->hasPrivilege('student_count_widget', 'can_view')) {
                ?>
                    <div class="col-md-3 col-sm-6">
                        <div class="info-box monthly-student-card">
                            <a href="<?php echo site_url('student/search') ?>">
                                <span class="info-box-icon bg-aqua"><i class="fa fa-user"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text"><?php echo $this->lang->line('student'); ?></span>
                                    <span class="info-box-number"><?php echo $total_students; ?></span>
                                </div>
                            </a>
                        </div>
                    </div>
                <?php }
                ?>

                <?php if ($this->rbac->hasPrivilege('staff_role_count_widget', 'can_view')) { ?>
                    <div class="col-md-3 col-sm-6">
                        <div class="info-box roles-carousel-box monthly-roles-card">
                            <span class="info-box-icon bg-yellow"><i class="fa fa-user-secret"></i></span>
                            <div class="info-box-content">
                                <div class="roles-carousel-inner">
                                    <?php foreach ($roles as $key => $value) { ?>
                                        <div class="role-item">
                                            <span class="info-box-text"><?php echo $key; ?></span>
                                            <span class="info-box-number"><?php echo $value; ?></span>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="roles-carousel-controls">
                                    <button type="button" class="btn btn-xs btn-default roles-prev"><i class="fa fa-chevron-left"></i></button>
                                    <button type="button" class="btn btn-xs btn-default roles-next"><i class="fa fa-chevron-right"></i></button>
                                </div>
                                <div class="roles-dots"></div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <?php
            if ($this->module_lib->hasActive('calendar_to_do_list')) {
                if ($this->rbac->hasPrivilege('calendar_to_do_list', 'can_view')) {
                    $div_rol = 3;
            ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box box-primary borderwhite">
                                <div class="box-body">
                                    <!-- THE CALENDAR -->
                                    <div id="calendar"></div>
                                </div>
                                <!-- /.box-body -->
                            </div>
                            <!-- /. box -->
                        </div>
                    </div>
            <?php }
            } ?>
        </div><!--./row-->
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#viewEventModal,#newEventModal').modal({
            backdrop: 'static',
            keyboard: false,
            show: false
        });

        $('.roles-carousel-box').each(function() {
            var $carousel = $(this);
            var $inner = $carousel.find('.roles-carousel-inner');
            var $items = $inner.find('.role-item');
            var $controls = $carousel.find('.roles-carousel-controls');
            var $prev = $controls.find('.roles-prev');
            var $next = $controls.find('.roles-next');
            var $dotsContainer = $carousel.find('.roles-dots');
            var current = 0;

            // build dots based on items
            $dotsContainer.empty();
            $items.each(function() {
                $('<span class="role-dot"></span>').appendTo($dotsContainer);
            });
            var $dots = $dotsContainer.find('.role-dot');

            function showItem(index, animate) {
                $items.hide();
                if (animate) {
                    $items.eq(index).fadeIn(200);
                } else {
                    $items.eq(index).show();
                }
                $dots.removeClass('active');
                $dots.eq(index).addClass('active');
            }

            if ($items.length > 0) {
                showItem(current, false);
            }

            function goNext() {
                current = (current + 1) % $items.length;
                showItem(current, true);
            }

            function goPrev() {
                current = (current - 1 + $items.length) % $items.length;
                showItem(current, true);
            }

            var autoTimer = setInterval(goNext, 3000);

            $prev.on('click', function() {
                goPrev();
            });

            $next.on('click', function() {
                goNext();
            });

            $dots.on('click', function() {
                var index = $(this).index();
                if (index !== current) {
                    current = index;
                    showItem(current, true);
                }
            });

            $carousel.hover(
                function() {
                    clearInterval(autoTimer);
                },
                function() {
                    autoTimer = setInterval(goNext, 3000);
                }
            );
        });
    });
</script>

<div id="newEventModal" class="modal fade " role="dialog">
    <div class="modal-dialog modal-dialog2 modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line("add_new_event"); ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <form role="form" id="addevent_form" method="post" enctype="multipart/form-data" action="">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('event_title'); ?></label><small class="req"> *</small>
                                <input class="form-control" name="title" id="input-field">
                                <span class="text-danger"><?php echo form_error('title'); ?></span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('description'); ?></label>
                                <textarea name="description" class="form-control" id="desc-field"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-12 col-sm-12">
                            <div class="row">
                                <div class="col-md-6 col-lg-6 col-sm-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('event_from'); ?><small class="req"> *</small></label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                            <input type="text" autocomplete="off" name="event_from" class="form-control pull-right event_from">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6 col-sm-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('event_to'); ?><small class="req"> *</small></label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                            <input type="text" autocomplete="off" name="event_to" class="form-control pull-right event_to">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('event_color'); ?></label>
                                <input type="hidden" name="eventcolor" autocomplete="off" id="eventcolor" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <?php
                                $i      = 0;
                                $colors = '';
                                foreach ($event_colors as $color) {
                                    $color_selected_class = 'cpicker-small';
                                    if ($i == 0) {
                                        $color_selected_class = 'cpicker-big';
                                    }
                                    $colors .= "<div class='calendar-cpicker cpicker " . $color_selected_class . "' data-color='" . $color . "' style='background:" . $color . ";border:1px solid " . $color . "; border-radius:100px'></div>";
                                    $i++;
                                }
                                echo '<div class="cpicker-wrapper">';
                                echo $colors;
                                echo '</div>';
                                ?>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="pt15 displayblock overflow-hidden w-100"><?php echo $this->lang->line('event_type'); ?></label>
                                <label class="radio-inline w-xs-45">
                                    <input type="radio" name="event_type" value="public" id="public"><?php echo $this->lang->line('public'); ?>
                                </label>
                                <label class="radio-inline w-xs-45">
                                    <input type="radio" name="event_type" value="private" checked id="private"><?php echo $this->lang->line('private'); ?>
                                </label>
                                <label class="radio-inline w-xs-45 ml-xs-0">
                                    <input type="radio" name="event_type" value="sameforall" id="public"><?php echo $this->lang->line('all'); ?> <?php echo json_decode($role)->name; ?>
                                </label>
                                <label class="radio-inline w-xs-45">
                                    <input type="radio" name="event_type" value="protected" id="public"><?php echo $this->lang->line('protected'); ?>
                                </label>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <input type="submit" class="btn btn-primary submit_addevent pull-right" value="<?php echo $this->lang->line('save'); ?>">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="viewEventModal" class="modal fade " role="dialog">
    <div class="modal-dialog modal-dialog2 modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('edit_event'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <form role="form" method="post" id="updateevent_form" enctype="multipart/form-data" action="">
                        <div class="form-group col-md-12">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('event_title') ?></label>
                            <input class="form-control" name="title" placeholder="" id="event_title">
                        </div>
                        <div class="form-group col-md-12">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('description') ?></label>
                            <textarea name="description" class="form-control" placeholder="" id="event_desc"></textarea>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="exampleInputEmail1"><?php echo $this->lang->line('event_from'); ?></label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" autocomplete="off" name="event_from" class="form-control pull-right event_from">
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="exampleInputEmail1"><?php echo $this->lang->line('event_to'); ?></label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" autocomplete="off" name="event_to" class="form-control pull-right event_to">
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="eventid" id="eventid">
                        <div class="form-group col-md-12">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('event_color') ?></label>
                            <input type="hidden" name="eventcolor" autocomplete="off" placeholder="Event Color" id="event_color" class="form-control">
                        </div>
                        <div class="form-group col-md-12">
                            <?php
                            $i      = 0;
                            $colors = '';
                            foreach ($event_colors as $color) {
                                $colorid              = trim($color, "#");
                                $color_selected_class = 'cpicker-small';
                                if ($i == 0) {
                                    $color_selected_class = 'cpicker-big';
                                }
                                $colors .= "<div id=" . $colorid . " class='calendar-cpicker cpicker " . $color_selected_class . "' data-color='" . $color . "' style='background:" . $color . ";border:1px solid " . $color . "; border-radius:100px'></div>";
                                $i++;
                            }
                            echo '<div class="cpicker-wrapper selectevent">';
                            echo $colors;
                            echo '</div>';
                            ?>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('event_type') ?></label>
                            <label class="radio-inline">
                                <input type="radio" name="eventtype" value="public" id="public"><?php echo $this->lang->line('public') ?>
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="eventtype" value="private" id="private"><?php echo $this->lang->line('private') ?>
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="eventtype" value="sameforall" id="public"><?php echo $this->lang->line('all') ?> <?php echo json_decode($role)->name; ?>
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="eventtype" value="protected" id="public"><?php echo $this->lang->line('protected') ?>
                            </label>
                        </div>
                        <div class="col-xs-11 col-sm-11 col-md-11 col-lg-11">
                            <input type="submit" class="btn btn-primary submit_update pull-right" value="<?php echo $this->lang->line('save'); ?>">
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
                            <?php if ($this->rbac->hasPrivilege('calendar_to_do_list', 'can_delete')) { ?>
                                <input type="button" id="delete_event" class="btn btn-primary submit_delete pull-right" value="<?php echo $this->lang->line('delete'); ?>">
                            <?php } ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#viewEventModal,#newEventModal').modal({
            backdrop: 'static',
            keyboard: false,
            show: false
        });
    });
</script>

<style>
    canvas {
        -moz-user-select: none;
        -webkit-user-select: none;
        -ms-user-select: none;
    }
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
<script type="text/javascript">
    <?php if ($this->rbac->hasPrivilege('income_donut_graph', 'can_view') && ($this->module_lib->hasActive('income'))) {
    ?>
        new Chart(document.getElementById("doughnut-chart"), {
            type: 'doughnut',
            data: {
                labels: [<?php foreach ($incomegraph as $value) { ?> "<?php echo $value['income_category']; ?>", <?php } ?>],
                datasets: [{
                    label: "Income",
                    backgroundColor: [<?php $s = 1;
                                        foreach ($incomegraph as $value) {
                                        ?> "<?php echo incomegraphColors($s++); ?>", <?php
                                                        if ($s == 8) {
                                                            $s = 1;
                                                        }
                                                    }
                                                        ?>],
                    data: [<?php $s = 1;
                            foreach ($incomegraph as $value) {
                            ?><?php echo $value['total']; ?>, <?php } ?>]
                }]
            },
            options: {
                responsive: true,
                circumference: Math.PI,
                rotation: -Math.PI,
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                },
                animation: {
                    animateScale: true,
                    animateRotate: true
                }
            }
        });
    <?php
    }
    if (($this->rbac->hasPrivilege('expense_donut_graph', 'can_view')) && ($this->module_lib->hasActive('expense'))) {
    ?>
        new Chart(document.getElementById("doughnut-chart1"), {
            type: 'doughnut',
            data: {
                labels: [<?php foreach ($expensegraph as $value) { ?> "<?php echo $value['exp_category']; ?>", <?php } ?>],
                datasets: [{
                    label: "Population (millions)",
                    backgroundColor: [<?php $ss = 1;
                                        foreach ($expensegraph as $value) {
                                        ?> "<?php echo expensegraphColors($ss++); ?>", <?php
                                                        if ($ss == 8) {
                                                            $ss = 1;
                                                        }
                                                    }
                                                        ?>],
                    data: [<?php foreach ($expensegraph as $value) { ?><?php echo $value['total']; ?>, <?php } ?>]
                }]
            },
            options: {
                responsive: true,
                circumference: Math.PI,
                rotation: -Math.PI,
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                },
                animation: {
                    animateScale: true,
                    animateRotate: true
                }
            }
        });
    <?php
    }
    if (($this->module_lib->hasActive('fees_collection')) || ($this->module_lib->hasActive('expense')) || ($this->module_lib->hasActive('income'))) {
    ?>
        $(function() {
            var areaChartOptions = {
                showScale: true,
                scaleShowGridLines: true,
                scaleGridLineColor: "rgba(148, 163, 184, 0.18)",
                scaleGridLineWidth: 1,
                scaleShowHorizontalLines: true,
                scaleShowVerticalLines: false,
                bezierCurve: true,
                bezierCurveTension: 0.3,
                pointDot: true,
                pointDotRadius: 3,
                pointDotStrokeWidth: 1,
                pointHitDetectionRadius: 10,
                datasetStroke: true,
                datasetStrokeWidth: 2,
                datasetFill: false,
                maintainAspectRatio: true,
                responsive: true
            };

            var bar_chart = "<?php echo $bar_chart ?>";
            var line_chart = "<?php echo $line_chart ?>";
            <?php
            if ($this->rbac->hasPrivilege('fees_collection_and_expense_yearly_chart', 'can_view')) {
            ?>
                if (line_chart) {

                    var lineChartCanvas = $("#lineChart").get(0).getContext("2d");
                    var lineChart = new Chart(lineChartCanvas);
                    var lineChartOptions = areaChartOptions;
                    lineChartOptions.datasetFill = false;

                    var yearly_collection_array = <?php echo json_encode($yearly_collection) ?>;
                    var yearly_expense_array = <?php echo json_encode($yearly_expense) ?>;
                    var total_month = <?php echo json_encode($total_month) ?>;
                    // convert full month names to short labels like Jan, Feb
                    var monthShortMap = {
                        'january': 'Jan',
                        'february': 'Feb',
                        'march': 'Mar',
                        'april': 'Apr',
                        'may': 'May',
                        'june': 'Jun',
                        'july': 'Jul',
                        'august': 'Aug',
                        'september': 'Sep',
                        'october': 'Oct',
                        'november': 'Nov',
                        'december': 'Dec'
                    };
                    var total_month_short = [];
                    for (var i = 0; i < total_month.length; i++) {
                        var key = ('' + total_month[i]).toLowerCase();
                        total_month_short.push(monthShortMap[key] || total_month[i]);
                    }
                    var areaChartData_expense_Income = {
                        labels: total_month_short,

                        datasets: [
                            <?php if (($this->module_lib->hasActive('expense'))) { ?> {
                                    label: "Expenses",
                                    fillColor: "rgba(249, 115, 22, 0.1)",
                                    strokeColor: "rgba(249, 115, 22, 1)",
                                    pointColor: "rgba(249, 115, 22, 1)",
                                    pointStrokeColor: "#ffffff",
                                    pointHighlightFill: "#ffffff",
                                    pointHighlightStroke: "rgba(249, 115, 22, 1)",
                                    data: yearly_expense_array
                                },

                            <?php } ?>
                            <?php if (($this->module_lib->hasActive('income'))) { ?> {
                                    label: "Fees Collection",
                                    fillColor: "rgba(45, 212, 191, 0.15)",
                                    strokeColor: "rgba(5, 150, 105, 1)",
                                    pointColor: "rgba(5, 150, 105, 1)",
                                    pointStrokeColor: "#ffffff",
                                    pointHighlightFill: "#ffffff",
                                    pointHighlightStroke: "rgba(5, 150, 105, 1)",
                                    data: yearly_collection_array
                                }

                            <?php } ?>
                        ]
                    };
                    lineChart.Line(areaChartData_expense_Income, lineChartOptions);
                }

                var current_month_days = <?php echo json_encode($current_month_days) ?>;
                var days_collection = <?php echo json_encode($days_collection) ?>;
                var days_expense = <?php echo json_encode($days_expense) ?>;
                var areaChartData_classAttendence = {
                    labels: current_month_days,
                    datasets: [
                        <?php if (($this->module_lib->hasActive('income'))) { ?> {
                                label: "Fees Collection",
                                fillColor: "rgba(45, 212, 191, 0.15)",
                                strokeColor: "rgba(5, 150, 105, 1)",
                                pointColor: "rgba(5, 150, 105, 1)",
                                pointStrokeColor: "#ffffff",
                                pointHighlightFill: "#ffffff",
                                pointHighlightStroke: "rgba(5, 150, 105, 1)",
                                data: days_collection
                            },

                        <?php }
                        if (($this->module_lib->hasActive('expense'))) { ?> {
                                label: "Expenses",
                                fillColor: "rgba(249, 115, 22, 0.1)",
                                strokeColor: "rgba(249, 115, 22, 1)",
                                pointColor: "rgba(249, 115, 22, 1)",
                                pointStrokeColor: "#ffffff",
                                pointHighlightFill: "#ffffff",
                                pointHighlightStroke: "rgba(249, 115, 22, 1)",
                                data: days_expense
                            }

                        <?php } ?>
                    ]
                };


            <?php }
            if ($this->rbac->hasPrivilege('fees_collection_and_expense_monthly_chart', 'can_view')) { ?>
                if (bar_chart) {
                    var current_month_days = <?php echo json_encode($current_month_days) ?>;
                    var days_collection = <?php echo json_encode($days_collection) ?>;
                    var days_expense = <?php echo json_encode($days_expense) ?>;

                    var areaChartData_classAttendence = {
                        labels: current_month_days,
                        datasets: [
                            <?php if (($this->module_lib->hasActive('income'))) { ?> {
                                    label: "Fees Collection",
                                    fillColor: "rgba(45, 212, 191, 0.15)",
                                    strokeColor: "rgba(5, 150, 105, 1)",
                                    pointColor: "rgba(5, 150, 105, 1)",
                                    pointStrokeColor: "#ffffff",
                                    pointHighlightFill: "#ffffff",
                                    pointHighlightStroke: "rgba(5, 150, 105, 1)",
                                    data: days_collection
                                },

                            <?php } ?>
                            <?php if (($this->module_lib->hasActive('expense'))) { ?> {
                                    label: "Expenses",
                                    fillColor: "rgba(249, 115, 22, 0.1)",
                                    strokeColor: "rgba(249, 115, 22, 1)",
                                    pointColor: "rgba(249, 115, 22, 1)",
                                    pointStrokeColor: "#ffffff",
                                    pointHighlightFill: "#ffffff",
                                    pointHighlightStroke: "rgba(249, 115, 22, 1)",
                                    data: days_expense
                                }

                            <?php } ?>
                        ]
                    };

                    var barChartCanvas = $("#barChart").get(0).getContext("2d");
                    var barChart = new Chart(barChartCanvas);
                    var barChartData = areaChartData_classAttendence;
                    // barChartData.datasets[1].fillColor = "rgba(233, 30, 99, 0.9)";
                    // barChartData.datasets[1].strokeColor = "rgba(233, 30, 99, 0.9)";
                    var barChartOptions = {
                        scaleBeginAtZero: true,
                        scaleShowGridLines: true,
                        scaleGridLineColor: "rgba(0,0,0,.05)",
                        scaleGridLineWidth: 1,
                        scaleShowHorizontalLines: false,
                        scaleShowVerticalLines: false,
                        barShowStroke: true,
                        barStrokeWidth: 2,
                        barValueSpacing: 5,
                        barDatasetSpacing: 1,
                        responsive: true,
                        maintainAspectRatio: true
                    };
                    barChartOptions.datasetFill = false;
                    barChart.Bar(barChartData, barChartOptions);
                }
            <?php } ?>
        });
    <?php
    }
    ?>

    $(document).ready(function() {
        // Date filter functionality
        $('#filter_type').change(function() {
            var filterType = $(this).val();

            // Hide all filter groups
            $('#monthly_filter, #yearly_filter, #custom_filter').hide();

            // Show relevant filter group
            if (filterType === 'monthly') {
                $('#monthly_filter').show();
            } else if (filterType === 'yearly') {
                $('#yearly_filter').show();
            } else if (filterType === 'custom') {
                $('#custom_filter').show();
            }
        });

        // Apply filter button click
        $('#apply_filter').click(function() {
            var filterType = $('#filter_type').val();
            var data = {
                filter_type: filterType
            };

            // Add specific filter data based on type
            if (filterType === 'monthly') {
                data.month = $('#month_select').val();
                data.year = $('#year_select').val();
            } else if (filterType === 'yearly') {
                data.year = $('#year_only_select').val();
            } else if (filterType === 'custom') {
                data.start_date = $('#start_date').val();
                data.end_date = $('#end_date').val();
            }

            // Show loading state
            $(this).html('<i class="fa fa-spinner fa-spin"></i> Loading...');
            $(this).prop('disabled', true);

            // Make AJAX request
            $.ajax({
                type: 'POST',
                url: base_url + 'admin/admin/getDashboardSummary',
                data: data,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        updateSummaryCards(response.data);
                    } else {
                        alert('Error loading data. Please try again.');
                    }
                },
                error: function() {
                    alert('Error loading data. Please try again.');
                },
                complete: function() {
                    // Reset button state
                    $('#apply_filter').html('<i class="fa fa-refresh"></i> Apply Filter');
                    $('#apply_filter').prop('disabled', false);
                }
            });
        });

        function updateSummaryCards(data) {
            var currencySymbol = '<?php echo $currency_symbol; ?>';

            console.log('=== UPDATE SUMMARY CARDS DEBUG ===');
            console.log('Received data:', data);
            console.log('Permissions:', data.permissions);

            // Update income card only if user has permission
            if (data.permissions && data.permissions.can_view_income) {
                $('#total_income_display').text(currencySymbol + numberFormat(data.total_income, 2));
                $('#income_period').text(data.period_display);
            }

            // Update expense card only if user has permission
            if (data.permissions && data.permissions.can_view_expense) {
                $('#total_expense_display').text(currencySymbol + numberFormat(data.total_expense, 2));
                $('#expense_period').text(data.period_display);
            }

            // Update fee collection card only if user has permission
            if (data.permissions && data.permissions.can_view_fees) {
                var feeCollectionFormatted = currencySymbol + numberFormat(data.total_fee_collection, 2);
                console.log('Formatted fee collection:', feeCollectionFormatted);
                $('#total_fee_collection_display').text(feeCollectionFormatted);
                $('#fee_period').text(data.period_display);
                console.log('Fee collection card updated');
            }

            // Update net profit/loss card only if user has permission for both income and expense
            if (data.permissions && data.permissions.can_view_profit) {
                var netProfit = data.net_profit;
                var isProfit = netProfit >= 0;

                $('#net_profit_display').text(currencySymbol + numberFormat(Math.abs(netProfit), 2));
                $('#net_profit_label').text(isProfit ? 'Net Profit' : 'Net Loss');
                $('#profit_period').text(data.period_display);

                // Update card color
                var cardElement = $('#net_profit_card');
                cardElement.removeClass('bg-green bg-red');
                cardElement.addClass(isProfit ? 'bg-green' : 'bg-red');

                // Update icon
                var iconElement = cardElement.find('.info-box-icon i');
                iconElement.removeClass('fa-line-chart fa-exclamation-triangle');
                iconElement.addClass(isProfit ? 'fa-line-chart' : 'fa-exclamation-triangle');
            }
        }

        function numberFormat(number, decimals) {
            return parseFloat(number).toLocaleString('en-US', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals
            });
        }

        $(document).on('click', '.close_notice', function() {
            var data = $(this).data();
            $.ajax({
                type: "POST",
                url: base_url + "admin/notification/read",
                data: {
                    'notice': data.noticeid
                },
                dataType: "json",
                success: function(data) {
                    if (data.status == "fail") {

                        errorMsg(data.msg);
                    } else {
                        successMsg(data.msg);
                    }

                }
            });
        });
    });
</script>