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

  private function getPerPage($selectnum = null)
  {
    $perPage = (int)($selectnum ?? $this->perPageDefault);
    return ($perPage > 0) ? $perPage : $this->perPageDefault;
  }

  private function getSearchValue($key, $default = '')
  {
    return $_GET[$key] ?? $_REQUEST[$key] ?? $default;
  }

  private function buildWhereClause($search, $columns, $additionalConditions = '')
  {
    if (empty($search) && empty($additionalConditions)) {
      return "";
    }

    $conditions = [];

    if (!empty($search)) {
      $searchConditions = [];
      foreach ($columns as $column) {
        $searchConditions[] = "$column LIKE '%" . addslashes($search) . "%'";
      }
      $conditions[] = "(" . implode(" OR ", $searchConditions) . ")";
    }

    if (!empty($additionalConditions)) {
      $conditions[] = $additionalConditions;
    }

    return "WHERE " . implode(" AND ", $conditions);
  }

  private function setupPagination($table, $where, $perPage, $page)
  {
    $query = "SELECT COUNT(*) AS numrows FROM $table $where";
    $totalRegistros = $this->pdo->query($query);
    $totalRegistros = $totalRegistros ? $totalRegistros->fetch()['numrows'] : 0;

    $totalPages = ($perPage > 0) ? ceil($totalRegistros / $perPage) : 0;
    $offset = ($page - 1) * $perPage;

    return [
      'total' => $totalRegistros,
      'totalPages' => $totalPages,
      'offset' => $offset,
      'perPage' => $perPage
    ];
  }

  private function generatePagination($reload, $page, $totalPages, $adjacents)
  {
    if (!file_exists('pagination.php')) {
      return '';
    }
    include_once 'pagination.php';
    if (!class_exists('Paginacion')) {
      return '';
    }

    $paginador = new Paginacion();
    $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
    $caller = $backtrace[1]['function'] ?? '';

    $methodMap = [
      'dtaClientes' => 'paginarClientes',
      'dtaProductos' => 'paginarProductos',
      'dtaProductosVentas' => 'paginarProductosVentas',
      'dtaProductosGuia' => 'paginarProductosGuia',
      'dtaVentas' => 'paginarVentas',
      'dtaResumenDiario' => 'paginarResumenesDiarios'
    ];

    $method = $methodMap[$caller] ?? null;
    if ($method && method_exists($paginador, $method)) {
      return $paginador->$method($reload, $page, $totalPages, $adjacents);
    }

    return '';
  }

  // ==================== CLIENTES ====================
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

    $query = "SELECT * FROM $table $where LIMIT {$pagination['offset']}, {$pagination['perPage']}";
    $registros = $this->pdo->prepare($query);
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
      }

      echo "    </div>
                </td>
            </tr>";
    }

    $reload = './index.php';
    $paginador = $this->generatePagination($reload, $page, $pagination['totalPages'], $this->adjacents);
    echo "<tr><td colspan='8' style='text-align:center;'>$paginador</td></tr>";
  }

  // ==================== PRODUCTOS ====================
  public function dtaProductos()
  {
    $action = $_REQUEST['action'] ?? '';
    if ($action != 'ajax') return;

    $perfilUsuario = $_REQUEST['perfilOculto'] ?? '';
    $search = $this->getSearchValue('searchProducto');
    $perPage = $this->getPerPage($_GET['selectnum'] ?? null);
    $page = (int)($_REQUEST['page'] ?? 1);

    $columns = ['codigo', 'serie', 'descripcion'];
    $table = 'productos';

    $where = $this->buildWhereClause($search, $columns);
    $pagination = $this->setupPagination($table, $where, $perPage, $page);

    $query = "SELECT * FROM $table $where LIMIT {$pagination['offset']}, {$pagination['perPage']}";
    $registros = $this->pdo->prepare($query);
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
    }

    $reload = './index.php';
    $paginador = $this->generatePagination($reload, $page, $pagination['totalPages'], $this->adjacents);
    echo "<tr><td colspan='10' style='text-align:center;'>$paginador</td></tr>";
  }

  // ==================== PRODUCTOS VENTAS ====================
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

    $query = "SELECT * FROM $table $where LIMIT {$pagination['offset']}, {$pagination['perPage']}";
    $registros = $this->pdo->prepare($query);
    $registros->execute();
    $registros = $registros->fetchAll();

    foreach ($registros as $key => $value) {
      $categoria = ControladorCategorias::ctrMostrarCategorias('id', $value['id_categoria']);

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
    }

    $reload = './index.php';
    $paginador = $this->generatePagination($reload, $page, $pagination['totalPages'], $this->adjacents);
    echo "<tr><td colspan='10' style='text-align:center;'>$paginador</td></tr>";
  }

  // ==================== PRODUCTOS GUIA ====================
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

    $query = "SELECT * FROM $table $where LIMIT {$pagination['offset']}, {$pagination['perPage']}";
    $registros = $this->pdo->prepare($query);
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
    echo "<tr><td colspan='7' style='text-align:center;'>$paginador</td></tr>";
  }

  // ==================== VENTAS ====================
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

    $query = "SELECT * FROM $table $where ORDER BY id DESC LIMIT {$pagination['offset']}, {$pagination['perPage']}";
    $registros = $this->pdo->prepare($query);
    $registros->execute();
    $registros = $registros->fetchAll();

    foreach ($registros as $key => $value) {
      $cliente = ControladorClientes::ctrMostrarClientes('id', $value['codcliente']);
      $emisor = ControladorEmpresa::ctrEmisor();
      $notaC = ControladorNotaCredito::ctrMostrarNotaCredito('id', $value["id_nc"] ?? null);
      $notaD = ControladorNotaDebito::ctrMostrarNotaDebito('id', $value["id_nd"] ?? null);
      $bajasComprobantes = ControladorEnvioSunat::ctrMostrarBajas('idenvio', $value['idbaja'] ?? null);

      $nombreRazon = '';
      $serieCorrelativo = '';

      if ($value['tipocomp'] == '01') {
        if (isset($notaC['feestado']) && $value['serie_correlativo'] == $notaC['seriecorrelativo_ref'] && $notaC['feestado'] == 1) {
          $nombreRazon = $cliente['ruc'] . "<br>" . $cliente['razon_social'];
          $serieCorrelativo = "NOTA DE CRÉDITO-" . $notaC['serie'] . '-' . $notaC['correlativo'] . "<br><i class='fas fa-bullseye' style='color:green'></i><span style='font-size:10px; margin-left:3px;'> FACTURA AFECTADA: " . $notaC['serie_ref'] . '-' . $notaC['correlativo_ref'] . "</span>";
        } else if (isset($notaD['feestado']) && $value['serie_correlativo'] == $notaD['seriecorrelativo_ref'] && $notaD['feestado'] == 1) {
          $nombreRazon = $cliente['ruc'] . "<br>" . $cliente['razon_social'];
          $serieCorrelativo = "NOTA DE DÉBITO-" . $notaD['serie'] . '-' . $notaD['correlativo'] . "<br><i class='fas fa-bullseye' style='color:green'></i><span style='font-size:10px; margin-left:3px;'> FACTURA AFECTADA: " . $notaD['serie_ref'] . '-' . $notaD['correlativo_ref'] . "</span>";
        } else {
          $nombreRazon = $cliente['ruc'] . "<br>" . $cliente['razon_social'];
          $serieCorrelativo = "FACTURA-" . $value['serie_correlativo'];
        }
      } else if ($value['tipocomp'] == '03') {
        if (isset($notaC['feestado']) && $value['serie_correlativo'] == $notaC['seriecorrelativo_ref'] && $notaC['feestado'] == 1) {
          $nombreRazon = $cliente['documento'] . "<br>" . $cliente['nombre'];
          $serieCorrelativo = "NOTA DE CRÉDITO-" . $notaC['serie'] . '-' . $notaC['correlativo'] . "<br><i class='fas fa-bullseye' style='color:red'></i><span style='font-size:10px; margin-left:3px;'> BOLETA AFECTADA: " . $notaC['serie_ref'] . '-' . $notaC['correlativo_ref'] . "</span>";
        } else if (isset($notaD['feestado']) && $value['serie_correlativo'] == $notaD['seriecorrelativo_ref'] && $notaD['feestado'] == 1) {
          $nombreRazon = $cliente['documento'] . "<br>" . $cliente['nombre'];
          $serieCorrelativo = "NOTA DE DÉBITO-" . $notaD['serie'] . '-' . $notaD['correlativo'] . "<br><i class='fas fa-bullseye' style='color:red'></i><span style='font-size:10px; margin-left:3px;'> BOLETA AFECTADA: " . $notaD['serie_ref'] . '-' . $notaD['correlativo_ref'] . "</span>";
        } else {
          $nombreRazon = $cliente['documento'] . "<br>" . $cliente['nombre'];
          $serieCorrelativo = "BOLETA DE VENTA-" . $value['serie_correlativo'];
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
                </td>
                <td>
                    <div class='contenedor-print-comprobantes'>
                        <a href='./api/$ruta_xml/{$value['nombrexml']}' target='_blank' class='xml' id='xml' idComp='{$value['id']}' download></a>
                    </div>
                </td>
                <td>
                    <div class='contenedor-print-comprobantes' estadocdr{$value['id']}>
                        <a href='./api/$ruta_cdr/R-{$value['nombrexml']}' target='_blank' class='cdr' id='cdr' idComp='{$value['id']}'></a>
                    </div>
                </td>
                <td>
                    <div class='contenedor-print-comprobantes estadosunat{$value['id']}'>
                        <button class='s-success'></button>
                    </div>
                </td>
                <td>
                    <div class='contenedor-print-comprobantes'>
                        <button class='option-menu'></button>
                    </div>
                </td>
            </tr>";
    }

    $reload = './index.php';
    $paginador = $this->generatePagination($reload, $page, $pagination['totalPages'], $this->adjacents);
    echo "<tr><td colspan='10' style='text-align:center;'>$paginador</td></tr>";
  }

  // ==================== RESUMEN DIARIO ====================
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

    $query = "SELECT * FROM $table $where ORDER BY idenvio DESC LIMIT {$pagination['offset']}, {$pagination['perPage']}";
    $registros = $this->pdo->prepare($query);
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
                </td>
                <td>
                    <div class='contenedor-print-comprobantes'>
                        <button class='s-success'></button>
                    </div>
                </td>
            </tr>";
    }

    $reload = './index.php';
    $paginador = $this->generatePagination($reload, $page, $pagination['totalPages'], $this->adjacents);
    echo "<tr><td colspan='9' style='text-align:center;'>$paginador</td></tr>";
  }

  // ==================== RESUMEN BOLETAS ====================
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
    $totalRegistros = $totalRegistros ? $totalRegistros->fetch()['numrows'] : 0;
    $totalPages = ($perPage > 0) ? ceil($totalRegistros / $perPage) : 0;
    $offset = ($page - 1) * $perPage;

    $query = "SELECT t1.idventa, t2.id, t2.fecha_emision, t2.tipocomp, t2.serie_correlativo, t2.serie, t2.correlativo, t2.total, t2.id_nc FROM envio_resumen_detalle t1 INNER JOIN venta t2 ON t1.idventa=t2.id $where LIMIT $offset, $perPage";
    $registros = $pdo->prepare($query);
    $registros->execute();
    $registros = $registros->fetchAll();

    foreach ($registros as $key => $value) {
      $notac = ControladorNotaCredito::ctrMostrarNotaCredito('id', $value['id_nc']);

      echo "<tr class='t-md'>
                <td>" . date_format(date_create($value['fecha_emision']), 'd-m-Y') . "</td>
                <td>" . htmlspecialchars($value['tipocomp']) . "</td>
                <td>" . htmlspecialchars($value['serie']) . "</td>
                <td>" . htmlspecialchars($value['correlativo']) . "</td>
                <td>" . htmlspecialchars($value['total']) . "</td>
                <td>" . ($value['id_nc'] !== null ? 'Afectado por NC: ' . htmlspecialchars($notac['serie'] ?? '') . '-' . htmlspecialchars($notac['correlativo'] ?? '') : 'Adicionar') . "</td>
            </tr>";
    }

    $reload = './index.php';
    include_once 'pagination.php';
    if (class_exists('Paginacion')) {
      $paginador = new Paginacion();
      $paginador = $paginador->paginarResumenBoletas($reload, $page, $totalPages, $this->adjacents);
      echo "<tr><td colspan='6' style='text-align:center;'>$paginador</td></tr>";
    }
  }
}

// ==================== EJECUCIÓN ====================
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