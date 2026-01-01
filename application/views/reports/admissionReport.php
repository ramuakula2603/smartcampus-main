<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>

<style>
/* Multi-select dropdown enhancements for Report Pages */
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

<style type="text/css">
    /*REQUIRED*/
    .carousel-row {
        margin-bottom: 10px;
    }
    .slide-row {
        padding: 0;
        background-color: #ffffff;
        min-height: 150px;
        border: 1px solid #e7e7e7;
        overflow: hidden;
        height: auto;
        position: relative;
    }
    .slide-carousel {
        width: 20%;
        float: left;
        display: inline-block;
    }
    .slide-carousel .carousel-indicators {
        margin-bottom: 0;
        bottom: 0;
        background: rgba(0, 0, 0, .5);
    }
    .slide-carousel .carousel-indicators li {
        border-radius: 0;
        width: 20px;
        height: 6px;
    }
    .slide-carousel .carousel-indicators .active {
        margin: 1px;
    }
    .slide-content {
        position: absolute;
        top: 0;
        left: 20%;
        display: block;
        float: left;
        width: 80%;
        max-height: 76%;
        padding: 1.5% 2% 2% 2%;
        overflow-y: auto;
    }
    .slide-content h4 {
        margin-bottom: 3px;
        margin-top: 0;
    }
    .slide-footer {
        position: absolute;
        bottom: 0;
        left: 20%;
        width: 78%;
        height: 20%;
        margin: 1%;
    }
    /* Scrollbars */
    .slide-content::-webkit-scrollbar {
        width: 5px;
    }
    .slide-content::-webkit-scrollbar-thumb:vertical {
        margin: 5px;
        background-color: #999;
        -webkit-border-radius: 5px;
    }
    .slide-content::-webkit-scrollbar-button:start:decrement,
    .slide-content::-webkit-scrollbar-button:end:increment {
        height: 5px;
        display: block;
    }
</style>

<div class="content-wrapper" style="min-height: 946px;">
    <section class="content-header">
        <h1>
            <i class="fa fa-user-plus"></i> <?php //echo $this->lang->line('student_information'); ?>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <?php $this->load->view('reports/_studentinformation'); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="box removeboxmius">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('select_criteria'); ?></h3>
                    </div>
                    <div class="box-body">   
                        <form role="form" action="<?php echo site_url('report/admissionsearchvalidation') ?>" method="post" class="" id="reportform">
                            <div class="row">

                                <?php echo $this->customlib->getCSRF(); ?>

                                <div class="col-sm-6 col-md-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('class'); ?></label>
                                        <select id="class_id" name="class_id[]" class="form-control multiselect-dropdown" multiple>
                                            <?php
                                            foreach ($classlist as $class) {
                                                ?>
                                                <option value="<?php echo $class['id'] ?>" <?php if (set_value('class_id') == $class['id']) echo "selected=selected" ?> ><?php echo $class['class'] ?></option>
                                                <?php
                                                $count++;
                                            }
                                            ?>
                                        </select>
                                         <span class="text-danger" id="error_class_id"></span>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('admission_year'); ?></label>
                                        <select id="year" name="year[]" class="form-control multiselect-dropdown" multiple>
                                            <?php foreach ($admission_year as $key => $value) { ?>

                                                <option value="<?php echo $value["year"] ?>" <?php
                                                if (isset($_POST['year']) && $_POST['year'] != '') {
                                                    if ($_POST['year'] == $value["year"]) {
                                                        echo "selected";
                                                    }
                                                }
                                                ?>><?php echo $value["year"] ?></option>

                                            <?php } ?>

                                        </select>
                                        <span class="text-danger" id="error_year"></span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <button type="submit" name="search" value="search_filter" class="btn btn-primary btn-sm checkbox-toggle pull-right"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                    </div>
                                </div>
                            </div><!--./row-->      
                        </form>
                    </div><!--./box-body-->   
                    <div class="">
                        <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><i class="fa fa-users"></i> <?php echo form_error('student'); ?> <?php echo $this->lang->line('student_history'); ?></h3>
                    </div>
                    <div class="box-body table-responsive">
                        <div class="download_label"></div>
                        <table class="table table-striped table-bordered table-hover student-list" data-export-title="<?php echo $this->lang->line('student_history') ; ?>">
                            <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('admission_no'); ?></th>
                                    <th><?php echo $this->lang->line('student_name'); ?></th>
                                    <?php if ($sch_setting->admission_date) { ?>
                                        <th><?php echo $this->lang->line('admission_date'); ?></th>
                                    <?php } ?>
                                    <th><?php echo $this->lang->line('class_start_end'); ?></th>
                                    <th><?php echo $this->lang->line('session_start_end') ?></th>
                                    <th><?php echo $this->lang->line('years'); ?></th>
                                    <?php if ($sch_setting->mobile_no) { ?>
                                        <th><?php echo $this->lang->line('mobile_number'); ?></th>
                                    <?php }  if ($sch_setting->guardian_name) { ?>
                                        <th><?php echo $this->lang->line('guardian_name'); ?></th>
                                    <?php }  if ($sch_setting->guardian_phone) { ?>
                                        <th><?php echo $this->lang->line('guardian_phone'); ?></th>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div><!--./box box-primary-->
            </div><!--./col-md-12-->  
        </div>   
    </div>  
</section>
</div>

<script type="text/javascript">
    function getSectionByClass(class_id, section_id) {
        if (class_id != "" && section_id != "") {
            $('#section_id').html("");
            var base_url = '<?php echo base_url() ?>';
            var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
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
                }
            });
        }
    }

    $(document).ready(function () {
        var class_id = $('#class_id').val();
        var section_id = '<?php echo set_value('section_id') ?>';
        getSectionByClass(class_id, section_id);
        $(document).on('change', '#class_id', function (e) {
            $('#section_id').html("");
            var class_id = $(this).val();
            var base_url = '<?php echo base_url() ?>';
            var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
            $.ajax({
                type: "GET",
                url: base_url + "sections/getByClass",
                data: {'class_id': class_id},
                dataType: "json",
                success: function (data) {
                    $.each(data, function (i, obj)
                    {
                        div_data += "<option value=" + obj.section_id + ">" + obj.section + "</option>";
                    });
                    $('#section_id').append(div_data);
                }
            });
        });
    });
</script>
<script type="text/javascript">
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

    // Initialize empty DataTable
    emptyDatatable('student-list','data');
});
</script>
<script type="text/javascript">
$(document).ready(function(){ 
$(document).on('submit','#reportform',function(e){
    e.preventDefault(); // avoid to execute the actual submit of the form.
    var $this = $(this).find("button[type=submit]:focus");  
    var form = $(this);
    var url = form.attr('action');
    var form_data = form.serializeArray();
  
    $.ajax({
           url: url,
           type: "POST",
           dataType:'JSON',
           data: form_data, // serializes the form's elements.
              beforeSend: function () {
                $('[id^=error]').html("");
                $this.button('loading');
               },
              success: function(response) { // your success handler
                
                if(!response.status){
                    $.each(response.error, function(key, value) {
                    $('#error_' + key).html(value);
                    });
                }else{
                 
                   initDatatable('student-list','report/dtadmissionreportlist',response.params);
                }
              },
             error: function() { // your error handler
                 $this.button('reset');
             },
             complete: function() {
             $this.button('reset');
             }
         });
        });
    });    
</script>