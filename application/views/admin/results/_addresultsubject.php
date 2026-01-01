<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();

// Debug: Log what's in subjectsData
if (ENVIRONMENT === 'development') {
    log_message('debug', '_addresultsubject view - subjectsData: ' . print_r($subjectsData, true));
}
?>
<div id="<?php echo $delete_string;?>">
          <div class="row">




        <div class="col-md-4">
            <div class="form-group" >
                <label for="exampleInputEmail1"><?php echo $this->lang->line('subject_name'); ?></label> <small class="req"> *</small>
                <input type="hidden" name="pickup_point_id[]" value="<?php echo $subjectsData['subject_id']?>">
                <h5><?php echo $subjectsData['examtype']; ?></h5>
            </div>
        </div>


        <div class="col-md-3">
            <div class="form-group">
                <label for="exampleInputEmail1"><?php echo $this->lang->line('marks_min'); ?></label> <small class="req"> *</small>
                <input type="text" disabled  value="<?php echo isset($subjectsData['minmarks']) ? $subjectsData['minmarks'] : '0'; ?>" name="minmarks[]"  class="form-control"/>
            </div>
        </div>


        <div class="col-md-2">
            <div class="form-group">
                <label for="exampleInputEmail1"><?php echo $this->lang->line('marks_max'); ?></label> <small class="req"> *</small>
                <input disabled value="<?php echo isset($subjectsData['maxmarks']) ? $subjectsData['maxmarks'] : '0'; ?>" class="form-control" name="maxmarks[]" />
            </div>
        </div>


        <div class="col-md-2">
            <div class="form-group">
                <label for="exampleInputEmail1"><?php echo $this->lang->line('total_scored_marks'); ?> </label> <small class="req"> *</small>
                <input value="" class="form-control full-width" name="actualmarks[]" placeholder="Enter marks or AB for absent" />
                <small class="text-muted">Enter marks or type "AB" for absent</small>
            </div>
        </div>



     </div>
</div>