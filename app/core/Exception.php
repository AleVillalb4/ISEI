<?php 
namespace app\controllers;

/*
what is the difference between Exception and \Exception inside a namespace?

Still valid against PHP 7.3.5:

\Exception: Refer to Exception in root namespace
Exception: Refer to Exception in current namespace
PHP does not fall back to root namespace, if the class cannot be found in current name space, it emits an error.

 */
class Exception extends \Exception {}