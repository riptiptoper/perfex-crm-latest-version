<div id="audio-wrapper" style="display: none;">
     <div id="audio_buttons">
          <div class="text-center">
               <h4 id="started_recording" class="text-white arrow-cursor"><?= _l('chat_click_to_record'); ?></h4>
               <button class="btn btn-primary" id="recordButton" data-toggle="tooltip" title="<?= _l('chat_record_btn_label'); ?>">
                    <i class="fa fa-circle"></i>
                    <?= _l('chat_record_btn_label'); ?>
               </button>
               <button class="btn btn-success mleft5" id="stopButton" data-toggle="tooltip" title="<?= _l('chat_send_btn_label'); ?>" disabled>
                    <?= _l('chat_send_btn_label'); ?>
               </button>
               <button class="btn btn-default mleft5" id="cancelRecording" onclick="showRecordingWrapper()" data-toggle="tooltip" title="<?= _l('cancel'); ?> / <?= _l('close'); ?>">
                    <i class="fa fa-close fa-1x"></i>
               </button>
               <div id="recording_time">
                    <h4 class="text-white arrow-cursor">
                         <span id="chat_rec_minutes">00</span>:<span id="chat_rec_seconds">00</span>
                    </h4>
               </div>
          </div>
     </div>
</div>
<script>
     window.chatAudioLang = {
          clickToRecord: "<?= _l('chat_click_to_record'); ?>",
          recording: "<?= _l('chat_is_recording'); ?>",
          recordingFinished: "<?= _l('chat_recording_finished'); ?>"
     }
</script>
<style>
     #audio-wrapper {
          display: none;
          height: 100vh;
          width: 100vw;
          background: rgba(0, 0, 0, 0.85);
          position: fixed;
          z-index: 999999999999;
          top: 0;
          justify-content: center;
     }

     #audio_buttons,
     #audio {
          z-index: 99999999;
          margin: 0 auto;
          margin-top: -140px;
          transform: translate(-50%, 50%);
     }

     @media (max-width: 768px) {
          #audio-wrapper {
               height: 100vh;
               background: rgba(0, 0, 0, 0.85);
               position: fixed;
               z-index: 999999999999;
               width: 100vw;
               top: 0px;
          }

          #audio_buttons,
          #audio {
               margin-top: 65%;
               position: unset;
               transform: none;
          }
     }

     .audio_dot_one {
          opacity: 0;
          -webkit-animation: dot 1.3s infinite;
          -webkit-animation-delay: 0.0s;
          animation: dot 1.3s infinite;
          animation-delay: 0.0s;
     }

     .audio_dot_two {
          opacity: 0;
          -webkit-animation: dot 1.3s infinite;
          -webkit-animation-delay: 0.2s;
          animation: dot 1.3s infinite;
          animation-delay: 0.2s;
     }

     .audio_dot_three {
          opacity: 0;
          -webkit-animation: dot 1.3s infinite;
          -webkit-animation-delay: 0.3s;
          animation: dot 1.3s infinite;
          animation-delay: 0.3s;
     }

     @-webkit-keyframes dot {
          0% {
               opacity: 0;
          }

          50% {
               opacity: 0;
          }

          100% {
               opacity: 1;
          }
     }

     @keyframes dot {
          0% {
               opacity: 0;
          }

          50% {
               opacity: 0;
          }

          100% {
               opacity: 1;
          }
     }

     .arrow-cursor {
          cursor: default;
          color: #fff;
     }

     /**
     * Mozilla
     */
     @-moz-document url-prefix() {
          #frame .content p audio {
               margin-bottom: -6px;
               height: 35px;
               background-color: #474747;
               border-radius: 5px;
          }

          body #audio_buttons,
          body #audio {
               top: 50%;
               left: auto;
          }
     }
</style>