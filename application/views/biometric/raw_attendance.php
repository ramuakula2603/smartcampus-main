<div class="content-wrapper" style="min-height: 946px;">
    <section class="content-header">
        <h1>
            <i class="fa fa-database"></i> Biometric Raw Attendance Data
        </h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box removeboxmius">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><i class="fa fa-list"></i> Raw Attendance Records</h3>
                    </div>

                    <!-- Filters -->
                    <div class="box-body">
                        <form method="get" action="<?php echo base_url('biometric/raw_attendance'); ?>" class="form-inline">
                            <div class="form-group">
                                <label>Device SN:</label>
                                <input type="text" name="device_sn" class="form-control" value="<?php echo isset($filters['device_sn']) ? $filters['device_sn'] : ''; ?>" placeholder="Device Serial Number">
                            </div>
                            <div class="form-group">
                                <label>Employee ID:</label>
                                <input type="text" name="employee_id" class="form-control" value="<?php echo isset($filters['employee_id']) ? $filters['employee_id'] : ''; ?>" placeholder="PIN/Employee ID">
                            </div>
                            <div class="form-group">
                                <label>Status:</label>
                                <select name="processed" class="form-control">
                                    <option value="">All</option>
                                    <option value="1" <?php echo (isset($filters['processed']) && $filters['processed'] == '1') ? 'selected' : ''; ?>>Processed</option>
                                    <option value="0" <?php echo (isset($filters['processed']) && $filters['processed'] == '0') ? 'selected' : ''; ?>>Unprocessed</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>From:</label>
                                <input type="date" name="date_from" class="form-control" value="<?php echo isset($filters['date_from']) ? $filters['date_from'] : ''; ?>">
                            </div>
                            <div class="form-group">
                                <label>To:</label>
                                <input type="date" name="date_to" class="form-control" value="<?php echo isset($filters['date_to']) ? $filters['date_to'] : ''; ?>">
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-search"></i> Filter</button>
                            <a href="<?php echo base_url('biometric/raw_attendance'); ?>" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i> Reset</a>
                        </form>
                    </div>

                    <!-- Attendance Table -->
                    <div class="box-body table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Created At</th>
                                    <th>Device SN</th>
                                    <th>Employee ID (PIN)</th>
                                    <th>Punch Time</th>
                                    <th>Status Fields</th>
                                    <th>Processed</th>
                                    <th>Processed At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($attendance)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No attendance records found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($attendance as $record): ?>
                                        <tr>
                                            <td><?php echo $record->id; ?></td>
                                            <td><?php echo date('Y-m-d H:i:s', strtotime($record->created_at)); ?></td>
                                            <td><?php echo htmlspecialchars($record->device_sn); ?></td>
                                            <td><strong><?php echo htmlspecialchars($record->employee_id); ?></strong></td>
                                            <td><?php echo $record->punch_time; ?></td>
                                            <td>
                                                <small>
                                                    S1:<?php echo $record->status1 !== null ? $record->status1 : '-'; ?>,
                                                    S2:<?php echo $record->status2 !== null ? $record->status2 : '-'; ?>,
                                                    S3:<?php echo $record->status3 !== null ? $record->status3 : '-'; ?>,
                                                    S4:<?php echo $record->status4 !== null ? $record->status4 : '-'; ?>,
                                                    S5:<?php echo $record->status5 !== null ? $record->status5 : '-'; ?>
                                                </small>
                                            </td>
                                            <td>
                                                <?php if ($record->processed == 1): ?>
                                                    <span class="label label-success"><i class="fa fa-check"></i> Yes</span>
                                                <?php else: ?>
                                                    <span class="label label-warning"><i class="fa fa-clock-o"></i> No</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php echo $record->processed_at ? date('Y-m-d H:i:s', strtotime($record->processed_at)) : '-'; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="box-footer">
                        <div class="row">
                            <div class="col-md-12">
                                <?php echo $this->pagination->create_links(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

