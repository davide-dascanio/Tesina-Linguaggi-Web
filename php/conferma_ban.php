<?php
    session_start();

    // Verifica se l'amministratore è loggato
    if (isset($_SESSION['id'])) {

        $admin = $_SESSION['ammin'];
        if ($admin == 0){
            // non siamo amministratori
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
        <title>Ban Utente</title>
        <link rel="stylesheet" href="../css/style_standard.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
        <link rel="stylesheet" href="../css/style_header.css">
    </head>
    <body>
        <?php
            require_once('../res/header.php');
        ?>

        <div class="cont">
            <h1 class="titolo">GESTIONE BAN UTENTE</h1>
            <table>
                <tr>
                    <td>
                        <form action="../res/ban.php" method="post">
                            <?php
                                if(isset($_GET['ban'], $_GET['id'])) {
                                    $banValue = $_GET['ban'];
                                    $id_utente = $_GET['id'];
                                    
                                    // Aggiungi un campo hidden per inviare il valore GET nel form
                                    echo '<input type="hidden" name="ban" value="' . $banValue . '">';
                                    echo '<input type="hidden" name="id" value="' . $id_utente . '">';

                                    if($banValue == 1) {
                                        echo '<p class="big">Attivare l\'utente?</p>';
                                        echo '<button class="done" type="submit" name="action" value="Approva"><span id="done" class="material-symbols-outlined">done</span></button>';
                                        echo '<button class="done" type="submit" name="action" value="Rifiuta"><span id="done" class="material-symbols-outlined">close</span></button>';
                                    } elseif($banValue == 0) {
                                        echo '<p class="big">Disattivare l\'utente?</p>';
                                        echo '<button class="done" type="submit" name="action" value="Approva"><span id="done" class="material-symbols-outlined">done</span></button>';
                                        echo '<button class="done" type="submit" name="action" value="Rifiuta"><span id="done" class="material-symbols-outlined">close</span></button>';
                                    } else {
                                        echo '<p>Utente non trovato...</p>';
                                    }
                                } else {
                                    echo '<p>Utente non trovato...</p>';
                                }
                            ?>
                        </form>
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>
