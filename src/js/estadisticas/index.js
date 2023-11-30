import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import Chart from "chart.js/auto";
// import { Toast } from "../js/funciones";

const canvaspromocion = document.getElementById("chartPromocion");

const canvaspostergado = document.getElementById("chartpostergados");


const btnBuscar = document.getElementById("btnBuscar");
const inputPromocion  = document.getElementById("inputPromocion")

const contextpromocion = canvaspromocion.getContext("2d");
const contextpostergado = canvaspostergado.getContext("2d");


const chartPromocion = new Chart(contextpromocion, {
type: "pie",
data: {
  labels: [],
  datasets: [
    {
      label: "Cantidad Oficiales",
      data: [],
      backgroundColor: [],
    },
  ],
},
options: {
  indexAxis: "x",
  scales: {
    x: {
      beginAtZero: true,
    },
    y: {
      beginAtZero: true,
    },
  },
},
});

const getRandomColor = () => {
  const r = Math.floor(Math.random() * 256);
  const g = Math.floor(Math.random() * 256);
  const b = Math.floor(Math.random() * 256);

  const rgbColor = `rgba(${r},${g},${b},0.5)`;
  return rgbColor;
};



const getPromocion = async () => {
  
  const promocion = inputPromocion.value;
  const url = `/arco/API/estadisticas/getPromocion?promocion=${promocion}`;

  try {
    const request = await fetch(url);
    const data = await request.json();
    console.log(data);


    chartPromocion.data.labels = [];
    chartPromocion.data.datasets[0].data = [];
    chartPromocion.data.datasets[0].backgroundColor = [];

    if (!data || Object.keys(data).length === 0) {
      Swal.fire({
        icon: 'info',
        title: 'No se encontraron datos',
        text: 'No hay registros para las fechas seleccionadas.',
      });
    } else {
      data.forEach((registro) => {

        const nombre = registro.nombre;
        const total= registro.total;
        const postergados = registro.postergados;
        const ascendidos = registro.ascendidos;

        chartPromocion.data.labels.push(nombre);
        chartPromocion.data.datasets[0].data.push(total);
        
        chartPromocion.data.datasets[0].backgroundColor.push(getRandomColor());
      });

      chartPromocion.update();
    }
  } catch (error) {
    console.log(error);
  }
};






const chartPostergado = new Chart(contextpostergado, {
  type: "bar",
  data: {
    labels: [], 
    datasets: [
      {
        label: "Oficiales Postergados",
        data: [],
        backgroundColor: [],
      },
      {
        label: "Oficiales Ascendidos",
        data: [], 
        backgroundColor: [],
      },
    ],
  },
  options: {
    indexAxis: "x",
    scales: {
      x: {
        beginAtZero: true,
      },
      y: {
        beginAtZero: true,
      },
    },
  },
});
  
const getPostergados = async () => {
  const promocion = inputPromocion.value;
  const url = `/arco/API/estadisticas/getPostergados?promocion=${promocion}`;

  try {
    const request = await fetch(url);
    const data = await request.json();
    console.log(data);

   
    chartPostergado.data.labels = [];
    chartPostergado.data.datasets[0].data = [];
    chartPostergado.data.datasets[1].data = []; 
    chartPostergado.data.datasets[0].backgroundColor = [];
    chartPostergado.data.datasets[1].backgroundColor = []; 

    if (!data || Object.keys(data).length === 0) {
      Swal.fire({
        icon: 'info',
        title: 'No se encontraron datos',
        text: 'No hay registros para la promocion seleccionada.',
      });
    } else {
      data.forEach((registro) => {
       
        const nombre = registro.nombre;
        const postergados = registro.postergados;
        const ascendidos = registro.ascendidos;

        chartPostergado.data.labels.push(nombre);
        chartPostergado.data.datasets[0].data.push(postergados);
        chartPostergado.data.datasets[1].data.push(ascendidos); 
        chartPostergado.data.datasets[0].backgroundColor.push(getRandomColor());
        chartPostergado.data.datasets[1].backgroundColor.push(getRandomColor()); 
      });

      chartPostergado.update();
    }
  } catch (error) {
    console.log(error);
  }
};


  btnBuscar.addEventListener('click', getPromocion);
  btnBuscar.addEventListener('click', getPostergados);

