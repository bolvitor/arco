import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import { validarFormulario, Toast, confirmacion } from "../funciones";
import Datatable from "datatables.net-bs5";
import { lenguaje } from "../lenguaje";


const formulario = document.querySelector('#formularioPromociones');
const formPromo = document.querySelector('#formularioPromocion');
const arma = document.getElementById('per_arma')
const btnBuscar = document.getElementById('btnBuscar');

const botonRecargar = document.getElementById('btnLimpiar');




const datatable = new Datatable('#tablaPromociones', {
    language: lenguaje,
    data: null,
    columns: [
        {
            title: 'NO',
            render: (data, type, row, meta) => {
                return type === 'display' ? meta.row + 1 : meta.row + 1;
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
            data: 'desempenio'
        },
        {
            title: 'Conducta Militar',
            data: 'demeritos1'
        },

        {
            title: 'Aptitud Fisica',
            data: 'pafeSQL'
        },

        {
            title: 'Perfil_Biofisico',
            data: 'perfilBio1',
            render: function (data, type, row) {
                if (parseFloat(data) === 1.66) {

                    return '<span style="color: red;">' + data + '</span>';
                } else {

                    return data;
                }
            }
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

});





const buscar = async () => {


    if (!validarFormulario(formulario, ['per_arma'])) {
        Swal.fire({
            icon: 'info',
            title: '¡Advertencia!',
            text: 'Debe ingresar un valor en el formulario',
        });
        return;
    }

    let per_promocion = formulario.per_promocion.value;
    let per_arma = arma.value;


    const url = `/arco/API/promocion/buscar?per_promocion=${per_promocion}&per_arma=${per_arma}`;
    console.log(url);

    const config = {
        method: 'GET'
    };

    try {




        Swal.fire({
            title: 'Buscando...',
            html: '<div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem; border-width: 0.4em;"><span class="sr-only"></span></div>',
            showConfirmButton: false,
            showCancelButton: true,
            allowOutsideClick: false,
            onBeforeOpen: () => {

                Swal.showLoading();
            }
        });

        const respuesta = await fetch(url, config);
        const data = await respuesta.json();
        console.log('Datos recibidos:', data);

        datatable.clear().draw();

        // Obtener la longitud máxima de todos los arrays
        const maxLength = Math.max(
            data.usuarios.length,
            data.cursoAscenso1.length,
            data.meritos1.length,
            data.demeritos1.length,
            data.perfilBio1.length,
            data.desempenio.length,
            data.pafeSQL.length
        );

        for (let index = 0; index < maxLength; index++) {
            const usuariosData = {
                per_catalogo: data.usuarios[index] ? data.usuarios[index].per_catalogo : '',
                grado: data.usuarios[index] ? data.usuarios[index].grado : '',
                nombre: data.usuarios[index] ? data.usuarios[index].nombre : '',
                cursoAscenso1: data.cursoAscenso1[index] ? (data.cursoAscenso1[index].promedio === '' ? '<span style="color: red;">Pendiente</span>' : parseFloat(data.cursoAscenso1[index].promedio)) : '',
                Meritos1: data.meritos1[index] ? parseFloat(data.meritos1[index].puntos_netos) : 0,
                demeritos1: data.demeritos1[index] ? parseFloat(data.demeritos1[index].punteo_demeritos) : 0,
                perfilBio1: data.perfilBio1[index] ? (data.perfilBio1[index].perfil_biofisico === '' ? '<span style="color: red;">Pendiente</span>' : parseFloat(data.perfilBio1[index].perfil_biofisico)) : '',
                desempenio: data.desempenio[index] ? (data.desempenio[index].resultado_final === '' ? 0 : parseFloat(data.desempenio[index].resultado_final)) : 0,
                pafeSQL: data.pafeSQL[index] ? (data.pafeSQL[index].total_notas === '' ? 0 : parseFloat(data.pafeSQL[index].total_notas)) : 0,
                punteo_total: (
                    (
                        (data.desempenio[index] ? (data.desempenio[index].resultado_final === '' ? 0 : parseFloat(data.desempenio[index].resultado_final)) : 0) +
                        (data.meritos1[index] ? parseFloat(data.meritos1[index].puntos_netos) : 0) +
                        (data.demeritos1[index] ? parseFloat(data.demeritos1[index].punteo_demeritos) : 0) +
                        (data.cursoAscenso1[index] ? (data.cursoAscenso1[index].promedio !== '' ? parseFloat(data.cursoAscenso1[index].promedio) : 0) : 0) +
                        (data.perfilBio1[index] ? (data.perfilBio1[index].perfil_biofisico === '' ? 0 : parseFloat(data.perfilBio1[index].perfil_biofisico)) : 0) +
                        (data.pafeSQL[index] ? (data.pafeSQL[index].total_notas === '' ? 0 : parseFloat(data.pafeSQL[index].total_notas)) : 0)
                    )
                ).toFixed(2)
            };


            datatable.rows.add([usuariosData]).draw();
        }

        Swal.fire({
            icon: data.usuarios && data.usuarios.length > 0 ? 'success' : 'info',
            title: data.usuarios && data.usuarios.length > 0 ? '¡Resultados obtenidos!' : 'Sin resultados',
            text: data.usuarios && data.usuarios.length > 0 ? 'Se encontraron registros' : 'No se encontraron registros para la búsqueda',
            didClose: () => {
                Swal.close();
            }
        });

    } catch (error) {
        console.log(error);
      
        Swal.close();

        Swal.fire({
            icon: 'error',
            title: '¡Error!',
            text: 'Hubo un error durante la búsqueda <br> Intentelo de nuevo',
        });
    }
};


$('#tablaPromociones').on('click', '.btn-outline-dark', function () {
  
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
  

    try {
        const url = `/arco/API/promocion/buscarOficial?per_catalogo=${per_catalogo}`;
        console.log(url);
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


        if (data.demeritos) {
            conta = 1
            tablademeritos.rows.add(data.demeritos).draw();
        } else {

         
        }

        if (data.cursoAscenso) {
            conta = 1
            tablacursoAscenso.rows.add(data.cursoAscenso).draw();
        } else {
           
        }if (data.meritos) {
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
        const url = `/arco/API/promocion/llenarFormulario?per_catalogo=${per_catalogo}`;
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
       
            formPromo.per_catalogo.value = data.per_catalogo;
            formPromo.grado.value = data.grado;
            formPromo.nombre.value = data.nombre;
            formPromo.per_promocion.value = data.per_promocion;
            formPromo.dependencia.value = data.dependencia;
            formPromo.puesto.value = data.puesto;
            formPromo.t_prox_asc.value = data.t_prox_asc;
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

botonRecargar.addEventListener('click', function () {

    const scrollPosition = window.scrollY || window.pageYOffset;

  
    location.reload(true);

   
    window.onload = function () {
        window.scrollTo(0, scrollPosition);
    };
});

btnBuscar.addEventListener('click', buscar);

