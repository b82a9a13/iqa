<?php
/**
 * @package     local_iqa
 * @author      Robert Tyrone Cullen
 * @var stdClass $plugin
 */

require_once(__DIR__.'/../../config.php');
require_login();
$context = context_system::instance();
require_capability('local/iqa:admin', $context);
use local_iqa\lib;
$lib = new lib;
$p = 'local_iqa';
$title = get_string('iqa_a', $p);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/iqa/admin.php'));
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('admin');

echo $OUTPUT->header();

$template = (Object)[
    'title' => $title,
    'assign' => get_string('assign', $p),
    'submit' => get_string('submit', $p),
    'view' => get_string('view', $p),
    'remove' => get_string('remove', $p),
    'logs' => get_string('logs', $p),
    'start_date' => get_string('start_date', $p),
    'end_date' => get_string('end_date', $p),
    'filter' => get_string('filter', $p),
    'user' => get_string('user', $p),
    'course' => get_string('course', $p),
    'oneweekago' => date('Y-m-d',strtotime('-1 week', time())),
    'currentdate' => date('Y-m-d', strtotime('+1 day',time()))
];
echo $OUTPUT->render_from_template('local_iqa/admin', $template);

echo $OUTPUT->footer();
$_SESSION['iqa_admin'] = true;
\local_iqa\event\viewed_admin::create(array('context' => $context))->trigger();