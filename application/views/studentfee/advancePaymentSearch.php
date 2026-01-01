<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>




<style>
/* Multi-select dropdown enhancements */
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
    .col-sm-6.col-md-6 {
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
    <section class="content-header">

    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('select_criteria'); ?></h3>
                    </div>
                    <div class="box-body">

                        <?php if ($this->session->flashdata('msg')) {?> <div class="alert alert-success">  <?php echo $this->session->flashdata('msg'); $this->session->unset_userdata('msg'); ?> </div> <?php }?>
                        <div class="row">
                              <form role="form" action="<?php echo site_url('studentfee/searchvalidation') ?>" method="post" class="class_search_form">
                            <div class="col-md-6">
                                <div class="row">
                                        <?php echo $this->customlib->getCSRF(); ?>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('class'); ?></label>
                                                <select id="class_id" name="class_id[]" class="form-control multiselect-dropdown" multiple>
                                                    <?php
if (isset($classlist) && !empty($classlist)) {
    foreach ($classlist as $class) {
        ?>
                                                        <option value="<?php echo $class['id'] ?>" <?php if (set_value('class_id') == $class['id']) {
            echo "selected=selected";
        }
        ?>><?php echo $class['class'] ?></option>
                                                        <?php
    }
} else {
    echo '<option value="">No classes available</option>';
}
?>
                                                </select>
                                                <span class="text-danger" id="error_class_id"></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('section'); ?></label>
                                                <select id="section_id" name="section_id[]" class="form-control multiselect-dropdown" multiple>
                                                </select>
                                                <span class="text-danger" id="error_section_id"></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <button type="submit" name="search" value="search_filter" class="btn btn-primary btn-sm pull-right checkbox-toggle"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                            </div>
                                        </div>
                                </div>
                            </div><!--./col-md-6-->

                            <div class="col-md-6">
                                <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('search_by_keyword'); ?></label>
                                        <input type="text" name="search_text" id="search_text" class="form-control" value="<?php echo set_value('search_text'); ?>"   placeholder="<?php echo $this->lang->line('search_by_student_name'); ?>">
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <button type="submit" name="search" value="search_full" class="btn btn-primary pull-right btn-sm checkbox-toggle"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                            </div>
                                        </div>
                                </div>
                           </div><!--./col-md-6-->
                       </form>
                        </div><!--./row-->

                    <div class="nav-tabs-custom border0 navnoshadow">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="true"><i class="fa fa-list"></i> <?php echo $this->lang->line('list_view'); ?></a></li>
                            <li class=""><a href="#tab_2" data-toggle="tab" aria-expanded="false"><i class="fa fa-newspaper-o"></i> <?php echo $this->lang->line('details_view'); ?></a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active table-responsive no-padding overflow-visible-lg" id="tab_1">
                                <table class="table table-striped table-bordered table-hover student-list" data-export-title="<?php echo $this->lang->line('student_list'); ?>">
                                    <thead>
                                        <tr>
                                            <th><?php echo $this->lang->line('admission_no'); ?></th>
                                            <th><?php echo $this->lang->line('student_name'); ?></th>
                                            <th><?php echo $this->lang->line('class'); ?></th>
                                             <?php if ($sch_setting->father_name) {?>
                                            <th><?php echo $this->lang->line('father_name'); ?></th>
                                            <?php }?>
                                            <th><?php echo $this->lang->line('date_of_birth'); ?></th>
                                            <th><?php echo $this->lang->line('gender'); ?></th>
                                            <?php if ($sch_setting->category) {
    ?>
                                              <?php if ($sch_setting->category) {?>
                                            <th><?php echo $this->lang->line('category'); ?></th>
                                            <?php }
}if ($sch_setting->mobile_no) {
    ?>
                                            <th><?php echo $this->lang->line('mobile_number'); ?></th>
                                            <?php
}
if (!empty($fields)) {

    foreach ($fields as $fields_key => $fields_value) {
        ?>
                                                    <th><?php echo $fields_value->name; ?></th>
                                                    <?php
}
}
?>
                                            <th><?php echo $this->lang->line('advance_balance'); ?></th>
                                            <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <div class="tab-pane detail_view_tab" id="tab_2">
                                <div class="alert alert-info"><?php echo $this->lang->line('no_record_found'); ?></div>
                            </div>
                        </div>
                    </div><!--./nav-tabs-custom-->
                </div><!--./box box-primary -->

            </div>
        </div>
    </section>
</div>

    </section>
</div>

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
                                <label><?php echo $this->lang->line('description'); ?></label>
                                <textarea name="description" id="advance_description" class="form-control" rows="3" placeholder="<?php echo $this->lang->line('description'); ?>"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" class="btn btn-primary" id="advancePaymentSubmitBtn">
                        <i class="fa fa-save"></i> <?php echo $this->lang->line('save'); ?>
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

<script type="text/javascript">

function getSectionByClass(class_id, section_id) {
    if (class_id != "" && section_id != "") {
        $('#section_id').html("");
        var base_url = '<?php echo base_url() ?>';
        var div_data = '';
        $.ajax({
            type: "GET",
            url: base_url + "sections/getByClass",
            data: {'class_id': class_id},
            dataType: "json",
            success: function (data) {
                $.each(data, function (i, obj)
                {
                    var sel = "";
                    if (section_id == obj.section_id) {
                        sel = "selected";
                    }
                    div_data += "<option value=" + obj.section_id + " " + sel + ">" + obj.section + "</option>";
                });
                $('#section_id').append(div_data);

                // Refresh SumoSelect after adding options
                if ($('#section_id')[0].sumo) {
                    $('#section_id')[0].sumo.reload();
                }
            }
        });
    }
}

$(document).ready(function () {
    // Check if SumoSelect is available
    console.log('Checking SumoSelect availability...');
    console.log('SumoSelect function type:', typeof $.fn.SumoSelect);
    console.log('jQuery plugins available:', Object.keys($.fn).filter(key => key.toLowerCase().includes('sumo')));

    if (typeof $.fn.SumoSelect === 'undefined') {
        console.error('❌ SumoSelect plugin not loaded!');
        console.log('Available jQuery methods:', Object.keys($.fn).slice(0, 20));

        // Try using window.jQuery if available
        if (typeof window.jQuery !== 'undefined' && typeof window.jQuery.fn.SumoSelect !== 'undefined') {
            console.log('🔄 Trying window.jQuery for SumoSelect...');
            $ = window.jQuery;
            console.log('Switched to jQuery version:', $.fn.jquery);
        } else {
            console.error('❌ SumoSelect not available in any jQuery version');
            return;
        }
    } else {
        console.log('✅ SumoSelect plugin is available!');
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



    // Initialize section dropdown on page load if class is pre-selected
    var preSelectedClass = $('#class_id').val();
    if (preSelectedClass && preSelectedClass.length > 0) {
        $('#class_id').trigger('change');
    }

    // Handle class dropdown changes for section population
    $(document).on('change', '#class_id', function (e) {
        console.log('Class dropdown changed');
        var class_ids = $(this).val(); // This will be an array for multi-select
        var base_url = '<?php echo base_url() ?>';

        // Clear section dropdown
        $('#section_id').html('');

        // Refresh SumoSelect to clear previous selections
        if ($('#section_id')[0].sumo) {
            $('#section_id')[0].sumo.reload();
        }

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
                // Sort sections by name for better UX
                allSections.sort(function(a, b) {
                    return a.text.localeCompare(b.text);
                });

                // Add all sections to dropdown
                var div_data = '';
                $.each(allSections, function(i, section) {
                    div_data += "<option value='" + section.value + "'>" + section.text + "</option>";
                });
                $('#section_id').html(div_data);

                // Refresh SumoSelect after adding options
                if ($('#section_id')[0].sumo) {
                    $('#section_id')[0].sumo.reload();
                }

                console.log('Sections loaded for selected classes:', allSections.length);
            });
        }
    });
});
</script>

 <script>
$(document).ready(function() {
     emptyDatatable('student-list','data');
});
</script>

<script type="text/javascript">
$(document).ready(function(){

$("form.class_search_form button[type=submit]").click(function() {
    $("button[type=submit]", $(this).parents("form")).removeAttr("clicked");
    $(this).attr("clicked", "true");
});

$(document).on('submit','.class_search_form',function(e){
   e.preventDefault(); // avoid to execute the actual submit of the form.
    var $this = $("button[type=submit][clicked=true]");
    var form = $(this);
    var url = form.attr('action');
    var form_data = form.serializeArray();
    form_data.push({name: 'search_type', value: $this.attr('value')});

    console.log('🚀 STUDENT SEARCH STARTED');
    console.log('Form URL:', url);
    console.log('Form Data:', form_data);
    console.log('Search Type:', $this.attr('value'));

    $.ajax({
           url: url,
           type: "POST",
           dataType:'JSON',
           data: form_data, // serializes the form's elements.
              beforeSend: function () {
                console.log('📤 AJAX Request Starting...');
                $('[id^=error]').html("");
                $this.button('loading');
                resetFields($this.attr('value'));
               },
              success: function(response) { // your success handler
                console.log('✅ AJAX Response Received:', response);

                if(!response.status){
                    console.error('❌ Validation Error:', response.error);
                    $.each(response.error, function(key, value) {
                    $('#error_' + key).html(value);
                    });
                }else{
                    console.log('🎯 Student Search - Initializing DataTable...');
                    console.log('DataTable ID: student-list');
                    console.log('DataTable URL: student/dtstudentlist');
                    console.log('DataTable Params:', response.params);

                    $('[id^=error]').html("");

                    // Use the same approach as studentReport.php
                    console.log('🎯 Initializing DataTable with response params:', response.params);

                    $('[id^=error]').html("");
                    initDatatable('student-list','studentfee/ajaxAdvanceSearch',response.params,[],100);
                }
              },
             error: function(xhr, status, error) { // your error handler
                 console.error('❌ AJAX Error:', status, error);
                 console.error('Response Text:', xhr.responseText);
                 console.error('Status Code:', xhr.status);
                 showErrorMessage('Network error occurred. Please check your connection and try again.');
                 $this.button('reset');
             },
             complete: function() {
                 console.log('🏁 AJAX Request Complete');
                 $this.button('reset');
             }
         });

});

    });
    function resetFields(search_type){

        if(search_type == "search_full"){
            // Reset multi-select dropdowns using SumoSelect
            if ($('#class_id')[0].sumo) {
                $('#class_id')[0].sumo.unSelectAll();
            }
            if ($('#section_id')[0].sumo) {
                $('#section_id')[0].sumo.unSelectAll();
            }
            $('#section_id').html('');
            if ($('#section_id')[0].sumo) {
                $('#section_id')[0].sumo.reload();
            }
        }else if (search_type == "search_filter") {
             $('#search_text').val("");
        }
    }

    // Helper functions for user feedback
    function showSuccessMessage(message) {
        $('.alert').remove(); // Remove any existing alerts
        var alertHtml = '<div class="alert alert-success alert-dismissible" role="alert">' +
                       '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                       '<span aria-hidden="true">&times;</span></button>' +
                       '<i class="fa fa-check-circle"></i> ' + message +
                       '</div>';
        $('.box-body').prepend(alertHtml);

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
        $('.box-body').prepend(alertHtml);

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

    // Advance Payment Modal Functions
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

        // Show modal
        $('#advancePaymentModal').modal('show');
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

                    // Refresh the advance history modal
                    var currentStudentSessionId = $('#advanceHistoryModal').data('student-session-id');
                    if (currentStudentSessionId) {
                        viewAdvanceHistory(currentStudentSessionId);
                    }

                    // Refresh the student list to show updated balance
                    if (typeof initDatatable === 'function') {
                        var currentParams = $('#student-list').data('params') || {};
                        initDatatable('student-list', 'studentfee/ajaxAdvanceSearch', currentParams, [], 100);
                    }

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

    // Form submission handler
    $(document).ready(function() {
        // Initialize date picker
        $('.date').datepicker({
            format: '<?php echo $this->customlib->getSchoolDateFormat(); ?>',
            autoclose: true,
            todayHighlight: true
        });

        // Handle advance payment form submission
        $('#advancePaymentForm').on('submit', function(e) {
            e.preventDefault();

            var form = $(this);
            var submitBtn = $('#advancePaymentSubmitBtn');
            var formData = form.serialize();

            // Clear previous errors
            $('[id^=error_]').html('');

            // Show loading state
            submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> <?php echo $this->lang->line("processing"); ?>');

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        // Show success message
                        showSuccessMessage(response.message || '<?php echo $this->lang->line("advance_payment_added_successfully"); ?>');

                        // Close modal
                        $('#advancePaymentModal').modal('hide');

                        // Refresh the student list to show updated balance
                        if (typeof initDatatable === 'function') {
                            // Get current search parameters and refresh
                            var currentParams = $('#student-list').data('params') || {};
                            initDatatable('student-list', 'studentfee/ajaxAdvanceSearch', currentParams, [], 100);
                        }

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
                    // Reset button state
                    submitBtn.prop('disabled', false).html('<i class="fa fa-save"></i> <?php echo $this->lang->line("save"); ?>');
                }
            });
        });
    });
</script>
