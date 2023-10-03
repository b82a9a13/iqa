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
                    $array = $lib->get_non_iqa_courses();
                    $string = 'Course';
                    break;
                case 'iqa':
                    $array = $lib->get_non_iqa_users();
                    $string = 'User';
                    break;
                case 'learner':
                    $array = $lib->get_non_iqa_learners();
                    $string = 'Learner';
                    break;
            }
            if($type == 'learner' && !empty($array)){
                $return = "<select id='assign_".$type."_au' onchange='select_changed(`assign`)' required><option disabled value='' selected>Choose a Course</option>";
                $learners = "<select id='assign_".$type."_au2' class='ml-1' required style='display:none;'><option disabled value='' selected>Choose a $string</option>";
                $iqas = "<select id='assign_".$type."_au3' class='ml-1' required style='display:none;'><option disabled value='' selected>Choose a IQA</option>";
                $haslearner = false;
                foreach($array as $arra){
                    switch($arra[0]){
                        case 'iqa':
                            foreach($arra[1] as $arr){
                                $iqas .= "<option value='".$arr[1]."'>$arr[0]</option>";
                            }
                            break;
                        default:
                            if($arra[1] != []){
                                $return .= "<option value='".$arra[0][1]."'>".$arra[0][0]."</option>";
                                foreach($arra[1] as $arr){
                                    $learners .= "<option value='".$arr[1]."' class='assignau2_".$arra[0][1]." assignau2' disabled hidden>".$arr[0]."</option>";
                                }
                                $haslearner = true;
                            }
                            break;
                    }
                }
                $return .= '</select>';
                $learners .= "</select>";
                $iqas .= "</select>";
                if($haslearner){
                    $returnText->return = str_replace("  ","",$return.$learners.$iqas);
                }
            } elseif(!empty($array)){
                $returnText->return = "<select id='assign_".$type."_au' required><option disabled value='' selected>Choose a $string</option>";
                foreach($array as $arra){
                    $returnText->return .= "<option value='$arra[1]'>$arra[0]</option>";
                }
                $returnText->return .= '</select>';
                $returnText->return = str_replace("  ","",$returnText->return);
            }
        }
    }
}
echo(json_encode($returnText));