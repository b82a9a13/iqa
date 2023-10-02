<?php
/**
 * @package     local_iqa
 * @author      Robert Tyrone Cullen
 * @var stdClass $plugin
 */
namespace local_iqa;
use stdClass;

class lib{

    //Get the current user id
    private function get_current_userid(): int{
        global $USER;
        return $USER->id;
    }

    //Check if a user exists
    private function check_user_exists($id): bool{
        global $DB;
        return $DB->record_exists('user', [$DB->sql_compare_text('id') => $id]);
    }

    //Check if a course exists
    private function check_course_exists($id): bool{
        global $DB;
        return $DB->record_exists('course', [$DB->sql_compare_text('id') => $id]);
    }

    //Function is used to get all users which can be assigned as iqa
    public function get_non_iqa_users(): array{
        global $DB;
        $records = $DB->get_records_sql('SELECT ra.id as id, eu.userid as userid, eu.firstname as firstname, eu.lastname as lastname FROM {course} c
        INNER JOIN {context} ctx ON c.id = ctx.instanceid
        INNER JOIN {role_assignments} ra ON ra.contextid = ctx.id AND (ra.roleid = 4 OR ra.roleid = 3)
        INNER JOIN (
            SELECT e.courseid, ue.userid, u.firstname, u.lastname FROM {enrol} e
            INNER JOIN {user_enrolments} ue ON ue.enrolid = e.id AND ue.status != 1
            INNER JOIN {user} u ON u.id = ue.userid
        ) eu ON c.id = eu.courseid AND ra.userid = eu.userid');
        $array = [];
        foreach($records as $record){
            if(!$DB->record_exists('iqa_assignment', [$DB->sql_compare_text('iqaid') => $record->userid])){
                if(!in_array([$record->firstname.' '.$record->lastname, $record->userid], $array)){
                    array_push($array, [$record->firstname.' '.$record->lastname, $record->userid]);
                }
            }
        }
        asort($array);
        return $array;
    }

    //Function is used to assign a user as iqa
    public function create_iqa($id): bool{
        global $DB;
        if($this->check_user_exists($id)){
            $record = new stdClass();
            $record->iqaid = $id;
            if($DB->record_exists('iqa_assignment', [$DB->sql_compare_text('iqaid') => $id])){
                return false;
            } elseif($DB->insert_record('iqa_assignment', $record) === false){
                return false;
            } else {
                $record->userid = $this->get_current_userid();
                $record->time = time();
                $record->type = 'Added';
                $record->option = 'User';
                $DB->insert_record('iqa_assignment_log', $record);
                return true;
            }
        } else {
            return false;
        }
    }

    //Function is used to get all users assigned as iqa
    public function get_iqa(): array{
        global $DB;
        $records = $DB->get_records_sql('SELECT i.id as id, i.iqaid as userid, u.firstname as firstname, u.lastname as lastname FROM {iqa_assignment} i
            LEFT JOIN {user} u ON u.id = i.iqaid
        ');
        $array = [];
        if(count($records) > 0){
            foreach($records as $record){
                array_push($array, [$record->firstname.' '.$record->lastname, $record->userid]);
            }
            asort($array);
        }
        return $array;
    }

    //Function is used to remove a user from iqa
    public function remove_iqa($id): bool{
        global $DB;
        if(!$DB->record_exists('iqa_assignment', [$DB->sql_compare_text('iqaid') => $id])){
            return false;
        } else {
            if($DB->delete_records('iqa_assignment', [$DB->sql_compare_text('iqaid') => $id]) > 0){
                $record = new stdClass();
                $record->userid = $this->get_current_userid();
                $record->time = time();
                $record->type = 'Removed';
                $record->option = 'User';
                $record->iqaid = $id;
                $DB->insert_record('iqa_assignment_log', $record);
                return true;
            } else {
                return false;
            }
        }
    }

    //Function is used to get all the assignment logs
    public function get_iqa_assign_logs($startdate, $enddate): array{
        global $DB;
        $records = $DB->get_records_sql('SELECT i.id as id, i.userid as userid, u.firstname as ufirstname, u.lastname as ulastname, i.iqaid as iqaid, ua.firstname as uafirstname, ua.lastname as ualastname, i.type as type, i.time as time FROM {iqa_assignment_log} i
            LEFT JOIN {user} u ON u.id = i.userid
            LEFT JOIN {user} ua ON ua.id = i.iqaid
            WHERE i.time >= ? AND i.time <= ? AND i.option = "User"',[$startdate, $enddate]);
        $array = [];
        if(count($records) > 0){
            foreach($records as $record){
                array_push($array, [$record->time, $record->type, $record->userid, $record->ufirstname.' '.$record->ulastname, $record->iqaid, $record->uafirstname.' '.$record->ualastname]);
            }
        }
        return $array;
    }

    //Get all courses that aren't already in the iqa_course table
    public function get_non_iqa_courses(): array{
        global $DB;
        $records = $DB->get_records_sql('SELECT id, fullname FROM {course} WHERE id != 1');
        $array = [];
        foreach($records as $record){
            if(!$DB->record_exists('iqa_course', [$DB->sql_compare_text('courseid') => $record->id])){
                array_push($array, [$record->fullname, $record->id]);
            }
        }
        asort($array);
        return $array;
    }

    //Create a record in iqa_course table
    public function create_iqa_course($id): bool{
        global $DB;
        if($this->check_course_exists($id)){
            $record = new stdClass();
            $record->courseid = $id;
            if($DB->record_exists('iqa_course', [$DB->sql_compare_text('courseid') => $id])){
                return false;
            } elseif($DB->insert_record('iqa_course', $record) === false){
                return false;
            } else {
                $record->userid = $this->get_current_userid();
                $record->time = time();
                $record->type = 'Added';
                $record->option = 'Course';
                $DB->insert_record('iqa_assignment_log', $record);
                return true;
            }
        } else {
            return false;
        }
    }

    //Get all courses assigned as needing iqa
    public function get_iqa_courses(): array{
        global $DB;
        $records = $DB->get_records_sql('SELECT ic.id as id, ic.courseid as courseid, c.fullname as fullname FROM {iqa_course} ic 
            LEFT JOIN {course} c ON c.id = ic.courseid'
        );
        $array = [];
        if(count($records) > 0){
            foreach($records as $record){
                array_push($array, [$record->fullname, $record->courseid]);
            }
            asort($array);
        }
        return $array;
    }

    //Remove a specific course from the iqa course database
    public function remove_iqa_course($id): bool{
        global $DB;
        if(!$DB->record_exists('iqa_course', [$DB->sql_compare_text('courseid') => $id])){
            return false;
        } else {
            if($DB->delete_records('iqa_course', [$DB->sql_compare_text('courseid') => $id]) > 0){
                $record = new stdClass();
                $record->userid = $this->get_current_userid();
                $record->time = time();
                $record->type = 'Removed';
                $record->option = 'Course';
                $record->courseid = $id;
                $DB->insert_record('iqa_assignment_log', $record);
                return true;
            } else {
                return false;
            }
        }
    }

    //Get course assignment logs
    public function get_course_assign_logs($startdate, $enddate): array{
        global $DB;
        $records = $DB->get_records_sql('SELECT i.id as id, i.userid as userid, i.courseid as courseid, i.type as type, i.time as time, c.fullname as fullname, u.firstname as firstname, u.lastname as lastname FROM {iqa_assignment_log} i 
            LEFT JOIN {course} c ON c.id = i.courseid
            LEFT JOIN {user} u ON u.id = i.userid
        WHERE i.time >= ? AND i.time <= ? AND i.option = "Course"',[$startdate, $enddate]);
        $array = [];
        if(count($records) > 0){
            foreach($records as $record){
                array_push($array, [$record->time, $record->type, $record->userid, $record->firstname.' '.$record->lastname, $record->courseid, $record->fullname]);
            }
        }
        return $array;
    }
}