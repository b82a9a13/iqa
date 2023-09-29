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
    $array = $lib->get_non_iqa_courses();
    if(!empty($array)){
        $returnText->return = "<option disabled value='' selected>Choose a Course</option>";
        foreach($array as $arra){
            $returnText->return .= "<option value='$arra[1]'>$arra[0]</option>";
        }
        $returnText->return = str_replace("  ","",$returnText->return);
    }
}
echo(json_encode($returnText));
