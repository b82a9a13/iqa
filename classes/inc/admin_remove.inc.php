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
    if(!isset($_POST['id'])){
        $returnText->error = get_string('missing_iv', $p);
    } else {
        $id = $_POST['id'];
        if(!preg_match("/^[0-9]*$/", $id) || empty($id)){
            $returnText->error = get_string('invalid_ip', $p);
        } else {
            if(!isset($_POST['t'])){
                $returnText->error = get_string('missing_tv', $p);
            } else {
                $type = $_POST['t'];
                if(!in_array($type, ['course', 'iqa', 'learner'])){
                    $returnText->error = get_string('invalid_tp', $p);
                } else {
                    switch($type){
                        case 'course':
                            $returnText->return = $lib->remove_iqa_course($id);
                            break;
                        case 'iqa':
                            $returnText->return = $lib->remove_iqa($id);
                            break;
                        case 'learner':
                            $returnText->return = $lib->remove_iqa_learner($id);
                            break;
                    }
                }
            }
        }
    }
}
echo(json_encode($returnText));