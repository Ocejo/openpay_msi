<?php
require_once '../vendor/autoload.php';
use GuzzleHttp\Client as Guzzle;
use Openpay\Data\Openpay as Openpay;
use Openpay\Resources\OpenpayErrors;
header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);
try {
    $openpay = Openpay::getInstance('mmey69zmnv0ub7zhny23', 'sk_1dc6b7d82dd24b8bac53bfaad6f9ec70');
    $charge  = $openpay->charges->get($data['id']);
    switch ($charge->status) {
        case 'failed':
            $message = json_encode([
                'success' => false,
                'message' => OpenpayErrors::getErrors()[$charge->error_code],
                'code'    => $charge->error_code
            ]);
            sendResponse($charge);
            break;
        case 'completed':
            $message = json_encode([
                'success' => true,
                'message' => "¡Gracias " . $charge->customer->name . "! Tu pago ha sido procesado correctamente por un monto de $" . number_format($charge->amount, 2) . " MXN. Para continuar verifique su correo realizando los pasos siguientes. Si usted no ve este correo por favor verifique en su bandeja de correo no deseado.",
                'code'    => 200
            ]);
            sendResponse($charge);
            break;
        default:
            $message = json_encode([
                'success' => false,
                'message' => 'Pago aun en proceso...',
                'code'    => 2811
            ]);
            break;
    }
    echo $message;
} catch (\Throwable$th) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => OpenpayErrors::getErrors()[$th->getCode()],
        'code'    => 400
    ]);
}

function sendResponse($data)
{
    $products    = explode('Producto:', $data->serializableData['description'])[1];
    $arrProducts = explode('||', $products);
    try {
        $client   = new Guzzle();
        $fecha    = date("Y-m-d");
        $response = $client->request('POST', 'https://hook.integromat.com/d146vbvksofvhvvjae5ayr6xns9o76ej', [
            'form_params' => [
                'nombre'        => $data->customer->name,
                'correo'        => $data->customer->email,
                'telefono'      => $data->customer->phone_number,
                "fecha_pago"    => $fecha,
                "monto"         => $data->fee->amount,
                "referido"      => null,
                "plan"          => $data->payment_plan->payments,
                "type"          => 'form-msi',
                "type-card"     => $data->card->type,
                "digitos"       => $data->card->card_number,
                "error"         => ($data->error_code != null) ? true : false,
                "error_message" => ($data->error_code != null) ? OpenpayErrors::getErrors()[$data->error_code] : '',
                "productos"     => $arrProducts
            ]
        ]);
    } catch (\Throwable$th) {
        throw $th;
    }
}