<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>

<style>
    /* Multi-select dropdown enhancements */
    .SumoSelect {
        width: 100% !important;
    }

    .SumoSelect>.CaptionCont {
        border: 1px solid #d2d6de;
        border-radius: 3px;
        background-color: #fff;
        min-height: 34px;
        padding: 6px 12px;
    }

    .SumoSelect>.CaptionCont>span {
        line-height: 1.42857143;
        color: #555;
        padding-right: 20px;
    }

    .SumoSelect>.CaptionCont>span.placeholder {
        color: #999;
        font-style: italic;
    }

    .SumoSelect.open>.CaptionCont,
    .SumoSelect:focus>.CaptionCont,
    .SumoSelect:hover>.CaptionCont {
        border-color: #66afe9;
        box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075), 0 0 8px rgba(102, 175, 233, .6);
    }

    .SumoSelect .optWrapper {
        border: 1px solid #d2d6de;
        border-radius: 3px;
        box-shadow: 0 6px 12px rgba(0, 0, 0, .175);
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

        .SumoSelect>.CaptionCont {
            min-height: 40px;
            padding: 8px 12px;
        }

        .form-group label {
            font-weight: 600;
            margin-bottom: 5px;
        }
    }

    @media (max-width: 480px) {
        .SumoSelect>.CaptionCont {
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
    .SumoSelect.loading>.CaptionCont {
        opacity: 0.6;
        pointer-events: none;
    }

    .SumoSelect.loading>.CaptionCont:after {
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
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
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

                        <?php if ($this->session->flashdata('msg')) { ?> <div class="alert alert-success"> <?php echo $this->session->flashdata('msg');
                                                                                                            $this->session->unset_userdata('msg'); ?> </div> <?php } ?>
                        <div class="row">
                            <form role="form" action="<?php echo site_url('student/searchvalidation') ?>" method="post" class="class_search_form">
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
                                                <input type="text" name="search_text" id="search_text" class="form-control" value="<?php echo set_value('search_text'); ?>" placeholder="<?php echo $this->lang->line('search_by_student_name'); ?>">
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
                    </div>

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
                                            <?php if ($sch_setting->father_name) { ?>
                                                <th><?php echo $this->lang->line('father_name'); ?></th>
                                            <?php } ?>
                                            <th><?php echo $this->lang->line('date_of_birth'); ?></th>
                                            <th><?php echo $this->lang->line('gender'); ?></th>
                                            <?php if ($sch_setting->category) {
                                            ?>
                                                <?php if ($sch_setting->category) { ?>
                                                    <th><?php echo $this->lang->line('category'); ?></th>
                                                <?php }
                                            }
                                            if ($sch_setting->mobile_no) {
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
                                            <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <div class="tab-pane detail_view_tab" id="tab_2">
                                <?php if (empty($resultlist)) {
                                ?>
                                    <div class="alert alert-info"><?php echo $this->lang->line('no_record_found'); ?></div>
                                    <?php
                                } else {
                                    $count = 1;
                                    foreach ($resultlist as $student) {

                                        if (empty($student["image"])) {
                                            if ($student['gender'] == 'Female') {
                                                $image = "uploads/student_images/default_female.jpg";
                                            } else {
                                                $image = "uploads/student_images/default_male.jpg";
                                            }
                                        } else {
                                            $image = $student['image'];
                                        }
                                    ?>
                                        <div class="carousel-row">
                                            <div class="slide-row">
                                                <div id="carousel-2" class="carousel slide slide-carousel" data-ride="carousel">
                                                    <div class="carousel-inner">
                                                        <div class="item active">
                                                            <a href="<?php echo base_url(); ?>student/view/<?php echo $student['id'] ?>">
                                                                <?php if ($sch_setting->student_photo) { ?><img class="img-responsive img-thumbnail width150" alt="<?php echo $student["firstname"] . " " . $student["lastname"] ?>" src="<?php echo $this->media_storage->getImageURL($image); ?>" alt="Image"><?php } ?></a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="slide-content">
                                                    <h4><a href="<?php echo base_url(); ?>student/view/<?php echo $student['id'] ?>"> <?php echo $this->customlib->getFullName($student['firstname'], $student['middlename'], $student['lastname'], $sch_setting->middlename, $sch_setting->lastname); ?></a></h4>
                                                    <div class="row">
                                                        <div class="col-xs-6 col-md-6">
                                                            <address>
                                                                <strong><b><?php echo $this->lang->line('class'); ?>: </b><?php echo $student['class'] . "(" . $student['section'] . ")" ?></strong><br>
                                                                <b><?php echo $this->lang->line('admission_no'); ?>: </b><?php echo $student['admission_no'] ?><br />
                                                                <b><?php echo $this->lang->line('date_of_birth'); ?>:
                                                                    <?php if ($student["dob"] != null && $student["dob"] != '0000-00-00') {
                                                                        echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($student['dob']));
                                                                    } ?><br>
                                                                    <b><?php echo $this->lang->line('gender'); ?>:&nbsp;</b><?php echo $this->lang->line(strtolower($student['gender'])) ?><br>
                                                            </address>
                                                        </div>
                                                        <div class="col-xs-6 col-md-6">
                                                            <b><?php echo $this->lang->line('local_identification_no'); ?>:&nbsp;</b><?php echo $student['samagra_id'] ?><br>
                                                            <?php if ($sch_setting->guardian_name) { ?>
                                                                <b><?php echo $this->lang->line('guardian_name'); ?>:&nbsp;</b><?php echo $student['guardian_name'] ?><br>
                                                            <?php }
                                                            if ($sch_setting->guardian_name) { ?>
                                                                <b><?php echo $this->lang->line('guardian_phone'); ?>: </b> <abbr title="Phone"><i class="fa fa-phone-square"></i>&nbsp;</abbr> <?php echo $student['guardian_phone'] ?><br> <?php } ?>
                                                            <b><?php echo $this->lang->line('current_address'); ?>:&nbsp;</b><?php echo $student['current_address'] ?> <?php echo $student['city'] ?><br>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="slide-footer">
                                                    <span class="pull-right buttons">
                                                        <a href="<?php echo base_url(); ?>student/view/<?php echo $student['id'] ?>" class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo $this->lang->line('view'); ?>">
                                                            <i class="fa fa-reorder"></i>
                                                        </a>
                                                        <?php
                                                        if ($this->rbac->hasPrivilege('student', 'can_edit')) {
                                                        ?>
                                                            <a href="<?php echo base_url(); ?>student/edit/<?php echo $student['id'] ?>" class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                                <i class="fa fa-pencil"></i>
                                                            </a>
                                                        <?php
                                                        }
                                                        if ($this->module_lib->hasActive('fees_collection') && $this->rbac->hasPrivilege('collect_fees', 'can_add')) {
                                                        ?>
                                                            <a href="<?php echo base_url(); ?>studentfee/addfee/<?php echo $student['id'] ?>" class="btn btn-default btn-xs" data-toggle="tooltip" title="" data-original-title="<?php echo $this->lang->line('add_fees'); ?>">
                                                                <?php echo $currency_symbol; ?>
                                                            </a>
                                                        <?php } ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                <?php
                                    }
                                    $count++;
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div><!--./box box-primary -->

            </div>
        </div>
    </section>
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
                data: {
                    'class_id': class_id
                },
                dataType: "json",
                success: function(data) {
                    $.each(data, function(i, obj) {
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

    $(document).ready(function() {
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
        $(document).on('change', '#class_id', function(e) {
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
                            data: {
                                'class_id': class_id
                            },
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
        emptyDatatable('student-list', 'data');
    });
</script>

<script type="text/javascript">
    $(document).ready(function() {

        $("form.class_search_form button[type=submit]").click(function() {
            $("button[type=submit]", $(this).parents("form")).removeAttr("clicked");
            $(this).attr("clicked", "true");
        });

        $(document).on('submit', '.class_search_form', function(e) {
            e.preventDefault(); // avoid to execute the actual submit of the form.
            var $this = $("button[type=submit][clicked=true]");
            var form = $(this);
            var url = form.attr('action');
            var form_data = form.serializeArray();
            form_data.push({
                name: 'search_type',
                value: $this.attr('value')
            });

            console.log('🚀 STUDENT SEARCH STARTED');
            console.log('Form URL:', url);
            console.log('Form Data:', form_data);
            console.log('Search Type:', $this.attr('value'));

            $.ajax({
                url: url,
                type: "POST",
                dataType: 'JSON',
                data: form_data, // serializes the form's elements.
                beforeSend: function() {
                    console.log('📤 AJAX Request Starting...');
                    $('[id^=error]').html("");
                    $this.button('loading');
                    resetFields($this.attr('value'));
                },
                success: function(response) { // your success handler
                    console.log('✅ AJAX Response Received:', response);

                    if (!response.status) {
                        console.error('❌ Validation Error:', response.error);
                        $.each(response.error, function(key, value) {
                            $('#error_' + key).html(value);
                        });
                    } else {
                        console.log('🎯 Student Search - Initializing DataTable...');
                        console.log('DataTable ID: student-list');
                        console.log('DataTable URL: student/dtstudentlist');
                        console.log('DataTable Params:', response.params);

                        $('[id^=error]').html("");

                        // Use the same approach as studentReport.php
                        console.log('🎯 Initializing DataTable with response params:', response.params);

                        $('[id^=error]').html("");
                        initDatatable('student-list', 'student/dtstudentlist', response.params, [], 100);
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

    function resetFields(search_type) {

        if (search_type == "search_full") {
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
        } else if (search_type == "search_filter") {
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
</script>