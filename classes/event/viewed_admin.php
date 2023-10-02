<?php
/**
 * @package     local_iqa
 * @author      Robert Tyrone Cullen
 * @var stdClass $plugin
 */

namespace local_iqa\event;
use core\event\base;
defined('MOODLE_INTERNAL') || die();

class viewed_admin extends base {
    protected function init(){
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }
    public static function get_name(){
        return "Administration viewed";
    }
    public function get_description(){
        return "The user with id '".$this->userid."' viewed the administration page";
    }
    public function get_url(){
        return new \moodle_url('/local/iqa/admin.php');
    }
    public function get_id(){
        return $this->objectid;
    }
}