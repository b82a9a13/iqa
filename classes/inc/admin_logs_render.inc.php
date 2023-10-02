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
    if(!isset($_POST['sd'])){
        $returnText->error = get_string('missing_sdv', $p);
    } elseif(!isset($_POST['ed'])){
        $returnText->error = get_string('missing_edv', $p);
    } else {
        $sd = $_POST['sd'];
        $ed = $_POST['ed'];
        if(!preg_match("/^[0-9\-]*$/", $sd) || empty($sd)){
            $returnText->error = get_string('invalid_sdp', $p);
        } elseif(!preg_match("/^[0-9\-]*$/", $ed) || empty($ed)){
            $returnText->error = get_string('invalid_edp', $p);
        } else {
            $sd = (new DateTime($sd))->format('U');
            $ed = (new DateTime($ed))->format('U');
            if($sd >= $ed){
                $returnText->error = get_string('invalid_dp', $p);
            } else {
                if(!isset($_POST['t'])){
                    $returnText->error = get_string('missing_tv', $p);
                } else {
                    $type = $_POST['t'];
                    if(!in_array($type, ['course', 'iqa'])){
                        $returnText->error = get_string('invalid_tp', $p);
                    } else {
                        $array = [];
                        $strings = ['', '', 'user/profile.php'];
                        switch($type){
                            case 'course':
                                $array = $lib->get_course_assign_logs($sd, $ed);
                                $strings[0] = 'Course';
                                $strings[1] = 'user/profile.php';
                                break;
                            case 'iqa':
                                $array = $lib->get_iqa_assign_logs($sd, $ed);
                                $strings[0] = 'User';
                                $strings[1] = 'course/view.php';
                                break;
                        }
                        if(!empty($array)){
                            $returnText->return = "<table class='table table-bordered table-striped table-hover'>
                                <thead id='logs_".$type."_thead'>
                                    <tr>
                                        <th class='c-pointer' onclick='header_clicked(`logs`, 0, `$type`)' sort='asc'>Date & Time <span>&uarr;</span></th>
                                        <th class='c-pointer' onclick='header_clicked(`logs`, 1, `$type`)' sort>Action <span></span></th>
                                        <th class='c-pointer' onclick='header_clicked(`logs`, 2, `$type`)' sort>Affected $strings[0] <span></span></th>
                                        <th class='c-pointer' onclick='header_clicked(`logs`, 3, `$type`)' sort>User <span></span></th>
                                    </tr>
                                </thead>
                                <tbody id='logs_".$type."_tbody'>
                            ";
                            foreach($array as $arra){
                                $returnText->return .= "
                                    <tr>
                                        <td dtval='$arra[0]'>".date('d/m/Y H:i:s',$arra[0])."</td>
                                        <td>$arra[1]</td>
                                        <td><a href='./../../$strings[1]?id=$arra[4]' target='_blank'>$arra[5]</a></td>
                                        <td><a href='./../../$strings[2]?id=$arra[2]' target='_blank'>$arra[3]</a></td>
                                    </tr>
                                ";
                            }
                            $returnText->return .= "</tbody></table>";
                            $returnText->return = str_replace("  ","",$returnText->return);
                        }
                    }
                }
            }
        }
    }
}
echo(json_encode($returnText));