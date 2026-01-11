document.addEventListener('DOMContentLoaded', function () {
    iniciarApp();
});

function iniciarApp() {
    buscarPorFecha();
}

function buscarPorFecha() {
    //console.log('Desde buscar por fecha');
    const fechaInput = document.querySelector('#fecha');
    fechaInput.addEventListener('input', function (e) {
        //console.log('Nueva Fecha');
        const fechaSeleccionada = e.target.value;
        //console.log(fechaSeleccionada);
        window.location = `?fecha=${fechaSeleccionada}`;//redireccion por querystring 
    });
}