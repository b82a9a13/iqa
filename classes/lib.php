<?php
/**
 * @package     local_iqa
 * @author      Robert Tyrone Cullen
 * @var stdClass $plugin
 */
namespace local_iqa;
use stdClass;

class lib{
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
        $record = new stdClass();
        $record->iqaid = $id;
        if($DB->record_exists('iqa_assignment', [$DB->sql_compare_text('iqaid') => $id])){
            return false;
        } elseif($DB->insert_record('iqa_assignment', $record) === false){
            return false;
        } else {
            return true;
        }
    }

    //Function is used to get all users assigned as iqa
    public function get_iqa(): array{
        global $DB;
        $records = $DB->get_records_sql('SELECT i.id as id, i.iqaid as userid, u.firstname as firstname, u.lastname as lastname FROM {iqa_assignment} i
            LEFT JOIN {user} u ON u.id = i.iqaid
        ');
        $array = [];
        foreach($records as $record){
            array_push($array, [$record->firstname.' '.$record->lastname, $record->userid]);
        }
        asort($array);
        return $array;
    }

    //Function is used to remove a user from iqa
    public function remove_iqa($id): bool{
        global $DB;
        if(!$DB->record_exists('iqa_assignment', [$DB->sql_compare_text('iqaid') => $id])){
            return false;
        } else {
            if($DB->delete_records('iqa_assignment', [$DB->sql_compare_text('iqaid') => $id]) > 0){
                return true;
            } else {
                return false;
            }
        }
    }
}