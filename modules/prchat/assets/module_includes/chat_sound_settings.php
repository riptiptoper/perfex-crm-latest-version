<script>
    var getUserSound = new Audio(site_url + 'modules/prchat/assets/chat_implements/sounds/push.mp3');
    var getSeenSound = new Audio(site_url + 'modules/prchat/assets/chat_implements/sounds/chat_seen.mp3');
    var soundDisabledMembers = [];

    (localStorage.getItem('soundDisabledMembers')) ?
    soundDisabledMembers = JSON.parse(localStorage.getItem('soundDisabledMembers')): soundDisabledMembers = [];


    (localStorage.getItem("soundDisabledMembers") === null) ?
    localStorage.setItem("soundDisabledMembers", JSON.stringify(soundDisabledMembers)): '';


    function appendUserSound() {
        return $(
            '<audio class="sound-player" autoplay="autoplay" style="display:none;">' + '<source src="' + arguments[0] + '" />' + '<embed src="' + arguments[0] + '" hidden="true" autostart="true" loop="false"/>' + '</audio>').appendTo('body');
    }

    function clientSeenNotify(args) {
        appendUserSound(getSeenSound.src) && stopUserSound();
    }

    function userSeenNotify(args) {
        (isSoundActive(args)) ? appendUserSound(getSeenSound.src) && stopUserSound(): '';
    }

    function stopUserSound() {
        setTimeout(function() {
            $(".sound-player").remove();
        }, 1000);
    }

    function initUserSound(args) {
        (isSoundActive(args)) ? appendUserSound(getUserSound.src) && stopUserSound(): '';
    }

    function initClientSound(args) {
        appendUserSound(getUserSound.src) && stopUserSound();
    }

    function isSoundActive(data) {
        var id = data.from;
        var currentSoundMembers = JSON.parse(localStorage.getItem("soundDisabledMembers"));
        return (currentSoundMembers.includes(id)) ?
            false :
            true;
    }
    // Handle sound icon 
    $('body').on('click', '.user_sound_icon', function() {
        sound_user_id = $(this).attr('data-sound_user_id');
        var soundDisabledMembers = JSON.parse(localStorage.getItem("soundDisabledMembers"));

        if (($(this).hasClass('fa-volume-up'))) {
            (!soundDisabledMembers.includes(sound_user_id)) ?
            soundDisabledMembers.push(sound_user_id): soundDisabledMembers = JSON.parse(localStorage.getItem('soundDisabledMembers'));

            localStorage.setItem("soundDisabledMembers", JSON.stringify(soundDisabledMembers));
            $(this).removeClass('fa-volume-up').addClass('fa-volume-off');

        } else {
            var filteredItems = soundDisabledMembers.filter(function(item) {
                return item !== sound_user_id
            })
            localStorage.setItem("soundDisabledMembers", JSON.stringify(filteredItems));

            $(this).removeClass('fa-volume-off').addClass('fa-volume-up');
        }
        sound_user_id = '';
    });
</script>