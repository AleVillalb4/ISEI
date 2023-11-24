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
    <?=$head ?>
</head>

<body class="bg">
    <div class="col_12 header-bar">
        <div class="row">
            <img src="<?=$ruta?>img/bg/LogoISEI.png" id="logo" alt="ISEI 40-30">
        </div>
    </div>
    <div class="row">
        <div class="section col_12 flex-center">
            <div class="col_4">
                <form action="" class="col_12 form" method="post">
                    <header class="col_12">
                        <h1 class="form-h1">Logueo de usuario</h1>
                    </header>
                    <div class="col_12 form-group">
                        <input type="text" name="email" id="email" class="col_12 form-input <?= strlen($error_msg['email']) > 0 ? 'form-error-border' : '' ?>" autocomplete="off" placeholder="E-mail" value="<?= $datos['email'] ?>">
                        <label for="email" class="form-label">E-mail</label>
                        <div class="col_12 form-error-msg <?= strlen($error_msg['email']) > 0 ? 'form-errror-show' : '' ?>">
                            <img src="<?=$ruta?>img/icons/error.svg" alt="error"><?= $error_msg['email'] ?>
                        </div>
                    </div>
                    <div class="col_12 form-group">
                        <input type="password" name="pass" id="pass" class="col_12 form-input <?= strlen($error_msg['pass']) > 0 ? 'form-error-border' : '' ?>" autocomplete="off" placeholder="Contraseña" value="<?= $datos['pass'] ?>">
                        <label for="pass" class="form-label">Contraseña</label>
                        <div class="col_12 form-error-msg <?= strlen($error_msg['pass']) > 0 ? 'form-errror-show' : '' ?>">
                            <img src="<?=$ruta?>img/icons/error.svg" alt="error"><?= $error_msg['pass'] ?>
                        </div>
                    </div>
                    <div class="col_100"></div>
                    <div class="col_12 flex-center">
                        <button type="submit" name="send" class="form-btn"> Ingresar </button>
                    </div>
                    <div class="col_12 flex-center">

                    </div>
                    <?php
                    // Verificar si hay un mensaje de error y mostrarlo si es necesario
                    if (isset($_SESSION['login_error'])) {
                        echo '<p style="color: red;">' . $_SESSION['login_error'] . '</p>';
                        unset($_SESSION['login_error']); // Limpiar el mensaje de error
                    }
                    ?>
                </form>
            </div>
        </div>
    </div>
</body>

</html>