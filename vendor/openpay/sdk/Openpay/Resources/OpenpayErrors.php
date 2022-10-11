<?php

namespace Openpay\Resources;

class OpenpayErrors
{
    public static function getErrors()
    {
        return array(
            1000 => 'Ocurrió un error interno en el servidor de Openpay',
            1001 => 'Esta orden ha sido procesada.',
            1002 => 'La llamada no esta autenticada o la autenticación es incorrecta.',
            1003 => 'La operación no se pudo completar por que el valor de uno o más de los parámetros no es correcto.',
            1004 => 'Un servicio necesario para el procesamiento de la transacción no se encuentra disponible.',
            1005 => 'Uno de los recursos requeridos no existe.',
            1006 => 'Esta orden ha sido pagada',
            1007 => 'La transferencia de fondos entre una cuenta de banco o tarjeta y la cuenta de Openpay no fue aceptada.',
            1008 => 'Una de las cuentas requeridas en la petición se encuentra desactivada.',
            1009 => 'El cuerpo de la petición es demasiado grande.',
            1010 => 'Se esta utilizando la llave pública para hacer una llamada que requiere la llave privada, o bien, se esta usando la llave privada desde JavaScript.',
            1011 => 'Se solicita un recurso que esta marcado como eliminado.',
            1012 => 'El monto transacción esta fuera de los limites permitidos.',
            1013 => 'La operación no esta permitida para el recurso.',
            1014 => 'La cuenta esta inactiva.',
            1015 => 'No se ha obtenido respuesta de la solicitud realizada al servicio.',
            1016 => 'El mail del comercio ya ha sido procesada.',
            1017 => 'El gateway no se encuentra disponible en ese momento.',
            1018 => 'El número de intentos de cargo es mayor al permitido.',
            1020 => 'El número de dígitos decimales es inválido para esta moneda',
            1023 => 'Se han terminado las transacciones incluidas en tu paquete. Para contratar otro paquete contacta a soporte@openpay.mx.',
            1024 => 'El monto de la transacción excede su límite de transacciones permitido por TPV',
            1025 => 'Se han bloqueado las transacciones CoDi contratadas en tu plan',
            2004 => 'El número de tarjeta es invalido.',
            2005 => 'La fecha de expiración de la tarjeta es anterior a la fecha actual.',
            2006 => 'El código de seguridad de la tarjeta (CVV2) no fue proporcionado.',
            2007 => 'El número de tarjeta es de prueba, solamente puede usarse en Sandbox.',
            2008 => 'La tarjeta no es valida para pago con puntos.',
            2009 => 'El código de seguridad de la tarjeta (CVV2) es inválido.',
            2010 => 'Autenticación 3D Secure fallida.',
            2011 => 'Tipo de tarjeta no soportada.',
            3001 => 'La tarjeta fue declinada.',
            3002 => 'La tarjeta ha expirado.',
            3003 => 'Tarjeta declinada',
            3004 => 'Tarjeta declinada',
            3005 => 'Tarjeta declinada',
            3006 => 'La operación no está permitida para este cliente o esta transacción.',
            3007 => 'La tarjeta fue declinada.',
            3008 => 'La tarjeta no es soportada en transacciones en línea.',
            3009 => 'La tarjeta fue reportada como perdida.',
            3010 => 'El banco ha restringido la tarjeta.',
            3011 => 'El banco ha solicitado que la tarjeta sea retenida. Contacte al banco.',
            3012 => 'Se requiere solicitar al banco autorización para realizar este pago.',
            3201 => 'Comercio no autorizado para procesar pago a meses sin intereses.',
            3203 => 'Promoción no valida para este tipo de tarjetas.',
            3204 => 'El monto de la transacción es menor al mínimo permitido para la promoción.',
            3205 => 'Promoción no permitida.',
            4001 => 'La cuenta de Openpay no tiene fondos suficientes.',
            4002 => 'La operación no puede ser completada hasta que sean pagadas las comisiones pendientes.',
        );
    }
}
