<?php
    session_start();

    if (!isset($_SESSION['loggato'])) {
        header("Location: login_cliente.php");
        exit();
    }

    $cliente = $_SESSION['cliente'];
    if ($cliente == 0){
        // non siamo clienti
        header("Location: accesso_negato.php");
        exit();
    }
?>


<?xml version = "1.0"?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
       "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">


<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Richiesta di Ricarica Crediti</title>
        <link rel="stylesheet" href="../css/style_standard.css">
        <link rel="stylesheet" href="../css/style_header.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    </head>
    <body>
        <?php
            require_once('../res/header.php');

            $email = $_SESSION['email'];
            $crediti = $_SESSION['crediti'];

            if(isset($_SESSION['successo_richiesta']) && $_SESSION['successo_richiesta'] == 'true'){
                echo '<h2 id="successo">Richiesta inviata con successo. Attendere l\'approvazione di un amministratore...</h2>';
                unset($_SESSION['successo_richiesta']);
            }
        ?>

        <div class="contenitore">
            <h1 class="titolo">Richiesta di Ricarica Crediti</h1>
            <table>
                <tr>
                    <th>Username:</th>
                    <td><?php echo $email; ?></td>
                </tr>
                <tr>
                    <th>Credito Residuo:</th>
                    <td><?php echo $crediti; ?></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <form class="form" action="../res/esito_richiesta_crediti.php" method="post">
                            <label>Importo richiesto:</label>
                            <input class="input" type="number" name="importo" min="0.01" step="0.01" max="10000" required>
                            <br><br><br>
                            <input class="btn" type="submit" value="Invia richiesta">
                        </form>
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>