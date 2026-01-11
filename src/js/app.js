let paso = 1;
const pasoInicial = 1;
const pasoFinal = 3;

const cita = {
  id: "",
  nombre: "",
  fecha: "",
  hora: "",
  servicios: [],
};

document.addEventListener("DOMContentLoaded", function () {
  iniciarApp();
});

function iniciarApp() {
  mostrarSeccion(); // Muestra y oculta las secciones
  tabs(); // Cambia la sección cuando se presonan los tabs
  botonesPaginador(); //Agrega o quita los botones del paginador
  paginaSiguiente();
  paginaAnterior();

  consultarAPI(); // COnsulta la API en el backend de PHP

  idCliente();
  nombreCliente(); // Añade el nombre del cliente al objeto de cita
  seleccionarFecha(); // Anñade la fecha a la cita en el objeto
  seleccionarHora(); // Añade la hora de la cita en el objeto

  mostrarResumen(); //Muestra el resumen de la cita
}
function mostrarSeccion() {
  // Ocualtar la sección que tenga la clase de mostrar
  const seccionAnterior = document.querySelector(".mostrar");
  if (seccionAnterior) {
    seccionAnterior.classList.remove("mostrar");
  }

  // Seleccionar la sección con el paso...
  // ``son template strings
  const pasoSelecctor = `#paso-${paso}`;
  const seccion = document.querySelector(pasoSelecctor);
  seccion.classList.add("mostrar");

  // Quita la clase actual al tab anterior
  const tabAnterior = document.querySelector(".actual");
  if (tabAnterior) {
    tabAnterior.classList.remove("actual");
  }

  //Resalta el tab actual
  const tab = document.querySelector(`[data-paso="${paso}" ]`);
  tab.classList.add("actual");
}
function tabs() {
  const botones = document.querySelectorAll(".tabs button");

  botones.forEach((boton) => {
    boton.addEventListener("click", function (e) {
      paso = parseInt(e.target.dataset.paso);

      mostrarSeccion();

      botonesPaginador();
    });
  });
}

function botonesPaginador() {
  const paginaAnterior = document.querySelector("#anterior");
  const paginaSiguiente = document.querySelector("#siguiente");

  if (paso === 1) {
    paginaAnterior.classList.add("ocultar");
    paginaSiguiente.classList.remove("ocultar");
  } else if (paso === 3) {
    paginaAnterior.classList.remove("ocultar");
    paginaSiguiente.classList.add("ocultar");

    mostrarResumen();
  } else {
    paginaAnterior.classList.remove("ocultar");
    paginaSiguiente.classList.remove("ocultar");
  }

  mostrarSeccion();
}

function paginaAnterior() {
  const paginaAnterior = document.querySelector("#anterior");
  paginaAnterior.addEventListener("click", function () {
    if (paso <= pasoInicial) return;
    paso--;
    botonesPaginador();
  });
}
function paginaSiguiente() {
  const paginaSiguiente = document.querySelector("#siguiente");
  paginaSiguiente.addEventListener("click", function () {
    if (paso >= pasoFinal) return;
    paso++;
    botonesPaginador();
  });
}

async function consultarAPI() {
  try {
    //const url = `${location.origin}/api/servicios`;
    const url = '/api/servicios';
    const resultado = await fetch(url);
    const servicios = await resultado.json();
    // console.log(servicios);
    mostrarServicios(servicios);
  } catch (error) {
    console.log(error);
  }
}

function mostrarServicios(servicios) {
  servicios.forEach((servicio) => {
    const { id, nombre, precio } = servicio;

    const nombreServicio = document.createElement("P");
    nombreServicio.classList.add("nombre-servicio");
    nombreServicio.textContent = nombre;

    const precioServicio = document.createElement("P");
    precioServicio.classList.add("precio-servicio");
    precioServicio.textContent = `$${precio}`;

    const servicioDiv = document.createElement("DIV");
    servicioDiv.classList.add("servicio");
    servicioDiv.dataset.idServicio = id;
    servicioDiv.onclick = function () {
      seleccionarServicio(servicio);
    };

    servicioDiv.appendChild(nombreServicio);
    servicioDiv.appendChild(precioServicio);

    document.querySelector("#servicios").appendChild(servicioDiv);
  });
}

function seleccionarServicio(servicio) {
  const { id } = servicio;
  const { servicios } = cita; //{} extrae el arreglo de servicios

  // Identifica el elemento al que se le da click
  const divServicio = document.querySelector(`[data-id-servicio="${id}"]`);

  // Comprobar si un servicio ya fue agregado
  if (servicios.some((agregado) => agregado.id === id)) {
    // después del .some es un callback
    //console.log("Ya está agregado");
    // Eliminarlo
    cita.servicios = servicios.filter((agregado) => agregado.id !== id);
    divServicio.classList.remove("seleccionado");
  } else {
    // console.log("Articulo nuevo, no estaba agregado");
    // Agregarlo
    cita.servicios = [...servicios, servicio]; // ...servicios, toma una copia del arreglo servicios y agrega un nuevo servicio
    divServicio.classList.add("seleccionado");
  }

  //   console.log(servicio);
  //console.log(cita);
}

function idCliente() {
  cita.id = document.querySelector("#id").value;
}

function nombreCliente() {
  cita.nombre = document.querySelector("#nombre").value;
  //   cita.nombre = nombre;
}

function seleccionarFecha() {
  const inputFecha = document.querySelector("#fecha");
  inputFecha.addEventListener("input", function (e) {
    //console.log(e.target.value);
    //cita.fecha = inputFecha.value;
    const dia = new Date(e.target.value).getUTCDay(); // getUTCDay se usa para obtener el número del día
    //console.log(dia);
    if ([6, 0].includes(dia)) {
      // con el 6 y 0 se controla que día van haber citas
      // includes es un array method
      e.target.value = "";
      //console.log("Sabados y domingos no abrimos");
      mostrarAlerta("Fines de semana no permitidos", "error", ".formulario");
    } else {
      //console.log("Correcto");
      cita.fecha = e.target.value;
    }
  });
}

function seleccionarHora() {
  const inputHora = document.querySelector("#hora");
  inputHora.addEventListener("input", function (e) {
    console.log(e.target.value);

    const horaCita = e.target.value;
    const hora = horaCita.split(":")[0]; // split es un separador
    //console.log(hora);
    if (hora < 10 || hora > 18) {
      //console.log('Horas no validas');
      e.target.value = "";
      mostrarAlerta("Hora no válida", "error", ".formulario");
    } else {
      //console.log('Hora valida');
      cita.hora = e.target.value;
      //console.log(cita);
    }
  });
}

function mostrarAlerta(mensaje, tipo, elemento, desaparece = true) {
  //Evita la aparición de más de una alerta de error
  const alertaPrevia = document.querySelector(".alerta");
  if (alertaPrevia) {
    alertaPrevia.remove();
  }

  //Scripting para crear la alerta
  const alerta = document.createElement("DIV");
  alerta.textContent = mensaje;
  alerta.classList.add("alerta");
  alerta.classList.add(tipo);

  //console.log(alerta);
  //const formulario = document.querySelector('#paso-2 p');
  const referencia = document.querySelector(elemento);
  referencia.appendChild(alerta);

  if (desaparece) {
    // Eliminar la alerta
    setTimeout(() => {
      alerta.remove();
    }, 3000);
  }
}

function mostrarResumen() {
  const resumen = document.querySelector(".contenido-resumen");

  resumen.innerHTML = "";
  //Limpiar el contenido de resumen
  while (resumen.firstChild) {
    resumen.removeChild(resumen.firstChild);
  }

  //console.log(Object.values(cita));

  //console.log(cita.servicios.length);

  if (Object.values(cita).includes("") || cita.servicios.length === 0) {
    //console.log('Hacen falta datos o servicios');
    mostrarAlerta(
      "Faltan datos de servicios, fecha u hora",
      "error",
      ".contenido-resumen",
      false
    );

    return;
  }
  //Formatear ek div de resumen
  const { nombre, fecha, hora, servicios } = cita;

  //Heading para servicios y resumen
  const headingServicios = document.createElement("H3");
  headingServicios.textContent = "Resumen de Servicios";
  resumen.appendChild(headingServicios);

  //Iterando y mostrando los servicios
  servicios.forEach((servicio) => {
    const { id, precio, nombre } = servicio;
    const contenedorServicio = document.createElement("DIV");
    contenedorServicio.classList.add("contenedor-servicio");

    const textoServicio = document.createElement("P");
    textoServicio.textContent = nombre;

    const precioServicio = document.createElement("P");
    precioServicio.innerHTML = `<span>Precio:</span> $${precio}`;

    contenedorServicio.appendChild(textoServicio);
    contenedorServicio.appendChild(precioServicio);

    resumen.appendChild(contenedorServicio);
  });

  //Heading para cita y resumen
  const headingCita = document.createElement("H3");
  headingCita.textContent = "Resumen de Cita";
  resumen.appendChild(headingCita);

  const nombreCliente = document.createElement("P");
  nombreCliente.innerHTML = `<span>Nombre:</span> ${nombre}`;

  //Formatear la fecha en español
  const fechaObj = new Date(fecha); //Se instancia el nuevo objeto 'fecha', así se tiene acceso a cada uno de los valores de fecha de forma indivoidual
  const mes = fechaObj.getMonth();
  const dia = fechaObj.getDate() + 2;
  const year = fechaObj.getFullYear();

  const fechaUTC = new Date(Date.UTC(year, mes, dia));
  //console.log(fechaUTC);

  const opciones = {
    weekday: "long",
    year: "numeric",
    month: "long",
    day: "numeric",
  };
  const fechaFormateada = fechaUTC.toLocaleDateString("es-MX", opciones);
  console.log(fechaFormateada);

  const fechaCita = document.createElement("P");
  fechaCita.innerHTML = `<span>Fecha:</span> ${fechaFormateada}`;

  const horaCita = document.createElement("P");
  horaCita.innerHTML = `<span>Hora:</span> ${hora} Horas`;

  // Botón para crear una cita
  const botonReservar = document.createElement("BUTTON");
  botonReservar.classList.add("boton");
  botonReservar.textContent = "Reservar Cita";
  // Cuando se asocia un evento con onclick, no se puede colocar el parentesis en reservarCita porque llama la función.
  // EN caso de querer un paramaetro, se recomienda un callback, es decir, una función
  botonReservar.onclick = reservarCita;

  resumen.appendChild(nombreCliente);
  resumen.appendChild(fechaCita);
  resumen.appendChild(horaCita);

  resumen.appendChild(botonReservar);

  //console.log(nombreCliente);
}

async function reservarCita() {

  const { fecha, hora, servicios, id } = cita;

  const idServicios = servicios.map(servicio => servicio.id);
  //console.log(idServicios);
  //return;

  const datos = new FormData();
  datos.append("fecha", fecha);
  datos.append("hora", hora);
  datos.append("usuarioId", id);
  datos.append("servicios", idServicios);

  //console.log([...datos]);
  //return;

  try {
    //Petición hacia la API
    const url = '/api/citas';

    // await se usa cuando se demora en dar respuesta el servidor, detiene la ejecución del código mientras se obtiene la respuesta
    const respuesta = await fetch(url, {
      method: "POST",
      body: datos
    });
    //console.log(respuesta);

    const resultado = await respuesta.json();
    console.log(resultado.resultado);
    //console.log([...datos]);

    if (resultado.resultado) {
      //Uso de Sweetalert2
      Swal.fire({
        icon: "success",
        title: "Cita Creada",
        text: "Tu cita fue creada correctamente",
        button: 'OK'
      }).then(() => {
        setTimeout(() => {
          window.location.reload();
        }, 1000);
      })
    }
  } catch (error) {
    Swal.fire({
      icon: "error",
      title: "Error",
      text: "Hubo un error al guardar la cita"
    });
  }


}
