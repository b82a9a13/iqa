<?php
require_once(__DIR__.'/../../../../config.php');
require_login();
use local_iqa\lib;
$lib = new lib();
$p = 'local_iqa';

$returnText = new stdClass();
if(!isset($_SESSION['iqa_admin'])){
    $returnText->error = get_string('missing_rv', $p);
} elseif($_SESSION['iqa_admin'] != true){
    $returnText->error = get_string('missing_rv', $p);
} else {
    if(!isset($_POST['t'])){
        $returnText->error = get_string('missing_tv', $p);
    } else {
        $type = $_POST['t'];
        if(!in_array($type, ['course', 'iqa'])){
            $returnText->error = get_string('invalid_tp', $p);
        } else {
            $array = [];
            $string = '';
            switch($type){
                case 'course':
                    $array = $lib->get_non_iqa_courses();
                    $string = 'Course';
                    break;
                case 'iqa':
                    $array = $lib->get_non_iqa_users();
                    $string = 'User';
                    break;
            }
            if(!empty($array)){
                $returnText->return = "<option disabled value='' selected>Choose a $string</option>";
                foreach($array as $arra){
                    $returnText->return .= "<option value='$arra[1]'>$arra[0]</option>";
                }
                $returnText->return = str_replace("  ","",$returnText->return);
            }
        }
    }
}
echo(json_encode($returnText));