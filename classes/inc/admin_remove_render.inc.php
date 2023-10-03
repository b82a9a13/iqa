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
        if(!in_array($type, ['course', 'iqa', 'learner'])){
            $returnText->error = get_string('invalid_tp', $p);
        } else {
            $array = [];
            $string = '';
            switch($type){
                case 'course':
                    $array = $lib->get_iqa_courses();
                    $string = 'Course';
                    break;
                case 'iqa':
                    $array = $lib->get_iqa();
                    $string = 'User';
                    break;
                case 'learner':
                    $array = $lib->get_iqa_learner();
                    break;
            }
            if(!empty($array)){
                switch($type){
                    case 'learner':
                        $returnText->return = "<select id='remove_".$type."_au' required><option disabled value='' selected>Choose a Assignment to remove</option>";
                        foreach($array as $arra){
                            $returnText->return .= "<option value='$arra[6]'>$arra[0] - $arra[2] - $arra[4]</option>";
                        }
                        $returnText->return .= '</select>';
                        $returnText->return = str_replace("  ","",$returnText->return);
                        break;
                    default:
                        $returnText->return = "<select id='remove_".$type."_au' required><option disabled value='' selected>Choose a $string</option>";
                        foreach($array as $arra){
                            $returnText->return .= "<option value='$arra[1]'>$arra[0]</option>";
                        }
                        $returnText->return .= '</select>';
                        $returnText->return = str_replace("  ","",$returnText->return);
                        break;
                }

            }
        }
    }
}
echo(json_encode($returnText));