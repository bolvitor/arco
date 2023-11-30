import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import { validarFormulario, Toast, confirmacion } from "../funciones";
import Datatable from "datatables.net-bs5";
import { lenguaje } from "../lenguaje";


const formulario = document.querySelector('#formularioUsuarios');
const btnBuscar = document.getElementById('btnBuscar');
const botonRecargar = document.getElementById('btnLimpiar');

let contador = 1;
const datatable = new Datatable('#tablaUsuarios', {
    language: lenguaje,
    data: null,
    columns: [
        {
            title: 'NO',
            render: () => contador++
        },
        {
            title: 'Curso Ascenso',
            data: 'Curso'
        },
        {
            title: 'Eva_Desempeño',
            data: 'desempenio'
        },
        {
            title: 'Conducta Militar',
            data: 'punteo_conducta'
        },
        {
            title: 'Aptitud Fisica',
            data: 'pafeSQL'
        },
        {
            title: 'Perfil Biofisico',
            data: 'perfil_biofisico'
        },
        {
            title: 'Creditos',
            data: 'Meritos'
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
                                data-catalogo="${formulario.per_catalogo.value}"
                                style="border: 3px solid #008000; border-radius: 8px;">
                        
                            <img src="./images/investigar.png" alt="Detalles" style="width: 60px; height: 50px; border: none;">
                        </button>`;
                } else {
                    return null;
                }
            }
        }

    ],
    order: [[7, 'asc']],

});




const buscar = async () => {
    if (!validarFormulario(formulario, ['per_nom1', 'per_nom2', 'per_ape1', 'per_ape2', 'per_grado', 'per_promocion', 'per_arma', 't_prox_asc'])) {
        Swal.fire({
            icon: 'info',
            title: '¡Advertencia!',
            text: 'Debe ingresar un valor en el formulario',
        });
        return;
    }
    let per_catalogo = formulario.per_catalogo.value;
    const url = `/arco/API/usuarios/buscar?per_catalogo=${per_catalogo}`;
    const config = {
        method: 'GET'
    };
    try {
        const respuesta = await fetch(url, config);
        const data = await respuesta.json();
        console.log('Datos recibidos:', data);

        if (data.usuarios && data.usuarios.length > 0) {
            const usuario = data.usuarios[0];
            contador = 1;

            // Crear un objeto con los datos del usuario
            const userData = {
                per_serie: usuario.per_serie,
                punteo_conducta: parseFloat(usuario.punteo_conducta),
                perfil_biofisico: usuario.perfil_biofisico === '' ? 'Pendiente' : parseFloat(usuario.perfil_biofisico),
                t_prox_asc: usuario.t_prox_asc,
                Curso: data.Curso.promedio === '' ? 'Pendiente' : parseFloat(data.Curso.promedio),
                Meritos: parseFloat(data.Meritos.puntos_netos),
                desempenio: data.desempenio === null ? 0 : (data.desempenio.resultado_final === 'null' ? 0 : parseFloat(data.desempenio.resultado_final)),
                pafeSQL: parseFloat(data.pafeSQL.suma_total),
                punteo_total: (
                    (parseFloat(usuario.punteo_conducta)) +
                    ((data.desempenio === null || data.desempenio.resultado_final === 'null') ? 0 : parseFloat(data.desempenio.resultado_final)) +
                    (parseFloat(data.Meritos.puntos_netos)) +
                    (data.Curso.promedio !== '' ? parseFloat(data.Curso.promedio) : 0) +
                    (usuario.perfil_biofisico !== '' ? parseFloat(usuario.perfil_biofisico) : 0) +
                    (parseFloat(data.pafeSQL.suma_total))
                ).toFixed(2)
            };

            // Autocompletar el formulario
            formulario.per_nom1.value = usuario.per_nom1;
            formulario.per_nom2.value = usuario.per_nom2;
            formulario.per_ape1.value = usuario.per_ape1;
            formulario.per_ape2.value = usuario.per_ape2;
            formulario.per_grado.value = usuario.per_grado;
            formulario.per_arma.value = usuario.per_arma;
            formulario.per_promocion.value = usuario.per_promocion;
            formulario.t_prox_asc.value = usuario.t_prox_asc;
            formulario.foto.src = `https://sistema.ipm.org.gt/sistema/fotos_afiliados/ACTJUB/${usuario.per_catalogo}.jpg`;



            datatable.clear().draw();
            datatable.rows.add([userData]).draw();

            Swal.fire({
                icon: 'success',
                title: '¡Resultados obtenidos!',
                text: 'Se encontraron registros',
            });
        } else {
            Swal.fire({
                title: 'No se encontraron registros',
                icon: 'info'
            });
        }
    } catch (error) {
        console.log(error);
    }


};




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



const buscarOficial = async (evento) => {
    evento && evento.preventDefault();
    try {
        const per_catalogo = formulario.per_catalogo.value;
        const url = `/arco/API/usuarios/buscarOficial?per_catalogo=${per_catalogo}`;
        const headers = new Headers();
        headers.append("X-requested-With", "fetch");

        const config = {
            method: 'GET'
        }

        const respuesta = await fetch(url, config)
        const data = await respuesta.json();

        console.log(data);

        contador = 1;
        tablademeritos.clear().draw();
        tablacursoAscenso.clear().draw();
        tablameritos.clear().draw();
        tablapafes.clear().draw();
        tablaperfilBio.clear().draw();
        tabladesempenio.clear().draw();


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
            conta =
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




botonRecargar.addEventListener('click', function () {
 
    const scrollPosition = window.scrollY || window.pageYOffset;


    location.reload(true);

   
    window.onload = function () {
        window.scrollTo(0, scrollPosition);
    };
});




btnBuscar.addEventListener('click', buscar);

datatable.on('click', '.btn-outline-dark', buscarOficial);





