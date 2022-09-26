<?php
require_once 'vendor/autoload.php';
use Openpay\Data\Openpay as Openpay;
header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);
try {
    $openpay  = Openpay::getInstance('mmey69zmnv0ub7zhny23', 'sk_1dc6b7d82dd24b8bac53bfaad6f9ec70');
    $customer = array(
        'name'         => 'Juan',
        'last_name'    => 'Vazquez Juarez',
        'phone_number' => '4423456723',
        'email'        => 'juan.vazquez@empresa.com.mx'
    );
    $payments = array('payments' => 6);
    $chargeRequest = array(
        'method'            => 'card',
        'source_id'         => $data['token'],
        'amount'            => 3500,
        'currency'          => 'MXN',
        'description'       => 'Cargo inicial a mi merchant',
        'device_session_id' => $data['device_session_id'],
        'payment_plan'      => $payments,
        'customer'          => $customer);

    $charge = $openpay->charges->create($chargeRequest);
    if ($charge->status=='completed') {
        echo json_encode(['success' => true, 'message' => 'Pago completado']);
    }else{
        echo json_encode(['success' => false, 'message' => $charge->description]);
    }
    var_dump($charge->status);
} catch (\Throwable$th) {
    echo '<pre>';
    die(var_dump($th));
}
