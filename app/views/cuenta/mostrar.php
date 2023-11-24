<?php
#----------------Valicaciones de campos de formularios
// session_start();

// if (isset($_SESSION['id_usuario'])) {
//     header("Location: pagina_protegida.php");
//     exit();
// }


?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario</title>
    <?= $head ?>
</head>

<body class="bg">
    <div class="col_12 header-bar">
        <div class="row">
            <img src="<?= $ruta ?>img/bg/LogoISEI.png" id="logo" alt="ISEI 40-30">
        </div>
    </div>
    <div class="row">
        <div class="section col_12 flex-center">
            <div class="col_4">
                <form action="altaAlumno" class="col_12 form" method="post">
                    <header class="col_12">
                        <h1 class="form-h1">Consultar</h1>
                    </header>
                    <div class="col_12 form-group">
                        <input type="text" name="email" class="col_12 form-input <?= strlen($error_msg['email']) > 0 ? 'form-error-border' : '' ?>" autocomplete="nope" placeholder="E-mail" value="<?= $datos['email'] ?>">
                        <label for="email" class="form-label">E-mail</label>
                        <div class="col_12 form-error-msg <?= strlen($error_msg['email']) > 0 ? 'form-errror-show' : '' ?>">
                            <img src="../img/icons/error.svg" alt="error"><?= $error_msg['email'] ?>
                        </div>
                    </div>
                    <div class="col_12 flex-center">
                        <button type="submit" name="add" class="form-btn"> consultar </button>
                    </div>
                    <div class="col_12 flex-center">
                        <?php
                        if ($error == false) {
                            echo $mensajeOk;
                            echo $mensajeNoOk;
                        }
                        ?>
                    </div>
                </form>
            </div>
        </div>
    </div>