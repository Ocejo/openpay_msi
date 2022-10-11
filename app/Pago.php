<?php
require_once '../vendor/autoload.php';
use GuzzleHttp\Client as Guzzle;
use Openpay\Data\Openpay as Openpay;
use Openpay\Resources\OpenpayErrors;
header('Content-Type: application/json');
$data     = json_decode(file_get_contents('php://input'), true);
$products = '';
for ($i=0; $i < count($data['products']); $i++) {
    $cont = ($i + 1 < count($data['products'])) ? '||' : '';
    $products .= $data['products'][$i]['name'] . $cont;
}
try {
    $openpay  = Openpay::getInstance('mmey69zmnv0ub7zhny23', 'sk_1dc6b7d82dd24b8bac53bfaad6f9ec70');
    $customer = array(
        'name'         => $data['customer']['name'],
        'last_name'    => $data['customer']['lastName'],
        'phone_number' => $data['customer']['phone'],
        'email'        => $data['customer']['email']
    );
    $payments      = array('payments' => $data['plan']);
    $url           = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://'.$_SERVER['HTTP_HOST'].'/openpay_msi/success.html';
    $security      = array('use_3d_secure' => $data['security'], 'redirect_url' => $url);
    
    $chargeRequest = array(
        'method'            => 'card',
        'source_id'         => $data['token'],
        'amount'            => $data['mount'],
        'currency'          => 'MXN',
        'description'       => 'Producto:'.$products,
        'device_session_id' => $data['device_session_id'],
        'payment_plan'      => $payments,
        'customer'          => $customer,
        'use_3d_secure'     => $security['use_3d_secure'],
        'redirect_url'      => $security['redirect_url']
    );
    $charge = $openpay->charges->create($chargeRequest);
    if ($charge->status == 'completed') {
        echo json_encode(['success' => true, 'message' => 'Pago completado', 'url' => $url . '?id=' . $charge->id]);
    } else if ($charge->status == 'charge_pending') {
        echo json_encode(['success' => true, 'message' => 'Te dirigiremos para que ingreses tu clave', 'url' => $charge->payment_method->url]);
    } else {
        echo json_encode(['success' => false, 'message' => $charge->description]);
    }
} catch (\Throwable$th) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => OpenpayErrors::getErrors()[$th->getCode()],
        'code'    => 400
    ]);
    sendResponse(OpenpayErrors::getErrors()[$th->getCode()]);
}

function sendResponse($error)
{
    $data     = json_decode(file_get_contents('php://input'), true);
    try {
        $client   = new Guzzle();
        $fecha    = date("Y-m-d");
        $response = $client->request('POST', 'https://hook.integromat.com/xbudrcblq0rdjkstn69umr0jz5nfne0k', [
            'form_params' => [
                'nombre'        => $data['customer']['name'],
                'correo'        => $data['customer']['email'],
                'telefono'      => $data['customer']['phone'],
                "fecha_pago"    => $fecha,
                "monto"         => $data['mount'],
                "referido"      => null,
                "plan"          => $data['plan'],
                "type"          => 'form-msi',
                "type-card"     => '',
                "digitos"       => '',
                "error"         => true,
                "error_message" => $error,
                "productos"     => $data['products']
            ]
        ]);
    } catch (\Throwable$th) {
        throw $th;
    }
}
