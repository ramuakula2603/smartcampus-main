<style>
    .capture-section {
        background: #f9f9f9;
        padding: 20px;
        border-radius: 8px;
        margin: 20px 0;
    }
    
    .video-container {
        position: relative;
        background: #000;
        border-radius: 8px;
        overflow: hidden;
        max-width: 640px;
        margin: 0 auto 20px;
    }
    
    #video {
        width: 100%;
        height: auto;
        display: block;
    }
    
    .video-overlay {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(0,0,0,0.7);
        color: white;
        padding: 8px 15px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
    }
    
    .captured-images {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: center;
        margin-top: 15px;
    }
    
    .image-preview {
        position: relative;
        width: 100px;
        height: 100px;
        border: 2px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
        background: #f5f5f5;
    }
    
    .image-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .image-preview .image-number {
        position: absolute;
        top: 5px;
        left: 5px;
        background: rgba(0,0,0,0.7);
        color: white;
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 11px;
    }
    
    .capture-controls {
        text-align: center;
        margin: 15px 0;
    }
    
    .btn-capture {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 6px;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .btn-capture:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
    }
    
    .btn-capture:disabled {
        background: #ccc;
        cursor: not-allowed;
        transform: none;
    }
    
    .student-list-section {
        margin-top: 30px;
    }
    
    .student-card {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        background: white;
        transition: all 0.3s ease;
    }
    
    .student-card:hover {
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }
    
    .student-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .student-images-thumb {
        display: flex;
        gap: 5px;
    }
    
    .student-images-thumb img {
        width: 40px;
        height: 40px;
        border-radius: 4px;
        object-fit: cover;
        border: 1px solid #ddd;
    }
    
    .alert-floating {
        position: fixed;
        top: 70px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        animation: slideInRight 0.3s ease;
    }
    
    @keyframes slideInRight {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-user-circle"></i> Face Attendance Registration
            <small>Register students with facial recognition</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Attendance</a></li>
            <li class="active">Face Attendance Registration</li>
        </ol>
    </section>

    <section class="content">
        <!-- Registration Form -->
        <div class="row">
            <div class="col-md-8">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-user-plus"></i> Register New Student</h3>
                    </div>
                    <form id="registrationForm" method="post">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="registration_number">Registration Number <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="registration_number" name="registration_number" required>
                                        <span class="text-danger" id="reg_error" style="display: none;">Registration number already exists</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="admission_no">Admission Number</label>
                                        <input type="text" class="form-control" id="admission_no" name="admission_no">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="first_name">First Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="first_name" name="first_name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="last_name" name="last_name" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" id="email" name="email">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">Phone</label>
                                        <input type="text" class="form-control" id="phone" name="phone">
                                    </div>
                                </div>
                            </div>

                            <!-- Face Capture Section -->
                            <div class="capture-section">
                                <h4><i class="fa fa-camera"></i> Capture Face Images (Minimum 3 Required)</h4>
                                <p class="text-muted">Please ensure good lighting and look directly at the camera</p>
                                
                                <div class="video-container" id="videoContainer" style="display: none;">
                                    <video id="video" autoplay playsinline></video>
                                    <div class="video-overlay" id="captureStatus">Ready</div>
                                </div>

                                <div class="capture-controls">
                                    <button type="button" class="btn btn-capture" id="startCaptureBtn" onclick="startCapture()">
                                        <i class="fa fa-camera"></i> Start Face Capture
                                    </button>
                                    <button type="button" class="btn btn-danger" id="stopCaptureBtn" onclick="stopCapture()" style="display: none;">
                                        <i class="fa fa-stop"></i> Stop Capture
                                    </button>
                                </div>

                                <div class="captured-images" id="capturedImages">
                                    <!-- Captured images will appear here -->
                                </div>
                            </div>
                        </div>

                        <div class="box-footer">
                            <button type="submit" class="btn btn-success btn-lg" id="submitBtn" disabled>
                                <i class="fa fa-check"></i> Register Student
                            </button>
                            <button type="button" class="btn btn-default btn-lg" onclick="resetForm()">
                                <i class="fa fa-refresh"></i> Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Registered Students List -->
            <div class="col-md-4">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-users"></i> Registered Students</h3>
                        <div class="box-tools">
                            <span class="badge bg-aqua" id="studentCount"><?php echo count($students); ?></span>
                        </div>
                    </div>
                    <div class="box-body" style="max-height: 600px; overflow-y: auto;">
                        <div id="studentsList">
                            <?php if (empty($students)): ?>
                                <p class="text-center text-muted">No students registered yet</p>
                            <?php else: ?>
                                <?php foreach ($students as $student): ?>
                                    <div class="student-card">
                                        <div class="student-info">
                                            <div>
                                                <strong><?php echo htmlspecialchars($student->first_name . ' ' . $student->last_name); ?></strong><br>
                                                <small class="text-muted"><?php echo htmlspecialchars($student->registration_number); ?></small>
                                            </div>
                                            <button class="btn btn-sm btn-danger" onclick="deleteStudent(<?php echo $student->id; ?>)">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                        <div class="student-images-thumb" style="margin-top: 10px;">
                                            <?php 
                                            $images = json_decode($student->face_images);
                                            if ($images) {
                                                foreach (array_slice($images, 0, 3) as $img) {
                                                    echo '<img src="' . base_url('uploads/face_attendance_images/' . $student->registration_number . '/' . $img) . '" alt="Face">';
                                                }
                                            }
                                            ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
let capturedImages = [];
let currentCaptureIndex = 0;
let videoStream = null;
let captureInterval = null;
const baseUrl = '<?php echo base_url(); ?>';

// Check registration number uniqueness
$('#registration_number').on('blur', function() {
    const regNumber = $(this).val();
    if (regNumber) {
        $.post(baseUrl + 'admin/face_attendance_register/check_registration', {
            registration_number: regNumber
        }, function(response) {
            if (response.exists) {
                $('#reg_error').show();
                $('#registration_number').addClass('has-error');
            } else {
                $('#reg_error').hide();
                $('#registration_number').removeClass('has-error');
            }
        }, 'json');
    }
});

async function startCapture() {
    try {
        const video = document.getElementById('video');
        const videoContainer = document.getElementById('videoContainer');
        const startBtn = document.getElementById('startCaptureBtn');
        const stopBtn = document.getElementById('stopCaptureBtn');
        const statusOverlay = document.getElementById('captureStatus');

        // Request camera access
        videoStream = await navigator.mediaDevices.getUserMedia({ 
            video: { width: 640, height: 480, facingMode: 'user' } 
        });
        video.srcObject = videoStream;

        // Wait for video to be ready
        await new Promise(resolve => {
            video.onloadedmetadata = () => {
                video.play();
                resolve();
            };
        });

        videoContainer.style.display = 'block';
        startBtn.style.display = 'none';
        stopBtn.style.display = 'inline-block';

        // Reset captured images
        capturedImages = [];
        currentCaptureIndex = 0;
        document.getElementById('capturedImages').innerHTML = '';

        statusOverlay.textContent = 'Capturing in 2 seconds...';

        // Start auto-capture after 2 seconds
        setTimeout(() => {
            captureInterval = setInterval(captureImage, 1000);
        }, 2000);

    } catch (error) {
        alert('Unable to access camera. Please ensure camera permissions are granted.\n\nError: ' + error.message);
        console.error('Camera error:', error);
    }
}

function captureImage() {
    if (currentCaptureIndex >= 5) {
        stopCapture();
        return;
    }

    const video = document.getElementById('video');
    const canvas = document.createElement('canvas');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    const context = canvas.getContext('2d');
    context.drawImage(video, 0, 0);

    const imageData = canvas.toDataURL('image/png');
    capturedImages.push(imageData);

    // Display captured image
    displayCapturedImage(imageData, currentCaptureIndex + 1);

    currentCaptureIndex++;
    document.getElementById('captureStatus').textContent = `Captured ${currentCaptureIndex}/5`;

    // Enable submit button if we have at least 3 images
    if (currentCaptureIndex >= 3) {
        document.getElementById('submitBtn').disabled = false;
    }
}

function displayCapturedImage(imageData, index) {
    const container = document.getElementById('capturedImages');
    const imageBox = document.createElement('div');
    imageBox.className = 'image-preview';
    imageBox.id = `preview-${index}`;

    const img = document.createElement('img');
    img.src = imageData;

    const numberLabel = document.createElement('div');
    numberLabel.className = 'image-number';
    numberLabel.textContent = `Image ${index}`;

    imageBox.appendChild(img);
    imageBox.appendChild(numberLabel);
    container.appendChild(imageBox);
}

function stopCapture() {
    const video = document.getElementById('video');
    const videoContainer = document.getElementById('videoContainer');
    const startBtn = document.getElementById('startCaptureBtn');
    const stopBtn = document.getElementById('stopCaptureBtn');

    if (captureInterval) {
        clearInterval(captureInterval);
        captureInterval = null;
    }

    if (videoStream) {
        videoStream.getTracks().forEach(track => track.stop());
        videoStream = null;
    }

    videoContainer.style.display = 'none';
    startBtn.style.display = 'inline-block';
    stopBtn.style.display = 'none';
}

// Form submission
$('#registrationForm').on('submit', function(e) {
    e.preventDefault();

    if (capturedImages.length < 3) {
        showAlert('error', 'Please capture at least 3 face images');
        return;
    }

    const formData = {
        registration_number: $('#registration_number').val(),
        admission_no: $('#admission_no').val(),
        first_name: $('#first_name').val(),
        last_name: $('#last_name').val(),
        email: $('#email').val(),
        phone: $('#phone').val()
    };

    // Add captured images
    capturedImages.forEach((img, index) => {
        formData[`captured_image_${index + 1}`] = img;
    });

    $.ajax({
        url: baseUrl + 'admin/face_attendance_register/register_student',
        type: 'POST',
        data: formData,
        dataType: 'json',
        beforeSend: function() {
            $('#submitBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Registering...');
        },
        success: function(response) {
            if (response.status === 'success') {
                showAlert('success', response.message);
                resetForm();
                // Reload page after 2 seconds
                setTimeout(function() {
                    location.reload();
                }, 2000);
            } else {
                showAlert('error', response.message);
                $('#submitBtn').prop('disabled', false).html('<i class="fa fa-check"></i> Register Student');
            }
        },
        error: function(xhr, status, error) {
            showAlert('error', 'An error occurred. Please try again.');
            $('#submitBtn').prop('disabled', false).html('<i class="fa fa-check"></i> Register Student');
            console.error('Error:', error);
        }
    });
});

function resetForm() {
    $('#registrationForm')[0].reset();
    capturedImages = [];
    currentCaptureIndex = 0;
    document.getElementById('capturedImages').innerHTML = '';
    document.getElementById('submitBtn').disabled = true;
    stopCapture();
    $('#reg_error').hide();
    $('#registration_number').removeClass('has-error');
}

function deleteStudent(studentId) {
    if (!confirm('Are you sure you want to delete this student? This will remove all face data.')) {
        return;
    }

    $.post(baseUrl + 'admin/face_attendance_register/delete_student', {
        student_id: studentId
    }, function(response) {
        if (response.status === 'success') {
            showAlert('success', response.message);
            setTimeout(function() {
                location.reload();
            }, 1500);
        } else {
            showAlert('error', response.message);
        }
    }, 'json');
}

function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const iconClass = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
    
    const alertHtml = `
        <div class="alert ${alertClass} alert-floating alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa ${iconClass}"></i> ${type === 'success' ? 'Success' : 'Error'}!</h4>
            ${message}
        </div>
    `;
    
    $('body').append(alertHtml);
    
    setTimeout(function() {
        $('.alert-floating').fadeOut('slow', function() {
            $(this).remove();
        });
    }, 5000);
}

// Clean up on page unload
window.addEventListener('beforeunload', function() {
    stopCapture();
});
</script>
