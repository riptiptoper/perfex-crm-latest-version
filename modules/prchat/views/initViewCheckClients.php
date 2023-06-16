<?php
if (get_option('pusher_chat_enabled') == '1' && is_client_logged_in()) {
    echo '<script src="https://js.pusher.com/7.0/pusher.min.js"></script>';
    echo '<script src="' . base_url('modules/prchat/assets/js/emoparser.js') . '"></script>';
    echo '<script src="' . base_url('modules/prchat/assets/js/lity.min.js') . '"></script>';

    $this->load->view('chat_clients_view');

    /**
     * After view is loaded  we inject the JS files otherwise it will throw and error
     */
    $isHttps = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? $isHttps = true : false);
    if ($isHttps) {
        echo '<script src="' . module_dir_url('prchat', 'assets/js/audio/WebAudioRecorder.min.js' . '?v=' . VERSIONING . '') . '"></script>';
        echo '<script src="' . module_dir_url('prchat', 'assets/js/audio/WebAudioRecorderOgg.min.js' . '?v=' . VERSIONING . '') . '"></script>';
        echo '<script src="' . module_dir_url('prchat', 'assets/js/audio/sound_app.js' . '?v=' . VERSIONING . '') . '"></script>';
    }
}
