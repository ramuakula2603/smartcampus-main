<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-building"></i> <?php echo $this->lang->line('hostel'); ?></h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('assign_hostel_fees'); ?></h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <form role="form" action="<?php echo site_url('admin/hostel/assignhostelfeestudent') ?>" method="post" class="">
                                <div class="col-sm-6 col-md-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('session'); ?></label><small class="req"> *</small>
                                        <select id="session_id" name="session_id" class="form-control">
                                            <?php
                                            if (!empty($sessionlist)) {
                                                foreach ($sessionlist as $session) {
                                                    $selected = ($session['id'] == $current_session) ? 'selected="selected"' : '';
                                                    ?>
                                                    <option value="<?php echo $session['id']; ?>" <?php echo $selected; ?>><?php echo $session['session']; ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('session_id'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('class'); ?></label><small class="req"> *</small>
                                        <select autofocus="" id="class_id" name="class_id" class="form-control" >
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                            foreach ($classlist as $class) {
                                                ?>
                                                <option value="<?php echo $class['id'] ?>" <?php if (set_value('class_id') == $class['id']) echo "selected=selected" ?>><?php echo $class['class'] ?></option>
                                                <?php
                                                $count++;
                                            }
                                            ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('class_id'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('section'); ?></label><small class="req"> *</small>
                                        <select  id="section_id" name="section_id" class="form-control" >
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('section_id'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('hostel_room'); ?></label><small class="req"> *</small>
                                        <select id="hostel_room_id" name="hostel_room_id" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('hostel_room_id'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <button type="submit" name="search" value="search_filter" class="btn btn-primary btn-sm checkbox-toggle pull-right"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div id="assign_hostel_fee_result">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var class_id = $('#class_id').val();
        var section_id = '<?php echo set_value('section_id') ?>';
        getSectionByClass(class_id, section_id);
        $(document).on('change', '#class_id', function () {
            $('#section_id').html("");
            var class_id = $(this).val();
            getSectionByClass(class_id, 0);
        });

        // Load hostel rooms
        loadHostelRooms();
        
        function getSectionByClass(class_id, section_id) {
            if (class_id != "" && class_id != 0) {
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

        function loadHostelRooms() {
            var base_url = '<?php echo base_url() ?>';
            var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
            $.ajax({
                type: "GET",
                url: base_url + "admin/hostelroom/getHostelRooms",
                dataType: "json",
                success: function (data) {
                    console.log('Hostel rooms data:', data); // Debug log
                    if (data && data.length > 0) {
                        $.each(data, function (i, obj) {
                            div_data += "<option value=" + obj.id + ">" + obj.hostel_name + " - " + obj.room_no + " (Cost: <?php echo $this->customlib->getSchoolCurrencyFormat(); ?>" + obj.cost_per_bed + ")</option>";
                        });
                    } else {
                        div_data += "<option value=''>No hostel rooms available</option>";
                    }
                    $('#hostel_room_id').html(div_data); // Use html() instead of append()
                },
                error: function(xhr, status, error) {
                    console.error('Error loading hostel rooms:', error);
                    $('#hostel_room_id').html('<option value="">Error loading rooms</option>');
                }
            });
        }
    });

    $(document).on('submit', 'form', function (e) {
        e.preventDefault();
        var $this = $(this);
        $this.find("button[type=submit]").prop('disabled', true);
        $.ajax({
            url: $this.attr('action'),
            type: "POST",
            data: $this.serialize(),
            dataType: 'html',
            success: function (data) {
                $('#assign_hostel_fee_result').html(data);
                $this.find("button[type=submit]").prop('disabled', false);
            }
        });
    });
</script>
