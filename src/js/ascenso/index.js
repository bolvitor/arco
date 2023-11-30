import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import { validarFormulario, Toast, confirmacion } from "../funciones";
import Datatable from "datatables.net-bs5";
import { lenguaje } from "../lenguaje";
import readXlsxFile from 'read-excel-file';

const exelInput = document.getElementById('exel-input');

const formulario = document.querySelector('#formularioAscensos');
const formPromo = document.querySelector('#formularioAscenso');
const btnBuscar = document.getElementById('btnBuscar');
const btnPdf = document.getElementById('btnPdf');
const btnexcel = document.getElementById('btnexcel');

const botonRecargar = document.getElementById('btnLimpiar');

const td_demerito = document.getElementById('td_demeritos');
const td_punteo = document.getElementById('td_punteo');



exelInput.addEventListener('change', async function () {
    try {
        const content = await readXlsxFile(exelInput.files[0]);

        // Obtén los encabezados
        const headers = content[0];
        // Almacena los datos en un array de objetos
        const datos = [];
        // Itera sobre los datos (ignora el primer elemento que son los encabezados)
        for (let i = 1; i < content.length; i++) {
            const row = content[i];
            // Crea un objeto para representar la fila
            const fila = {};
            // Itera sobre los valores de la fila y asigna a las propiedades según los encabezados
            for (let j = 0; j < row.length; j++) {
                const header = headers[j];
                const value = row[j];
                // Asigna el valor al objeto usando el nombre de la columna como clave
                fila[header] = value;
            }
            // Agrega el objeto al array de datos
            datos.push(fila);
        }
        // Ahora, "datos" es un array de objetos, cada uno representa una fila del archivo Excel
        console.log(datos);
        // Puedes usar "datos" según tus necesidades, por ejemplo, enviarlo al servidor o realizar otras operaciones.
    } catch (error) {
        console.error('Error al procesar el archivo Excel:', error);
    }
});



let contador = 1;
const datatable = new Datatable('#tablaAscensos', {
    language: lenguaje,
    data: null,
    columns: [
        {
            title: 'NO',
            data: null,
            render: (data, type, row, meta) => {

                return type === 'display' ? contador + meta.row : contador + meta.row;
            }
        },
        {
            title: 'Catalogo',
            data: 'per_catalogo'
        },

        {
            title: 'Grado',
            data: 'grado'
        },
        {
            title: 'Nombre',
            data: 'nombre'
        },
        {
            title: 'Curso Ascenso',
            data: 'cursoAscenso1'
        },
        {
            title: 'Eva_Desempeño',
            data: 'desempenio1'
        },
        {
            title: 'Conducta Militar',
            data: 'demeritos1'
        },

        {
            title: 'Aptitud Fisica',
            data: 'pafeSQL1'
        },


        {
            title: 'Perfil_Biofisico',
            data: 'perfilBio1'
        },

        {
            title: 'Creditos',
            data: 'Meritos1'
        },

        {
            title: 'punteo total',
            data: 'punteo_total'
        },
        {
            title: 'Ver Detalles',
            render: (data, type, row) => {
                if (type === 'display') {
                    return `
                    <button class="btn btn-outline-dark" 
                                data-id="${data}" 
                                data-catalogo="${row.per_catalogo}" style="border: 3px solid #008000; border-radius: 8px;">

                        
                        <img src="./images/investigar.png" alt="Detalles" style="width: 60px; height: 50px; border: none;">
                        </button>`;
                } else {
                    return null;
                }
            }
        }
    ],
    order: [[10, 'desc']],

});





const buscar = async () => {

    const controller = new AbortController();
    const signal = controller.signal;

    if (!exelInput.files || exelInput.files.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: '¡Advertencia!',
            text: 'Debe seleccionar un archivo excel antes de buscar.',
        });
        return;
    }

    const perGradoSelect = formulario.per_grado;
    const selectedOption = perGradoSelect.options[perGradoSelect.selectedIndex];

    const per_grado = selectedOption.value;
    const codigo2 = selectedOption.getAttribute('data-codigo2');

    if (!per_grado) {
        Swal.fire({
            icon: 'info',
            title: '¡Advertencia!',
            text: 'Debe seleccionar un grado antes de buscar.',
        });
        return;
    }

    const fecha = formulario.fecha.value;
    if (!fecha) {
        Swal.fire({
            icon: 'info',
            title: '¡Advertencia!',
            text: 'Debe seleccionar una fecha antes de buscar.',
        });
        return;
    }



    const content = await readXlsxFile(exelInput.files[0]);
    const catalogos = content.slice(1).map(row => row[0]);

    const url = `/arco/API/ascenso/buscar?grado=${per_grado}&fecha=${fecha}&catalogos=${JSON.stringify(catalogos)}&grado2=${codigo2}`;
    const config = {
        method: 'GET',
        signal: signal,
    };


    const cancelButton = Swal.getCancelButton();
    if (cancelButton) {

        cancelButton.addEventListener('click', () => {

            controller.abort();
        });
    }

    try {
        Swal.fire({
            title: 'Buscando...',
            html: '<div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem; border-width: 0.4em;"><span class="sr-only"></span></div>',
            showConfirmButton: false,
            showCancelButton: true,
            allowOutsideClick: false,
            onBeforeOpen: () => {
                const cancelButton = Swal.getCancelButton();
                if (cancelButton) {
                    cancelButton.disabled = true;
                }

                Swal.showLoading();
            }
        });

        const respuesta = await fetch(url, config);
        const data = await respuesta.json();
        console.log('Datos recibidos:', data);

        datatable.clear().draw();


        const maxLength = Math.max(
            data.usuarios1.length,
            data.cursoAscenso1.length,
            data.meritos1.length,
            data.demeritos1.length,
            data.perfilBio1.length,
            data.desempenio1.length,
            data.pafeSQL1.length
        );


        for (let index = 0; index < maxLength; index++) {
            if (signal.aborted) {
                console.log('Búsqueda cancelada');
                break;
            }
            const usuariosData = {

                per_catalogo: data.usuarios1[index] ? data.usuarios1[index].per_catalogo : '',
                grado: data.usuarios1[index] ? data.usuarios1[index].grado : '',
                nombre: data.usuarios1[index] ? data.usuarios1[index].nombre : '',
                cursoAscenso1: data.cursoAscenso1[index] ? (data.cursoAscenso1[index].promedio === '' ? 'Pendiente' : parseFloat(data.cursoAscenso1[index].promedio)) : '',
                Meritos1: data.meritos1[index] ? parseFloat(data.meritos1[index].puntos_netos) : 0,
                demeritos1: data.demeritos1[index] ? parseFloat(data.demeritos1[index].punteo_demeritos) : 0,
                perfilBio1: data.perfilBio1[index] ? (data.perfilBio1[index].perfil_biofisico === '' ? 'Pendiente' : parseFloat(data.perfilBio1[index].perfil_biofisico)) : '',
                desempenio1: data.desempenio1[index] ? (data.desempenio1[index].resultado_final === '' ? 0 : parseFloat(data.desempenio1[index].resultado_final)) : 0,
                pafeSQL1: data.pafeSQL1[index] ? (data.pafeSQL1[index].suma_total === '' ? 0 : parseFloat(data.pafeSQL1[index].suma_total)) : 0,
                punteo_total: (
                    (
                        (data.desempenio1[index] ? (data.desempenio1[index].resultado_final === '' ? 0 : parseFloat(data.desempenio1[index].resultado_final)) : 0) +
                        (data.meritos1[index] ? parseFloat(data.meritos1[index].puntos_netos) : 0) +
                        (data.demeritos1[index] ? parseFloat(data.demeritos1[index].punteo_demeritos) : 0) +
                        (data.cursoAscenso1[index] ? (data.cursoAscenso1[index].promedio !== '' ? parseFloat(data.cursoAscenso1[index].promedio) : 0) : 0) +
                        (data.perfilBio1[index] ? (data.perfilBio1[index].perfil_biofisico === '' ? 0 : parseFloat(data.perfilBio1[index].perfil_biofisico)) : 0) +
                        (data.pafeSQL1[index] ? (data.pafeSQL1[index].suma_total === '' ? 0 : parseFloat(data.pafeSQL1[index].suma_total)) : 0)
                    )
                ).toFixed(2)
            };

            datatable.rows.add([usuariosData]).draw();
        }
        Swal.fire({
            icon: data.usuarios1 && data.usuarios1.length > 0 ? 'success' : 'info',
            title: data.usuarios1 && data.usuarios1.length > 0 ? '¡Resultados obtenidos!' : 'Sin resultados',
            text: data.usuarios1 && data.usuarios1.length > 0 ? 'Se encontraron registros' : 'No se encontraron registros para la búsqueda',
            didClose: () => {

                Swal.close();
            }
        });
        btnPdf.style.display = '';
        btnexcel.style.display = '';

    } catch (error) {

        if (error.name === 'AbortError') {

            console.log('Búsqueda cancelada');
        } else {
            console.error(error);

            Swal.fire({
                icon: 'error',
                title: '¡Error!',
                text: 'Hubo un error durante la búsqueda. Intentelo de nuevo',
            });
        }
    }
};


$('#tablaAscensos').on('click', '.btn-outline-dark', function () {

    const per_catalogo = parseInt($(this).data('catalogo'));


    buscarOficial(per_catalogo);
    llenarFormulario(per_catalogo);
});


let conta = 1;

const tablademeritos = new Datatable('#tablademeritos', {
    language: lenguaje,
    data: null,
    columns: [
        {
            title: 'NO',
            render: () => conta++
        },
        {
            title: 'Catalogo',
            data: 'det_catalogo'
        },
        {
            title: 'Grado',
            data: 'grado'
        },

        {
            title: 'Fecha',
            data: 'fecha'
        },

        {
            title: 'Descripcion',
            data: 'descripcion'
        },

        {
            title: 'Tipo',
            data: 'tipo'
        },
        {
            title: 'Cantidad',
            data: 'cantidad'
        },
    ],

});

const tablacursoAscenso = new Datatable('#tablacursoAscenso', {
    language: lenguaje,
    data: null,
    columns: [
        {
            title: 'NO',
            render: () => conta++
        },
        {
            title: 'Catalogo',
            data: 'cur_catalogo'
        },
        {
            title: 'Descripcion',
            data: 'descripcion'
        },
        {
            title: 'Fecha',
            data: 'cur_fec_fin'
        },

        {
            title: 'Punteo',
            data: 'cur_punteo'
        },
        {
            title: 'Promedio',
            data: 'promedio'
        },

    ],

});

const tablameritos = new Datatable('#tablameritos', {
    language: lenguaje,
    data: null,
    columns: [
        {
            title: 'NO',
            render: () => conta++
        },
        {
            title: 'Catalogo',
            data: 'catalogo'
        },
        {
            title: 'Tipo',
            data: 'tipo'
        },
        {
            title: 'Descripcion',
            data: 'descripcion'
        },
        {
            title: 'Meritos',
            data: 'meritos'
        },

    ],

});

const tablapafes = new Datatable('#tablapafes', {
    language: lenguaje,
    data: null,
    columns: [
        {
            title: 'NO',
            render: () => conta++
        },
        {
            title: 'Catalogo',
            data: 'not_catalogo'
        },
        {
            title: 'Grado',
            data: 'grado'
        },
        {
            title: 'Fecha',
            data: 'not_fecha',
            defaultContent: 's/r',
            render: function (data, type, row) {
                return data ? data : 'S/R';
            }
        },
        {
            title: 'Tipo',
            data: 'not_tipo'
        },
        {
            title: 'Periodo',
            data: 'not_periodo'
        },
        {
            title: 'Promedio',
            data: 'promedio',
            defaultContent: 's/r',
            render: function (data, type, row) {
                return data ? data : 'S/R';
            }
        },

    ],

});

const tablaperfilBio = new Datatable('#tablaperfilBio', {
    language: lenguaje,
    data: null,
    columns: [
        {
            title: 'NO',
            render: () => conta = 1
        },
        {
            title: 'Catalogo',
            data: 'e_catalogo'
        },

        {
            title: 'Fecha',
            data: 'e_fecha'
        },
        {
            title: 'Resultado',
            data: 'e_resultado'
        },
        {
            title: 'Diagnositico',
            data: 'diagnostico'
        },
        {
            title: 'Puntos',
            data: 'puntos'
        },

    ],

});

const tabladesempenio = new Datatable('#tabladesempenio', {
    language: lenguaje,
    data: null,
    columns: [
        {
            title: 'NO',
            render: () => conta++
        },
        {
            title: 'Catalogo',
            data: 'per_catalogo'
        },

        {
            title: 'Comando/Dependencia',
            data: 'eva_dest_actual',
            defaultContent: 's/r',
            render: function (data, type, row) {
                return data ? data : 'S/R';
            }
        },
        {
            title: 'Periodo',
            data: 'eva_periodo'
        },
        {
            title: 'puntuacion',
            data: 'notas',
            defaultContent: 's/r',
            render: function (data, type, row) {
                return data ? data : 'S/R';
            }
        },

    ],


});


const buscarOficial = async (per_catalogo) => {


    const fecha = formulario.fecha.value;

    try {
        const url = `/arco/API/ascenso/buscarOficial?per_catalogo=${per_catalogo}&fecha=${fecha}`;

        const headers = new Headers();
        headers.append("X-requested-With", "fetch");

        const config = {
            method: 'GET'
        };
        const respuesta = await fetch(url, config)
        const data = await respuesta.json();

        console.log(data);


        tablademeritos.clear().draw();
        tablacursoAscenso.clear().draw();
        tablameritos.clear().draw();
        tablapafes.clear().draw();
        tablaperfilBio.clear().draw();
        tabladesempenio.clear().draw();


        if (data.conducta) {
            td_demerito.innerHTML = data.conducta[0]["demeritos"]

            td_punteo.innerHTML = data.conducta[0]["punteo"]
        }
        if (data.demeritos) {
            conta = 1
            tablademeritos.rows.add(data.demeritos).draw();
        } else {

        }

        if (data.cursoAscenso) {
            conta = 1
            tablacursoAscenso.rows.add(data.cursoAscenso).draw();
        } else {

        }
        if (data.meritos) {
            conta = 1
            tablameritos.rows.add(data.meritos).draw();
        } else {


        }
        if (data.pafes) {
            conta = 1
            tablapafes.rows.add(data.pafes).draw();
        } else {

        }

        if (data.perfilBio) {
            conta = 1
            tablaperfilBio.rows.add(data.perfilBio).draw();
        } else {

        }
        if (data.desempenio) {
            conta = 1
            tabladesempenio.rows.add(data.desempenio).draw();
        } else {

        }


    } catch (error) {
        console.log(error);
    }
}


const llenarFormulario = async (per_catalogo) => {
    try {
        const url = `/arco/API/ascenso/llenarFormulario?per_catalogo=${per_catalogo}`;
        console.log(url);
        const headers = new Headers();
        headers.append("X-requested-With", "fetch");
        const config = {
            method: 'GET'
        };
        const respuesta = await fetch(url, config);
        const data = await respuesta.json();

        console.log(data)
        if (data) {
            contador = 1;

            formPromo.per_catalogo.value = data.per_catalogo;
            formPromo.grado.value = data.grado;
            formPromo.nombre.value = data.nombre;
            formPromo.per_promocion.value = data.per_promocion,
                formPromo.dependencia.value = data.dependencia,
                formPromo.puesto.value = data.puesto;
            formPromo.t_prox_asc.value = data.t_prox_asc,
                formPromo.foto.src = `https://sistema.ipm.org.gt/sistema/fotos_afiliados/ACTJUB/${data.per_catalogo}.jpg`;

        } else {
            Toast.fire({
                title: 'No se encontraron registros',
                icon: 'info'
            });
        }
    } catch (error) {
        console.log(error);
    }
}


const modalVerExistencias = document.getElementById('verExistencias');
const botonCerrarModal = document.querySelector('.modal-header .close');


datatable.on('click', '.btn-outline-dark', () => {

    modalVerExistencias.classList.add('show');
    modalVerExistencias.style.display = 'block';
    document.body.classList.add('modal-open');
});


botonCerrarModal.addEventListener('click', function () {

    modalVerExistencias.style.display = 'none';
    document.body.classList.remove('modal-open');


});


modalVerExistencias.addEventListener('click', function (event) {
    if (event.target === modalVerExistencias) {
        modalVerExistencias.style.display = 'none';
        document.body.classList.remove('modal-open');

    }
});





const pdf = async (e) => {

    e.preventDefault()

    if (await confirmacion('question', 'Desea imprimir PDF?')) {


        const perGradoSelect = formulario.per_grado;
        const selectedOption = perGradoSelect.options[perGradoSelect.selectedIndex];

        const per_grado = selectedOption.value;
        const codigo2 = selectedOption.getAttribute('data-codigo2');

        const content = await readXlsxFile(exelInput.files[0]);
        const catalogos = content.slice(1).map(row => row[0]);

        const url = `/arco/pdf?grado=${per_grado}&catalogos=${JSON.stringify(catalogos)}&grado2=${codigo2}`;

        const headers = new Headers();
        headers.append("X-Requested-With", "fetch");
        const config = {
            method: 'GET',
            headers,
        };

        try {
            const respuesta = await fetch(url, config)
            if (respuesta.ok) {
                const blob = await respuesta.blob();

                if (blob) {
                    const urlBlob = window.URL.createObjectURL(blob);

                    // Abre el PDF en una nueva ventana o pestaña
                    window.open(urlBlob, '_blank');
                } else {
                    console.error('No se pudo obtener el blob del PDF.');
                }
            } else {
                console.error('Error al generar el PDF.');
            }
        } catch (error) {
            console.error(error);
        }
    };
};




const excel = async (e) => {

    e.preventDefault()

    if (await confirmacion('question', 'Desea imprimir su archivo EXCEL?')) {


        const perGradoSelect = formulario.per_grado;
        const selectedOption = perGradoSelect.options[perGradoSelect.selectedIndex];

        const per_grado = selectedOption.value;
        const codigo2 = selectedOption.getAttribute('data-codigo2');

        const content = await readXlsxFile(exelInput.files[0]);
        const catalogos = content.slice(1).map(row => row[0]);

        const fecha = formulario.fecha.value;

        const url = `/arco/excel?grado=${per_grado}&fecha=${fecha}&catalogos=${JSON.stringify(catalogos)}&grado2=${codigo2}`;

        const headers = new Headers();
        headers.append("X-Requested-With", "fetch");
        const config = {
            method: 'GET',
            headers,
        };

        try {
            const respuesta = await fetch(url, config)
            if (respuesta.ok) {
                const blob = await respuesta.blob();

                if (blob) {
                    const urlBlob = window.URL.createObjectURL(blob);

                    // Abre el PDF en una nueva ventana o pestaña
                    window.open(urlBlob, '_blank');
                } else {
                    console.error('No se pudo obtener el blob del PDF.');
                }
            } else {
                console.error('Error al generar el PDF.');
            }
        } catch (error) {
            console.error(error);
        }
    };
};




botonRecargar.addEventListener('click', function () {

    const scrollPosition = window.scrollY || window.pageYOffset;


    location.reload(true);


    window.onload = function () {
        window.scrollTo(0, scrollPosition);
    };
});





btnBuscar.addEventListener('click', buscar);
btnPdf.addEventListener('click', pdf);
btnexcel.addEventListener('click', excel);



