<?php
session_start();

require_once __DIR__ . '/../../vendor/autoload.php';

use Conect\Conexion;
use Controladores\ControladorClientes;
use Controladores\ControladorNotaCredito;
use Controladores\ControladorNotaDebito;
use Controladores\ControladorCategorias;
use Controladores\ControladorEnvioSunat;
use Controladores\ControladorResumenDiario;
use Controladores\ControladorEmpresa;
use Controladores\ControladorSunat;
use Controladores\ControladorProdutos;

class DataTables
{
  private $pdo;
  private $perPageDefault = 10;
  private $adjacents = 4;

  public function __construct()
  {
    $this->pdo = Conexion::conectar();
  }

  /**
   * Obtiene el valor de selectnum de forma segura
   */
  private function getPerPage($selectnum = null)
  {
    $perPage = (int)($selectnum ?? $this->perPageDefault);
    return ($perPage > 0) ? $perPage : $this->perPageDefault;
  }

  /**
   * Obtiene el valor de search de forma segura
   */
  private function getSearchValue($key, $default = '')
  {
    return $_GET[$key] ?? $_REQUEST[$key] ?? $default;
  }

  /**
   * Construye la cláusula WHERE para búsquedas
   */
  private function buildWhereClause($search, $columns, $additionalConditions = '')
  {
    if (empty($search)) {
      return !empty($additionalConditions) ? "WHERE $additionalConditions" : "";
    }

    $conditions = [];
    foreach ($columns as $column) {
      $conditions[] = "$column LIKE '%" . addslashes($search) . "%'";
    }

    $where = "WHERE (" . implode(" OR ", $conditions) . ")";
    if (!empty($additionalConditions)) {
      $where .= " AND $additionalConditions";
    }

    return $where;
  }

  /**
   * Configura la paginación
   */
  private function setupPagination($table, $where, $perPage, $page)
  {
    $totalRegistros = $this->pdo->query("SELECT COUNT(*) AS numrows FROM $table $where");
    $totalRegistros = $totalRegistros->fetch()['numrows'];

    $totalPages = ceil($totalRegistros / $perPage);
    $offset = ($page - 1) * $perPage;

    return [
      'total' => $totalRegistros,
      'totalPages' => $totalPages,
      'offset' => $offset,
      'perPage' => $perPage
    ];
  }

  /**
   * Genera la paginación HTML
   */
  private function generatePagination($reload, $page, $totalPages, $adjacents)
  {
    include_once 'pagination.php';
    $paginador = new Paginacion();

    if (strpos($reload, '?') !== false) {
      $reload .= '&';
    } else {
      $reload .= '?';
    }

    return $paginador->paginarClientes($reload, $page, $totalPages, $adjacents);
  }

  // DATA_TABLE CLIENTES LISTAR CLIENTES
  public function dtaClientes()
  {
    $action = $_REQUEST['action'] ?? '';
    if ($action != 'ajax') return;

    $perfilUsuario = $_REQUEST['perfilOcultoc'] ?? '';
    $search = $this->getSearchValue('search');
    $perPage = $this->getPerPage($_GET['selectnum'] ?? null);
    $page = (int)($_REQUEST['page'] ?? 1);

    $columns = ['nombre', 'documento', 'ruc'];
    $table = 'clientes';

    $where = $this->buildWhereClause($search, $columns);
    $pagination = $this->setupPagination($table, $where, $perPage, $page);

    $registros = $this->pdo->prepare("SELECT * FROM $table $where LIMIT {$pagination['offset']}, {$pagination['perPage']}");
    $registros->execute();
    $registros = $registros->fetchAll();

    foreach ($registros as $key => $value) {
      $nombreRazon = ($value['ruc'] != '') ? $value['razon_social'] : $value['nombre'];
      $rucdni = ($value['ruc'] != '') ? $value['ruc'] : $value['documento'];

      echo "<tr class='id{$value['id']}'>
                <td>" . (++$key) . "</td>
                <td>" . htmlspecialchars($nombreRazon) . "</td>
                <td>" . htmlspecialchars($rucdni) . "</td>
                <td>" . htmlspecialchars($value['email']) . "</td>
                <td>" . htmlspecialchars($value['telefono']) . "</td>
                <td>" . htmlspecialchars($value['direccion']) . "</td>
                <td>" . date_format(date_create($value['fecha']), 'd/m/Y H:i:s') . "</td>
                <td>
                    <div class='btn-group'>
                        <button class='btn btn-warning btnEditarCliente' idCliente='{$value['id']}' data-toggle='modal' data-target='#modalEditarCliente'><i class='fas fa-user-edit'></i></button>";

      if ($perfilUsuario == 'Administrador') {
        echo "<button class='btn btn-danger btnEliminarCliente' idCliente='{$value['id']}'><i class='fas fa-trash-alt'></i></button>";

    $action = (isset($_REQUEST['action']) && $_REQUEST['action'] != NULL) ? $_REQUEST['action'] : '';
    if ($action == 'ajax') {
      // escaping, additionally removing everything that could be (html/javascript-) code
      $perfilUsuario = $_REQUEST['perfilOcultoc'] ?? '';
      $search = $_GET['search'] ?? '';
      $selectnum = $_GET['selectnum'] ?? 10;
      $aColumns = array('nombre', 'documento', 'ruc'); //Columnas de busqueda
      $sTable = 'clientes';
      $sWhere = "";
      if (isset($search)) {
        $sWhere = "WHERE (";
        for ($i = 0; $i < count($aColumns); $i++) {
          $sWhere .= $aColumns[$i] . " LIKE '%" . $search . "%' OR ";
        }
        $sWhere = substr_replace($sWhere, "", -3);
        $sWhere .= ')';
      }

      echo "    </div>
                </td>
            </tr>";
    }

    $reload = './index.php';
    $paginador = $this->generatePagination($reload, $page, $pagination['totalPages'], $this->adjacents);
    echo "<tr>
            <td colspan='8' style='text-align:center;'>$paginador</td>
        </tr>";
  }

  // DATA_TABLE LISTAR PRODUCTOS
  public function dtaProductos()
  {
    $action = $_REQUEST['action'] ?? '';
    if ($action != 'ajax') return;

    $perfilUsuario = $_REQUEST['perfilOculto'] ?? '';
    $search = $this->getSearchValue('searchProducto');
    $perPage = $this->getPerPage($_GET['selectnum'] ?? null);
    $page = (int)($_REQUEST['page'] ?? 1);

    $action = (isset($_REQUEST['action']) && $_REQUEST['action'] != NULL) ? $_REQUEST['action'] : '';
    if ($action == 'ajax') {
      // escaping, additionally removing everything that could be (html/javascript-) code
      $perfilUsuario = $_REQUEST['perfilOculto'] ?? '';
      $searchProducto = $_GET['searchProducto'] ?? '';
      $selectnum = $_GET['selectnum'] ?? 10;
      $aColumns = array('codigo', 'serie', 'descripcion'); //Columnas de busqueda
      $sTable = 'productos';
      $sWhere = "";
      if (isset($searchProducto)) {
        $sWhere = "WHERE (";
        for ($i = 0; $i < count($aColumns); $i++) {
          $sWhere .= $aColumns[$i] . " LIKE '%" . $searchProducto . "%' OR ";
        }
        $sWhere = substr_replace($sWhere, "", -3);
        $sWhere .= ')';
      }

    $columns = ['codigo', 'serie', 'descripcion'];
    $table = 'productos';

    $where = $this->buildWhereClause($search, $columns);
    $pagination = $this->setupPagination($table, $where, $perPage, $page);

    $registros = $this->pdo->prepare("SELECT * FROM $table $where LIMIT {$pagination['offset']}, {$pagination['perPage']}");
    $registros->execute();
    $registros = $registros->fetchAll();

    foreach ($registros as $key => $value) {
      $categoria = ControladorCategorias::ctrMostrarCategorias('id', $value['id_categoria']);

      echo "<tr>
                <td>" . ++$key . "</td>
                <td><img src='" . htmlspecialchars($value['imagen']) . "' alt='' class='img-thumbnail' width='40px'></td>
                <td>" . htmlspecialchars($value['codigo']) . "</td>
                <td>" . htmlspecialchars($value['serie']) . "</td>
                <td>" . htmlspecialchars($value['descripcion']) . "</td>
                <td>" . htmlspecialchars($categoria['categoria'] ?? '') . "</td>
                <td><button class='btn btn-primary btn-stock' idProducto='{$value['id']}'>" . htmlspecialchars($value['stock']) . "</button></td>
                <td>" . htmlspecialchars($value['precio_unitario']) . "</td>
                <td>" . date_format(date_create($value['fecha']), 'd/m/Y H:i:s') . "</td>
                <td>
                    <div class='btn-group'>
                        <button class='btn btn-warning btnEditarProducto' idProducto='{$value['id']}' data-toggle='modal' data-target='#modalEditarProducto'><i class='fas fa-user-edit'></i></button>";

      if ($perfilUsuario == 'Administrador') {
        echo "<button class='btn btn-danger btnEliminarProducto' idProducto='{$value['id']}' codigo='{$value['codigo']}' imagen='{$value['imagen']}'><i class='fas fa-trash-alt'></i></button>";
      }

      echo "    </div>
                </td>
            </tr>";
      echo "    </div>
                </td>
            </tr>";
      //Count the total number of row in your table*/
      $pdo =  Conexion::conectar();
      $totalRegistros   = $pdo->query("SELECT count(*) AS numrows FROM $sTable  $sWhere");
      $totalRegistros = $totalRegistros->fetch()['numrows'];
      // $tpages = ceil($totalRegistros / $per_page);
      // $limit = $limit > 0 ? $limit : 10; // Valor por defecto
      // $tpages = $limit > 0 ? ceil($totalRegistros / $limit) : 1;
      $per_page = $per_page > 0 ? $per_page : 10; // Valor por defecto
      $tpages = ceil($totalRegistros / $per_page);
      $reload = './index.php';
      //main query to fetch the data
      $pdo =  Conexion::conectar();
      $registros = $pdo->prepare("SELECT * FROM  $sTable $sWhere LIMIT $offset,$per_page");
      $registros->execute();

      $registros = $registros->fetchall();

      foreach ($registros as $key => $value) :

        $item = 'id';
        $valor = $value['id_categoria'];
        $categoria = ControladorCategorias::ctrMostrarCategorias($item, $valor);
        // <td><img src="'.$value['imagen'].'" alt="" class="img-thumbnail" width="40px"></td>
        echo '<tr class="contenedor-items">
           <td>' . ++$key . '</td>
          
           <td> ' . $value['codigo'] . '</td>
           <td> ' . $value['serie'] . '</td>
           <td>' . $value['descripcion'] . '</td>';

        echo '<td>' . $categoria['categoria'] . '</td>
           <td> <button class="btn-primary stock' . $value['id'] . ' btn-stock"  stock="' . $value["stock"] . '">' . $value['stock'] . '</button></td>
           <td>
           <input type="number" class="number cantidad-stock" name="cantidad" id="cantidad' . $value['id'] . '"  idProducto="' . $value["id"] . '" min="1" value="" onkeyup="this.value=Numeros(this.value)">
           </td>
           <td> ' . $value['precio_unitario'] . '</td>           
           <td class="btn-prod">
           <div class="btn-group"  style="; !important; justify-content: center;">       
           <button class="btn btn-primary btn-sm agregarProducto" descripcionP="' . $value["descripcion"] . '" idProducto="' . $value["id"] . '" ><i class="fa fa-plus"></i></button>  
          </div>
           </td>
           <td class="btn-prod">
           <div class="btn-group">   
           <button class="btn btn-primary btn-sm vermasProductos btn-close" idProducto="' . $value["id"] . '"><i class="fas fa-tools"></i></button>      
            </div>
    
            </td>            
          
       
         </tr> ';

        echo '<tr >
         <td class="" colspan="10">
         
         <div class="super-contenedor-precios super-cont-precios' . $value["id"] . '">

         <div class="desc-productos" ><h3>' . $value['descripcion'] . '</h3></div>
         <div class="contenedor-precios">
               
        <label>Tipo IGV</label>';
        $item = 'codigo';
        $valor = $value['codigoafectacion'];
        $afectacionT = ControladorSunat::ctrMostrarTipoAfectacion($item, $valor);

        echo '<select class="tipo_afectacion' . $value["id"] . ' pre-css" id="tipoAfectacion" idProducto="' . $value["id"] . '" tpa="' . $value['codigoafectacion'] . '">
        <option value="' . $afectacionT['codigo'] . '">' . $afectacionT['descripcion'] . '</option>';
        $item = null;
        $valor = null;
        $tipoAfectacion = ControladorSunat::ctrMostrarTipoAfectacion($item, $valor);

        foreach ($tipoAfectacion as $k => $afectacion) :
          echo '
   
        <option value="' . $afectacion['codigo'] . '">' . $afectacion['descripcion'] . '</option>
       ';
        endforeach;

        echo ' </select>    
        

        <label class="">Precio Unitario</label> 
        <input type="text" class="precio_unitario' . $value['id'] . ' pre-css" id="precio_unitario" name="" value="' . $value['precio_unitario'] . '" idProducto="' . $value["id"] . '" onkeyup="this.value=Numeros(this.value)">
        <label>Valor Unitario</label>
         <input type="text" class="valor_unitario' . $value['id'] . ' pre-css" name="" value="' . $value['valor_unitario'] . '" onkeyup="this.value=Numeros(this.value)" readonly>
         
         <label>Impuesto a la bolsa plástica</label>
         <div class="contenedor_icbper">

         <div class="modo-contenedor-icbper">
         <label for="siic" id="s' . $value["id"] . '" class="s alterno btn-icb-si" idProducto="' . $value["id"] . '" val="s" >||</label>              
         <label for="siic" id="n' . $value["id"] . '" class="n icbno btn-icb-no" idProducto="' . $value["id"] . '" val="n" >
         No</label>
        
         </div>

          <input type="hidden" name="modo" id="modo_icbper" class="modo-icbper' . $value['id'] . '" value="n">
          <input type="text" class="icbper' . $value['id'] . ' pre-css" name="" value="" onkeyup="this.value=Numeros(this.value)" readonly>
        </div>

        </div>
         <div class="contenedor-precios">
        
        <label>Descuento</label>
         <input type="text" class="descuento_item' . $value['id'] . ' pre-css" id="descuento_item" name="precio_v" value="0.00" idProducto="' . $value["id"] . '">
        <label>Sub total</label>
         <input type="text" class="subtotal' . $value['id'] . ' pre-css" name="precio_v" value="' . $value['valor_unitario'] . '" onkeyup="this.value=Numeros(this.value)" readonly>

        <label>IGV de la linea</label>
         <input type="text" class="igv' . $value['id'] . ' pre-css" name="precio_v" value="' . $value['igv'] . '" readonly>
         
        <label>Total</label>
         <input type="text" class="total' . $value['id'] . ' pre-css" name="precio_v" value="' . $value['precio_unitario'] . '" onkeyup="this.value=Numeros(this.value)" readonly>

         <div class="btn-grupos" >   
       <button class="btn btn-primary btn-sm closeModalProductos btn-close" descripcionP="' . $value["descripcion"] . '" idProducto="' . $value["id"] . '"><i class="fa fa-times-circle"></i></button>  

       <button class="btn btn-primary btn-sm agregarProducto" descripcionP="' . $value["descripcion"] . '" idProducto="' . $value["id"] . '"><i class="fa fa-plus"></i></button>  
         </div>
         </div>
         </div>
         </td>
         </tr>';
      endforeach;
      $paginador = new Paginacion();
      $paginador = $paginador->paginarProductosVentas($reload, $page, $tpages, $adjacents);
      echo "<tr>
                     <td colspan='10' style='text-align:center;'>" . $paginador . "</td>
                    </tr>";
    }

    $reload = './index.php';
    $paginador = $this->generatePagination($reload, $page, $pagination['totalPages'], $this->adjacents);
    echo "<tr>
            <td colspan='10' style='text-align:center;'>$paginador</td>
        </tr>";
  }

  // DATA_TABLE LISTA LOS PRODUCTOS PARA AGREGAR AL CARRITO
  public function dtaProductosVentas()
  {
    $action = $_REQUEST['action'] ?? '';
    if ($action != 'ajax') return;

    $search = $this->getSearchValue('searchProductoV');
    $perPage = $this->getPerPage($_GET['selectnum'] ?? null);
    $page = (int)($_REQUEST['page'] ?? 1);
    $categorias = $_GET['categorias'] ?? '';

    $columns = ['codigo', 'serie', 'descripcion'];
    $table = 'productos';

    $additionalConditions = '';
    if (!empty($categorias)) {
      $additionalConditions = "id_categoria = '$categorias'";
    }

    $where = $this->buildWhereClause($search, $columns, $additionalConditions);
    $pagination = $this->setupPagination($table, $where, $perPage, $page);

    $registros = $this->pdo->prepare("SELECT * FROM $table $where LIMIT {$pagination['offset']}, {$pagination['perPage']}");
    $registros->execute();
    $registros = $registros->fetchAll();

    foreach ($registros as $key => $value) {
      $categoria = ControladorCategorias::ctrMostrarCategorias('id', $value['id_categoria']);
      $afectacionT = ControladorSunat::ctrMostrarTipoAfectacion('codigo', $value['codigoafectacion']);

      echo "<tr class='contenedor-items'>
                <td>" . ++$key . "</td>
                <td>" . htmlspecialchars($value['codigo']) . "</td>
                <td>" . htmlspecialchars($value['serie']) . "</td>
                <td>" . htmlspecialchars($value['descripcion']) . "</td>
                <td>" . htmlspecialchars($categoria['categoria'] ?? '') . "</td>
                <td><button class='btn-primary stock{$value['id']} btn-stock' stock='{$value['stock']}'>" . htmlspecialchars($value['stock']) . "</button></td>
                <td>
                    <input type='number' class='number cantidad-stock' name='cantidad' id='cantidad{$value['id']}' idProducto='{$value['id']}' min='1' value='' onkeyup='this.value=Numeros(this.value)'>
                </td>
                <td>" . htmlspecialchars($value['precio_unitario']) . "</td>
                <td class='btn-prod'>
                    <div class='btn-group' style='justify-content: center;'>
                        <button class='btn btn-primary btn-sm agregarProducto' descripcionP='{$value['descripcion']}' idProducto='{$value['id']}'><i class='fa fa-plus'></i></button>
                    </div>
                </td>
                <td class='btn-prod'>
                    <div class='btn-group'>
                        <button class='btn btn-primary btn-sm vermasProductos btn-close' idProducto='{$value['id']}'><i class='fas fa-tools'></i></button>
                    </div>
                </td>
            </tr>";

      // Fila expandible con detalles
      echo "<tr>
                <td colspan='10'>
                    <div class='super-contenedor-precios super-cont-precios{$value['id']}'>
                        <div class='desc-productos'><h3>" . htmlspecialchars($value['descripcion']) . "</h3></div>
                        <div class='contenedor-precios'>
                            <label>Tipo IGV</label>
                            <select class='tipo_afectacion{$value['id']} pre-css' id='tipoAfectacion' idProducto='{$value['id']}' tpa='{$value['codigoafectacion']}'>
                                <option value='" . htmlspecialchars($afectacionT['codigo'] ?? '') . "'>" . htmlspecialchars($afectacionT['descripcion'] ?? '') . "</option>";

      $tipoAfectacion = ControladorSunat::ctrMostrarTipoAfectacion(null, null);
      foreach ($tipoAfectacion as $afectacion) {
        echo "<option value='" . htmlspecialchars($afectacion['codigo']) . "'>" . htmlspecialchars($afectacion['descripcion']) . "</option>";
      }

      echo "      </select>
                            <label>Precio Unitario</label>
                            <input type='text' class='precio_unitario{$value['id']} pre-css' id='precio_unitario' name='' value='" . htmlspecialchars($value['precio_unitario']) . "' idProducto='{$value['id']}' onkeyup='this.value=Numeros(this.value)'>
                            <label>Valor Unitario</label>
                            <input type='text' class='valor_unitario{$value['id']} pre-css' name='' value='" . htmlspecialchars($value['valor_unitario']) . "' onkeyup='this.value=Numeros(this.value)' readonly>
                            <label>Impuesto a la bolsa plástica</label>
                            <div class='contenedor_icbper'>
                                <div class='modo-contenedor-icbper'>
                                    <label for='siic' id='s{$value['id']}' class='s alterno btn-icb-si' idProducto='{$value['id']}' val='s'>||</label>
                                    <label for='siic' id='n{$value['id']}' class='n icbno btn-icb-no' idProducto='{$value['id']}' val='n'>No</label>
                                </div>
                                <input type='hidden' name='modo' id='modo_icbper' class='modo-icbper{$value['id']}' value='n'>
                                <input type='text' class='icbper{$value['id']} pre-css' name='' value='' onkeyup='this.value=Numeros(this.value)' readonly>
                            </div>
                        </div>
                        <div class='contenedor-precios'>
                            <label>Descuento</label>
                            <input type='text' class='descuento_item{$value['id']} pre-css' id='descuento_item' name='precio_v' value='0.00' idProducto='{$value['id']}'>
                            <label>Sub total</label>
                            <input type='text' class='subtotal{$value['id']} pre-css' name='precio_v' value='" . htmlspecialchars($value['valor_unitario']) . "' onkeyup='this.value=Numeros(this.value)' readonly>
                            <label>IGV de la linea</label>
                            <input type='text' class='igv{$value['id']} pre-css' name='precio_v' value='" . htmlspecialchars($value['igv']) . "' readonly>
                            <label>Total</label>
                            <input type='text' class='total{$value['id']} pre-css' name='precio_v' value='" . htmlspecialchars($value['precio_unitario']) . "' onkeyup='this.value=Numeros(this.value)' readonly>
                            <div class='btn-grupos'>
                                <button class='btn btn-primary btn-sm closeModalProductos btn-close' descripcionP='{$value['descripcion']}' idProducto='{$value['id']}'><i class='fa fa-times-circle'></i></button>
                                <button class='btn btn-primary btn-sm agregarProducto' descripcionP='{$value['descripcion']}' idProducto='{$value['id']}'><i class='fa fa-plus'></i></button>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>";
    }

    $reload = './index.php';
    $paginador = $this->generatePagination($reload, $page, $pagination['totalPages'], $this->adjacents);
    echo "<tr>
            <td colspan='10' style='text-align:center;'>$paginador</td>
        </tr>";
  }

  // DATA_TABLE LISTA LOS PRODUCTOS PARA GUÍA
  public function dtaProductosGuia()
  {
    $action = $_REQUEST['action'] ?? '';
    if ($action != 'ajax') return;

    $search = $this->getSearchValue('searchProductoG');
    $perPage = $this->getPerPage($_GET['selectnum'] ?? null);
    $page = (int)($_REQUEST['page'] ?? 1);

    $columns = ['codigo', 'serie', 'descripcion'];
    $table = 'productos';

    $where = $this->buildWhereClause($search, $columns);
    $pagination = $this->setupPagination($table, $where, $perPage, $page);

    $registros = $this->pdo->prepare("SELECT * FROM $table $where LIMIT {$pagination['offset']}, {$pagination['perPage']}");
    $registros->execute();
    $registros = $registros->fetchAll();

    foreach ($registros as $key => $value) {
      echo "<tr class='contenedor-items'>
                <td>" . ++$key . "</td>
                <td>" . htmlspecialchars($value['codigo']) . "</td>
                <td>" . htmlspecialchars($value['serie']) . "</td>
                <td>" . htmlspecialchars($value['descripcion']) . "</td>
                <td>" . htmlspecialchars($value['codunidad']) . "</td>
                <td>
                    <input type='number' class='number cantidad-stock' name='cantidad' id='cantidad{$value['id']}' idProducto='{$value['id']}' min='1' value='' onkeyup='this.value=Numeros(this.value)'>
                </td>
                <td class='btn-prod'>
                    <div class='btn-group' style='justify-content: center;'>
                        <button class='btn btn-primary btn-sm agregarProductoGuia' descripcionP='{$value['descripcion']}' idProducto='{$value['id']}'><i class='fa fa-plus'></i></button>
                    </div>
                </td>
            </tr>";
    }

    $reload = './index.php';
    $paginador = $this->generatePagination($reload, $page, $pagination['totalPages'], $this->adjacents);
    echo "<tr>
            <td colspan='7' style='text-align:center;'>$paginador</td>
        </tr>";
  }

  // DATA_TABLE LISTAR LAS VENTAS
  public function dtaVentas()
  {
    $action = $_REQUEST['action'] ?? '';
    if ($action != 'ajax') return;

    $ruta_xml = "xml";
    $ruta_cdr = "cdr";

    $search = $this->getSearchValue('searchVentas');
    $perPage = $this->getPerPage($_GET['selectnum'] ?? null);
    $page = (int)($_REQUEST['page'] ?? 1);
    $fechaInicial = $_GET['fechaInicial'] ?? '';
    $fechaFinal = $_GET['fechaFinal'] ?? '';

    $columns = ['serie_correlativo', 'correlativo'];
    $table = 'venta';

    $additionalConditions = "tipocomp != '02' AND resumen='n'";

    if (!empty($fechaInicial) && !empty($fechaFinal)) {
      if ($fechaInicial == $fechaFinal) {
        $additionalConditions .= " AND fecha_emision LIKE '%$fechaFinal%'";
      } else {
        $additionalConditions .= " AND fecha_emision BETWEEN '$fechaInicial' AND '$fechaFinal'";
      }
    }

    $where = $this->buildWhereClause($search, $columns, $additionalConditions);
    $pagination = $this->setupPagination($table, $where, $perPage, $page);

    $registros = $this->pdo->prepare("SELECT * FROM $table $where ORDER BY id DESC LIMIT {$pagination['offset']}, {$pagination['perPage']}");
    $registros->execute();
    $registros = $registros->fetchAll();

    foreach ($registros as $key => $value) {
      $cliente = ControladorClientes::ctrMostrarClientes('id', $value['codcliente']);
      $emisor = ControladorEmpresa::ctrEmisor();
      $notaC = ControladorNotaCredito::ctrMostrarNotaCredito('id', $value["id_nc"] ?? null);
      $notaD = ControladorNotaDebito::ctrMostrarNotaDebito('id', $value["id_nd"] ?? null);
      $bajasComprobantes = ControladorEnvioSunat::ctrMostrarBajas('idenvio', $value['idbaja'] ?? null);

      // Determinar tipo de comprobante
      $nombreRazon = '';
      $serieCorrelativo = '';

      if ($value['tipocomp'] == '01') {
        if (isset($notaC['feestado']) && $value['serie_correlativo'] == $notaC['seriecorrelativo_ref'] && $notaC['feestado'] == 1) {
          $nombreRazon = $cliente['ruc'] . "<br>" . $cliente['razon_social'];
          $serieCorrelativo = "NOTA DE CRÉDITO-" . $notaC['serie'] . '-' . $notaC['correlativo'] . "
                        <br>
                        <i class='fas fa-bullseye' style='color:green'></i>
                        <span style='font-size:10px; margin-left:3px;'> FACTURA AFECTADA: " . $notaC['serie_ref'] . '-' . $notaC['correlativo_ref'] . "</span>";
        } else if (isset($notaD['feestado']) && $value['serie_correlativo'] == $notaD['seriecorrelativo_ref'] && $notaD['feestado'] == 1) {
          $nombreRazon = $cliente['ruc'] . "<br>" . $cliente['razon_social'];
          $serieCorrelativo = "NOTA DE DÉBITO-" . $notaD['serie'] . '-' . $notaD['correlativo'] . "
                        <br>
                        <i class='fas fa-bullseye' style='color:green'></i>
                        <span style='font-size:10px; margin-left:3px;'> FACTURA AFECTADA: " . $notaD['serie_ref'] . '-' . $notaD['correlativo_ref'] . "</span>";
        } else {
          $nombreRazon = $cliente['ruc'] . "<br>" . $cliente['razon_social'];
          $serieCorrelativo = "FACTURA-" . $value['serie_correlativo'];
        }
      } else if ($value['tipocomp'] == '03') {
        if (isset($notaC['feestado']) && $value['serie_correlativo'] == $notaC['seriecorrelativo_ref'] && $notaC['feestado'] == 1) {
          $nombreRazon = $cliente['documento'] . "<br>" . $cliente['nombre'];
          $serieCorrelativo = "NOTA DE CRÉDITO-" . $notaC['serie'] . '-' . $notaC['correlativo'] . "
                        <br>
                        <i class='fas fa-bullseye' style='color:red'></i>
                        <span style='font-size:10px; margin-left:3px;'> BOLETA AFECTADA: " . $notaC['serie_ref'] . '-' . $notaC['correlativo_ref'] . "</span>";
        } else if (isset($notaD['feestado']) && $value['serie_correlativo'] == $notaD['seriecorrelativo_ref'] && $notaD['feestado'] == 1) {
          $nombreRazon = $cliente['documento'] . "<br>" . $cliente['nombre'];
          $serieCorrelativo = "NOTA DE DÉBITO-" . $notaD['serie'] . '-' . $notaD['correlativo'] . "
                        <br>
                        <i class='fas fa-bullseye' style='color:red'></i>
                        <span style='font-size:10px; margin-left:3px;'> BOLETA AFECTADA: " . $notaD['serie_ref'] . '-' . $notaD['correlativo_ref'] . "</span>";
        } else {
          $nombreRazon = $cliente['documento'] . "<br>" . $cliente['nombre'];
          $serieCorrelativo = "BOLETA DE VENTA--" . $value['serie_correlativo'];
        }
      }

      $textMoneda = ($value['codmoneda'] == 'PEN') ? 'S/ ' : '$USD ';
      $fecha = date_create($value['fechahora']);

      echo "<tr>
                <td>" . ++$key . "</td>
                <td>" . date_format($fecha, 'd-m-Y / H:i:s') . "</td>
                <td>$serieCorrelativo</td>
                <td>$nombreRazon</td>
                <td>" . $textMoneda . number_format($value['total'], 2) . "</td>
                <td>
                    <div class='contenedor-print-comprobantes'>
                        <form id='printC' name='printC' method='post' action='vistas/print/printer/' target='_blank'>
                            <input type='radio' class='a4{$value['id']}' id='a4' name='a4' value='A4'>
                            <input type='radio' class='tk{$value['id']}' id='tk' name='a4' value='TK'>
                            <input type='hidden' id='idCo' name='idCo' value='{$value['id']}'>
                            <button class='printA4' id='printA4' idComp='{$value['id']}'></button>
                            <button class='printT' id='printT' idComp='{$value['id']}'></button>
                        </form>
                    </div>
                </td>";

      // XML
      $btnXml = '';
      if ($value['anulado'] == 'n') {
        $nombre = $emisor['ruc'] . '-' . $value['tipocomp'] . '-' . $value['serie'] . '-' . $value['correlativo'];
        $btnXml = "<a href='./api/$ruta_xml/" . $nombre . ".XML' target='_blank' class='xml' id='xml' idComp='{$value['id']}' download></a>";
      } else {
        foreach ($bajasComprobantes as $v) {
          $btnXml = "<a href='./api/$ruta_xml/" . $v['nombrexml'] . "' target='_blank' class='xml' id='xml' idComp='{$value['id']}' download></a>";
        }
      }

      echo "<td><div class='contenedor-print-comprobantes'>$btnXml</div></td>";

      // CDR
      $botonEstadoCdr = '';
      if ($value['feestado'] == '1' && $value['anulado'] == 'n') {
        $botonEstadoCdr = "<a href='./api/$ruta_cdr/R-" . $value['nombrexml'] . "' target='_blank' class='cdr' id='cdr' idComp='{$value['id']}'></a>";
      } else if ($value['feestado'] == '2') {
        $botonEstadoCdr = "<button class='s-rechazo'></button>";
      } else {
        $botonEstadoCdr = "<button class='s-getcdr' id='getcdr1' idVenta='{$value['id']}'></button>";
      }

      echo "<td><div class='contenedor-print-comprobantes' estadocdr{$value['id']}>$botonEstadoCdr</div></td>";

      // Estado SUNAT
      $botonEstado = '';
      if ($value['feestado'] == '1' && $value['anulado'] == 'n') {
        $botonEstado = "<button class='s-success'></button>";
      } else if ($value['feestado'] == '2') {
        $botonEstado = "<button class='s-rechazo'></button>";
      } else {
        $botonEstado = "<button class='s-getcdr' id='getcdr3' idVenta='{$value['id']}'></button>";
      }

      echo "<td><div class='contenedor-print-comprobantes estadosunat{$value['id']}'>$botonEstado</div></td>";

      // Menú de opciones
      $btnAnulado = '';
      if ($value['anulado'] == 's') {
        $btnAnulado = '<button class="anulado"></button>';
      } else if ($value['anulado'] == 'n' && $value['feestado'] == '1') {
        if ($value['tipocomp'] == '01') {
          $btnAnulado = "<nav class='navbar navbar-static-top'>
                        <div class='navbar-custom-menu'>
                            <ul class='nav navbar-nav'>
                                <li class='dropdown tasks-menu option-menu'>
                                    <a href='#' class='dropdown-toggle option-menu' data-toggle='dropdown'></a>
                                    <ul class='dropdown-menu' style='width: 220px; color:black;'>
                                        <li class=''>
                                            <button id='bajaDoc' idDoc='{$value['id']}'>
                                                <i class='fas fa-times'></i> <span>Anular comprobante</span>
                                            </button>
                                        </li>
                                        <li class=''>
                                            <form class='form' action='nota-credito' method='post'>
                                                <input type='hidden' class='resultadoSerie' name='resultadoSerie' value='{$value['serie_correlativo']}'>
                                                <input type='hidden' id='tipoComprobante' name='tipoComprobante' value='{$value['tipocomp']}'>
                                                <button><i class='fas fa-plus'></i> <span>Crear nota de crédito</span></button>
                                            </form>
                                        </li>
                                        <li class=''>
                                            <form class='form' action='nota-debito' method='post'>
                                                <input type='hidden' class='resultadoSerie' name='resultadoSerie' value='{$value['serie_correlativo']}'>
                                                <input type='hidden' id='tipoComprobante' name='tipoComprobante' value='{$value['tipocomp']}'>
                                                <button><i class='fas fa-plus'></i> <span>Crear nota de débito</span></button>
                                            </form>
                                        </li>
                                        <li class=''>
                                            <form class='form' action='crear-factura' method='post'>
                                                <input type='hidden' class='numCorrelativo' name='numCorrelativo' value='{$value['serie_correlativo']}'>
                                                <button><i class='fas fa-plus'></i> <span>Volver a crear</span></button>
                                            </form>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </nav>";
        } else if ($value['tipocomp'] == '03') {
          $btnAnulado = "<nav class='navbar navbar-static-top'>
                        <div class='navbar-custom-menu'>
                            <ul class='nav navbar-nav'>
                                <li class='dropdown tasks-menu option-menu'>
                                    <a href='#' class='dropdown-toggle option-menu' data-toggle='dropdown'></a>
                                    <ul class='dropdown-menu' style='width: 220px; color:black;'>
                                        <li class=''>
                                            <form class='form' action='nota-credito' method='post'>
                                                <input type='hidden' class='resultadoSerie' name='resultadoSerie' value='{$value['serie_correlativo']}'>
                                                <input type='hidden' id='tipoComprobante' name='tipoComprobante' value='{$value['tipocomp']}'>
                                                <button><i class='fas fa-plus'></i> <span>Crear nota de crédito</span></button>
                                            </form>
                                        </li>
                                        <li class=''>
                                            <form class='form' action='nota-debito' method='post'>
                                                <input type='hidden' class='resultadoSerie' name='resultadoSerie' value='{$value['serie_correlativo']}'>
                                                <input type='hidden' id='tipoComprobante' name='tipoComprobante' value='{$value['tipocomp']}'>
                                                <button><i class='fas fa-plus'></i> <span>Crear nota de débito</span></button>
                                            </form>
                                        </li>
                                        <li class=''>
                                            <form class='form' action='crear-boleta' method='post'>
                                                <input type='hidden' class='numCorrelativo' name='numCorrelativo' value='{$value['serie_correlativo']}'>
                                                <button><i class='fas fa-plus'></i> <span>Volver a crear</span></button>
                                            </form>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </nav>";
        }
      }

      echo "<td><div class='contenedor-print-comprobantes'>$btnAnulado</div></td>";
      echo "</tr>";
    }

    $reload = './index.php';
    $paginador = $this->generatePagination($reload, $page, $pagination['totalPages'], $this->adjacents);
    echo "<tr>
            <td colspan='10' style='text-align:center;'>$paginador</td>
        </tr>";
  }

  // DATA_TABLE QUE LISTA TODOS LOS RESÚMENES DIARIOS
  public function dtaResumenDiario()
  {
    $action = $_REQUEST['action'] ?? '';
    if ($action != 'ajax') return;

    $ruta_xml = "xml";
    $ruta_cdr = "cdr";

    $search = $this->getSearchValue('searchResumen');
    $perPage = $this->getPerPage($_GET['selectnum'] ?? null);
    $page = (int)($_REQUEST['page'] ?? 1);

    $columns = ['correlativo'];
    $table = 'envio_resumen';

    $where = $this->buildWhereClause($search, $columns, "resumen=1");
    $pagination = $this->setupPagination($table, $where, $perPage, $page);

    $registros = $this->pdo->prepare("SELECT * FROM $table $where ORDER BY idenvio DESC LIMIT {$pagination['offset']}, {$pagination['perPage']}");
    $registros->execute();
    $registros = $registros->fetchAll();

    foreach ($registros as $key => $value) {
      echo "<tr>
                <td>" . ++$key . "</td>
                <td class='t-md'>" . date_format(date_create($value['fecha_envio']), 'd-m-Y') . "</td>
                <td class='t-md'>" . date_format(date_create($value['fecha_emision']), 'd-m-Y') . "</td>
                <td>
                    <div class='btn-ver_boletas'>
                        <button id='btnVerBoletas' class='btn btn-primary' idenvio='{$value['idenvio']}' data-toggle='modal' data-target='#modalBoletassssss'><i class='far fa-eye'></i> VER BOLETAS</button>
                    </div>
                </td>
                <td class='t-md'>" . htmlspecialchars($value['ticket']) . "</td>
                <td>
                    <div class='contenedor-print-comprobantes'>
                        <button class='printA4' id='printA4' idComp='{$value['idenvio']}'></button>
                    </div>
                </td>
                <td>
                    <div class='contenedor-print-comprobantes'>
                        <a href='./api/$ruta_xml/" . htmlspecialchars($value['nombrexml']) . "' target='_blank' class='xml' id='xml' idComp='{$value['idenvio']}'></a>
                    </div>
                </td>
                <td>
                    <div class='contenedor-print-comprobantes'>
                        <a href='./api/$ruta_cdr/R-" . htmlspecialchars($value['nombrexml']) . "' target='_blank' class='cdr' id='cdr' idComp='{$value['idenvio']}'></a>
                    </div>
                </td>";

      // Estado SUNAT
      $botonEstado = '';
      if ($value['feestado'] == '1') {
        $botonEstado = "<button class='s-success'></button>";
      } else if ($value['feestado'] == '2') {
        $botonEstado = "<button class='s-rechazo'></button>";
      } else {
        $botonEstado = "<button class='s-getcdr' id='getcdr2' idVenta='{$value['id']}'></button>";
      }

      echo "<td>
                <div class='contenedor-print-comprobantes'>$botonEstado</div>
            </td>
            </tr>";
    }

    $reload = './index.php';
    $paginador = $this->generatePagination($reload, $page, $pagination['totalPages'], $this->adjacents);
    echo "<tr>
            <td colspan='9' style='text-align:center;'>$paginador</td>
        </tr>";
  }

  // DATA_TABLE QUE LISTA LAS BOLETAS DE UN RESÚMEN
  public function dtaResumenBoletas()
  {
    $action = $_REQUEST['action'] ?? '';
    if ($action != 'ajax') return;

    $idenvio = $_REQUEST['idenvio'] ?? 0;
    $search = $this->getSearchValue('searchBoleta');
    $perPage = $this->getPerPage($_GET['selectnum'] ?? null);
    $page = (int)($_REQUEST['page'] ?? 1);

    if ($idenvio == 0) return;

    $where = "WHERE t1.idenvio = $idenvio";
    if (!empty($search)) {
      $where .= " AND t2.serie_correlativo LIKE '%$search%'";
    }

    $pdo = Conexion::conectar();
    $totalRegistros = $pdo->query("SELECT COUNT(*) AS numrows FROM envio_resumen_detalle t1 INNER JOIN venta t2 ON t1.idventa=t2.id $where");
    $totalRegistros = $totalRegistros->fetch()['numrows'];
    $totalPages = ceil($totalRegistros / $perPage);
    $offset = ($page - 1) * $perPage;

    $registros = $pdo->prepare("SELECT t1.idventa, t2.id, t2.fecha_emision, t2.tipocomp, t2.serie_correlativo, t2.serie, t2.correlativo, t2.total, t2.id_nc FROM envio_resumen_detalle t1 INNER JOIN venta t2 ON t1.idventa=t2.id $where LIMIT $offset, $perPage");
    $registros->execute();
    $registros = $registros->fetchAll();

    foreach ($registros as $key => $value) {
      $notac = ControladorNotaCredito::ctrMostrarNotaCredito('id', $value['id_nc']);

      echo "<tr class='t-md'>
                <td>" . date_format(date_create($value['fecha_emision']), 'd-m-Y') . "</td>
                <td>" . htmlspecialchars($value['tipocomp']) . "</td>
                <td>" . htmlspecialchars($value['serie']) . "</td>
                <td>" . htmlspecialchars($value['correlativo']) . "</td>
                <td>" . htmlspecialchars($value['total']) . "</td>";

      if ($value['id_nc'] !== null) {
        echo "<td>Afectado por NC: " . htmlspecialchars($notac['serie'] ?? '') . '-' . htmlspecialchars($notac['correlativo'] ?? '') . "</td>";
      } else {
        echo "<td>Adicionar</td>";
      }

      echo "</tr>";
    }

    $reload = './index.php';
    include_once 'pagination.php';
    $paginador = new Paginacion();
    $paginador = $paginador->paginarResumenBoletas($reload, $page, $totalPages, $this->adjacents);
    echo "<tr>
            <td colspan='6' style='text-align:center;'>$paginador</td>
        </tr>";
  }
}

// Ejecución de los métodos según los parámetros
if (isset($_REQUEST['dc']) && $_REQUEST['dc'] == "dc") {
  $data = new DataTables();
  $data->dtaClientes();
}

if (isset($_REQUEST['dp']) && $_REQUEST['dp'] == "dp") {
  $data = new DataTables();
  $data->dtaProductos();
}

if (isset($_REQUEST['dpv']) && $_REQUEST['dpv'] == "dpv") {
  $data = new DataTables();
  $data->dtaProductosVentas();
}

if (isset($_REQUEST['dpg']) && $_REQUEST['dpg'] == "dpg") {
  $data = new DataTables();
  $data->dtaProductosGuia();
}

if (isset($_REQUEST['dv']) && $_REQUEST['dv'] == "dv") {
  $data = new DataTables();
  $data->dtaVentas();
}

if (isset($_REQUEST['rd']) && $_REQUEST['rd'] == "rd") {
  $data = new DataTables();
  $data->dtaResumenDiario();
}

if (isset($_REQUEST['loadBoletas'])) {
  $data = new DataTables();
  $data->dtaResumenBoletas();
}
?>
<script>
  function Numeros(string) {
    var out = '';
    var filtro = '1234567890.';

    for (var i = 0; i < string.length; i++) {
      if (filtro.indexOf(string.charAt(i)) != -1) {
        if (string.charAt(i) === '.' && out.indexOf('.') != -1) {
          continue;
        }
        out += string.charAt(i);
      }
    }
    return out;
  }
</script>
