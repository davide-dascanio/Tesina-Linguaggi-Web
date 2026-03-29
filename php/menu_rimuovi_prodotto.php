<?php
    session_start();

    // Verifica se il gestore è loggato
    if (isset($_SESSION['id'])) {

        $gestore = $_SESSION['gestore'];
        if ($gestore == 0){
            // non siamo gestori
            header("Location: accesso_negato.php");
            exit();
        }
    }else{
        // Reindirizza alla pagina di accesso se non è loggato
        header("Location: login_cliente.php");
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
        <title>Rimuovi Prodotto</title>
        <link rel="stylesheet" href="../css/style_standard.css">
        <link rel="stylesheet" href="../css/style_header.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    </head>
    <body>
        <?php
            require_once('../res/header.php');
        ?>
        <?php
            if(isset($_SESSION['successo_rimozione_prodotto']) && $_SESSION['successo_rimozione_prodotto'] == 'true'){
                echo '<h2 id="successo">Prodotto rimosso con successo!!!</h2>';
                unset($_SESSION['successo_rimozione_prodotto']);
            }
            if(isset($_SESSION['fallimento_rimozione_prodotto']) && $_SESSION['fallimento_rimozione_prodotto'] == 'true'){
                echo '<h2>Prodotto inesistente, controlla che il nome del prodotto inserito sia nel catalogo...</h2>';
                unset($_SESSION['fallimento_rimozione_prodotto']);
            }
        ?>
        <div class="cont">
            <h1 class="titolo">Rimuovi Prodotto</h1>
            <table>
                <tr>
                    <td>
                        <form class="form" action="../res/rimuovi_prodotto.php" method="post">
                            <label>Inserire il Nome del Prodotto:</label>
                            <input style="width: 300px" class="input" type="text" name="nome" required>
                            <br><br><br>
                            <input class="btn" type="submit" value="Rimuovi Prodotto">
                        </form>
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>