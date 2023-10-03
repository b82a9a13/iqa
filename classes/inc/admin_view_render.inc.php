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
                case 'learner':
                    $array = $lib->get_iqa_learner();
                    break;
            }
            if(!empty($array)){
                switch($type){
                    case 'learner':
                        $returnText->return = "<table class='table table-bordered table-striped table-hover'>
                            <thead id='view_".$type."_thead'>
                                <tr>
                                    <th class='c-pointer' onclick='header_clicked(`view`, 0, `$type`)' sort='asc'>Learner <span>&uarr;</span></th>
                                    <th class='c-pointer' onclick='header_clicked(`view`, 1, `$type`)' sort>Course <span></span></th>
                                    <th class='c-pointer' onclick='header_clicked(`view`, 2, `$type`)' sort>IQA <span></span></th>
                                </tr>
                            </thead>
                            <tbody id='view_".$type."_tbody'>
                        ";
                        foreach($array as $arra){
                            $returnText->return .= "
                                <tr>
                                    <td><a href='./../../user/profile.php?id=$arra[1]' target='_blank'>$arra[0]</a></td>
                                    <td><a href='./../../course/view.php?id=$arra[3]' target='_blank'>$arra[2]</td>
                                    <td><a href='./../../user/profile.php?id=$arra[5]' target='_blank'>$arra[4]</a></td>
                                </tr>
                            ";
                        }
                        $returnText->return .= '</tbody></table>';
                        $returnText->return = str_replace("  ","", $returnText->return);
                        break;
                    default:
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
                        break;
                }
            }
        }
    }
}
echo(json_encode($returnText));