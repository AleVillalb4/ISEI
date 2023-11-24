<?php 
namespace app\controllers;
/*
Convertir errores en excepciones

Puede usar set_error_handler () y la clase ErrorException para convertir todos los errores de php en excepciones.

Lo importante a tener en cuenta al usar su propio controlador de errores es que omitirá la configuración de error_reporting y pasará todos los errores (avisos, advertencias, etc.) a su controlador de errores. Puede establecer un segundo argumento en set_error_handler () para definir qué tipos de error desea recibir, o acceder a la configuración actual usando ... = error_reporting () dentro del controlador de errores.

Suprimiendo la advertencia

Otra posibilidad es suprimir la llamada con el operador @ y luego verificar el valor de retorno de dns_get_record (). Pero desaconsejaría esto ya que los errores / advertencias se activan para ser manejados, no para ser suprimidos.


        set_error_handler(function($errno, $errstr, $errfile, $errline) {
            // error was suppressed with the @-operator
            if (0 === error_reporting()) {
                return false;
            }
            
            throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
        });
        
 */
class ErrorException extends \ErrorException {}