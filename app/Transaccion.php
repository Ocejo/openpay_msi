<?php
require_once '../vendor/autoload.php';
use Openpay\Data\Openpay as Openpay;
header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);
try {
    $openpay = Openpay::getInstance('mmey69zmnv0ub7zhny23', 'sk_1dc6b7d82dd24b8bac53bfaad6f9ec70');
    $charge  = $openpay->charges->get($data['id']);
    switch ($charge->status) {
        case 'failed':
            $message = json_encode([
                'success'  => false,
                'message'  => "Lo sentimos " . $charge->customer->name . " Tu pago no ha sido completado, por favor verifique con su banco para poder realizar el pago.",
                'code'     => $charge->error_code
            ]);
            break;
        case 'completed':
            $message = json_encode([
                'success'  => true,
                'message'  => "¡Gracias " . $charge->customer->name . "! Tu pago ha sido procesado correctamente por un monto de $" . number_format($charge->amount, 2) . " MXN. Para continuar verifique su correo realizando los pasos siguientes. Si usted no ve este correo por favor verifique en su bandeja de correo no deseado.",
                'code'     => 200
            ]);
            break;
        default:
            $message = json_encode([
                'success'  => false,
                'message'  => 'Pago aun en proceso...',
                'code'     => 2811
            ]);
            break;
    }
    echo $message;
} catch (\Throwable$th) {
    http_response_code(400);
    echo json_encode([
        'success'  => false,
        'message'  => 'Transacción Incorrecta',
        'code'     => 400
    ]);
}
