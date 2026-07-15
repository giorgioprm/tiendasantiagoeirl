<?php
if (isset($_POST['tipo_cambio'])) {
    // API alternativa sin token
    $fecha = $_POST['fecha'];

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.apis.net.pe/v2/sunat/tipo-cambio?fecha=" . $fecha,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT => 10,
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    $data = json_decode($response, true);

    if ($data && isset($data['compra']) && isset($data['venta'])) {
        echo json_encode(array(
            'compra' => floatval($data['compra']),
            'venta' => floatval($data['venta'])
        ));
    } else {
        echo json_encode(array('compra' => 3.72, 'venta' => 3.75));
    }
    exit;
}
