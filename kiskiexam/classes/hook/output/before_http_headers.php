<?php
namespace local_kiskiexam\hook;

class before_http_request {
    public function __invoke(): void {
        error_log('[KISKI] Hook dipanggil di before_http_request');

        global $DB;

        // Cek apakah ini request untuk modul quiz
        $requesturi = $_SERVER['REQUEST_URI'] ?? '';
        if (strpos($requesturi, '/mod/quiz') === false) {
            return;
        }

        // Ambil course module ID dari URL (?id=123)
        $cmid = optional_param('id', 0, PARAM_INT);
        if (!$cmid) {
            error_log('[KISKI] Tidak ada CM ID');
            return;
        }

        // Ambil CM
        $cm = get_coursemodule_from_id('quiz', $cmid, 0, false, MUST_EXIST);
        if (!$cm || $cm->modname !== 'quiz') {
            error_log('[KISKI] Bukan modul quiz');
            return;
        }

        // Ambil quiz
        $quiz = $DB->get_record('quiz', ['id' => $cm->instance]);
        if (!$quiz) {
            error_log('[KISKI] Quiz tidak ditemukan');
            return;
        }

        // Ambil config plugin
        $config = $DB->get_record('local_kiskiexam_data', ['quizid' => $quiz->id]);
        if (!$config || empty($config->enable)) {
            error_log('[KISKI] Plugin tidak aktif untuk quiz ini');
            return;
        }

        // Validasi user agent
        $useragent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        if (strpos($useragent, 'KiskiAppAndroid') === false) {
            error_log('[KISKI] User agent tidak cocok');

            // Langsung output error dan hentikan
            header('Content-Type: text/html; charset=utf-8');
            http_response_code(403);
            echo "<h1>Akses Ditolak</h1><p>Silakan gunakan aplikasi resmi untuk mengakses kuis ini.</p>";
            exit;
        }

        error_log('[KISKI] User-Agent cocok, lanjutkan akses');
    }
}
