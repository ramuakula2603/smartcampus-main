<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Subjectgroup_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    /**
     * Get subjects in a subject group
     * 
     * @param int $subject_group_id Subject group ID
     * @param int $session_id Session ID (optional)
     * @return array Array of subjects
     */
    public function getGroupsubjects($subject_group_id, $session_id = NULL) {
        $session_id = ($session_id === NULL || $session_id === '') ? $this->current_session : $session_id;

        $sql = "SELECT subject_group_subjects.*,subjects.name,subjects.code,subjects.type FROM `subject_group_subjects` INNER JOIN subjects on subjects.id=subject_group_subjects.subject_id WHERE subject_group_id =" . $this->db->escape($subject_group_id) . " and session_id =" . $this->db->escape($session_id);
        $query = $this->db->query($sql);

        return $query->result();
    }

    /**
     * Get subject groups by class and section
     * 
     * @param int $class_id Class ID
     * @param int $section_id Section ID
     * @param int $session_id Session ID (optional)
     * @return array Array of subject groups
     */
    public function getGroupByClassandSection($class_id, $section_id, $session_id = NULL) {
        $session_id = ($session_id === NULL || $session_id === '') ? $this->current_session : $session_id;

        $sql = "SELECT subject_groups.name, subject_group_class_sections.* from subject_group_class_sections INNER JOIN class_sections on class_sections.id=subject_group_class_sections.class_section_id INNER JOIN subject_groups on subject_groups.id=subject_group_class_sections.subject_group_id WHERE class_sections.class_id=" . $this->db->escape($class_id) . " and class_sections.section_id=" . $this->db->escape($section_id) . " and subject_groups.session_id=" . $this->db->escape($session_id) . " ORDER by subject_groups.id DESC";
        $query = $this->db->query($sql);

        return $query->result_array();
    }

    /**
     * Get class sections by subject group
     * 
     * @param int $subject_group_id Subject group ID
     * @return array Array of class sections
     */
    public function getClassSectionByGroup($subject_group_id) {
        $sql = "SELECT subject_group_class_sections.*,classes.id as `class_id`,classes.class,sections.id as `section_id`,sections.section FROM `subject_group_class_sections` INNER JOIN class_sections on class_sections.id=subject_group_class_sections.class_section_id INNER JOIN classes on classes.id=class_sections.class_id INNER join sections on sections.id=class_sections.section_id WHERE subject_group_class_sections.session_id=" . $this->db->escape($this->current_session) . " and subject_group_id=" . $this->db->escape($subject_group_id);
        $query = $this->db->query($sql);
        return $query->result();
    }

    /**
     * Get subject group by ID
     * 
     * @param int $id Subject group ID (optional)
     * @return array Array of subject groups
     */
    public function getByID($id = null) {
        $this->db->select('subject_groups.*')->from('subject_groups');
        $this->db->where('subject_groups.session_id', $this->current_session);

        if ($id != null) {
            $this->db->where('subject_groups.id', $id);
        } else {
            $this->db->order_by('subject_groups.id', 'DESC');
        }

        $query = $this->db->get();
        $subject_groups = $query->result();
        if (!empty($subject_groups)) {
            foreach ($subject_groups as $subject_group_key => $subject_group_value) {
                $subject_groups[$subject_group_key]->group_subject = $this->getGroupsubjects($subject_group_value->id);
                $subject_groups[$subject_group_key]->sections = $this->getClassSectionByGroup($subject_group_value->id);
            }
        }
        return $subject_groups;
    }

    /**
     * Get all subjects by class and section
     * 
     * @param int $class_id Class ID
     * @param int $section_id Section ID
     * @return array Array of subjects
     */
    public function getAllsubjectByClassSection($class_id, $section_id) {
        $sql = "SELECT subject_group_class_sections.*,subject_groups.name as subject_group_name,subject_group_subjects.id as subject_group_subject_id,subjects.id as subject_id,subjects.name as subject_name,subjects.code as subject_code FROM `subject_group_class_sections` INNER JOIN class_sections on subject_group_class_sections.class_section_id=class_sections.id INNER JOIN subject_groups on subject_groups.id=subject_group_class_sections.subject_group_id  INNER JOIN subject_group_subjects on subject_group_subjects.subject_group_id=subject_groups.id INNER JOIN subjects on subjects.id=subject_group_subjects.subject_id WHERE  class_sections.class_id=" . $this->db->escape($class_id) . " and class_sections.section_id=" . $this->db->escape($section_id) . " and subject_group_class_sections.session_id=" . $this->db->escape($this->current_session);

        $query = $this->db->query($sql);
        return $query->result();
    }
}

