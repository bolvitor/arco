<h1 class="text-center">Asignacion de Meritos</h1>

<div class="row justify-content-center mb-5">
    <form class="col-lg-8 border bg-light p-3" id="formMeritos">
    <input type="hidden" name="id_meritos" id="id_meritos" class="form-control" >
    <div class="row mb-3">
            <div class="col">
                <label for="tipo_opcion">Seleccionar tipo</label>
                <select name="tipo_opcion" id="tipo_opcion" class="form-control">
                    <option value="cursos">Cursos</option>
                    <option value="condecoraciones">Condecoraciones</option>
                </select>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <button type="button" id="btnBuscar" class="btn btn-info w-100">Buscar</button>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <label for="descripcion">Descripción</label>
                <input type="text" name="descripcion" id="descripcion" class="form-control">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <label for="meritos">Méritos</label>
                <input type="number" name="meritos" id="meritos" class="form-control">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <button type="button" id="btnModificar" class="btn btn-warning w-100">Modificar</button>
            </div>
            <div class="col">
                <button type="button" id="btnCancelar" class="btn btn-danger w-100">Cancelar</button>
            </div>
        </div>
    </form>
</div>


<div class="row justify-content-center">
    <div class="col table-responsive" style="max-width: 80%; padding: 10px;">
        <table id="tablaCreditos" class="table table-bordered table-hover">
        </table>
    </div>
</div>



<script src="<?= asset('./build/js/creditos/index.js') ?>"></script>