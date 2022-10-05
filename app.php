<?php
require_once 'vendor/autoload.php';
use Openpay\Data\Openpay as Openpay;
header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);
try {
    $openpay  = Openpay::getInstance('mmey69zmnv0ub7zhny23', 'sk_1dc6b7d82dd24b8bac53bfaad6f9ec70');
    $customer = array(
        'name'         => $data['customer']['name'],
        'last_name'    => $data['customer']['lastName'],
        'phone_number' => $data['customer']['phone'],
        'email'        => $data['customer']['email']
    );
    $payments      = array('payments' => $data['plan']);
    $security      = array('use_3d_secure' => $data['security'], 'redirect_url' => 'http://localhost:8888/openpay_msi/success.html');
    $chargeRequest = array(
        'method'            => 'card',
        'source_id'         => $data['token'],
        'amount'            => $data['mount'],
        'currency'          => 'MXN',
        'description'       => 'Cargo inicial a mi merchant',
        'device_session_id' => $data['device_session_id'],
        'payment_plan'      => $payments,
        'customer'          => $customer,
        'use_3d_secure'     => $data['security'],
        'redirect_url'      => 'http://localhost:8888/openpay_msi/success.html'
    );
    $charge = $openpay->charges->create($chargeRequest);
    if ($charge->status == 'completed') {
        echo json_encode(['success' => true, 'message' => 'Pago completado', 'url' => '']);
    } else if ($charge->status == 'charge_pending') {
        echo json_encode(['success' => true, 'message' => 'Te dirigiremos para que ingreses tu clave', 'url' => $charge->payment_method->url]);
    } else {
        echo json_encode(['success' => false, 'message' => $charge->description]);
    }
} catch (\Throwable$th) {
    echo '<pre>';
    die(var_dump($th));
    foreach (json_decode($th) as $key => $value) {
        echo '<pre>';
        die(var_dump($value));
    }
    // echo json_encode(['success' => false, 'message' => $th]);
}
