<?php

defined('MOODLE_INTERNAL') || die();

class quizaccess_kiskiandro extends quiz_access_rule_base {

    /**
     * Constructor method.
     */
    public static function make(quiz $quizobj, $timenow, $canignoretimelimits) {
        return new self($quizobj, $timenow);
    }

    /**
     * Whether this rule is globally enabled.
     */
    public static function is_enabled() {
        return true;
    }

    /**
     * Optional UI form field — not needed here since settings come from local plugin.
     */
    public static function add_settings_form_fields(mod_quiz_mod_form $quizform, MoodleQuickForm $mform) {
        // Tidak perlu menampilkan apa-apa di form, karena data dari local_kiskiexam.
    }

    /**
     * Save settings — disabled, handled by local_kiskiexam.
     */
    public static function save_settings($quiz) {
        // Disimpan oleh local_kiskiexam, tidak perlu disimpan di sini.
    }

    /**
     * Ambil konfigurasi dari plugin lokal.
     */
    public static function get_settings($quiz) {
        global $DB;

        if (empty($quiz) || empty($quiz->id)) {
            return ['kiskiandro_enabled' => 0];
        }

        $record = $DB->get_record('local_kiskiexam_data', ['quizid' => $quiz->id]);
        return ['kiskiandro_enabled' => $record->enable ?? 0];
    }

    /**
     * Prevent access if not accessed via KiskiExam app.
     */
    public function prevent_access() {
        global $DB;

        $quizid = $this->quizobj->get_quizid();
        $record = $DB->get_record('local_kiskiexam_data', ['quizid' => $quizid]);

        $enabled = $record->enable ?? 0;

        // Jika rule tidak diaktifkan, izinkan akses
        if (!$enabled) {
            return false;
        }

        $useragent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        // Hanya izinkan akses dari aplikasi Android KiskiExam
        if (strpos($useragent, 'KISKIExam/1.0 AndroidClient') === false) {
            throw new \moodle_exception('accessdenied', 'quizaccess_kiskiandro');
        }

        return false; // akses diizinkan
    }
}
