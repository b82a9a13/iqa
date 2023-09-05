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
            $array = $lib->get_assign_logs($sd, $ed);
            if(!empty($array)){
                $returnText->return = "<table class='table table-bordered table-striped table-hover'>
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>Action</th>
                            <th>Affected User</th>
                            <th>User</th>
                        </tr>
                    </thead>
                    <tbody>
                ";
                foreach($array as $arra){
                    $returnText->return .= "
                        <tr>
                            <td>".date('d/m/Y H:i:s',$arra[0])."</td>
                            <td>$arra[1]</td>
                            <td><a href='./../../user/profile.php?id=$arra[4]' target='_blank'>$arra[5]</a></td>
                            <td><a href='./../../user/profile.php?id=$arra[2]' target='_blank'>$arra[3]</a></td>
                        </tr>
                    ";
                }
                $returnText->return .= "</tbody></table>";
                $returnText->return = str_replace("  ","",$returnText->return);
            }
        }
    }
}
echo(json_encode($returnText));