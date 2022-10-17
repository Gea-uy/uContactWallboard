
// const apiConfigUrl = "http://localhost/Wallboard/Api/config/";
// const apiCampaignUrl = "http://localhost/Wallboard/Api/campaign/";

const apiConfigUrl = "https://soporteuc.isbel.com.uy:8443/Wallboard/Api/config/";
const apiCampaignUrl = "https://soporteuc.isbel.com.uy:8443/Wallboard/Api/campaign/";


// const apiConfigUrl = "https://ucontactcloud.casmu.com:8443/Wallboard/Api/config/";
// const apiCampaignUrl = "https://ucontactcloud.casmu.com:8443/Wallboard/Api/campaign/";

var toastLiveExample;
let selectedQueue;

$().ready(cargaPronta);


function cargaPronta() {

    inicializarEventos();
}
function inicializarEventos() {
    getQueuesInfo();
    loadUtilitys();
}


//GET QUEUES INFO
function getQueuesInfo() {
    var dt = new Date();
    var time = dt.getHours() + ":" + dt.getMinutes() + ":" + dt.getSeconds();

    $.ajax({
        type: 'GET',
        url: apiConfigUrl + `?fecha=${time}`,
        success: getQueuesInfoSuccess,
        error: getQueuesInfoError,
        complete: getQueuesInfoComplete
    })
}
function getQueuesInfoSuccess(data) {
    // console.log(data);
    fullQueuesSelect(data.data.datoscampana);

}
function getQueuesInfoError(data) {

    console.log(data);
    // console.log(data.status);
    if (data.status == 401) {
        getQueuesInfo()
    } else {
        showToast('danger', "Error al obtener información de uContact");
    }

}
function getQueuesInfoComplete(data) {
    // console.log("getQueuesInfoComplete");
    hideSpinner();
}


// SELECCIONADOR DE CAMPAÑAS
function fullQueuesSelect(queues) {

    if (Object.keys(queues).length > 0) {

        $("#selectCampanas").empty();

        queues.map((camp) => {
            $("#selectCampanas").append(`
            <li><a class="dropdown-item selectQueuesItem" onClick="changeSelectedQueue(this.id)" href="#" id="${camp}">${camp}</a></li>`);
        })
    }
}
function changeSelectedQueue(queue) {

    $("#labelSelectQueue").html(queue);
    showSpinner();
    if (!selectedQueue) {
        selectedQueue = queue;
        getCampaignData();
    } else {
        selectedQueue = queue;
    }
    // console.log("selectedQueue " + selectedQueue);
}

//OBTENER DATOS DE LA CAMPAÑA SELECCIONADA
function getCampaignData() {

    let fecha = getDate();

    $.ajax({
        type: 'GET',
        url: apiCampaignUrl + `?campaign=${selectedQueue}&fecha=${fecha}`,
        success: getCampaignDataSuccess,
        error: getCampaignDataError,
        complete: getCampaignDataComplete
    })
}
function getCampaignDataSuccess(data) {
    // console.log(data);
    updateWallboard(data.data.datoscampana, data.data.parametros, data.data.ultimaActualización);

    if (data.data.callbacks != null && data.data.datoscampana.campaign == selectedQueue) {
        showCallbacks();
        updateCallbacks(data.data.callbacks);
    } else {
        hideCallbacks();
    }
}
function getCampaignDataError(data) {
    // showToast('danger', "Error al obtener información de uContact");
    console.log(data);

    console.log(data.status);
    if (data.status == 401) {
        getCampaignData();
    } else {
        showToast('danger', "Error al obtener información de uContact");
        setTimeout(() => {
            getCampaignData();
        }, 5000);
    }


}
function getCampaignDataComplete(data) {
    // hideSpinner();
}



//UPDATE WALLBOARD DATA
function updateWallboard(queue, parametros, fecha) {
    // console.log("Datos actualizados -> " + getDate());

    hideWelcomeMessage();
    updateUsersStatus(queue);
    updateCallsCounters(queue, parametros);
    updateStatsCounters(queue, parametros);

    $("#navbarTime").html(`Actualizado ` + fecha);
    if (queue.campaign == selectedQueue) {
        hideSpinner();
    }
    setTimeout(getCampaignData, 5000);
}
function hideWelcomeMessage() {

    $("#welcomeMessage").addClass('d-none');
    $("#dataDashboard").removeClass('d-none');
}
function updateUsersStatus(queue) {

    $("#usuariosDisponibles").html(queue.availables);
    $("#usuariosOcupados").html(queue.busy);
    $("#usuariosPausados").html(queue.paused);
    $("#usuariosWrapup").html(queue.onwrapup);

}
function updateCallbacks(data) {

    $("#rellamadaMarcador").html(data[selectedQueue].CompletedCalls);
    $("#rellamadaProcesando").html(data[selectedQueue].CallsSpool);
    $("#rellamadaAgendado").html(data[selectedQueue].CallsScheduler);
}
function hideCallbacks() {

    $("#col-callbacks").addClass("d-none");
    $("#col-callbacks").removeClass("d-flex");
}
function showCallbacks() {
    $("#col-callbacks").removeClass("d-none");
    $("#col-callbacks").addClass("d-flex");
}




//LLAMADAS
function updateCallsCounters(queue, parametros) {

    $("#llamadasAtendidas").html(queue.completed);
    $("#llamadasAbandonadas").html(queue.abandoned);
    $("#llamadasEspera").html(queue.acd);
    $("#llamadasTotal").html(queue.totalcalls);

    if (queue.acd >= parametros.umbralesLlamadas[0] && queue.acd < parametros.umbralesLlamadas[1]) {
        setLlamadasEsperandoWarning();
    } else if (queue.acd >= parametros.umbralesLlamadas[1]) {
        setLlamadasEsperandoDanger();
    } else if (queue.acd < parametros.umbralesLlamadas[0]) {
        setLlamadasEsperandoNormal();
    } else {
        setLlamadasEsperandoNormal();
    }


}
function setLlamadasEsperandoWarning() {

    $("#llamadasesperandoDiv1").removeClass("bg-danger");

    $("#llamadasesperandoDiv2").removeClass("px-1");
    // $("#llamadasesperandoDiv2").addClass("p-0");

    $("#llamadasesperandoDiv2").removeClass("fs-2");
    $("#llamadasesperandoDiv2").addClass("fs-1");


    $("#llamadasesperandoDiv1").addClass("bg-warning");
    $("#llamadasesperandoDiv2").addClass("fw-bold");

}
function setLlamadasEsperandoDanger() {

    $("#llamadasesperandoDiv1").removeClass("bg-warning");

    $("#llamadasesperandoDiv2").removeClass("px-1");
    // $("#llamadasesperandoDiv2").addClass("p-0");

    $("#llamadasesperandoDiv2").removeClass("fs-2");
    $("#llamadasesperandoDiv2").addClass("fs-1");


    $("#llamadasesperandoDiv1").addClass("bg-danger");
    $("#llamadasesperandoDiv2").addClass("fw-bold");

}
function setLlamadasEsperandoNormal() {

    $("#llamadasesperandoDiv2").addClass("px-1");
    $("#llamadasesperandoDiv2").removeClass("p-0");

    $("#llamadasesperandoDiv2").addClass("fs-2");
    $("#llamadasesperandoDiv2").removeClass("fs-1");


    $("#llamadasesperandoDiv1").removeClass("bg-danger");
    $("#llamadasesperandoDiv1").removeClass("bg-warning");
    $("#llamadasesperandoDiv2").removeClass("fw-bold");

}

//INDICADORES
function updateStatsCounters(queue, parametros) {

    $("#indicadorTiempoHablado").html(queue.talked.slice(0, 8));
    $("#indicadorTiempoEspera").html(queue.hold.slice(0, 8));
    $("#indicadorNivelServicio").html(queue.servicelevel);

    if (queue.servicelevel != 0) {
        if (parseInt(queue.servicelevel) <= parseInt(parametros.nivelservicio[0])) {
            setNivelServicioDanger();
        } else if (parseInt(queue.servicelevel) > parseInt(parametros.nivelservicio[0]) && parseInt(queue.servicelevel) < parseInt(parametros.nivelservicio[1])) {
            setNivelServicioWarning();
        } else if (parseInt(queue.servicelevel) >= parseInt(parametros.nivelservicio[1])) {
            setNivelServicioNormal();
        } else {
            setNivelServicioNormal();
        }
    } else {
        setNivelServicioNormal();
    }


}
function setNivelServicioWarning() {

    $("#trnivelservicio").removeClass("bg-danger");
    $("#trnivelservicio").addClass("bg-warning");
    $("#trnivelservicio").addClass("fw-bold");
}
function setNivelServicioDanger() {
    $("#trnivelservicio").removeClass("bg-warning");
    $("#trnivelservicio").addClass("bg-danger");
    $("#trnivelservicio").addClass("fw-bold");
}
function setNivelServicioNormal() {
    $("#trnivelservicio").removeClass("bg-warning");
    $("#trnivelservicio").removeClass("bg-danger");
    $("#trnivelservicio").removeClass("fw-bold");
}


//GET DATE
function cargarHoraNavbar() {

    $("#navbarTime").html(getDate(false));
    setInterval(() => {
        cargarHoraNavbar();
    }, 60000);
}
let getDate = (seconds = true) => {


    const today = new Date();
    let h = today.getHours();
    let m = today.getMinutes();
    let s = today.getSeconds();
    m = checkTime(m);
    s = checkTime(s);

    if (seconds) {
        var time = h + ":" + m + ":" + s;
    } else {
        var time = h + ":" + m;
    }

    return time;
}
function checkTime(i) {
    if (i < 10) { i = "0" + i };  // add zero in front of numbers < 10
    return i;
}
//TOASTS
function showToast(type, message) {

    $("#Toast").removeClass("bg-danger");
    $("#Toast").removeClass("bg-success");


    switch (type) {
        case 'success':
            var toast = new bootstrap.Toast(toastLiveExample);
            $("#Toast").addClass("bg-success");
            $("#msjToast").html(message);
            toast.show()
            break;
        case 'danger':
            var toast = new bootstrap.Toast(toastLiveExample);
            $("#Toast").addClass("bg-danger");
            $("#msjToast").html(message);
            toast.show()
            break;
        default:
            break;
    }
}

//Spinner
function showSpinner(timer) {

    if (!timer) {

        $(".loading").show();
    } else {

        $(".loading").show();
        setTimeout(hideSpinner, timer);

    }
}
function hideSpinner() {

    $(".loading").hide();

}
function loadUtilitys() {
    $(document).on('keypress', function (e) {
        if (e.which == 13) {
            //do something
        }
    });

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
    toastLiveExample = document.getElementById('Toast');
}





function startQueueData() {
    if (apiConfigUrl.indexOf("casmu") == -1) {

        getQueueCallbacks();
    } else {
        hideCallbacks();
    }
    getQueueData();
}
//GET SELECTED QUEUE DATA
function getQueueData() {

    let fecha = getDate();

    $.ajax({
        type: 'GET',
        url: apiConfigUrl + `?fecha=${fecha}`,
        success: getQueueDataSuccess,
        error: getQueueDataError,
        complete: getQueueDataComplete
    })
}
function getQueueDataSuccess(data) {

    let queues = data.data.datoscampana;
    console.log(queues[selectedQueue]);
    updateWallboard(queues[selectedQueue], data.data.parametros);

}
function getQueueDataError(data) {
    showToast('danger', "Error al obtener información de uContact");
    // setTimeout(startQueueData, 5000);
    // clearInterval(updateQueueInterval);
    // console.log(data);

}
function getQueueDataComplete() {
    hideSpinner();
}


//GET CALLBACKS
function getQueueCallbacks() {

    let fecha = getDate();

    $.ajax({
        type: 'GET',
        url: apiCallbacksUrl + `?campaign=${selectedQueue}&fecha=${fecha}`,
        success: getQueueCallbacksSuccess,
        error: getQueueCallbacksError,
        complete: getQueueCallbacksComplete
    })
}
function getQueueCallbacksSuccess(data) {
    console.log(data);
    if (data.data) {
        $("#col-callbacks").removeClass("d-none");
        $("#col-callbacks").addClass("d-flex");
        updateCallbacks(data);
    } else {
        // showToast("success", "Campaña sin MARCADOR asociado");
        hideCallbacks();
    }

    // console.log(data);


}
function getQueueCallbacksError(data) {
    console.log(data);
}
function getQueueCallbacksComplete() {
    hideSpinner();
}