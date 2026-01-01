<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-camera"></i> <?php echo $this->lang->line('face_attendance_mark_attendance'); ?>
        </h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo $this->lang->line('face_attendance_mark_attendance'); ?></h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        </div>
                    </div>

                    <div class="box-body">
                        <!-- Alert Messages -->
                        <div id="alertContainer"></div>

                        <!-- Control Buttons -->
                        <div class="row mb-3">
                            <div class="col-md-12 text-center">
                                <button id="startBtn" class="btn btn-lg btn-primary">
                                    <i class="fa fa-play"></i> Start Face Recognition
                                </button>
                                <button id="stopBtn" class="btn btn-lg btn-danger" style="display: none;">
                                    <i class="fa fa-stop"></i> Stop Recognition
                                </button>
                                <button id="saveBtn" class="btn btn-lg btn-success" style="display: none;">
                                    <i class="fa fa-save"></i> Save Attendance
                                </button>
                            </div>
                        </div>

                        <!-- Statistics -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="info-box bg-aqua">
                                    <span class="info-box-icon"><i class="fa fa-users"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Students</span>
                                        <span class="info-box-number" id="totalCount"><?php echo count($students); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box bg-green">
                                    <span class="info-box-icon"><i class="fa fa-check"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Present</span>
                                        <span class="info-box-number" id="presentCount">0</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box bg-red">
                                    <span class="info-box-icon"><i class="fa fa-times"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Absent</span>
                                        <span class="info-box-number" id="absentCount"><?php echo count($students); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Main Content -->
                        <div class="row">
                            <!-- Video Section -->
                            <div class="col-md-6">
                                <div class="box box-solid">
                                    <div class="box-header">
                                        <h3 class="box-title"><i class="fa fa-video-camera"></i> Camera Feed</h3>
                                    </div>
                                    <div class="box-body">
                                        <div class="video-container-wrapper">
                                            <div class="video-container">
                                                <video id="video" autoplay playsinline></video>
                                                <canvas id="overlay"></canvas>
                                                <div class="face-overlay" id="faceOverlay"></div>
                                                <div class="recognition-status" id="recognitionStatus">
                                                    <span class="status-indicator processing" id="statusIndicator"></span>
                                                    <span id="statusText">Initializing...</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Student List Section -->
                            <div class="col-md-6">
                                <div class="box box-solid">
                                    <div class="box-header">
                                        <h3 class="box-title"><i class="fa fa-list"></i> Student List</h3>
                                        <div class="box-tools pull-right">
                                            <span class="label label-default">Date: <?php echo date('d M Y'); ?></span>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="student-list" id="studentList">
                                            <?php if (!empty($students)): ?>
                                                <?php foreach ($students as $student): ?>
                                                    <div class="student-item" id="student-<?php echo $student->registration_number; ?>" data-student-id="<?php echo $student->id; ?>">
                                                        <div class="student-info">
                                                            <div class="student-name">
                                                                <?php echo htmlspecialchars($student->first_name . ' ' . $student->last_name); ?>
                                                            </div>
                                                            <div class="student-reg">
                                                                Reg: <?php echo htmlspecialchars($student->registration_number); ?>
                                                                <?php if (!empty($student->admission_no)): ?>
                                                                    | Adm: <?php echo htmlspecialchars($student->admission_no); ?>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                        <span class="status-badge status-absent" id="status-<?php echo $student->registration_number; ?>">Absent</span>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <div class="alert alert-info">
                                                    <i class="fa fa-info-circle"></i> No students registered yet. Please register students first.
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Include Face-API.js -->
<script defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>

<style>
    .mb-3 {
        margin-bottom: 20px;
    }
    
    .video-container-wrapper {
        position: relative;
        background: #1a1a1a;
        border-radius: 5px;
        overflow: hidden;
    }
    
    .video-container {
        position: relative;
        width: 100%;
        max-width: 640px;
        margin: 0 auto;
    }
    
    #video {
        width: 100%;
        height: auto;
        display: block;
        object-fit: cover;
        max-height: 480px;
    }
    
    #overlay {
        position: absolute;
        top: 0;
        left: 0;
        pointer-events: none;
        width: 100%;
        height: 100%;
    }
    
    .face-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 10;
    }
    
    .face-box {
        position: absolute;
        border: 3px solid;
        border-radius: 8px;
        transition: all 0.2s ease;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
    }
    
    .face-box.processing {
        border-color: #ffc107;
        background: rgba(255, 193, 7, 0.1);
        animation: pulse 1.5s infinite;
    }
    
    .face-box.recognized {
        border-color: #00a65a;
        background: rgba(0, 166, 90, 0.1);
        box-shadow: 0 0 15px rgba(0, 166, 90, 0.5);
    }
    
    .face-box.unknown {
        border-color: #dd4b39;
        background: rgba(221, 75, 57, 0.1);
    }
    
    .face-label {
        position: absolute;
        top: -30px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
        min-width: 100px;
        text-align: center;
    }
    
    .face-label.processing {
        background: rgba(255, 193, 7, 0.9);
    }
    
    .face-label.recognized {
        background: rgba(0, 166, 90, 0.9);
    }
    
    .face-label.unknown {
        background: rgba(221, 75, 57, 0.9);
    }
    
    .confidence-score {
        font-size: 10px;
        opacity: 0.8;
        display: block;
        margin-top: 2px;
    }
    
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.6; }
        100% { opacity: 1; }
    }
    
    .recognition-status {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 10px 15px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 600;
        z-index: 20;
    }
    
    .status-indicator {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        margin-right: 8px;
        animation: blink 2s infinite;
    }
    
    .status-indicator.active {
        background: #00a65a;
    }
    
    .status-indicator.processing {
        background: #ffc107;
    }
    
    @keyframes blink {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.3; }
    }
    
    .student-list {
        max-height: 450px;
        overflow-y: auto;
        padding: 10px;
    }
    
    .student-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 15px;
        margin-bottom: 10px;
        background: #f9f9f9;
        border-radius: 5px;
        border: 1px solid #ddd;
        transition: all 0.3s ease;
    }
    
    .student-item:hover {
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    }
    
    .student-item.present {
        background: #d4edda;
        border-color: #c3e6cb;
    }
    
    .student-info {
        flex: 1;
    }
    
    .student-name {
        font-weight: 600;
        color: #333;
        font-size: 15px;
    }
    
    .student-reg {
        font-size: 13px;
        color: #666;
        margin-top: 3px;
    }
    
    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .status-present {
        background: #00a65a;
        color: white;
    }
    
    .status-absent {
        background: #dd4b39;
        color: white;
    }
</style>

<script>
    let students = [];
    let recognizedStudents = new Map(); // Store recognized students with confidence
    let isRecognizing = false;
    let videoStream = null;
    let faceDetectionInterval = null;
    let faceMatcher = null;
    
    // DOM elements
    const video = document.getElementById('video');
    const overlay = document.getElementById('overlay');
    const faceOverlay = document.getElementById('faceOverlay');
    const recognitionStatus = document.getElementById('recognitionStatus');
    const statusIndicator = document.getElementById('statusIndicator');
    const statusText = document.getElementById('statusText');
    const startBtn = document.getElementById('startBtn');
    const stopBtn = document.getElementById('stopBtn');
    const saveBtn = document.getElementById('saveBtn');
    const presentCount = document.getElementById('presentCount');
    const absentCount = document.getElementById('absentCount');
    const alertContainer = document.getElementById('alertContainer');
    
    // Event listeners
    startBtn.addEventListener('click', startFaceRecognition);
    stopBtn.addEventListener('click', stopFaceRecognition);
    saveBtn.addEventListener('click', saveAttendance);
    
    // Load students on page load
    loadStudents();
    
    function loadStudents() {
        $.ajax({
            url: '<?php echo base_url("admin/face_attendance_register/get_registered_students"); ?>',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    students = response.students;
                    console.log('Loaded ' + students.length + ' students');
                } else {
                    showAlert('Error loading students', 'danger');
                }
            },
            error: function() {
                showAlert('Error loading students', 'danger');
            }
        });
    }
    
    function showAlert(message, type) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <i class="fa fa-${type === 'success' ? 'check' : 'exclamation-triangle'}"></i> ${message}
            </div>
        `;
        alertContainer.innerHTML = alertHtml;
        
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    }
    
    function updateRecognitionStatus(status, text) {
        statusIndicator.className = `status-indicator ${status}`;
        statusText.textContent = text;
    }
    
    function createFaceOverlay(detection, result) {
        const box = detection.detection.box;
        const videoRect = video.getBoundingClientRect();
        
        const scaleX = videoRect.width / video.videoWidth;
        const scaleY = videoRect.height / video.videoHeight;
        
        const faceBox = document.createElement('div');
        faceBox.className = 'face-box';
        
        faceBox.style.left = `${box.x * scaleX}px`;
        faceBox.style.top = `${box.y * scaleY}px`;
        faceBox.style.width = `${box.width * scaleX}px`;
        faceBox.style.height = `${box.height * scaleY}px`;
        
        const label = document.createElement('div');
        label.className = 'face-label';
        
        if (result && result.distance < 0.6) {
            faceBox.classList.add('recognized');
            label.classList.add('recognized');
            
            const student = students.find(s => s.registration_number === result.label);
            const studentName = student ? `${student.first_name} ${student.last_name}` : result.label;
            const confidence = Math.round((1 - result.distance) * 100);
            
            label.innerHTML = `
                ${studentName}
                <span class="confidence-score">${confidence}% match</span>
            `;
            
            updateStudentAttendance(result.label, 'Present', confidence, student);
        } else {
            faceBox.classList.add('unknown');
            label.classList.add('unknown');
            label.textContent = result ? 'Unknown Face' : 'Processing...';
        }
        
        faceBox.appendChild(label);
        return faceBox;
    }
    
    function clearFaceOverlays() {
        faceOverlay.innerHTML = '';
    }
    
    function updateStudentAttendance(registrationNumber, status, confidence, studentData) {
        if (status === 'Present') {
            if (!recognizedStudents.has(registrationNumber)) {
                recognizedStudents.set(registrationNumber, {
                    registration_number: registrationNumber,
                    student_id: studentData.id,
                    confidence: confidence,
                    class_id: studentData.class_id,
                    section_id: studentData.section_id,
                    status: 'Present',
                    timestamp: new Date().toISOString()
                });
                
                const studentElement = document.getElementById(`student-${registrationNumber}`);
                const statusElement = document.getElementById(`status-${registrationNumber}`);
                
                if (studentElement && statusElement) {
                    studentElement.classList.add('present');
                    statusElement.textContent = 'Present';
                    statusElement.className = 'status-badge status-present';
                    
                    studentElement.style.animation = 'pulse 0.5s ease-in-out';
                    setTimeout(() => {
                        studentElement.style.animation = '';
                    }, 500);
                }
                
                updateStats();
            }
        }
    }
    
    async function startFaceRecognition() {
        if (students.length === 0) {
            showAlert('No students registered. Please register students first.', 'warning');
            return;
        }
        
        try {
            updateRecognitionStatus('processing', 'Loading models...');
            
            // Load models
            const MODEL_URL = '<?php echo base_url("assets/face_attendance_models"); ?>';
            await Promise.all([
                faceapi.nets.ssdMobilenetv1.loadFromUri(MODEL_URL),
                faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL),
                faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL)
            ]);
            
            updateRecognitionStatus('processing', 'Starting camera...');
            
            videoStream = await navigator.mediaDevices.getUserMedia({
                video: { 
                    width: { ideal: 640 },
                    height: { ideal: 480 },
                    facingMode: 'user'
                }
            });
            video.srcObject = videoStream;
            
            await new Promise(resolve => {
                video.onloadedmetadata = () => {
                    video.play();
                    resolve();
                };
            });
            
            updateRecognitionStatus('processing', 'Loading face descriptors...');
            
            const labeledDescriptors = await loadFaceDescriptors();
            if (labeledDescriptors.length === 0) {
                updateRecognitionStatus('', 'No student images found');
                showAlert('No student images found. Please ensure students have face images registered.', 'warning');
                stopFaceRecognition();
                return;
            }
            
            faceMatcher = new faceapi.FaceMatcher(labeledDescriptors, 0.6);
            
            const canvas = faceapi.createCanvasFromMedia(video);
            const videoContainer = document.querySelector('.video-container');
            videoContainer.appendChild(canvas);
            
            const displaySize = { 
                width: video.videoWidth, 
                height: video.videoHeight 
            };
            faceapi.matchDimensions(canvas, displaySize);
            
            canvas.style.position = 'absolute';
            canvas.style.top = '0';
            canvas.style.left = '0';
            canvas.style.width = '100%';
            canvas.style.height = '100%';
            
            isRecognizing = true;
            startBtn.style.display = 'none';
            stopBtn.style.display = 'inline-block';
            
            updateRecognitionStatus('active', 'Face Recognition Active');
            showAlert('Face recognition started successfully!', 'success');
            
            recognizeFaces(canvas, displaySize);
            
        } catch (error) {
            console.error('Error starting face recognition:', error);
            updateRecognitionStatus('', 'Error: ' + error.message);
            showAlert('Error starting face recognition: ' + error.message, 'danger');
            stopFaceRecognition();
        }
    }
    
    async function loadFaceDescriptors() {
        const labeledDescriptors = [];
        
        for (const student of students) {
            const descriptions = [];
            
            if (student.face_images && student.face_images.length > 0) {
                for (const imageUrl of student.face_images) {
                    try {
                        const img = await faceapi.fetchImage(imageUrl);
                        const detection = await faceapi
                            .detectSingleFace(img)
                            .withFaceLandmarks()
                            .withFaceDescriptor();
                        
                        if (detection) {
                            descriptions.push(detection.descriptor);
                        }
                    } catch (error) {
                        console.log(`Error loading image for ${student.registration_number}:`, error);
                    }
                }
            }
            
            if (descriptions.length > 0) {
                labeledDescriptors.push(
                    new faceapi.LabeledFaceDescriptors(student.registration_number, descriptions)
                );
            }
        }
        
        return labeledDescriptors;
    }
    
    async function recognizeFaces(canvas, displaySize) {
        if (!isRecognizing) return;
        
        try {
            const detections = await faceapi
                .detectAllFaces(video)
                .withFaceLandmarks()
                .withFaceDescriptors();
            
            const resizedDetections = faceapi.resizeResults(detections, displaySize);
            
            canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
            clearFaceOverlays();
            
            const results = resizedDetections.map(d => {
                return faceMatcher.findBestMatch(d.descriptor);
            });
            
            resizedDetections.forEach((detection, i) => {
                const result = results[i];
                const faceOverlayElement = createFaceOverlay(detection, result);
                faceOverlay.appendChild(faceOverlayElement);
            });
            
            const hasRecognitions = results.some(r => r && r.distance < 0.6);
            if (hasRecognitions) {
                updateRecognitionStatus('active', 'Face Recognition Active');
            } else if (detections.length > 0) {
                updateRecognitionStatus('processing', 'Processing faces...');
            }
            
        } catch (error) {
            console.error('Error in face recognition:', error);
            updateRecognitionStatus('', 'Recognition error');
        }
        
        faceDetectionInterval = setTimeout(() => recognizeFaces(canvas, displaySize), 150);
    }
    
    function updateStats() {
        const present = recognizedStudents.size;
        const total = students.length;
        const absent = total - present;
        
        presentCount.textContent = present;
        absentCount.textContent = absent;
    }
    
    function stopFaceRecognition() {
        isRecognizing = false;
        
        if (faceDetectionInterval) {
            clearTimeout(faceDetectionInterval);
            faceDetectionInterval = null;
        }
        
        if (videoStream) {
            videoStream.getTracks().forEach(track => track.stop());
            videoStream = null;
        }
        
        clearFaceOverlays();
        
        const canvas = document.querySelector('.video-container canvas');
        if (canvas) {
            canvas.remove();
        }
        
        updateRecognitionStatus('', 'Recognition stopped');
        
        stopBtn.style.display = 'none';
        
        if (recognizedStudents.size > 0) {
            saveBtn.style.display = 'inline-block';
            showAlert(`Recognition stopped. ${recognizedStudents.size} student(s) marked present. Click "Save Attendance" to save.`, 'info');
        } else {
            startBtn.style.display = 'inline-block';
            showAlert('Recognition stopped. No students were recognized.', 'warning');
        }
    }
    
    function saveAttendance() {
        if (recognizedStudents.size === 0) {
            showAlert('No attendance to save', 'warning');
            return;
        }
        
        const attendanceData = [];
        
        // Add recognized students as present
        recognizedStudents.forEach((data, regNumber) => {
            attendanceData.push(data);
        });
        
        // Add remaining students as absent
        students.forEach(student => {
            if (!recognizedStudents.has(student.registration_number)) {
                attendanceData.push({
                    registration_number: student.registration_number,
                    student_id: student.id,
                    class_id: student.class_id,
                    section_id: student.section_id,
                    status: 'Absent',
                    confidence: 0
                });
            }
        });
        
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving...';
        
        $.ajax({
            url: '<?php echo base_url("admin/face_attendance_register/save_attendance"); ?>',
            type: 'POST',
            data: {
                attendance_data: JSON.stringify(attendanceData),
                detected_faces: recognizedStudents.size
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    showAlert(response.message, 'success');
                    
                    // Reset for new session
                    recognizedStudents.clear();
                    updateStats();
                    
                    // Reset all students to absent
                    $('.student-item').removeClass('present');
                    $('.status-badge').removeClass('status-present').addClass('status-absent').text('Absent');
                    
                    saveBtn.style.display = 'none';
                    startBtn.style.display = 'inline-block';
                    
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                } else {
                    showAlert('Error: ' + response.message, 'danger');
                }
            },
            error: function() {
                showAlert('Error saving attendance', 'danger');
            },
            complete: function() {
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<i class="fa fa-save"></i> Save Attendance';
            }
        });
    }
</script>
