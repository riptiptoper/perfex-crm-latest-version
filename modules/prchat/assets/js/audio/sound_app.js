if (location.protocol != 'http:') {
    //webkitURL is deprecated but nevertheless
    URL = window.URL || window.webkitURL;
    var gumStream; //stream from getUserMedia()
    var recorder; //WebAudioRecorder object
    var input; //MediaStreamAudioSourceNode  we'll be recording
    var encodingType = 'ogg'; // current is set to ogg audio (file) there is wav and mp3 also but bigger files ogg suits best
    var encodeAfterRecord = true; // when to encode
    var chat_rec_sec = 0;
    // shim for AudioContext when it's not avb. 
    var AudioContext = window.AudioContext || window.webkitAudioContext;
    var audioContext; //new audio context to help us record

    var recordButton = document.getElementById("recordButton");
    var stopButton = document.getElementById("stopButton");
    var cancelButton = document.getElementById("cancelRecording");

    var timer;
    var chat_seconds_element = document.getElementById("chat_rec_seconds");
    var chat_minutes_element = document.getElementById("chat_rec_minutes");
    //add events to those 2 buttons
    recordButton.addEventListener("click", startRecording);
    stopButton.addEventListener("click", stopRecording);


    function startRecording() {

        this.children[0].classList.add("flashit");

        var constraints = {audio: true, video: false}

        /*
    	We're using the standard promise based getUserMedia() 
    	https://developer.mozilla.org/en-US/docs/Web/API/MediaDevices/getUserMedia
	*/

        navigator.mediaDevices.getUserMedia(constraints).then(function (stream) {
            /*
            	create an audio context after getUserMedia is called
            	sampleRate might change after getUserMedia is called, like it does on macOS when recording through AirPods
            	the sampleRate defaults to the one set in your OS for your playback device
            */
            audioContext = new AudioContext();

            //assign to gumStream for later use
            gumStream = stream;

            /* use the stream */
            input = audioContext.createMediaStreamSource(stream);

            //stop the input from playing back through the speakers
            //input.connect(audioContext.destination)

            recorder = new WebAudioRecorder(input, {
                workerDir: site_url + "/modules/prchat/assets/js/audio/", // must end with slash
                encoding: encodingType,
                numChannels: 2, //2 is the default, mp3 encoding supports only 2
                onEncoderLoading: function (recorder, encoding) {
                    // show "loading encoder..." display
                },
                onEncoderLoaded: function (recorder, encoding) {
                    function padRecordTime(val) {
                        return val > 9 ? val : "0" + val;
                    }

                    timer = setInterval(function () {
                        chat_seconds_element.innerHTML = padRecordTime(++chat_rec_sec % 60);
                        chat_minutes_element.innerHTML = padRecordTime(parseInt(chat_rec_sec / 60, 10));
                    }, 1000);
                    // hide "loading encoder..." display
                    $('#started_recording').html(chatAudioLang.recording + ' <span class="audio_dot_one">.</span><span class="audio_dot_two">.</span><span class="audio_dot_three">.</span>');
                }
            });

            recorder.onComplete = function (recorder, blob) {
                var clients = false;
                var activeTab = $('.chat_nav li.active');
                var chatBox = $('body').find('.client_chatbox');

                clients = ($('body').find('.ch_pointer').length) && true;

                if (!clients) {
                    if (activeTab.hasClass('staff')) {
                        chatBox = $('textarea.chatbox');
                    } else if (activeTab.hasClass('groups')) {
                        chatBox = $('textarea.group_chatbox');
                    }
                } else {
                    chatBox = $('.clients_textarea');
                }

                var sendButton = chatBox.parents('.wrap').children('button');

                if (clients) {
                    sendButton = $('.send_client_message');
                }

                sendButton.children('i').removeClass('flashit');

                var reader = new window.FileReader();
                reader.readAsDataURL(blob);

                reader.onloadend = function () {
                    base64data = reader.result;
                    /** If request is from clients side */
                    var url;

                    if (typeof admin_url == "undefined") {
                        url = site_url + "prchat/Prchat_ClientsController/handleAudio"
                    } else {
                        url = admin_url + "prchat/Prchat_Controller/handleAudio";
                    }

                    $.post(url, {"audio": base64data}, function (res) {
                        if (res.filename) {

                            var audio = '<audio controls src="' + site_url + 'modules/prchat/uploads/audio/' + res.filename + '" type="audio/ogg"></audio>';

                            $('#audio-wrapper').hide(1, function () {
                                stopRecording();
                                setTimeout(() => {
                                    chatBox.val(audio);
                                    sendButton.click();
                                    recorder.finishRecording();
                                    recordButton.disabled = false;
                                    clearMinutesAndSeconds();
                                }, 200);
                            });

                        } else {
                            console.log(res.error);
                        }
                    }, "json");
                };
            }

            recorder.setOptions({
                timeLimit: 600, // 10 minutes max recording
                encodeAfterRecord: encodeAfterRecord,
                ogg: {quality: 0.5},
                mp3: {bitRate: 160}
            });

            //start the recording process
            recorder.startRecording();

        }).catch(function (err) {

            //enable the record button if getUSerMedia() fails
            recordButton.disabled = false;
            stopButton.disabled = true;

        });

        //disable the record button
        recordButton.disabled = true;
        stopButton.disabled = false;
    }

    /**
     * Clear recording fields
     */
    function clearMinutesAndSeconds() {
        chat_seconds_element.innerHTML = "00";
        chat_minutes_element.innerHTML = "00";
        chat_rec_sec = 0;
        $('#started_recording').text(chatAudioLang.clickToRecord);
        $('#audio_buttons #recordButton i').removeClass('flashit')
    }

    function ifRecordingCancelledAndClose() {

        // If no microphone device is found
        navigator.mediaDevices.getUserMedia({audio: true, video: false}).then(function (stream) {
        }).catch(function (err) {
            err = err.toString()

            if (err == 'NotFoundError: Requested device not found') {
                recordButton.disabled = true;
                stopButton.disabled = true;
                $('#started_recording').html('<h4>' + err + '</h4>');
            }

            setTimeout(() => {
                $('#audio-wrapper').hide();
            }, 2500);

            return;
        });


        var audioWrapper = $('#audio-wrapper');
        if (audioWrapper.is(':hidden')) {
            audioWrapper.css('display', 'flex');
        } else {
            audioWrapper.css('display', 'none');
        }

        cancelButton.disabled = false;
        //  button is disabled on send due bugs prevention must enable when wrapper is shown again
        if (recorder !== undefined) {
            recorder.onEncodingCanceled;
            clearInterval(timer);
            gumStream.getAudioTracks()[0].stop();

            //disable the stop button
            stopButton.disabled = true;
            recordButton.disabled = false;
            clearMinutesAndSeconds();
            recorder = '';
        }
    }

    /**
     * Trigger recorder wrapper
     */
    function showRecordingWrapper() {
        ifRecordingCancelledAndClose();
    }

    function stopRecording(e) {
        $('#started_recording').html(chatAudioLang.recordingFinished)
        cancelButton.disabled = true;
        clearInterval(timer);
        $('#recordButton i.fa-circle').removeClass('flashit');

        //stop microphone access
        if (recorder !== undefined) {
            gumStream.getAudioTracks()[0].stop();

            //disable the stop button
            stopButton.disabled = true;
            recordButton.disabled = true;

            //tell the recorder to finish the recording (stop recording + encode the recorded audio)
            recorder.finishRecording();
        }
    }

    document.onkeydown = function (evt) {
        evt = evt || window.event;
        var isEscape = false;
        if ("key" in evt) {
            isEscape = (evt.key === "Escape" || evt.key === "Esc");
        } else {
            isEscape = (evt.keyCode === 27);
        }
        if (isEscape) {
            $('#audio-wrapper').hide();
            clearMinutesAndSeconds();
        }
    };
}