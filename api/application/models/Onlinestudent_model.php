<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Onlinestudent_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get online admission records
     * Returns online admission records with related data (class, section, category, etc.)
     *
     * @param int $id Optional - specific admission ID to retrieve
     * @param array $carray Optional - array of class IDs to filter by
     * @return array Single record (if $id provided) or array of records
     */
    public function get($id = null, $carray = null)
    {
        $this->db->select('online_admissions.vehroute_id,vehicle_routes.route_id,vehicle_routes.vehicle_id,transport_route.route_title,vehicles.vehicle_no,hostel_rooms.room_no,vehicles.driver_name,vehicles.driver_contact,hostel.id as `hostel_id`,hostel.hostel_name,room_types.id as `room_type_id`,room_types.room_type ,online_admissions.hostel_room_id,class_sections.id as class_section_id,classes.id AS `class_id`,classes.class,sections.id AS `section_id`,sections.section,online_admissions.id,online_admissions.admission_no , online_admissions.roll_no,online_admissions.admission_date,online_admissions.firstname,online_admissions.middlename, online_admissions.lastname,online_admissions.image,    online_admissions.mobileno, online_admissions.email ,online_admissions.state ,   online_admissions.city , online_admissions.pincode , online_admissions.note, online_admissions.religion, online_admissions.cast, school_houses.house_name,   online_admissions.dob ,online_admissions.current_address, online_admissions.previous_school,
            online_admissions.guardian_is,
            online_admissions.permanent_address,IFNULL(online_admissions.category_id, 0) as `category_id`,IFNULL(categories.category, "") as `category`,online_admissions.adhar_no,online_admissions.samagra_id,online_admissions.bank_account_no,online_admissions.bank_name, online_admissions.ifsc_code , online_admissions.guardian_name , online_admissions.father_pic ,online_admissions.height ,online_admissions.weight,online_admissions.measurement_date, online_admissions.mother_pic , online_admissions.guardian_pic , online_admissions.guardian_relation,online_admissions.guardian_phone,online_admissions.guardian_address,online_admissions.is_enroll ,online_admissions.created_at,online_admissions.document ,online_admissions.updated_at,online_admissions.father_name,online_admissions.father_phone,online_admissions.blood_group,online_admissions.school_house_id,online_admissions.father_occupation,online_admissions.mother_name,online_admissions.mother_phone,online_admissions.mother_occupation,online_admissions.guardian_occupation,online_admissions.gender,online_admissions.guardian_is,online_admissions.rte,online_admissions.guardian_email,online_admissions.paid_status,online_admissions.form_status,online_admissions.reference_no,online_admissions.class_section_id')->from('online_admissions');

        $this->db->join('class_sections', 'class_sections.id = online_admissions.class_section_id', 'left');
        $this->db->join('classes', 'class_sections.class_id = classes.id', 'left');
        $this->db->join('sections', 'sections.id = class_sections.section_id', 'left');
        $this->db->join('hostel_rooms', 'hostel_rooms.id = online_admissions.hostel_room_id', 'left');
        $this->db->join('hostel', 'hostel.id = hostel_rooms.hostel_id', 'left');
        $this->db->join('room_types', 'room_types.id = hostel_rooms.room_type_id', 'left');
        $this->db->join('categories', 'online_admissions.category_id = categories.id', 'left');
        $this->db->join('vehicle_routes', 'vehicle_routes.id = online_admissions.vehroute_id', 'left');
        $this->db->join('transport_route', 'vehicle_routes.route_id = transport_route.id', 'left');
        $this->db->join('vehicles', 'vehicles.id = vehicle_routes.vehicle_id', 'left');
        $this->db->join('school_houses', 'school_houses.id = online_admissions.school_house_id', 'left');

        if ($carray != null) {
            $this->db->where_in('classes.id', $carray);
        }

        if ($id != null) {
            $this->db->where('online_admissions.id', $id);
        } else {
            $this->db->order_by('online_admissions.id', 'desc');
        }

        $query = $this->db->get();

        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    /**
     * Get online admission fee collection report
     * Returns online admission payments with student details for a date range
     *
     * @param string $start_date Start date (Y-m-d format)
     * @param string $end_date End date (Y-m-d format)
     * @return array
     */
    public function getOnlineAdmissionFeeCollectionReport($start_date, $end_date)
    {
        $query = "SELECT online_admissions.*,
                         online_admission_payment.*,
                         classes.class,
                         sections.section,
                         categories.category,
                         hostel.hostel_name,
                         room_types.room_type,
                         hostel_rooms.room_no,
                         transport_route.route_title,
                         vehicles.vehicle_no,
                         school_houses.house_name
                  FROM online_admissions
                  JOIN online_admission_payment ON online_admissions.id = online_admission_payment.online_admission_id
                  LEFT JOIN class_sections ON class_sections.id = online_admissions.class_section_id
                  LEFT JOIN classes ON class_sections.class_id = classes.id
                  LEFT JOIN sections ON sections.id = class_sections.section_id
                  LEFT JOIN hostel_rooms ON hostel_rooms.id = online_admissions.hostel_room_id
                  LEFT JOIN hostel ON hostel.id = hostel_rooms.hostel_id
                  LEFT JOIN room_types ON room_types.id = hostel_rooms.room_type_id
                  LEFT JOIN categories ON online_admissions.category_id = categories.id
                  LEFT JOIN vehicle_routes ON vehicle_routes.id = online_admissions.vehroute_id
                  LEFT JOIN transport_route ON vehicle_routes.route_id = transport_route.id
                  LEFT JOIN vehicles ON vehicles.id = vehicle_routes.vehicle_id
                  LEFT JOIN school_houses ON school_houses.id = online_admissions.school_house_id
                  WHERE DATE_FORMAT(online_admission_payment.date, '%Y-%m-%d') >= " . $this->db->escape($start_date) . "
                  AND DATE_FORMAT(online_admission_payment.date, '%Y-%m-%d') <= " . $this->db->escape($end_date) . "
                  ORDER BY online_admission_payment.date DESC, online_admissions.id DESC";

        $query = $this->db->query($query);
        return $query->result_array();
    }

    /**
     * Get online admission payment summary
     *
     * @param string $start_date Start date (Y-m-d format)
     * @param string $end_date End date (Y-m-d format)
     * @return array
     */
    public function getOnlineAdmissionPaymentSummary($start_date, $end_date)
    {
        $this->db->select('COUNT(DISTINCT online_admission_payment.online_admission_id) as total_admissions,
                          SUM(online_admission_payment.paid_amount) as total_amount,
                          online_admission_payment.payment_mode,
                          COUNT(online_admission_payment.id) as payment_count');
        $this->db->from('online_admission_payment');
        $this->db->where('DATE_FORMAT(online_admission_payment.date, "%Y-%m-%d") >=', $start_date);
        $this->db->where('DATE_FORMAT(online_admission_payment.date, "%Y-%m-%d") <=', $end_date);
        $this->db->group_by('online_admission_payment.payment_mode');

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get online admissions by class
     *
     * @param string $start_date Start date (Y-m-d format)
     * @param string $end_date End date (Y-m-d format)
     * @return array
     */
    public function getOnlineAdmissionsByClass($start_date, $end_date)
    {
        $this->db->select('classes.class,
                          sections.section,
                          COUNT(DISTINCT online_admissions.id) as admission_count,
                          SUM(online_admission_payment.paid_amount) as total_amount');
        $this->db->from('online_admissions');
        $this->db->join('online_admission_payment', 'online_admissions.id = online_admission_payment.online_admission_id');
        $this->db->join('class_sections', 'class_sections.id = online_admissions.class_section_id', 'left');
        $this->db->join('classes', 'class_sections.class_id = classes.id', 'left');
        $this->db->join('sections', 'sections.id = class_sections.section_id', 'left');
        $this->db->where('DATE_FORMAT(online_admission_payment.date, "%Y-%m-%d") >=', $start_date);
        $this->db->where('DATE_FORMAT(online_admission_payment.date, "%Y-%m-%d") <=', $end_date);
        $this->db->group_by('classes.id, sections.id');
        $this->db->order_by('classes.id', 'asc');

        $query = $this->db->get();
        return $query->result_array();
    }

}

