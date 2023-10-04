<?php
/**
 * @package     local_iqa
 * @author      Robert Tyrone Cullen
 * @var stdClass $plugin
 */

namespace local_iqa\event;
use core\event\base;
defined('MOODLE_INTERNAL') || die();

class viewed_assign_logs extends base {
    protected function init(){
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }
    public static function get_name(){
        return "Assignment logs viewed";
    }
    public function get_description(){
        return "The user with id '".$this->userid."' viewed the assignments logs for ".$this->other;
    }
    public function get_url(){
        return new \moodle_url('/local/iqa/admin.php');
    }
    public function get_id(){
        return $this->objectid;
    }
}