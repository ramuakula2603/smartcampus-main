<div class="content-wrapper" style="min-height: 946px;">
    <section class="content-header">
        <h1>
            <i class="fa fa-server"></i> Biometric Device Logs
        </h1>
    </section>

    <section class="content">
        <div class="row">
            <!-- Statistics Cards -->
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="fa fa-database"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Logs</span>
                        <span class="info-box-number"><?php echo isset($statistics['total_logs']) ? $statistics['total_logs'] : 0; ?></span>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa fa-check"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Success</span>
                        <span class="info-box-number"><?php echo isset($statistics['by_status']['success']) ? $statistics['by_status']['success'] : 0; ?></span>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="info-box">
                    <span class="info-box-icon bg-red"><i class="fa fa-times"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Errors</span>
                        <span class="info-box-number"><?php echo isset($statistics['by_status']['error']) ? $statistics['by_status']['error'] : 0; ?></span>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="fa fa-clock-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Today's Logs</span>
                        <span class="info-box-number"><?php echo isset($statistics['today_logs']) ? $statistics['today_logs'] : 0; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="box removeboxmius">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><i class="fa fa-list"></i> Device Request Logs</h3>
                    </div>

                    <!-- Filters -->
                    <div class="box-body">
                        <form method="get" action="<?php echo base_url('biometric/device_logs'); ?>" class="form-inline">
                            <div class="form-group">
                                <label>Device SN:</label>
                                <input type="text" name="device_sn" class="form-control" value="<?php echo isset($filters['device_sn']) ? $filters['device_sn'] : ''; ?>" placeholder="Device Serial Number">
                            </div>
                            <div class="form-group">
                                <label>Status:</label>
                                <select name="status" class="form-control">
                                    <option value="">All</option>
                                    <option value="success" <?php echo (isset($filters['processing_status']) && $filters['processing_status'] == 'success') ? 'selected' : ''; ?>>Success</option>
                                    <option value="error" <?php echo (isset($filters['processing_status']) && $filters['processing_status'] == 'error') ? 'selected' : ''; ?>>Error</option>
                                    <option value="pending" <?php echo (isset($filters['processing_status']) && $filters['processing_status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
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
                            <a href="<?php echo base_url('biometric/device_logs'); ?>" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i> Reset</a>
                        </form>
                    </div>

                    <!-- Logs Table -->
                    <div class="box-body table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Time</th>
                                    <th>Method</th>
                                    <th>Device SN</th>
                                    <th>IP Address</th>
                                    <th>Status</th>
                                    <th>Records</th>
                                    <th>Error</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($logs)): ?>
                                    <tr>
                                        <td colspan="9" class="text-center">No logs found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($logs as $log): ?>
                                        <tr>
                                            <td><?php echo $log->id; ?></td>
                                            <td><?php echo date('Y-m-d H:i:s', strtotime($log->created_at)); ?></td>
                                            <td>
                                                <span class="label label-<?php echo $log->request_method == 'GET' ? 'info' : 'primary'; ?>">
                                                    <?php echo $log->request_method; ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars($log->device_sn); ?></td>
                                            <td><?php echo htmlspecialchars($log->ip_address); ?></td>
                                            <td>
                                                <?php if ($log->processing_status == 'success'): ?>
                                                    <span class="label label-success">Success</span>
                                                <?php elseif ($log->processing_status == 'error'): ?>
                                                    <span class="label label-danger">Error</span>
                                                <?php else: ?>
                                                    <span class="label label-warning">Pending</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo $log->records_processed; ?></td>
                                            <td>
                                                <?php if (!empty($log->error_message)): ?>
                                                    <span class="text-danger" title="<?php echo htmlspecialchars($log->error_message); ?>">
                                                        <?php echo substr(htmlspecialchars($log->error_message), 0, 50); ?>...
                                                    </span>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-xs btn-info" onclick="viewLogDetails(<?php echo $log->id; ?>)">
                                                    <i class="fa fa-eye"></i> View
                                                </button>
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

<!-- Modal for Log Details -->
<div class="modal fade" id="logDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Log Details</h4>
            </div>
            <div class="modal-body" id="logDetailsContent">
                <div class="text-center">
                    <i class="fa fa-spinner fa-spin fa-3x"></i>
                    <p>Loading...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function viewLogDetails(logId) {
    $('#logDetailsModal').modal('show');
    $('#logDetailsContent').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-3x"></i><p>Loading...</p></div>');
    
    $.ajax({
        url: '<?php echo base_url('biometric/get_log_details/'); ?>' + logId,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var log = response.data;
                var html = '<table class="table table-bordered">';
                html += '<tr><th width="200">ID</th><td>' + log.id + '</td></tr>';
                html += '<tr><th>Created At</th><td>' + log.created_at + '</td></tr>';
                html += '<tr><th>Request Method</th><td>' + log.request_method + '</td></tr>';
                html += '<tr><th>Request URI</th><td>' + log.request_uri + '</td></tr>';
                html += '<tr><th>Device SN</th><td>' + log.device_sn + '</td></tr>';
                html += '<tr><th>IP Address</th><td>' + log.ip_address + '</td></tr>';
                html += '<tr><th>User Agent</th><td>' + log.user_agent + '</td></tr>';
                html += '<tr><th>Processing Status</th><td>' + log.processing_status + '</td></tr>';
                html += '<tr><th>Records Processed</th><td>' + log.records_processed + '</td></tr>';
                
                if (log.error_message) {
                    html += '<tr><th>Error Message</th><td class="text-danger">' + log.error_message + '</td></tr>';
                }
                
                if (log.query_string) {
                    html += '<tr><th>Query String</th><td><pre>' + log.query_string + '</pre></td></tr>';
                }
                
                if (log.raw_body) {
                    html += '<tr><th>Raw Body</th><td><pre>' + log.raw_body + '</pre></td></tr>';
                }
                
                html += '</table>';
                $('#logDetailsContent').html(html);
            } else {
                $('#logDetailsContent').html('<div class="alert alert-danger">Failed to load log details</div>');
            }
        },
        error: function() {
            $('#logDetailsContent').html('<div class="alert alert-danger">Error loading log details</div>');
        }
    });
}
</script>

