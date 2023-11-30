import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import { validarFormulario, Toast, confirmacion} from "../funciones";
import Datatable from "datatables.net-bs5";
import { lenguaje  } from "../lenguaje";


let contador = 1;
const datatable = new Datatable('#tablaescalafon', {
    language: lenguaje,
    data: null,
    columns: [
        {
            title: 'NO',
            render: () => contador++
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
            title: 'Promocion',
            data: 'per_promocion'
        },
        {
            title: 'Dependencia',
            data: 'dependencia'
        },
        
        {
            title: 'Puesto',
            data: 'puesto'
        },
   
    ],

});



const escalafon = async () => {
        const url = `/arco/API/escalafon/escalafon`;
    
        const headers = new Headers();
        headers.append("X-requested-With", "fetch");
    const config = {
        method: 'GET'
    }
        try {
            const respuesta = await fetch(url, config)
            const data = await respuesta.json();
    
            console.log(data);
            datatable.clear().draw()
            if(data){
                contador = 1;
                datatable.rows.add(data).draw();
            }else{
                Toast.fire({
                    title : 'No se encontraron registros',
                    icon : 'info'
                })
            }
           
        } catch (error) {
            console.log(error);
        }
    }
    escalafon();