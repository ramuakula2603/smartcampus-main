<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>

<style>
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

/* Select all/clear all button styling */
.SumoSelect .select-all {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    padding: 8px 12px;
    font-weight: 600;
    color: #495057;
    cursor: pointer;
    display: block !important;
}

.SumoSelect .select-all:hover {
    background-color: #e9ecef;
}

/* Ensure Select All option is visible */
.SumoSelect .optWrapper .options li.opt {
    display: list-item !important;
    padding: 6px 12px;
    cursor: pointer;
}

.SumoSelect .optWrapper .options li.opt:hover {
    background-color: #f5f5f5;
}

/* Select All specific styling */
.SumoSelect .optWrapper .options li.opt.select-all {
    background-color: #e3f2fd;
    border-bottom: 1px solid #bbdefb;
    font-weight: 600;
    color: #1976d2;
}

.SumoSelect .optWrapper .options li.opt.select-all:hover {
    background-color: #bbdefb;
}

/* Loading state for dropdowns */
.SumoSelect.loading > .CaptionCont {
    opacity: 0.6;
    pointer-events: none;
}

.SumoSelect.loading > .CaptionCont:after {
    content: "";
    position: absolute;
    right: 10px;
    top: 50%;
    margin-top: -8px;
    width: 16px;
    height: 16px;
    border: 2px solid #ccc;
    border-top-color: #337ab7;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Error message styling */
.text-danger {
    font-size: 12px;
    margin-top: 5px;
    display: block;
}

/* Form alignment improvements */
.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
}

/* Alert message styling */
.alert {
    margin-bottom: 20px;
    border-radius: 4px;
}

.alert-success {
    background-color: #dff0d8;
    border-color: #d6e9c6;
    color: #3c763d;
}

.alert-danger {
    background-color: #f2dede;
    border-color: #ebccd1;
    color: #a94442;
}

.alert .fa {
    margin-right: 8px;
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
                    <form role="form" action="<?php echo site_url('financereports/total_fee_collection_report') ?>" method="post" class="">
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
                                    <label><?php echo $this->lang->line('collect_by'); ?></label>
                                    <select id="collect_by" name="collect_by[]" class="form-control multiselect-dropdown" multiple>
                                        <?php
                                            foreach ($collect_by as $key => $value) {
                                                ?>
                                            <option value="<?php echo $key ?>" <?php
                                                if ((isset($received_by)) && ($received_by == $key)) {
                                                        echo "selected";
                                                    }
                                                    ?> ><?php echo $value ?></option>
                                                <?php }?>
                                    </select>
                                    <span class="text-danger" id="error_collect_by"></span>
                                </div>
                            </div>
                            <div id='date_result'>
                            </div>
                            <div class="col-sm-2 col-lg-2 col-md-2">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('group_by'); ?></label>
                                    <select class="form-control" name="group" >
                                        <?php foreach ($group_by as $key => $value) {
    ?>
                                            <option value="<?php echo $key ?>" <?php
if ((isset($group_byid)) && ($group_byid == $key)) {
        echo "selected";
    }
    ?> ><?php echo $value ?></option>
                                                <?php }?>
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
                            <h3 class="box-title titlefix"><i class="fa fa-money"></i> <?php ?> <?php echo $this->lang->line('total_fee_collection_report'); ?> (<?php echo $this->lang->line('combined'); ?>)</h3>
                        </div>

                        <div class="box-body table-responsive" id="transfee">
                        <div id="printhead"><center><b><h4><?php echo $this->lang->line('total_fee_collection_report') . " (" . $this->lang->line('combined') . ")<br>";
    $this->customlib->get_postmessage();
    ?></h4></b></center></div>
                            <div class="download_label"><?php echo $this->lang->line('total_fee_collection_report') . " (" . $this->lang->line('combined') . ")<br>";
    $this->customlib->get_postmessage();
    ?></div>



                            <a class="btn btn-default btn-xs pull-right" id="print" onclick="printDiv()" ><i class="fa fa-print"></i></a>
                            <a class="btn btn-default btn-xs pull-right" id="btnExport" onclick="exportToExcel();"> <i class="fa fa-file-excel-o"></i> </a>

                            <table class="table table-striped table-bordered table-hover " id="headerTable">
                                <thead class="header">
                                    <tr>
                                        <th><?php echo $this->lang->line('payment_id'); ?></th>
                                        <th><?php echo $this->lang->line('date'); ?></th>
                                        <th><?php echo $this->lang->line('admission_no'); ?></th>
                                        <th><?php echo $this->lang->line('name'); ?></th>
                                        <th><?php echo $this->lang->line('class'); ?></th>
                                        <th><?php echo $this->lang->line('fee_type'); ?></th>
                                        <th><?php echo $this->lang->line('collect_by'); ?></th>
                                        <th><?php echo $this->lang->line('mode'); ?></th>
                                        <th style="mso-number-format:'\@'" class="text text-right"><?php echo $this->lang->line('paid'); ?> <span><?php echo "(" . $currency_symbol . ")"; ?></span></th>
                                        <th><?php echo $this->lang->line('note'); ?></th>
                                        <th style="mso-number-format:'\@'" class="text text-right"><?php echo $this->lang->line('discount'); ?> <span><?php echo "(" . $currency_symbol . ")"; ?></span></th>
                                        <th style="mso-number-format:'\@'" class="text text-right"><?php echo $this->lang->line('fine'); ?> <span><?php echo "(" . $currency_symbol . ")"; ?></span></th>
                                        <th style="mso-number-format:'\@'" class="text text-right"><?php echo $this->lang->line('total'); ?> <span><?php echo "(" . $currency_symbol . ")"; ?></span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                   <?php

    $count            = 1;
    $grdamountLabel   = array();
    $grddiscountLabel = array();
    $grdfineLabel     = array();
    $grdTotalLabel    = array();
//print_r($results);die;
    foreach ($results as $key => $value) {
        $payment_id    = array();
        $date          = array();
        $student_name  = array();
        $student_class = array();
        $fees_type     = array();
        $pay_mode      = array();
        $collection_by = array();
        $amountLabel   = array();
        $discountLabel = array();
        $fineLabel     = array();
        $TotalLabel    = array();
        $admission_no  = array();
        foreach ($value as $collect) {
            // $payment_id[]   = $collect['id'] . "/" . $collect['inv_no'];
            // $date[]         = date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($collect['date']));
            // $student_name[] = $this->customlib->getFullName($collect['firstname'], $collect['middlename'], $collect['lastname'], $sch_setting->middlename, $sch_setting->lastname);

            // $admission_no[] = $collect['admission_no'];

            // $student_class[] = $collect['class'] . " (" . $collect['section'] . ")";

            // if ( $collect['is_system']) {
                // $fees_type[]     = $this->lang->line($collect['type']);
            // } else {
                // $fees_type[]     =$collect['type'];
            // }

            $pay_mode[]      = $collect['payment_mode'];
            // if (is_array($collect['received_byname'])) {
                // $collection_by[] = $collect['received_byname']['name'] . " (" . $collect['received_byname']['employee_id'] . ")";
            // }

            $amountLabel[]   = number_format($collect['amount'], 2, '.', '');
            $discountLabel[] = number_format($collect['amount_discount'], 2, '.', '');
            $fineLabel[]     = number_format($collect['amount_fine'], 2, '.', '');
            $t               = $collect['amount'] + $collect['amount_fine'];
            $TotalLabel[]    = number_format($t, 2, '.', '');
        }
        ?>

                <?php
                 foreach ($value as $collect) { ?>
                        <tr>
                            <td><?php echo $collect['id'] . "/" . $collect['inv_no']; ?></td>
                            <td><?php echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($collect['date'])); ?></td>
                            <td><?php echo $collect['admission_no']; ?></td>
                            <td><?php echo $this->customlib->getFullName($collect['firstname'], $collect['middlename'], $collect['lastname'], $sch_setting->middlename, $sch_setting->lastname); ?></td>
                            <td><?php echo $collect['class'] . " (" . $collect['section'] . ")";    ?></td>

                            <td>
                                <?php
                                    if ( $collect['is_system']) {
                                        echo $this->lang->line($collect['type']);
                                    } else {
                                        echo $collect['type'];
                                    }
                                ?>
                            </td>
                            <td>
                                <?php
                                    if (is_array($collect['received_byname'])) {
                                        echo $collect['received_byname']['name'] . " (" . $collect['received_byname']['employee_id'] . ")";
                                    }
                                ?>
                            </td>
                            <td>
                                <?php echo $this->lang->line(strtolower($collect['payment_mode'])); ?>
                            </td>
                            <td class="text text-right">
                                <?php echo number_format($collect['amount'], 2, '.', ''); ?>
                            </td>
                            <td class="text">
                                <?php echo $collect['description'];?>
                            </td>
                            <td class="text text-right">
                                <?php echo number_format($collect['amount_discount'], 2, '.', ''); ?>
                            </td>
                            <td class="text text-right">
                                <?php echo number_format($collect['amount_fine'], 2, '.', ''); ?>
                            </td>
                            <td class="text text-right">
                                <?php echo number_format($t, 2, '.', ''); ?>
                            </td>


                        </tr>

                 <?php } ?>

                                            <?php
$count++;
        if ($subtotal) {
            ?>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td style="font-weight:bold"><?php echo $this->lang->line('sub_total'); ?></td>
                                                <td class="text text-right" style="font-weight:bold"><?php echo amountFormat(array_sum($amountLabel)); ?></td>
                                                <td class="text text-right" style="font-weight:bold" ><?php echo amountFormat(array_sum($discountLabel)); ?></td>
                                                <td class="text text-right" style="font-weight:bold" ><?php echo amountFormat(array_sum($fineLabel)); ?></td>
                                                <td class="text text-right " style="font-weight:bold" ><?php echo amountFormat(array_sum($TotalLabel)); ?></td>
                                            </tr>
                                            <?php
}
        $grdamountLabel[]   = array_sum($amountLabel);
        $grddiscountLabel[] = array_sum($discountLabel);
        $grdfineLabel[]     = array_sum($fineLabel);
        $grdTotalLabel[]    = array_sum($TotalLabel);
    }
    ?>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td style="font-weight:bold"><?php echo $this->lang->line('grand_total'); ?></td>
                                            <td class="text text-right" style="font-weight:bold"><?php echo amountFormat(array_sum($grdamountLabel)); ?></td>
                                            <td class="text text-right" style="font-weight:bold" ><?php echo amountFormat(array_sum($grddiscountLabel)); ?></td>
                                            <td class="text text-right" style="font-weight:bold" ><?php echo amountFormat(array_sum($grdfineLabel)); ?></td>
                                            <td class="text text-right " style="font-weight:bold" ><?php echo amountFormat(array_sum($grdTotalLabel)); ?></td>
                                        </tr>

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
</div>
</section>
</div>
<iframe id="txtArea1" style="display:none"></iframe>

<script>
$(document).ready(function () {
    console.log('Document ready, jQuery version:', $.fn.jquery);
    console.log('Found multiselect dropdowns:', $('.multiselect-dropdown').length);

    // Check if SumoSelect is available
    if (typeof $.fn.SumoSelect === 'undefined') {
        console.error('SumoSelect plugin not loaded!');
        return;
    }

    // Initialize SumoSelect for all multi-select dropdowns
    $('.multiselect-dropdown').SumoSelect({
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

    // Helper functions for user feedback
    function showSuccessMessage(message) {
        $('.alert').remove(); // Remove any existing alerts
        var alertHtml = '<div class="alert alert-success alert-dismissible" role="alert">' +
                       '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                       '<span aria-hidden="true">&times;</span></button>' +
                       '<i class="fa fa-check-circle"></i> ' + message +
                       '</div>';
        $('.box-body.row').prepend(alertHtml);

        // Auto-hide after 5 seconds
        setTimeout(function() {
            $('.alert-success').fadeOut();
        }, 5000);
    }

    function showErrorMessage(message) {
        $('.alert').remove(); // Remove any existing alerts
        var alertHtml = '<div class="alert alert-danger alert-dismissible" role="alert">' +
                       '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                       '<span aria-hidden="true">&times;</span></button>' +
                       '<i class="fa fa-exclamation-triangle"></i> ' + message +
                       '</div>';
        $('.box-body.row').prepend(alertHtml);

        // Auto-hide after 8 seconds
        setTimeout(function() {
            $('.alert-danger').fadeOut();
        }, 8000);
    }

    // Enhanced loading state for SumoSelect dropdowns
    function showDropdownLoading(selector) {
        $(selector).prop('disabled', true);
        $(selector).next('.SumoSelect').addClass('loading');
    }

    function hideDropdownLoading(selector) {
        $(selector).prop('disabled', false);
        $(selector).next('.SumoSelect').removeClass('loading');
    }
});

<?php
if ($search_type == 'period') {
    ?>

        $(document).ready(function () {
            showdate('period');
        });

    <?php
}
?>

document.getElementById("print").style.display = "block";
document.getElementById("btnExport").style.display = "block";
document.getElementById("printhead").style.display = "none";

function printDiv() {
    document.getElementById("print").style.display = "none";
    document.getElementById("btnExport").style.display = "none";
     document.getElementById("printhead").style.display = "block";
    var divElements = document.getElementById('transfee').innerHTML;
    var oldPage = document.body.innerHTML;
    document.body.innerHTML =
            "<html><head><title><?php echo $this->lang->line('total_fee_collection_report') . " (" . $this->lang->line('combined') . ")"; ?></title></head><body>" +
            divElements + "</body>";
    window.print();
    document.body.innerHTML = oldPage;
    document.getElementById("printhead").style.display = "none";
    location.reload(true);
}

function fnExcelReport(){
    exportToExcel();
}

function exportToExcel(){
var htmls = "";
            var uri = 'data:application/vnd.ms-excel;base64,';
            var template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>';
            var base64 = function(s) {
                return window.btoa(unescape(encodeURIComponent(s)))
            };

            var format = function(s, c) {
                return s.replace(/{(\w+)}/g, function(m, p) {
                    return c[p];
                })
            };
        var tab_text = "<tr >";
                     var textRange;
         var j = 0;
          var val="";
         tab = document.getElementById('headerTable'); // id of table

         for (j = 0; j < tab.rows.length; j++)
         {
             tab_text = tab_text + tab.rows[j].innerHTML + "</tr>";
       }

            var ctx = {
                worksheet : 'Worksheet',
                table : tab_text
            }

            var link = document.createElement("a");
            link.download = "student_combined_fee_collection_report.xls";
            link.href = uri + base64(format(template, ctx));
            link.click();
}

</script>