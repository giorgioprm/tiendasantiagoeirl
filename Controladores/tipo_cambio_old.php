<?php
if (isset($_POST['tipo_cambio'])) {
  $fecha = $_POST['fecha'];

  // Convertir fecha a formato dd-mm-yyyy para BCR
  $fechaBCR = date('d-m-Y', strtotime($fecha));

  $url = "https://estadisticas.bcrp.gob.pe/estadisticas/series/api/PD04637PD/json/{$fechaBCR}/{$fechaBCR}";

  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
  ));

  $response = curl_exec($curl);
  curl_close($curl);

  $data = json_decode($response, true);

  if ($data && isset($data['periods'][0]['values'][0])) {
    $tipoCambio = $data['periods'][0]['values'][0];
    echo json_encode(array(
      'compra' => $tipoCambio - 0.005,
      'venta' => floatval($tipoCambio)
    ));
  } else {
    echo json_encode(array('error' => 'No disponible'));
  }
}
