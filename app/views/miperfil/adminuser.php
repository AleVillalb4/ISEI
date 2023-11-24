<?php

namespace app\controllers;

use app\controllers\UserController;
use \app\controllers\CuentaController;
use \Controller;
use \Response;
use \DataBase;

?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido</title>
    <link rel="stylesheet" href="../../../public/css/main.css">
    <?= $head ?>
</head>

<body class="bg">
    <div class="col_12 header-bar">
        <div class="row">
            <img src="<?= $ruta ?>img/bg/LogoISEI.png" id="logo" alt="ISEI 40-30">
        </div>
    </div>
    <h1> Bienvenido <?= $tipo ?> <?= $nombre ?></h1>

    <div class="panel">
        <div class="block">
            <img src="<?= $ruta ?>img/bg/agregar.png" alt="Agregar">

            <a href="<?= $ruta ?>cuenta/agregarAlumno" class="form-btn" > Agregar</a>
        </div>
        <div class="block">
            <img src="<?= $ruta ?>img/bg/modificar.png" alt="Modificar">
            <a href="#">Modificar</a>
        </div>
        <div class="block">
            <img src="<?= $ruta ?>img/bg/eliminar.png" alt="Eliminar">
            <a href="#">Eliminar</a>
        </div>
        <div class="block">
            <img src="<?= $ruta ?>img/bg/consultar.png" alt="Agregar">
            <a href="">Consultar</a>
        </div>
        <div class="block">
            <img src="<?= $ruta ?>img/bg/consultarv.png" alt="Agregar">
            <a href="">Consultar varios</a>
        </div>
    </div>