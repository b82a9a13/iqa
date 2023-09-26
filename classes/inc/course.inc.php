<?php
require_once(__DIR__.'/../../../../config.php');
require_login();
use block_iqa\lib;
$lib = new lib();
$returnText = new stdClass();
$p = 'block_iqa';

if(!isset($_SESSION['iqa_course_content'])){
    $returnText->return = false;
} else if($_SESSION['iqa_course_content']){
    if(!isset($_POST['c'])){
        $returnText->error = 'No course provided';
    } else if(!isset($_POST['u'])){
        $returnText->error = 'No user provided';
    } else {
        $courseid = $_POST['c'];
        $userid = $_POST['u'];
        if(!preg_match("/^[0-9]*$/", $courseid) || empty($courseid)){
            $returnText->error = 'Invalid course provided';
        } else if(!preg_match("/^[0-9]*$/", $userid) || empty($userid)){
            $returnText->error = 'Invalid user provided';
        } else {
            $returnText->return = $lib->get_course_content_learner($courseid, $userid);
        }
    }
}
echo(json_encode($returnText));