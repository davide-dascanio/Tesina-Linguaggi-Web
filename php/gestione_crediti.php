<?php
    session_start();

    // Verifica se il cliente è loggato
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
        <title>Gestione Crediti</title>
        <link rel="stylesheet" href="../css/style_menu.css">
        <link rel="stylesheet" href="../css/style_header.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    </head>
    <body>
        <?php
            require_once('../res/header.php');
        ?>
        <div class="cont">
            <a class="voce" href="storico_richieste_crediti.php">Storico Richieste Crediti</a>
            <div class="sep"></div>
            <a class="voce" href="richiesta_crediti.php">Richiedi Nuovi Crediti</a>
        </div>
    </body>
</html>