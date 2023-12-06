import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import { validarFormulario, Toast, confirmacion } from "../funciones";
import Datatable from "datatables.net-bs5";
import { lenguaje } from "../lenguaje";



const formulariomeritos = document.getElementById('formMeritos')

const btnBuscar = document.getElementById('btnBuscar');
const btnModificar = document.getElementById('btnModificar');
const btnCancelar = document.getElementById('btnCancelar');
const inputmeritos = document.getElementById('meritos');
const inputdescripcion = document.getElementById('descripcion');
const selectOpcion = document.getElementById('tipo_opcion');


let contador = 1;

    btnModificar.disabled = true;
    btnModificar.parentElement.style.display = 'none';
    btnCancelar.disabled = true;
    btnCancelar.parentElement.style.display = 'none';
    inputmeritos.disabled = true;
    inputmeritos.parentElement.style.display = 'none';
    inputdescripcion.disabled = true;
    inputdescripcion.parentElement.style.display = 'none';

const datatable = new Datatable('#tablaCreditos', {
    language: lenguaje,
    data: null,
    columns: [
        {
            title: 'NO',
            render: () => contador++
        },
        {
            title: 'DESCRIPCION',
            data: 'descripcion'
        },
        {
            title: 'MERITOS',
            data: 'meritos'
        },
        {
            title: 'MODIFICAR',
            data: 'id_meritos',
            searchable: false,
            orderable: false,
            render: (data, type, row, meta) => `<button class="btn btn-warning" data-id='${data}' data-meritos='${row["meritos"]}' data-descripcion='${row["descripcion"]}'> Modificar </button>`
        },
        {
            title: 'ELIMINAR',
            data: 'id_meritos',
            searchable: false,
            orderable: false,
            render: (data, type, row, meta) => `<button class="btn btn-danger" data-id='${data}' >Eliminar</button>`
        },
    ]
});




const modificar = async () => {
    if (!formulariomeritos.checkValidity()) {
        Toast.fire({
            icon: 'info',
            text: 'Debe llenar todos los campos'
        });
        return;
    }

    const id = formulariomeritos.id_meritos.value;  // Cambiado de id_meritos a id
    const meritos = formulariomeritos.meritos.value;
    
    const body = new FormData();
    body.append('id', id);  // Cambiado de id_meritos a id
    body.append('Meritos_Curso', meritos);
    
    // body.append('tipo_opcion', document.getElementById('tipo_opcion').value);

    const url = '/arco/API/creditos/modificar';
    const config = {
        method: 'POST',
        body
    };

    try {
        const respuesta = await fetch(url, config);
        const data = await respuesta.json();
       
        const { codigo, mensaje, detalle } = data;
        let icon = 'info';
        switch (codigo) {
            case 1:
                formulariomeritos.reset();
                icon = 'success';
                buscar();
                cancelarAccion();
                break;
            case 0:
                icon = 'error';
                console.log(detalle);
                break;
            default:
                break;
        }
        Toast.fire({
            icon,
            text: mensaje
        });
    } catch (error) {
        console.log(error);
    }
};



const traeDatos = (e) => {
    const button = e.target;
    const id = button.dataset.id;
    const descripcion = button.dataset.descripcion;
    const meritos = button.dataset.meritos;


    const dataset = {
        id,
        descripcion,
        meritos,
    };

    colocarDatos(dataset);
    const body = new FormData(formulariomeritos);
    body.append('descripcion', descripcion);
    body.append('meritos', meritos);
    body.append('id_meritos', id);

};


const colocarDatos = (dataset) => {
    formulariomeritos.descripcion.value = dataset.descripcion;
    formulariomeritos.meritos.value = dataset.meritos; 
    formulariomeritos.id_meritos.value = dataset.id; 


    btnBuscar.disabled = true;
    btnBuscar.parentElement.style.display = 'none';
    selectOpcion.disabled = true;
    selectOpcion.parentElement.style.display = 'none';

   inputdescripcion.disabled = false;
   inputdescripcion.parentElement.style.display = '';
   inputmeritos.disabled = false;
   inputmeritos.parentElement.style.display = '';
   btnCancelar.disabled = false;
   btnCancelar.parentElement.style.display = '';
   btnModificar.disabled = false;
   btnModificar.parentElement.style.display = '';

};


const cancelarAccion = () => {
    btnBuscar.disabled = false;
    btnBuscar.parentElement.style.display = '';
    selectOpcion.disabled = false;
    selectOpcion.parentElement.style.display = '';


    inputdescripcion.disabled = true;
   inputdescripcion.parentElement.style.display = 'none';
   inputmeritos.disabled = true;
   inputmeritos.parentElement.style.display = 'none';
   btnCancelar.disabled = true;
   btnCancelar.parentElement.style.display = 'none';
   btnModificar.disabled = true;
   btnModificar.parentElement.style.display = 'none';
}



const buscar = async () => {
    let tipo_opcion = formulariomeritos.tipo_opcion.value;

    const url = `/arco/API/creditos/buscar?tipo_opcion=${tipo_opcion}`;
    const config = {
        method: 'GET'
    }
    try {
        const respuesta = await fetch(url, config)
        const data = await respuesta.json();

        console.log(data);
        datatable.clear().draw()
        if (data) {
            contador = 1;
            datatable.rows.add(data).draw();
        } else {
            Toast.fire({
                title: 'No se encontraron registros',
                icon: 'info'
            })
        }

    } catch (error) {
        console.log(error);
    }
}



const eliminar = async (e) => {
    const button = e.target;
    const id = button.dataset.id;

    if (await confirmacion('warning', '¿Desea eliminar este registro?')) {
        const body = new FormData();
        body.append('id_meritos', id);
        const url = '/arco/API/creditos/eliminar';
        const config = {
            method: 'POST',
            body
        };
        try {
            const respuesta = await fetch(url, config);
            const data = await respuesta.json();
            console.log(data);
            const { codigo, mensaje, detalle } = data;
            let icon = 'info';
            switch (codigo) {
                case 1:
                    icon = 'success';
                    buscar();
                    break;
                case 0:
                    icon = 'error';
                    console.log(detalle);
                    break;
                default:
                    break;
            }
            Toast.fire({
                icon,
                text: mensaje
            });
        } catch (error) {
            console.log(error);
        }
    }
};




buscar();

datatable.on('click', '.btn-warning', traeDatos )
datatable.on('click', '.btn-danger', eliminar)
// formulario.addEventListener('submit', guardar)
btnBuscar.addEventListener('click', buscar)
btnCancelar.addEventListener('click', cancelarAccion)
btnModificar.addEventListener('click', modificar)




