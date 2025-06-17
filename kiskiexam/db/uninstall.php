<?php
defined('MOODLE_INTERNAL') || die();

function xmldb_local_kiskiexam_uninstall() {
    global $DB;
    $DB->delete_records('local_kiskiexam_data');
    return true;
}
