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
    $array = $lib->get_iqa();
    if(!empty($array)){
        $returnText->return = "<table class='table table-bordered table-striped table-hover'>
            <thead id='view_thead'>
                <tr>
                    <th class='c-pointer' onclick='header_clicked(`view`, 0)' sort='asc'>User Fullname <span>&uarr;</span></th>
                </tr>
            </thead>
            <tbody id='view_tbody'>
        ";
        foreach($array as $arra){
            $returnText->return .= "<tr><td><a href='./../../user/profile.php?id=$arra[1]' target='_blank'>$arra[0]</a></td></tr>";
        }
        $returnText->return .= "</tbody></table>";
        $returnText->return = str_replace("  ","", $returnText->return);
    }
}
echo(json_encode($returnText));