<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
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
        <?php $this->load->view('reports/_studentinformation');?>
        <div class="row">
            <div class="col-md-12">
                <div class="box removeboxmius">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('select_criteria'); ?></h3>
                    </div>
                    <div class="box-body">
                        <form role="form" id="reportform" action="<?php echo site_url('report/guardiansearchvalidation') ?>" method="post" class="">
                            <div class="row">
                                <?php echo $this->customlib->getCSRF(); ?>
                                <div class="col-sm-6 col-md-6">
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
                                <div class="col-sm-6 col-md-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('section'); ?></label>
                                        <select id="section_id" name="section_id[]" class="form-control multiselect-dropdown" multiple>
                                        </select>
                                        <span class="text-danger" id="error_section_id"></span>
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
                            <h3 class="box-title titlefix"><i class="fa fa-users"></i> <?php echo form_error('student'); ?> <?php echo $this->lang->line('guardian_report'); ?></h3>
                        </div>
                        <div class="box-body table-responsive">
                            <div class="download_label"><?php
echo $this->lang->line('guardian_report') . "<br>";
$this->customlib->get_postmessage();
?></div>
                            <table class="table table-striped table-bordered table-hover guardian-list" id="guardian-list">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('class_section'); ?></th>
                                        <th><?php echo $this->lang->line('admission_no'); ?></th>
                                        <th><?php echo $this->lang->line('student_name'); ?></th>
                                        <?php if ($sch_setting->mobile_no) {?>
                                            <th><?php echo $this->lang->line('mobile_number'); ?></th>
                                        <?php }if ($sch_setting->guardian_name) {?>
                                        <th><?php echo $this->lang->line('guardian_name'); ?></th>
                                        <?php }if ($sch_setting->guardian_relation) {?>
                                            <th><?php echo $this->lang->line('guardian_relation'); ?></th>
                                        <?php }if ($sch_setting->guardian_phone) {?>
                                        <th><?php echo $this->lang->line('guardian_phone'); ?></th>
                                        <?php }if ($sch_setting->father_name) {?>
                                            <th><?php echo $this->lang->line('father_name'); ?></th>
                                        <?php }if ($sch_setting->father_phone) {?>
                                            <th><?php echo $this->lang->line('father_phone'); ?></th>
                                        <?php }if ($sch_setting->mother_name) {?>
                                            <th><?php echo $this->lang->line('mother_name'); ?></th>
                                        <?php }if ($sch_setting->mother_phone) {?>
                                            <th><?php echo $this->lang->line('mother_phone'); ?></th>
<?php }?>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div><!--./box box-primary -->
            </div><!-- ./col-md-12 -->
        </div>
</div>
</section>
</div>

<!-- SumoSelect files are already included in layout/header.php -->

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
    z-index: 9999 !important;
    position: absolute !important;
}

.SumoSelect .optWrapper ul.options {
    max-height: 200px;
    overflow-y: auto;
}

.SumoSelect .optWrapper ul.options li {
    padding: 8px 12px;
    border-bottom: 1px solid #f4f4f4;
    cursor: pointer !important;
    user-select: none;
}

.SumoSelect .optWrapper ul.options li:hover {
    background-color: #f5f5f5;
}

.SumoSelect .optWrapper ul.options li.selected {
    background-color: #337ab7;
    color: #fff;
}

/* Ensure dropdown items are clickable */
.SumoSelect .optWrapper ul.options li label {
    cursor: pointer !important;
    display: block;
    width: 100%;
    padding: 0;
    margin: 0;
}

.SumoSelect .optWrapper ul.options li input[type="checkbox"] {
    margin-right: 8px;
    cursor: pointer !important;
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
    to { transform: rotate(360deg); }
}
</style>

<script type="text/javascript">
    $(document).ready(function () {
        console.log('Document ready, jQuery version:', $.fn.jquery);
        console.log('Found multiselect dropdowns:', $('.multiselect-dropdown').length);

        // Check for jQuery conflicts
        if (typeof window.jQuery !== 'undefined' && window.jQuery.fn.jquery !== $.fn.jquery) {
            console.warn('⚠️ Multiple jQuery versions detected!');
            console.log('Current $ version:', $.fn.jquery);
            console.log('Window jQuery version:', window.jQuery.fn.jquery);
        }

        // Debug dropdown elements
        $('.multiselect-dropdown').each(function(index) {
            var $this = $(this);
            console.log('Dropdown #' + index + ':', {
                id: $this.attr('id'),
                tagName: $this.prop('tagName'),
                hasMultiple: $this.prop('multiple'),
                optionsCount: $this.find('option').length,
                isVisible: $this.is(':visible'),
                options: $this.find('option').map(function() { return $(this).text(); }).get()
            });
        });

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



        // Initialize empty datatable
        if (typeof emptyDatatable === 'function') {
            emptyDatatable('guardian-list','data');
            console.log('✅ Empty DataTable initialized');
        } else {
            console.error('❌ emptyDatatable function not found');
        }
    });
</script>

<script type="text/javascript">
$(document).ready(function(){
$(document).on('submit','#reportform',function(e){
    e.preventDefault(); // avoid to execute the actual submit of the form.
    var $this = $(this).find("button[type=submit]:focus");
    var form = $(this);
    var url = form.attr('action');

        // Use standard form serialization - works with both single and multi-select
        var form_data = form.serializeArray();
        form_data.push({name: 'search_type', value: $this.attr('value')});

        console.log('🚀 GUARDIAN REPORT SEARCH STARTED');
        console.log('Form URL:', url);
        console.log('Form Data:', form_data);
        console.log('Search Type:', $this.attr('value'));

        $.ajax({
            url: url,
            type: "POST",
            dataType:'JSON',
            data: form_data,
            beforeSend: function () {
                console.log('📤 AJAX Request Starting...');
                $('[id^=error]').html("");
                $this.button('loading');
            },
            success: function(response) { // your success handler
                console.log('✅ AJAX Response Received:', response);
                console.log('Response Status:', response.status);
                console.log('Response Params:', response.params);

                if(!response.status){
                    console.log('❌ Validation Errors:', response.error);
                    $.each(response.error, function(key, value) {
                        $('#error_' + key).html(value);
                    });
                }else{
                    console.log('🎯 Initializing DataTable...');
                    console.log('DataTable ID: guardian-list');
                    console.log('DataTable URL: report/dtguardianreportlist');
                    console.log('DataTable Params:', response.params);

                    $('[id^=error]').html("");

                    if (typeof initDatatable === 'function') {
                        console.log('🎯 Calling initDatatable function...');
                        initDatatable('guardian-list','report/dtguardianreportlist',response.params,[],100);
                        console.log('✅ initDatatable called successfully');
                    } else {
                        console.error('❌ initDatatable function not found');
                        console.log('Available functions:', Object.keys(window).filter(key => key.includes('Datatable')));
                    }
                }
            },
            error: function(xhr, status, error) { // your error handler
                console.error('❌ AJAX Error:', status, error);
                console.error('Response Text:', xhr.responseText);
                console.error('Status Code:', xhr.status);
                showErrorMessage('Network error occurred. Please check your connection and try again.');
            },
            complete: function() {
                $this.button('reset');
            }
        });
        });
    });
</script>