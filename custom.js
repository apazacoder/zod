document.addEventListener("DOMContentLoaded", function (event) {
    // ask for the weekly horoscope page, if it's loaded start the instance
    if (isWeeklyHoroscopePage) {
        weeklyHoroscopeSetup();
    }
});

function isWeeklyHoroscopePage() {
    return window.location.href.indexOf("admin.php") !== -1
        && document.getElementsByTagName("title")[0].innerText.indexOf("Horoscopos semanales") !== -1
}

const API_PREFIX = "/wp-json/zs/v1";

let appinstance = null;

function weeklyHoroscopeSetup() {
    appinstance = new Vue({
        el: document.getElementById("zodiaco-app"),
        data() {
            return {
                message: "Test message",
                composicion: [],
                composicionSemana: [],
                composicionMes: [],
                semanaActual: -1,
                diaActual: -1,
                signoActual: -1,
                mesActual: -1,
                datosCargados: false,
                processing: false,
                processResponse: [],
                formModified: false
            }
        },
        mounted: function () {
            this.cargarHoroscopos();
            this.preventLeaving();
        },
        methods: {
            setModifiedForm: function(){
                this.processResponse = [];
                this.formModified = true;
            },
            cargarHoroscopos: function () {
                this.processing = true;
                // console.log("Intento de carga de horóscopos");
                let xhr = new XMLHttpRequest();
                xhr.open('GET', API_PREFIX + '/horoscopos');
                xhr.send(null);
                xhr.onreadystatechange = () => {
                    let DONE = 4; // request done.
                    let OK = 200; // status 200 is a successful return.
                    if (xhr.readyState === DONE) {
                        if (xhr.status === OK) {
                            this.processing = false;
                            let response = JSON.parse(xhr.responseText);
                            this.composicion = response.composicion;
                            this.composicionSemana = response.composicionSemana;
                            this.composicionMes = response.composicionMes;
                            if (this.datosCargados == false) {
                                this.setIndicesIniciales();
                                this.datosCargados = true;
                            }
                        } else {
                            console.log('Error: ' + xhr.status);
                        }
                    }
                };
            },
            setIndicesIniciales() {
                this.semanaActual = 1;
                this.diaActual = 0;
                this.signoActual = 0;
                this.mesActual = 0;
            },

            cambiarSemana: function (index) {
                // console.log("escogida la " + index);
                this.semanaActual = index;
            },
            cambiarDia: function (index) {
                // console.log("dia " + index);
                this.diaActual = index;
            },
            cambiarSigno: function (index) {
                // console.log("signo " + index);
                this.signoActual = index;
            },
            cambiarMes: function(index){
                this.mesActual = index;
            },
            guardarHoroscopos: function () {
                this.processing = true;
                // console.log("Intento de guardado de horóscopos");
                const data = {
                    composicion: this.composicion,
                    composicion_semana: this.composicionSemana,
                    composicion_mes: this.composicionMes,
                };
                let xhr = new XMLHttpRequest();
                xhr.open('POST', API_PREFIX + '/horoscopos');
                xhr.send(JSON.stringify(data));
                xhr.onreadystatechange = () => {
                    let DONE = 4; // request done.
                    let OK = 200; // status 200 is a successful return.
                    if (xhr.readyState === DONE) {
                        if (xhr.status === OK) {
                            this.processing = false;
                            this.processResponse = JSON.parse(xhr.responseText);
                            if (this.processResponse.hasOwnProperty("errores") &&
                                this.processResponse.errores.length === 0){
                                    this.cargarHoroscopos();
                                    this.formModified = false;
                            }
                        } else {
                            console.log('Error: ' + xhr.status);
                        }
                    }
                };
            },
            preventLeaving: function(){
                window.onbeforeunload = (e) =>{
                    var message = "¿Confirma que quiere salir sin guardar?",
                        e = e || window.event;
                    // For IE and Firefox
                    if (e && this.formModified) {
                        e.returnValue = message;
                    }

                    // For Safari
                    if (this.formModified){
                        return message;
                    }
                };
            }
        }
    });
}
