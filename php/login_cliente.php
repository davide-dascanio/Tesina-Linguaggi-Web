<?php
    session_start();
?>

<?xml version = "1.0"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
        <link rel="stylesheet" href="../css/style_login.css">
        <link rel="stylesheet" href="../css/style_header.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    </head>
    <body>
        <?php
        require_once('../res/header.php');

        if (isset($_SESSION['errore_login']) && $_SESSION['errore_login'] == 'true') {
            echo '<h2>Errore in fase di login...</h2>';
            unset($_SESSION['errore_login']);
        }
        if (isset($_SESSION['registrazione_ok']) && $_SESSION['registrazione_ok'] == 'true') {
            echo '<h2 id="successo">Registrazione effettuata con successo!!!</h2>';
            unset($_SESSION['registrazione_ok']);
        }
        ?>
        <div class="wrapper">
            <form action="../res/cliente_login.php" class="form" method="post">
                <h1 class="titolo">LOGIN</h1>
                <table>
                    <tr>
                        <td class="inp">
                            <i class="fas fa-user"></i>
                            <input type="text" name="email" class="input" placeholder="Email">
                        </td>
                    </tr>
                    <tr>
                        <td class="inp">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="password" class="input" placeholder="Password">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <button class="submit" type="submit">Inizia la sessione</button>
                            <p class="footer">Non hai un account? <a href="registrazione_cliente.php" class="link">Per favore, Registrati</a></p>
                        </td>
                    </tr>
                </table>
            </form>
            <div class="banner">
                <table>
                    <tr>
                        <td>
                            <h1 class="wel_text">Benvenuto</h1>
                            <img src="../img/logo.PNG">
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </body>
</html>
