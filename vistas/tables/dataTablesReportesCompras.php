<?php
session_start();
require_once __DIR__ . '/../../vendor/autoload.php';

use Conect\Conexion;
use Controladores\ControladorProveedores;
use Controladores\ControladorReportes;

class DataTablesReportes
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

  private function formatDate($date)
  {
    if (empty($date)) return '';
    $date = str_replace('/', '-', $date);
    $dateObj = date_create($date);
    return $dateObj ? date_format($dateObj, 'Y-m-d') : '';
  }

  private function generatePagination($reload, $page, $totalPages, $adjacents)
  {
    include_once 'pagination-reportes-compras.php';
    $paginador = new PaginacionRC();
    return $paginador->paginarComprobantes($reload, $page, $totalPages, $adjacents);
  }

  public function dtaReportesCompras()
  {
    $action = $_REQUEST['action'] ?? '';
    if ($action != 'ajax') return;

    $fechaini = $_GET['fechaini'] ?? '';
    $fechafin = $_GET['fechafin'] ?? '';
    $tipocomp = $_GET['tipocomp'] ?? '';
    $search = $this->getSearchValue('searchRC');
    $perPage = $this->getPerPage($_GET['selectnum'] ?? null);
    $page = (int)($_REQUEST['page'] ?? 1);

    $fechaInicial = $this->formatDate($fechaini);
    $fechaFinal = $this->formatDate($fechafin);

    if (empty($fechaInicial) || empty($fechaFinal)) {
      echo "<tr><td colspan='10' style='text-align:center;'>Error: Fechas inválidas</td></tr>";
      return;
    }

    $sTable = 'compra';
    $sTable2 = 'proveedores';

    $sWhere = $this->buildWhereClause($tipocomp, $search, $fechaInicial, $fechaFinal);

    $pdo = Conexion::conectar();

    $queryCount = "SELECT COUNT(*) AS numrows FROM $sTable t1 INNER JOIN $sTable2 t2 ON t1.codproveedor=t2.id $sWhere";
    $totalRegistros = $pdo->query($queryCount);

    if (!$totalRegistros) {
      echo "<tr><td colspan='10' style='text-align:center;'>Error en la consulta</td></tr>";
      return;
    }

    $totalRegistros = $totalRegistros->fetch()['numrows'] ?? 0;
    $totalPages = ($perPage > 0) ? ceil($totalRegistros / $perPage) : 0;
    $offset = ($page - 1) * $perPage;

    $query = "SELECT t1.id, t2.nombre, t1.igv, t1.fecha_emision, 
                     t1.tipocomp, t1.serie, t1.codmoneda, t1.correlativo, 
                     t1.subtotal, t1.total, t1.serie_correlativo, t1.codproveedor, 
                     t1.tipodoc, t1.serie_ref, t1.correlativo_ref, 
                     t2.razon_social, t2.ruc, t2.documento 
              FROM $sTable t1 
              INNER JOIN $sTable2 t2 ON t1.codproveedor=t2.id 
              $sWhere 
              ORDER BY t1.id DESC 
              LIMIT $offset, $perPage";

    $registros = $pdo->prepare($query);
    $registros->execute();
    $registros = $registros->fetchAll();

    $totaligv = 0;
    $subtotal = 0;
    $total = 0;

    if ($totalRegistros > 0) {
      foreach ($registros as $key => $value) {
        $proveedores = ControladorProveedores::ctrMostrarProveedores('id', $value['codproveedor']);

        if ($value['tipodoc'] == '6') {
          $verproveedor = ($proveedores['razon_social'] ?? '') . ' - ' . ($proveedores['ruc'] ?? '');
        } else {
          $verproveedor = ($proveedores['nombre'] ?? '') . ' - ' . ($proveedores['documento'] ?? '');
        }

        if ($tipocomp == '07') {
          $serieCorrelativo = "Nota de crédito N°-" . $value['serie_correlativo'] . "<br>que afecta a comprobante N°- " . ($value['serie_ref'] ?? '') . "-" . ($value['correlativo_ref'] ?? '');
        } elseif ($tipocomp == '08') {
          $serieCorrelativo = "Nota de débito N°-" . $value['serie_correlativo'] . "<br>que afecta a comprobante N°- " . ($value['serie_ref'] ?? '') . "-" . ($value['correlativo_ref'] ?? '');
        } else {
          $serieCorrelativo = $value['serie_correlativo'];
        }

        echo "<tr>
          <td>" . ++$key . "</td>
          <td>" . date_format(date_create($value['fecha_emision']), 'd/m/Y') . "</td>
          <td>$serieCorrelativo</td>
          <td>" . htmlspecialchars($verproveedor) . "</td>
          <td>" . number_format($value['igv'], 2) . "</td>
          <td>" . number_format($value['subtotal'], 2) . "</td>
          <td>" . number_format($value['total'], 2) . "</td>
          <td style='text-align:center;'>
            <button class='btn btn-print-compra' idCompra='{$value['id']}' data-toggle='modal' data-target='#modalImprimir'>+</button>
          </td>
          <td style='text-align:center;'>
            <div class='dropdown'>
              <button class='btn btn-danger btn-xs dropdown-toggle' type='button' id='dropdownMenu1' data-toggle='dropdown' aria-expanded='true'>
                <i class='fa fa-cog fa-lg'></i>
                <span class='caret'></span>
              </button>
              <ul class='dropdown-menu dropdown-menu-right' role='menu' aria-labelledby='dropdownMenu1' style='font-size:17px'>";

        if (($_SESSION['perfil'] ?? '') == 'Administrador') {
          echo "<li role='presentation'><a role='menuitem' tabindex='-1' href='#' idCompra='{$value['id']}' class='btn-anular-compra'><i class='fas fa-ban' style='color:red;'></i> Anular compra</a></li>";
        }

        echo "        <li role='presentation'><a role='menuitem' tabindex='-1' href='nueva-compra'><i class='fa fa-plus'></i> Nueva compra</a></li>
              </ul>
            </div>
          </td>
        </tr>";

        $totaligv += $value['igv'];
        $subtotal += $value['subtotal'];
        $total += $value['total'];
      }

      $moneda = 'S/ ';
      echo "<tr>
        <td colspan='4'></td>
        <td>" . $moneda . number_format($totaligv, 2) . "</td>
        <td>" . $moneda . number_format($subtotal, 2) . "</td>
        <td>" . $moneda . number_format($total, 2) . "</td>
      </tr>";

      $reload = './index.php';
      $paginador = $this->generatePagination($reload, $page, $totalPages, $this->adjacents);
      echo "<tr>
        <td colspan='10' style='text-align:center;'>$paginador</td>
      </tr>";
    } else {
      echo "<tr>
        <td colspan='10' style='text-align:center;'>
          <div class='result-report'>
            <i class='fas fa-times'></i> NO SE HA ENCONTRADO RESULTADOS
          </div>
        </td>
      </tr>";
    }
  }

  private function buildWhereClause($tipocomp, $search, $fechaInicial, $fechaFinal)
  {
    $conditions = [];

    // CORREGIDO: Usar fecha_emision (coincide con la estructura de la tabla)
    if (!empty($fechaInicial) && !empty($fechaFinal)) {
      $conditions[] = "t1.fecha_emision BETWEEN '$fechaInicial' AND '$fechaFinal'";
    }

    if (!empty($tipocomp)) {
      if ($tipocomp != '00' && $tipocomp != '07' && $tipocomp != '08') {
        $conditions[] = "t1.tipocomp = '$tipocomp'";
        $conditions[] = "t1.anulado = 'n'";
      } elseif ($tipocomp == '00') {
        $conditions[] = "t1.anulado = 'n'";
        $conditions[] = "(t1.tipocomp = '01' OR t1.tipocomp = '03')";
      } elseif ($tipocomp == '07' || $tipocomp == '08') {
        $conditions[] = "t1.tipocomp = '$tipocomp'";
      }
    }

    if (!empty($search)) {
      $searchConditions = [];
      $searchFields = ['t2.nombre', "CONCAT(t1.serie,'-',t1.correlativo)", 't2.ruc', 't2.documento'];
      foreach ($searchFields as $field) {
        $searchConditions[] = "$field LIKE '%" . addslashes($search) . "%'";
      }
      $conditions[] = "(" . implode(" OR ", $searchConditions) . ")";
    }

    if (empty($conditions)) {
      return "";
    }

    return "WHERE " . implode(" AND ", $conditions);
  }
}

if (isset($_REQUEST['reportesCompras']) && $_REQUEST['reportesCompras'] == "reportesCompras") {
  $dataReportes = new DataTablesReportes();
  $dataReportes->dtaReportesCompras();
}