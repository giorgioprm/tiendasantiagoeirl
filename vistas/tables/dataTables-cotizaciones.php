<?php
session_start();
require_once __DIR__ . '/../../vendor/autoload.php';

use Conect\Conexion;
use Controladores\ControladorClientes;

class DataTablesCotizaciones
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
        return $paginador->paginarCotizaciones($reload, $page, $totalPages, $adjacents);
    }

    public function dtaListarCotizaciones()
    {
        $action = $_REQUEST['action'] ?? '';
        if ($action != 'ajax') return;

        $search = $this->getSearchValue('searchCotiza');
        $perPage = $this->getPerPage($_GET['selectnum'] ?? null);
        $page = (int)($_REQUEST['page'] ?? 1);
        $fechaInicial = $_GET['fechaInicial'] ?? '';
        $fechaFinal = $_GET['fechaFinal'] ?? '';

        $sTable = 'cotizaciones';
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
            $cliente = ControladorClientes::ctrMostrarClientes('id', $v['codcliente']);

            if ($v['tipodoc'] == 1) {
                $nombreRazon = $cliente['nombre'] ?? '';
                $doc = $cliente['documento'] ?? '';
            } else {
                $nombreRazon = $cliente['razon_social'] ?? '';
                $doc = $cliente['ruc'] ?? '';
            }

            echo '<tr>
        <td>' . ++$k . '</td>
        <td>' . $v['fecha_emision'] . '</td>
        <td>' . $v['serie'] . '-' . $v['correlativo'] . '</td>
        <td>' . htmlspecialchars($nombreRazon) . '<br>' . htmlspecialchars($doc) . '</td>
        <td>' . number_format($v['subtotal'], 2) . '</td>
        <td>' . number_format($v['total'], 2) . '</td>
        <td>
          <div class="contenedor-print-comprobantes">
            <form id="printC" name="printC" method="post" action="vistas/print/printcotizacion/" target="_blank">
              <input type="hidden" id="idCo" name="idCo" value="' . $v['id'] . '">
              <button class="printA4" id="printA4" idComp="' . $v['id'] . '"></button>
            </form>
          </div>
        </td>
        </tr>';
        }
        // <td>
        //   <button class="btn btn-danger btn-xs btn-eliminar-cotizacion" idCotizacion="' . $v['id'] . '">
        //     <i class="fas fa-trash-alt"></i>
        //   </button>
        // </td>

        $reload = './index.php';
        $paginador = $this->generatePagination($reload, $page, $totalPages, $this->adjacents);
        echo "<tr>
      <td colspan='7' style='text-align:center;'>$paginador</td>
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

if (isset($_REQUEST['cotizar']) && $_REQUEST['cotizar'] == "cotizar") {
    $cotizaciones = new DataTablesCotizaciones();
    $cotizaciones->dtaListarCotizaciones();
}
