<?php
require_once '../vendor/autoload.php';
use Openpay\Data\Openpay as Openpay;
header('Content-Type: application/json');
try {
    $openpay = Openpay::getInstance('mmey69zmnv0ub7zhny23', 'sk_1dc6b7d82dd24b8bac53bfaad6f9ec70');
    $charge  = $openpay->charges->get('trdckaxemmphnrld0itm');
    switch ($charge->status) {
        case 'failed':
            $message = json_encode([
                'success' => false,
                'message' => $charge->error_message,
                'code'    => $charge->error_code
            ]);
            break;
        case 'completed':
            $message = json_encode([
                'success' => true,
                'message' => 'Pago completado con ¡exito!',
                'code'    => 200
            ]);
            break;
        default:
            $message = json_encode([
                'success' => false,
                'message' => '',
                'code'    => ''
            ]);
            break;
    }
    echo $message;
} catch (\Throwable$th) {
    throw $th;
}
