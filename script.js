document.addEventListener("DOMContentLoaded", function (event) {
    if (isTodayHoroscope()) {
        todayHoroscopeSetup();
    } else if (isTomorrowHoroscope()) {
        tomorrowHoroscopeSetup();
    } else if (isWeeklyHoroscope() ){
        weeklyHoroscopeSetup();
    } else if (isMonthlyHoroscope() ){
        monthlyHoroscopeSetup();
    }
});

let curLocation = window.location.href;
let curSign = "";


/**
 * Determine if the page is for today's horoscope and set the curSign
 * @returns {boolean}
 */
function isTodayHoroscope() {
    curSign = "";
    if (curLocation.indexOf("/horoscopo/aries/") !== -1) {
        curSign = "aries";
    } else if (curLocation.indexOf("/horoscopo/tauro/") !== -1) {
        curSign = "tauro";
    } else if (curLocation.indexOf("/horoscopo/geminis/") !== -1) {
        curSign = "geminis";
    } else if (curLocation.indexOf("/horoscopo/cancer/") !== -1) {
        curSign = "cancer";
    } else if (curLocation.indexOf("/horoscopo/leo/") !== -1) {
        curSign = "leo";
    } else if (curLocation.indexOf("/horoscopo/virgo/") !== -1) {
        curSign = "virgo";
    } else if (curLocation.indexOf("/horoscopo/libra/") !== -1) {
        curSign = "libra";
    } else if (curLocation.indexOf("/horoscopo/escorpio/") !== -1) {
        curSign = "escorpio";
    } else if (curLocation.indexOf("/horoscopo/sagitario/") !== -1) {
        curSign = "sagitario";
    } else if (curLocation.indexOf("/horoscopo/capricornio/") !== -1) {
        curSign = "capricornio";
    } else if (curLocation.indexOf("/horoscopo/acuario/") !== -1) {
        curSign = "acuario";
    } else if (curLocation.indexOf("/horoscopo/piscis/") !== -1) {
        curSign = "piscis";
    }
    return curSign !== "";
}

/**
 * Determine if the page is for tomorrow's horoscope and set the curSign
 * @returns {boolean}
 */
function isTomorrowHoroscope() {
    curSign = "";
    if (curLocation.indexOf("/horoscopo-manana/aries/") !== -1) {
        curSign = "aries";
    } else if (curLocation.indexOf("/horoscopo-manana/tauro/") !== -1) {
        curSign = "tauro";
    } else if (curLocation.indexOf("/horoscopo-manana/geminis/") !== -1) {
        curSign = "geminis";
    } else if (curLocation.indexOf("/horoscopo-manana/cancer/") !== -1) {
        curSign = "cancer";
    } else if (curLocation.indexOf("/horoscopo-manana/leo/") !== -1) {
        curSign = "leo";
    } else if (curLocation.indexOf("/horoscopo-manana/virgo/") !== -1) {
        curSign = "virgo";
    } else if (curLocation.indexOf("/horoscopo-manana/libra/") !== -1) {
        curSign = "libra";
    } else if (curLocation.indexOf("/horoscopo-manana/escorpio/") !== -1) {
        curSign = "escorpio";
    } else if (curLocation.indexOf("/horoscopo-manana/sagitario/") !== -1) {
        curSign = "sagitario";
    } else if (curLocation.indexOf("/horoscopo-manana/capricornio/") !== -1) {
        curSign = "capricornio";
    } else if (curLocation.indexOf("/horoscopo-manana/acuario/") !== -1) {
        curSign = "acuario";
    } else if (curLocation.indexOf("/horoscopo-manana/piscis/") !== -1) {
        curSign = "piscis";
    }
    return curSign !== "";
}

/**
 * Determine if the page is for weekly horoscope and set the curSign
 * @returns {boolean}
 */
function isWeeklyHoroscope() {
    curSign = "";
    if (curLocation.indexOf("/horoscopo-semanal/aries/") !== -1) {
        curSign = "aries";
    } else if (curLocation.indexOf("/horoscopo-semanal/tauro/") !== -1) {
        curSign = "tauro";
    } else if (curLocation.indexOf("/horoscopo-semanal/geminis/") !== -1) {
        curSign = "geminis";
    } else if (curLocation.indexOf("/horoscopo-semanal/cancer/") !== -1) {
        curSign = "cancer";
    } else if (curLocation.indexOf("/horoscopo-semanal/leo/") !== -1) {
        curSign = "leo";
    } else if (curLocation.indexOf("/horoscopo-semanal/virgo/") !== -1) {
        curSign = "virgo";
    } else if (curLocation.indexOf("/horoscopo-semanal/libra/") !== -1) {
        curSign = "libra";
    } else if (curLocation.indexOf("/horoscopo-semanal/escorpio/") !== -1) {
        curSign = "escorpio";
    } else if (curLocation.indexOf("/horoscopo-semanal/sagitario/") !== -1) {
        curSign = "sagitario";
    } else if (curLocation.indexOf("/horoscopo-semanal/capricornio/") !== -1) {
        curSign = "capricornio";
    } else if (curLocation.indexOf("/horoscopo-semanal/acuario/") !== -1) {
        curSign = "acuario";
    } else if (curLocation.indexOf("/horoscopo-semanal/piscis/") !== -1) {
        curSign = "piscis";
    }
    return curSign !== "";
}

/**
 * Determine if the page is for monthly horoscope and set the curSign
 * @returns {boolean}
 */
function isMonthlyHoroscope() {
    curSign = "";
    if (curLocation.indexOf("/horoscopo-mensual/aries/") !== -1) {
        curSign = "aries";
    } else if (curLocation.indexOf("/horoscopo-mensual/tauro/") !== -1) {
        curSign = "tauro";
    } else if (curLocation.indexOf("/horoscopo-mensual/geminis/") !== -1) {
        curSign = "geminis";
    } else if (curLocation.indexOf("/horoscopo-mensual/cancer/") !== -1) {
        curSign = "cancer";
    } else if (curLocation.indexOf("/horoscopo-mensual/leo/") !== -1) {
        curSign = "leo";
    } else if (curLocation.indexOf("/horoscopo-mensual/virgo/") !== -1) {
        curSign = "virgo";
    } else if (curLocation.indexOf("/horoscopo-mensual/libra/") !== -1) {
        curSign = "libra";
    } else if (curLocation.indexOf("/horoscopo-mensual/escorpio/") !== -1) {
        curSign = "escorpio";
    } else if (curLocation.indexOf("/horoscopo-mensual/sagitario/") !== -1) {
        curSign = "sagitario";
    } else if (curLocation.indexOf("/horoscopo-mensual/capricornio/") !== -1) {
        curSign = "capricornio";
    } else if (curLocation.indexOf("/horoscopo-mensual/acuario/") !== -1) {
        curSign = "acuario";
    } else if (curLocation.indexOf("/horoscopo-mensual/piscis/") !== -1) {
        curSign = "piscis";
    }
    return curSign !== "";
}


// in this point in curSign we have the current sign

function todayHoroscopeSetup() {
    function getTodayDate() {
        let d = new Date();
        let month = '' + (d.getMonth() + 1);
        let day = '' + d.getDate();
        let year = '' + d.getFullYear();
        if (month.length < 2)
            month = '0' + month;
        if (day.length < 2)
            day = '0' + day;
        return [year, month, day].join('-');
    }

    assignValues(cache, getTodayDate(), curSign);
}

function tomorrowHoroscopeSetup() {
    /**
     * @returns {string yyyy-mm-dd}
     */
    function getTomorrowDate() {
        let d = new Date();
        d.setDate(d.getDate() + 1);
        let month = '' + (d.getMonth() + 1);
        let day = '' + d.getDate();
        let year = '' + d.getFullYear();
        if (month.length < 2)
            month = '0' + month;
        if (day.length < 2)
            day = '0' + day;
        return [year, month, day].join('-');
    }

    assignValues(cache, getTomorrowDate(), curSign);
}

function weeklyHoroscopeSetup() {
    /**
     * @returns {string yyyy-mm-dd}
     */

    function getFirstMonday() {
        const now = new Date()
        const today = new Date(now)
        today.setMilliseconds(0)
        today.setSeconds(0)
        today.setMinutes(0)
        today.setHours(0)

        const prevMonday = new Date(today)

        do {
            prevMonday.setDate(prevMonday.getDate() - 1) // Going back 1 day until necessary
        } while (prevMonday.getDay() !== 1)

        let month = '' + (prevMonday.getMonth() + 1);
        let day = '' + prevMonday.getDate();
        let year = '' + prevMonday.getFullYear();
        if (month.length < 2)
            month = '0' + month;
        if (day.length < 2)
            day = '0' + day;
        return [year, month, day].join('-');
    }

    assignValues(cache_semana, getFirstMonday(), curSign);
}


function monthlyHoroscopeSetup() {
    /**
     * @returns {string yyyy-mm-dd}
     */

    function getMonthInit() {
        const now = new Date()
        const today = new Date(now)
        today.setMilliseconds(0)
        today.setSeconds(0)
        today.setMinutes(0)
        today.setHours(0)

        let month = '' + (today.getMonth() + 1);
        let day = '01';
        let year = '' + today.getFullYear();
        if (month.length < 2)
            month = '0' + month;
        if (day.length < 2)
            day = '0' + day;
        return [year, month, day].join('-');
    }

    assignValues(cache_mes, getMonthInit(), curSign);
}

/**
 *  Assign values only if the horoscope hasn't been defined
 * @param horoscopo array
 */
function assignValues(cache, fecha, signo) {
    let requiredTypes = 4;
    let obtainedTypes = 0;
    for (let i in cache) {
        if (cache[i]["fecha"] == fecha && cache[i]["slug_signo"] == signo &&
            cache[i]["slug_tipo"] == "sal" && cache[i]["texto"].trim() !== ""
        ) {
            document.getElementById("horoscopo-salud").innerText = cache[i]["texto"];
            obtainedTypes++;
        }
        if (cache[i]["fecha"] == fecha && cache[i]["slug_signo"] == signo &&
            cache[i]["slug_tipo"] == "tyd" && cache[i]["texto"].trim() !== ""
        ) {
            document.getElementById("horoscopo-trabajo-y-dinero").innerText = cache[i]["texto"];
            obtainedTypes++;
        }
        if (cache[i]["fecha"] == fecha && cache[i]["slug_signo"] == signo &&
            cache[i]["slug_tipo"] == "amo" && cache[i]["texto"].trim() !== ""
        ) {
            document.getElementById("horoscopo-amor").innerText = cache[i]["texto"];
            obtainedTypes++;
        }
        if (cache[i]["fecha"] == fecha && cache[i]["slug_signo"] == signo &&
            cache[i]["slug_tipo"] == "nos" && cache[i]["texto"].trim() !== ""
        ) {
            document.getElementById("horoscopo-numeros").innerText = cache[i]["texto"];
            obtainedTypes++;
        }
        // optimization, as soon as we found all three we need to search further
        if (obtainedTypes == requiredTypes) {
            break;
        }
    }
    console.log("loaded for", fecha, signo);
}

// function clearValues() {
//     document.getElementById("horoscopo-salud").textContent = "";
// }


