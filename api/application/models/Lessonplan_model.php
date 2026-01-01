<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Lessonplan_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    /**
     * Get subject_group_class_sections ID by class, section, and subject group
     * 
     * @param int $class_id Class ID
     * @param int $section_id Section ID
     * @param int $subject_group_id Subject group ID
     * @param int $session_id Session ID (optional)
     * @return array Subject group class sections data
     */
    public function getsubject_group_class_sectionsId($class_id, $section_id, $subject_group_id, $session_id = NULL)
    {
        $session_id = ($session_id === NULL || $session_id === '') ? $this->current_session : $session_id;
        
        $sql = "SELECT subject_groups.name, subject_group_class_sections.* from subject_group_class_sections INNER JOIN class_sections on class_sections.id=subject_group_class_sections.class_section_id INNER JOIN subject_groups on subject_groups.id=subject_group_class_sections.subject_group_id WHERE class_sections.class_id=" . $this->db->escape($class_id) . " and class_sections.section_id=" . $this->db->escape($section_id) . " and subject_groups.id=" . $this->db->escape($subject_group_id) . " and subject_groups.session_id=" . $this->db->escape($session_id) . " ORDER by subject_groups.id DESC";
        
        $query = $this->db->query($sql);
        return $query->row_array();
    }

    /**
     * Get lessons by subject ID
     * 
     * @param int $sub_id Subject group subject ID
     * @param int $subject_group_class_sections_id Subject group class sections ID
     * @return array Array of lessons
     */
    public function getlessonBysubjectid($sub_id, $subject_group_class_sections_id)
    {
        return $this->db->select('*')->from('lesson')->where('subject_group_subject_id', $sub_id)->where('subject_group_class_sections_id', $subject_group_class_sections_id)->get()->result_array();
    }

    /**
     * Get lesson by lesson ID
     * 
     * @param int $lesson_id Lesson ID
     * @return array Array of lesson data
     */
    public function getlessonBylessonid($lesson_id)
    {
        return $this->db->select('*')->from('lesson')->where('id', $lesson_id)->get()->result_array();
    }

    /**
     * Add or update lesson
     * 
     * @param array $data Lesson data
     * @return int Insert ID or boolean
     */
    public function add_lesson($data)
    {
        if (isset($data['id']) && $data['id'] != '') {
            $this->db->where('id', $data['id']);
            $query = $this->db->update('lesson', $data);
            $insert_id = $data['id'];
        } else {
            $this->db->insert('lesson', $data);
            $insert_id = $this->db->insert_id();
        }

        return $insert_id;
    }

    /**
     * Get all lessons with topics
     * 
     * @param int $subject_group_class_sections_id Subject group class sections ID
     * @return array Array of lessons with topics
     */
    public function getLessonsWithTopics($subject_group_class_sections_id)
    {
        $this->db->select('lesson.*, subject_group_subjects.subject_id, subjects.name as subject_name, subjects.code as subject_code');
        $this->db->from('lesson');
        $this->db->join('subject_group_subjects', 'subject_group_subjects.id = lesson.subject_group_subject_id');
        $this->db->join('subjects', 'subjects.id = subject_group_subjects.subject_id');
        $this->db->where('lesson.subject_group_class_sections_id', $subject_group_class_sections_id);
        $this->db->order_by('lesson.id');
        $query = $this->db->get();
        
        $lessons = $query->result_array();
        
        // Get topics for each lesson
        if (!empty($lessons)) {
            foreach ($lessons as $key => $lesson) {
                $lessons[$key]['topics'] = $this->getTopicsByLessonId($lesson['id']);
            }
        }
        
        return $lessons;
    }

    /**
     * Get topics by lesson ID
     * 
     * @param int $lesson_id Lesson ID
     * @return array Array of topics
     */
    public function getTopicsByLessonId($lesson_id)
    {
        return $this->db->select('*')->from('topic')->where('lesson_id', $lesson_id)->order_by('id')->get()->result_array();
    }
}

