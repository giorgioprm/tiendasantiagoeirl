<?php
session_start();
require_once __DIR__ . '/../../vendor/autoload.php';

use Conect\Conexion;
use Controladores\ControladorClientes;
use Controladores\ControladorNotaCredito;
use Controladores\ControladorNotaDebito;
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
    include_once 'pagination-reportes.php';
    $paginador = new PaginacionR();
    return $paginador->paginarComprobantes($reload, $page, $totalPages, $adjacents);
  }

  public function dtaReportes()
  {
    $action = $_REQUEST['action'] ?? '';
    if ($action != 'ajax') return;

    $fechaini = $_GET['fechaini'] ?? '';
    $fechafin = $_GET['fechafin'] ?? '';
    $tipocomp = $_GET['tipocomp'] ?? '';
    $search = $this->getSearchValue('searchR');
    $perPage = $this->getPerPage($_GET['selectnum'] ?? null);
    $page = (int)($_REQUEST['page'] ?? 1);

    $fechaInicial = $this->formatDate($fechaini);
    $fechaFinal = $this->formatDate($fechafin);

    if (empty($fechaInicial) || empty($fechaFinal)) {
      echo "<tr><td colspan='10' style='text-align:center;'>Error: Fechas inválidas</td></tr>";
      return;
    }

    // Determinar la tabla según el tipo de comprobante
    if ($tipocomp == '07') {
      $sTable = 'nota_credito';
    } elseif ($tipocomp == '08') {
      $sTable = 'nota_debito';
    } else {
      $sTable = 'venta';
    }

    $sTable2 = 'clientes';
    $sWhere = $this->buildWhereClause($tipocomp, $search, $fechaInicial, $fechaFinal, $sTable);

    $pdo = Conexion::conectar();

    // CORREGIDO: Usar alias correctos en la consulta
    $queryCount = "SELECT COUNT(*) AS numrows FROM $sTable t1 INNER JOIN $sTable2 t2 ON t1.codcliente=t2.id $sWhere";
    $totalRegistros = $pdo->query($queryCount);

    if (!$totalRegistros) {
      echo "<tr><td colspan='10' style='text-align:center;'>Error en la consulta</td></tr>";
      return;
    }

    $totalRegistros = $totalRegistros->fetch()['numrows'] ?? 0;
    $totalPages = ($perPage > 0) ? ceil($totalRegistros / $perPage) : 0;
    $offset = ($page - 1) * $perPage;

    // CORREGIDO: Usar alias correctos en la consulta principal
    if ($tipocomp == '07' || $tipocomp == '08') {
      $query = "SELECT t1.id, t2.nombre, t1.igv, t1.fecha_emision, t1.tipocomp, t1.serie, 
                       t1.codmoneda, t1.correlativo, t1.total, t2.razon_social, 
                       t1.tipocomp_ref, t1.seriecorrelativo_ref, t2.ruc, t2.documento 
                FROM $sTable t1 
                INNER JOIN $sTable2 t2 ON t1.codcliente=t2.id 
                $sWhere 
                ORDER BY t1.id DESC 
                LIMIT $offset, $perPage";
    } else {
      $query = "SELECT t1.id, t2.nombre, t1.igv, t1.fecha_emision, t1.tipocomp, t1.serie, 
                       t1.codmoneda, t1.correlativo, t1.subtotal, t1.total, 
                       t1.serie_correlativo, t1.id_nc, t1.id_nd, t2.razon_social, 
                       t2.ruc, t2.documento 
                FROM $sTable t1 
                INNER JOIN $sTable2 t2 ON t1.codcliente=t2.id 
                $sWhere 
                ORDER BY t1.id DESC 
                LIMIT $offset, $perPage";
    }

    $registros = $pdo->prepare($query);
    $registros->execute();
    $registros = $registros->fetchAll();

    $totaligv = 0;
    $total = 0;

    if ($totalRegistros > 0) {
      foreach ($registros as $key => $value) {
        $moneda = ($value['codmoneda'] == 'PEN') ? "S/ " : "UD$ ";

        echo "<tr>
          <td>" . ++$key . "</td>
          <td>" . date_format(date_create($value['fecha_emision']), 'd/m/Y') . "</td>";

        // Mostrar serie/correlativo según tipo
        if ($tipocomp == '07' || $tipocomp == '08') {
          echo "<td>" . $value['serie'] . '-' . $value['correlativo'] . " AFECTADO N° " . ($value['seriecorrelativo_ref'] ?? '') . "</td>";
        } else {
          if ($value['id_nc'] != null) {
            $notaC = ControladorNotaCredito::ctrMostrarNotaCredito('id', $value['id_nc']);
            echo "<td>" . $value['serie_correlativo'] . "<br/>AFECTADA - NOTA DE CRÉDITO N°: " . ($notaC['serie'] ?? '') . "-" . ($notaC['correlativo'] ?? '') . "</td>";
          } elseif ($value['id_nd'] != null) {
            $notaD = ControladorNotaDebito::ctrMostrarNotaDebito('id', $value['id_nd']);
            echo "<td>" . $value['serie_correlativo'] . "<br/>AFECTADA - NOTA DE DÉBITO N°: " . ($notaD['serie'] ?? '') . "-" . ($notaD['correlativo'] ?? '') . "</td>";
          } else {
            echo "<td>" . $value['serie_correlativo'] . "</td>";
          }
        }

        // Mostrar cliente
        if ($tipocomp == '01' || ($value['tipocomp_ref'] ?? '') == '01') {
          echo "<td>" . $value['razon_social'] . "<br>R.U.C. " . $value['ruc'] . "</td>";
        } else {
          echo "<td>" . $value['nombre'] . "<br>D.N.I. " . $value['documento'] . "</td>";
        }

        echo "<td>" . $moneda . number_format($value['igv'], 2) . "</td>
          <td>" . $moneda . number_format($value['total'], 2) . "</td>
          <td>
            <div class='contenedor-print-comprobantes'>
              <input type='radio' class='a4" . $value['id'] . "' id='a4' name='a4' value='A4'>
              <input type='radio' class='tk" . $value['id'] . "' id='tk' name='a4' value='TK'>
              <input type='hidden' id='idCo' name='idCo' value='" . $value['id'] . "'>
              <button class='printA4' id='printA4' idComp='" . $value['id'] . "' data-toggle='modal' data-target='#modalImprimir'></button>
              <button class='printT' id='printT' idComp='" . $value['id'] . "' data-toggle='modal' data-target='#modalImprimir'></button>
            </div>
          </td>
          <td>
            <div class='contenedor-print-comprobantes'>
              <button class='s-success'></button>
            </div>
          </td>
          <td>
            <div class='contenedor-print-comprobantes'>
              <button class='senda4' idComp='" . $value['id'] . "'></button>
            </div>
          </td>
        </tr>";

        $totaligv += $value['igv'];
        $total += $value['total'];
      }

      $moneda = "S/ ";
      echo "<tr>
        <td colspan='4'></td>
        <td>" . $moneda . number_format($totaligv, 2) . "</td>
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

  private function buildWhereClause($tipocomp, $search, $fechaInicial, $fechaFinal, $table)
  {
    $conditions = [];

    if (!empty($fechaInicial) && !empty($fechaFinal)) {
      $conditions[] = "t1.fecha_emision BETWEEN '$fechaInicial' AND '$fechaFinal'";
    }

    if (!empty($tipocomp)) {
      if ($tipocomp != '00' && $tipocomp != '07' && $tipocomp != '08') {
        $conditions[] = "t1.tipocomp = '$tipocomp'";
        $conditions[] = "t1.anulado = 'n'";
        if ($table == 'venta') {
          $conditions[] = "t1.id_nc IS NULL";
        }
      } elseif ($tipocomp == '00') {
        $conditions[] = "t1.anulado = 'n'";
        $conditions[] = "(t1.tipocomp = '01' OR t1.tipocomp = '03')";
        if ($table == 'venta') {
          $conditions[] = "t1.id_nc IS NULL";
        }
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

if (isset($_REQUEST['reportes']) && $_REQUEST['reportes'] == "reportes") {
  $dataReportes = new DataTablesReportes();
  $dataReportes->dtaReportes();
}