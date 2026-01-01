<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Externalresult_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('setting_model'); // Added for API compatibility
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    /**
     * Get external results report based on filters
     * @param array $filters - Array containing session_id, exam_type_id, class_id, section_id, status
     * @return array - Results data grouped by student
     */
    public function getExternalResultsReport($filters = array())
    {
        try {
            // Build the query with joins
            $this->db->select('
                students.id as student_id,
                students.firstname,
                students.middlename,
                students.lastname,
                students.admission_no,
                students.mobileno,
                students.email,
                classes.class,
                sections.section,
                sessions.id as session_id,
                sessions.session,
                publicexamtype.id as exam_type_id,
                publicexamtype.examtype as exam_name,
                resultsubjects.examtype as subject_name,
                resultsubjects.subject_code,
                publicresultsubject_group_subjects.minmarks,
                publicresultsubject_group_subjects.maxmarks,
                publicresulttable.actualmarks,
                publicresulttable.id as result_id
            ');
            
            $this->db->from('publicresulttable');
            $this->db->join('students', 'students.id = publicresulttable.stid');
            $this->db->join('student_session', 'student_session.student_id = students.id');
            $this->db->join('classes', 'classes.id = student_session.class_id');
            $this->db->join('sections', 'sections.id = student_session.section_id');
            $this->db->join('sessions', 'sessions.id = publicresulttable.session_id');
            $this->db->join('publicexamtype', 'publicexamtype.id = publicresulttable.resulgroup_id');
            $this->db->join('resultsubjects', 'resultsubjects.id = publicresulttable.subjectid');
            $this->db->join('publicresultsubject_group_subjects', 'publicresultsubject_group_subjects.id = publicresulttable.markstableid');

            // Apply filters
            if (!empty($filters['session_id']) && is_array($filters['session_id'])) {
                $this->db->where_in('publicresulttable.session_id', $filters['session_id']);
            }

            if (!empty($filters['exam_type_id']) && is_array($filters['exam_type_id'])) {
                $this->db->where_in('publicresulttable.resulgroup_id', $filters['exam_type_id']);
            }

            if (!empty($filters['class_id']) && is_array($filters['class_id'])) {
                $this->db->where_in('student_session.class_id', $filters['class_id']);
            }

            if (!empty($filters['section_id']) && is_array($filters['section_id'])) {
                $this->db->where_in('student_session.section_id', $filters['section_id']);
            }

            // Order by student, session, exam type
            $this->db->order_by('students.admission_no', 'ASC');
            $this->db->order_by('sessions.id', 'DESC');
            $this->db->order_by('publicexamtype.examtype', 'ASC');
            $this->db->order_by('resultsubjects.examtype', 'ASC');

            $query = $this->db->get();
            $results = $query->result_array();

            if (empty($results)) {
                return array();
            }

            // Organize results by student, session, and exam type
            $organized_results = array();

            foreach ($results as $row) {
                $student_id = $row['student_id'];
                $session_id = $row['session_id'];
                $exam_id = $row['exam_type_id'];

                // Initialize student if not exists
                if (!isset($organized_results[$student_id])) {
                    $organized_results[$student_id] = array(
                        'student_id' => $student_id,
                        'student_name' => $row['firstname'] . ' ' . ($row['middlename'] ? $row['middlename'] . ' ' : '') . $row['lastname'],
                        'admission_no' => $row['admission_no'],
                        'class' => $row['class'],
                        'section' => $row['section'],
                        'mobileno' => $row['mobileno'],
                        'email' => $row['email'],
                        'sessions' => array()
                    );
                }

                // Initialize session if not exists
                if (!isset($organized_results[$student_id]['sessions'][$session_id])) {
                    $organized_results[$student_id]['sessions'][$session_id] = array(
                        'session_id' => $session_id,
                        'session_name' => $row['session'],
                        'exams' => array()
                    );
                }

                // Initialize exam if not exists
                if (!isset($organized_results[$student_id]['sessions'][$session_id]['exams'][$exam_id])) {
                    $organized_results[$student_id]['sessions'][$session_id]['exams'][$exam_id] = array(
                        'exam_id' => $exam_id,
                        'exam_name' => $row['exam_name'],
                        'subjects' => array(),
                        'total_marks' => 0,
                        'total_max_marks' => 0,
                        'total_min_marks' => 0,
                        'pass_status' => true,
                        'has_absent' => false
                    );
                }

                // Add subject data
                $is_absent = (strtoupper(trim($row['actualmarks'])) === 'AB');
                
                $subject_data = array(
                    'subject_name' => $row['subject_name'],
                    'subject_code' => $row['subject_code'],
                    'minmarks' => $row['minmarks'],
                    'maxmarks' => $row['maxmarks'],
                    'actualmarks' => $row['actualmarks'],
                    'is_absent' => $is_absent,
                    'pass' => false
                );

                if ($is_absent) {
                    $organized_results[$student_id]['sessions'][$session_id]['exams'][$exam_id]['has_absent'] = true;
                    $organized_results[$student_id]['sessions'][$session_id]['exams'][$exam_id]['pass_status'] = false;
                } else {
                    $marks = (int)$row['actualmarks'];
                    $subject_data['pass'] = ($marks >= (int)$row['minmarks']);
                    
                    if (!$subject_data['pass']) {
                        $organized_results[$student_id]['sessions'][$session_id]['exams'][$exam_id]['pass_status'] = false;
                    }

                    $organized_results[$student_id]['sessions'][$session_id]['exams'][$exam_id]['total_marks'] += $marks;
                }

                $organized_results[$student_id]['sessions'][$session_id]['exams'][$exam_id]['total_max_marks'] += (int)$row['maxmarks'];
                $organized_results[$student_id]['sessions'][$session_id]['exams'][$exam_id]['total_min_marks'] += (int)$row['minmarks'];
                $organized_results[$student_id]['sessions'][$session_id]['exams'][$exam_id]['subjects'][] = $subject_data;
            }

            // Calculate percentages and filter by status if needed
            $final_results = array();

            foreach ($organized_results as $student_id => $student_data) {
                foreach ($student_data['sessions'] as $session_id => $session_data) {
                    foreach ($session_data['exams'] as $exam_id => $exam_data) {
                        // Calculate percentage
                        if ($exam_data['has_absent']) {
                            $exam_data['percentage'] = 'AB';
                            $exam_data['pass_status'] = 'absent';
                        } else {
                            if ($exam_data['total_max_marks'] > 0) {
                                $exam_data['percentage'] = round(($exam_data['total_marks'] / $exam_data['total_max_marks']) * 100, 2);
                            } else {
                                $exam_data['percentage'] = 0;
                            }
                        }

                        $organized_results[$student_id]['sessions'][$session_id]['exams'][$exam_id] = $exam_data;

                        // Apply status filter
                        if (!empty($filters['status'])) {
                            $status_filter = $filters['status'];
                            
                            if ($status_filter === 'pass' && $exam_data['pass_status'] === true) {
                                $final_results[$student_id] = $organized_results[$student_id];
                            } elseif ($status_filter === 'fail' && $exam_data['pass_status'] === false && $exam_data['pass_status'] !== 'absent') {
                                $final_results[$student_id] = $organized_results[$student_id];
                            } elseif ($status_filter === 'absent' && $exam_data['pass_status'] === 'absent') {
                                $final_results[$student_id] = $organized_results[$student_id];
                            } elseif ($status_filter === 'all') {
                                $final_results[$student_id] = $organized_results[$student_id];
                            }
                        } else {
                            // No status filter, include all
                            $final_results[$student_id] = $organized_results[$student_id];
                        }
                    }
                }
            }

            return array_values(!empty($filters['status']) ? $final_results : $organized_results);

        } catch (Exception $e) {
            log_message('error', 'Error fetching external results report: ' . $e->getMessage());
            return array();
        }
    }

    /**
     * Get all exam types for a specific session
     * @param int $session_id - Session ID (optional, if null gets all active exam types)
     * @return array - List of exam types
     */
    public function getExamTypes($session_id = null)
    {
        $this->db->select('id, examtype, session_id');
        $this->db->from('publicexamtype');
        
        if ($session_id !== null && is_array($session_id)) {
            $this->db->where_in('session_id', $session_id);
        } elseif ($session_id !== null) {
            $this->db->where('session_id', $session_id);
        }
        
        $this->db->order_by('examtype', 'ASC');
        $query = $this->db->get();
        
        return $query->result_array();
    }

    /**
     * Get distinct sessions that have external results
     * @return array - List of sessions
     */
    public function getSessionsWithResults()
    {
        $this->db->distinct();
        $this->db->select('sessions.id, sessions.session');
        $this->db->from('publicresulttable');
        $this->db->join('sessions', 'sessions.id = publicresulttable.session_id');
        $this->db->order_by('sessions.id', 'DESC');
        $query = $this->db->get();
        
        return $query->result_array();
    }
}
