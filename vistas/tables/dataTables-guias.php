<?php
session_start();

require_once __DIR__ . '/../../vendor/autoload.php';

use Conect\Conexion;
use Controladores\ControladorClientes;

class DataTablesGuias
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

  private function generatePagination($reload, $page, $totalPages, $adjacents)
  {
    include_once 'pagination.php';
    $paginador = new Paginacion();
    return $paginador->paginarGuias($reload, $page, $totalPages, $adjacents);
  }

  public function dtaListarGuias()
  {
    $action = $_REQUEST['action'] ?? '';
    if ($action != 'ajax') return;

    $ruta_xml = "xml";
    $ruta_cdr = "cdr";

    $search = $this->getSearchValue('searchGuias');
    $perPage = $this->getPerPage($_GET['selectnum'] ?? null);
    $page = (int)($_REQUEST['page'] ?? 1);
    $fechaInicial = $_GET['fechaInicial'] ?? '';
    $fechaFinal = $_GET['fechaFinal'] ?? '';

    $sTable = 'guia';
    $sWhere = $this->buildWhereClause($search, $fechaInicial, $fechaFinal);

    $pdo = Conexion::conectar();
    $totalRegistros = $pdo->query("SELECT COUNT(*) AS numrows FROM $sTable $sWhere");
    $totalRegistros = $totalRegistros->fetch()['numrows'] ?? 0;
    $totalPages = ($perPage > 0) ? ceil($totalRegistros / $perPage) : 0;
    $offset = ($page - 1) * $perPage;

    $registros = $pdo->prepare("SELECT * FROM $sTable $sWhere ORDER BY id DESC LIMIT $offset, $perPage");
    $registros->execute();
    $registros = $registros->fetchAll();

    foreach ($registros as $k => $v) {
      $cliente = ControladorClientes::ctrMostrarClientes('id', $v['id_cliente']);

      if (($v['cli_tipodoc'] ?? 1) == 1) {
        $nombreRazon = $cliente['nombre'] ?? '';
        $doc = $cliente['documento'] ?? '';
      } else {
        $nombreRazon = $cliente['razon_social'] ?? '';
        $doc = $cliente['ruc'] ?? '';
      }

      $btnXml = '<a href="./api/' . $ruta_xml . '/' . $v['nombrexml'] . '" target="_blank" class="xml" id="xml" idComp="' . $v['id'] . '"></a>';
      $botonEstadoCdr = '<a href="./api/' . $ruta_cdr . '/' . $v['xmlbase64'] . '" target="_blank" class="cdr" id="cdr" idComp="' . $v['id'] . '"></a>';

      if ($v['feestado'] == '1') {
        $botonEstado = "<button class='s-success'></button>";
      } elseif ($v['feestado'] == '2') {
        $botonEstado = "<button class='s-rechazo'></button>";
      } else {
        $botonEstado = "<button class='s-getcdr' id='getcdr3' idVenta='" . $v['id'] . "'></button>";
      }

      echo '<tr>
        <td>' . ++$k . '</td>
        <td>' . $v['fecha_emision'] . '</td>
        <td>' . $v['serie'] . '-' . $v['correlativo'] . '</td>
        <td>' . htmlspecialchars($nombreRazon) . '<br>' . htmlspecialchars($doc) . '</td>
        <td>' . $v['comp_ref'] . '</td>
        <td>
          <div class="contenedor-print-comprobantes">
            <form id="printC" name="printC" method="post" action="vistas/print/printguia/" target="_blank">
              <input type="hidden" id="idCo" name="idCo" value="' . $v['id'] . '">
              <button class="printA4" id="printA4" idComp="' . $v['id'] . '"></button>
            </form>
          </div>
        </td>
        <td>
          <div class="contenedor-print-comprobantes" estadocdr' . $v['id'] . '>
            ' . $btnXml . '
          </div>
        </td>
        <td>
          <div class="contenedor-print-comprobantes" estadocdr' . $v['id'] . '>
            ' . $botonEstadoCdr . '
          </div>
        </td>
        <td>
          <div class="contenedor-print-comprobantes" estadocdr' . $v['id'] . '>
            ' . $botonEstado . '
          </div>
        </td>
      </tr>';
    }

    $reload = './index.php';
    $paginador = $this->generatePagination($reload, $page, $totalPages, $this->adjacents);
    echo "<tr>
      <td colspan='9' style='text-align:center;'>$paginador</td>
    </tr>";
  }

  private function buildWhereClause($search, $fechaInicial, $fechaFinal)
  {
    $conditions = [];

    if (!empty($fechaInicial) && !empty($fechaFinal)) {
      if ($fechaInicial == $fechaFinal) {
        $conditions[] = "fecha_emision LIKE '%$fechaFinal%'";
      } else {
        $conditions[] = "fecha_emision BETWEEN '$fechaInicial' AND '$fechaFinal'";
      }
    }

    if (!empty($search)) {
      $conditions[] = "(serie LIKE '%" . addslashes($search) . "%' OR correlativo LIKE '%" . addslashes($search) . "%')";
    }

    if (empty($conditions)) {
      return "";
    }

    return "WHERE " . implode(" AND ", $conditions);
  }
}

if (isset($_REQUEST['lig']) && $_REQUEST['lig'] == 'lig') {
  $guias = new DataTablesGuias();
  $guias->dtaListarGuias();
}