function doomsday_timer(ui, callback) {
    // init
    if (!window.Timer) {
        if (!ui
                || !( ui = document.getElementById(ui) )) {
            // anything is over
            return ;
        }

        window.Timer            = {};
        window.Timer.timer      = ui;
        window.Timer.timeoutid  = true;
    }
    // end init


    var now, seconds, Timer = window.Timer;

    if (!Timer.timer
            || !Timer.timeoutid) {
        return ;
    }

    // let's go

    if (callback) {
        Timer.callback = callback;
    }


    if (!Timer.aday) {
        Timer.aday = 24 * 60 * 60;
    }


    if (!Timer.doomsday) {
        Timer.doomsday = new Date('2012/12/21').getTime();
    }


    now     = new Date().getTime();
    seconds = Math.floor(( Timer.doomsday - now ) / 1000);


    if (Timer.callback) {
        try { Timer.callback(seconds, Timer.aday); } catch (e) {}

    } else {
        Timer.timer.innerHTML = 'only seconds:' + seconds;
    }

    Timer.timeoutid = window.setTimeout('doomsday_timer();', 1000);
}
