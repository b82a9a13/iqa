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
            $strings = ['',''];
            switch($type){
                case 'course':
                    $array = $lib->get_iqa_courses();
                    $strings[0] = 'Course';
                    $strings[1] = 'course/view.php';
                    break;
                case 'iqa':
                    $array = $lib->get_iqa();
                    $strings[0] = 'User';
                    $strings[1] = 'user/profile.php';
                    break;
            }
            if(!empty($array)){
                $returnText->return = "<table class='table table-bordered table-striped table-hover'>
                    <thead id='view_".$type."_thead'>
                        <tr>
                            <th class='c-pointer' onclick='header_clicked(`view`, 0, `$type`)' sort='asc'>$strings[0] Fullname <span>&uarr;</span></th>
                        </tr>
                    </thead>
                    <tbody id='view_".$type."_tbody'>
                ";
                foreach($array as $arra){
                    $returnText->return .= "<tr><td><a href='./../../$strings[1]?id=$arra[1]' target='_blank'>$arra[0]</a></td></tr>";
                }
                $returnText->return .= "</tbody></table>";
                $returnText->return = str_replace("  ","", $returnText->return);
            }
        }
    }
}
echo(json_encode($returnText));