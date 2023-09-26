<?php
use block_iqa\lib;
class block_iqa extends block_base{
    
    public function init(){
        $this->title = 'IQA';
    }
    
    public function get_content(){
        $this->content = new stdClass();
        $this->content->text = '';
        $lib = new lib();
        $uri = $_SERVER['REQUEST_URI'];
        if(isset($_GET['id'])){
            $id = $_GET['id'];
            if(!preg_match("/^[0-9]*$/", $id) || empty($id)){
                return;
            } else {
                if(!isset($_GET['course'])){
                    if(strpos($uri, '/user/profile.php') != false){
                        //User profile page
                        $content = $lib->get_profile_content($id);
                        if($content != ''){
                            $this->content->text .= $content.'<div id="iqa_course_content"></div><script src="./../blocks/iqa/amd/min/block_iqa_profile.min.js"></script>';
                            $_SESSION['iqa_profile_content'] = true;
                        }
                    } else if(strpos($uri, '/course/view.php') != false){
                        //Course page
                        $context = context_course::instance($id);
                        if(has_capability('block/iqa:coach', $context)){
                            require_capability('block/iqa:coach', $context);
                            $content = $lib->get_course_content_coach($id);
                            if($content != ''){
                                $this->content->text .= $content.'<div id="iqa_course_content"></div><script src="./../blocks/iqa/amd/min/block_iqa_course.min.js"></script>';
                            }
                            $_SESSION['iqa_course_content'] = true;
                        } else if(has_capability('block/iqa:learner', $context)){
                            require_capability('block/iqa:learner', $context);
                            $content = $lib->get_course_content_learner_user($id);
                            if($content != ''){
                                $this->content->text .= $content;
                            }
                        }

                    }
                } else {
                    //Need to add block to the view.php page by the user for this to work
                    $courseid = $_GET['course'];
                    if(!preg_match("/^[0-9]*$/", $courseid) || empty($courseid)){
                        return;
                    } else {
                        $context = context_course::instance($courseid);
                        if(strpos($uri, '/user/view.php') != false){
                            //User course profile page
                            if(has_capability('block/iqa:coach', $context)){
                                require_capability('block/iqa:coach', $context);
                                $content = $lib->get_profile_content_course($id, $courseid);
                                if($content != ''){
                                    $this->content->text .= $content;
                                }
                            } else if(has_capability('block/iqa:learner', $context)){
                                require_capability('block/iqa:learner', $context);
                                $content = $lib->get_profile_content_course_user($courseid);
                                if($content != ''){
                                    $this->content->text .= $content;
                                }
                            }
                        }
                    }
                }
            }
        } else {
            if(strpos($uri, '/my/index.php')){
                //Dashboard page
                if(has_capability('block/iqa:admin', \context_system::instance())){
                    $this->content->text .= '<div class="text-center"><button class="btn btn-primary" onclick="window.location.href=`./../local/iqa/admin.php`">IQA Administration</button></div><br>';
                }
                $this->content->text .= '<h2 class="text-center">Modules you are assigned IQA for</h2>'.$lib->get_user_content().'<div id="iqa_dashboard_content"></div><script src="./../blocks/iqa/amd/src/block_iqa.js"></script>';
            }
        }
    }
}