<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<div class="table-responsive">
    <table class="table table-striped table-bordered table-hover example">
        <thead>
            <tr>
                <th><?php echo $this->lang->line('admission_no'); ?></th>
                <th><?php echo $this->lang->line('student_name'); ?></th>
                <th><?php echo $this->lang->line('class'); ?></th>
                <th><?php echo $this->lang->line('father_name'); ?></th>
                <th><?php echo $this->lang->line('gender'); ?></th>
                <th><?php echo $this->lang->line('category'); ?></th>
                <th><?php echo $this->lang->line('mobile_no'); ?></th>
                <th class="text-right"><?php echo $this->lang->line('action'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (empty($resultlist)) {
                ?>
                <tr>
                    <td colspan="8" class="text-center"><?php echo $this->lang->line('no_record_found'); ?></td>
                </tr>
                <?php
            } else {
                $count = 1;
                foreach ($resultlist as $student) {
                    ?>
                    <tr>
                        <td><?php echo $student['admission_no']; ?></td>
                        <td>
                            <a href="<?php echo base_url(); ?>student/view/<?php echo $student['id']; ?>"><?php echo $this->customlib->getFullName($student['firstname'], $student['middlename'], $student['lastname'], $sch_setting->middlename, $sch_setting->lastname); ?>
                            </a>
                        </td>
                        <td><?php echo $student['class'] . " (" . $student['section'] . ")" ?></td>
                        <td><?php echo $student['father_name']; ?></td>
                        <td><?php echo $student['gender']; ?></td>
                        <td><?php echo $student['category']; ?></td>
                        <td><?php echo $student['mobileno']; ?></td>
                        <td class="text-right">
                            <a href="#" class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo $this->lang->line('assign_hostel_fees'); ?>" onclick="assignHostelFee('<?php echo $student['student_session_id'] ?>')">
                                <i class="fa fa-tag"></i>
                            </a>
                        </td>
                    </tr>
                    <?php
                    $count++;
                }
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Assign Hostel Fee Modal -->
<div class="modal fade" id="assignHostelFeeModal" tabindex="-1" role="dialog" aria-labelledby="assignHostelFeeModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="assignHostelFeeModalLabel"><?php echo $this->lang->line('assign_hostel_fees'); ?></h4>
            </div>
            <form id="assignHostelFeeForm" action="<?php echo site_url('admin/hostel/assignhostelfeepost') ?>" method="post">
                <div class="modal-body">
                    <input type="hidden" name="student_session_id" id="modal_student_session_id" value="">
                    <input type="hidden" name="hostel_room_id" value="<?php echo $hostel_room_id; ?>">
                    <input type="hidden" name="session_id" id="modal_session_id" value="<?php echo isset($selected_session) ? $selected_session : ''; ?>">

                    <div class="row">
                        <div class="col-md-12">
                            <h5><?php echo $this->lang->line('select_hostel_fees'); ?>:</h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th width="10%">
                                                <input type="checkbox" id="select_all_fees" />
                                            </th>
                                            <th><?php echo $this->lang->line('month'); ?></th>
                                            <th><?php echo $this->lang->line('due_date'); ?></th>
                                            <th><?php echo $this->lang->line('amount'); ?> (<?php echo $currency_symbol; ?>)</th>
                                            <th><?php echo $this->lang->line('fine_type'); ?></th>
                                            <th><?php echo $this->lang->line('fine_amount'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody id="hostel_fees_list">
                                        <?php
                                        if (!empty($hostelfees)) {
                                            foreach ($hostelfees as $fee) {
                                                ?>
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" name="hostel_fees[]" value="<?php echo $fee->id; ?>" class="hostel_fee_checkbox" />
                                                    </td>
                                                    <td><?php echo $fee->month; ?></td>
                                                    <td><?php echo $this->customlib->dateformat($fee->due_date); ?></td>
                                                    <td><?php echo $currency_symbol; ?><span class="room_cost">0.00</span></td>
                                                    <td>
                                                        <?php
                                                        if ($fee->fine_type == 'percentage') {
                                                            echo $this->lang->line('percentage') . ' (' . $fee->fine_percentage . '%)';
                                                        } elseif ($fee->fine_type == 'fix') {
                                                            echo $this->lang->line('fix_amount');
                                                        } else {
                                                            echo $this->lang->line('none');
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        if ($fee->fine_type == 'percentage') {
                                                            echo $fee->fine_percentage . '%';
                                                        } elseif ($fee->fine_type == 'fix') {
                                                            echo $currency_symbol . number_format($fee->fine_amount, 2);
                                                        } else {
                                                            echo '-';
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('assign'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    function assignHostelFee(student_session_id) {
        $('#modal_student_session_id').val(student_session_id);
        
        // Load existing hostel fees for this student
        loadExistingHostelFees(student_session_id);
        
        // Get room cost and update display
        updateRoomCost();
        
        $('#assignHostelFeeModal').modal('show');
    }

    function loadExistingHostelFees(student_session_id) {
        var hostel_room_id = <?php echo $hostel_room_id; ?>;
        var session_id = $('#modal_session_id').val(); // FIXED: Get selected session

        $.ajax({
            url: '<?php echo site_url("admin/hostel/getStudentHostelFees"); ?>',
            type: 'POST',
            data: {
                student_session_id: student_session_id,
                hostel_room_id: hostel_room_id,
                session_id: session_id
            },
            dataType: 'json',
            success: function(response) {
                // Uncheck all checkboxes first
                $('.hostel_fee_checkbox').prop('checked', false);

                // Check the assigned fees
                if (response.assigned_fees) {
                    $.each(response.assigned_fees, function(index, fee_id) {
                        $('input[name="hostel_fees[]"][value="' + fee_id + '"]').prop('checked', true);
                    });
                }
            }
        });
    }

    function updateRoomCost() {
        var hostel_room_id = <?php echo $hostel_room_id; ?>;
        
        $.ajax({
            url: '<?php echo site_url("admin/hostelroom/getRoomCost"); ?>',
            type: 'POST',
            data: {
                hostel_room_id: hostel_room_id
            },
            dataType: 'json',
            success: function(response) {
                if (response.cost_per_bed) {
                    $('.room_cost').text(parseFloat(response.cost_per_bed).toFixed(2));
                }
            }
        });
    }

    // Select all fees checkbox
    $('#select_all_fees').change(function() {
        $('.hostel_fee_checkbox').prop('checked', this.checked);
    });

    // Handle form submission
    $('#assignHostelFeeForm').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#assignHostelFeeModal').modal('hide');
                    showSuccessMessage(response.message);
                } else {
                    showErrorMessage(response.message);
                }
            },
            error: function() {
                showErrorMessage('<?php echo $this->lang->line("something_went_wrong"); ?>');
            }
        });
    });

    function showSuccessMessage(message) {
        // You can customize this based on your notification system
        alert(message);
    }

    function showErrorMessage(message) {
        // You can customize this based on your notification system
        alert(message);
    }
</script>
