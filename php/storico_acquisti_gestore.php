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
        <title>Storico Acquisti Utenti</title>
        <link rel="stylesheet" href="../css/style_standard.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
        <link rel="stylesheet" href="../css/style_header.css">
    </head>
    <body>
        <?php
            require_once('../res/header.php');
        ?>

        <?php
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'],$_POST['email'])) {

                $userId = $_POST['id'];
                $email = $_POST['email'];

                $xmlFile = '../xml/storico_acquisti.xml';
                $dom = new DOMDocument();
                $dom->preserveWhiteSpace = false;
                $dom->formatOutput = true;

                $dom->load($xmlFile);
                
                $acquisti = $dom->getElementsByTagName('acquisto');

                // Verifica se ci sono acquisti per il cliente specificato
                $acquistiCliente = [];
                foreach ($acquisti as $acquisto) {
                    $idUtente = $acquisto->getAttribute('id_utente');

                    if ($idUtente == $userId) {
                        $acquistiCliente[] = $acquisto;
                    }
                }

                echo '<div class="cont">';
                echo '<a class="go-back" href="../php/gestione_utenti_gestore.php">
                <span class="material-symbols-outlined" style="vertical-align:middle;">arrow_back</span>
                Torna alla gestione degli utenti </a>';

                if (count($acquistiCliente) > 0) { 
                    echo '<h1 class="titolo">Storico acquisti del cliente: ' . $email . '</h1>';
                    echo '<table border="1">';
                    echo '<tr>';
                    echo '<th>Nome Prodotto</th>';
                    echo '<th>Prezzo Base</th>';
                    echo '<th>Prezzo Finale</th>';
                    echo '<th>Quantità</th>';
                    echo '<th>Prezzo Totale</th>';
                    echo '<th>Bonus Crediti</th>';
                    echo '<th>Data Acquisto</th>';
                    echo '<th>Ora Acquisto</th>';
                    echo '</tr>';

                    foreach ($acquistiCliente as $acquisto) {
                        $nome = $acquisto->getElementsByTagName('nome_prodotto')->item(0)->nodeValue;
                        $prezzoBase = $acquisto->getElementsByTagName('prezzo_unitario')->item(0)->nodeValue;
                        $prezzoFinale = $acquisto->getElementsByTagName('prezzo_scontato')->item(0)->nodeValue;
                        $quantita = $acquisto->getElementsByTagName('quantita')->item(0)->nodeValue;
                        $prezzoTotale = $acquisto->getElementsByTagName('prezzo_totale')->item(0)->nodeValue;
                        $bonus = $acquisto->getElementsByTagName('bonus')->item(0)->nodeValue;
                        $data = $acquisto->getElementsByTagName('data')->item(0)->nodeValue;
                        $ora = $acquisto->getElementsByTagName('ora')->item(0)->nodeValue;

                        echo '<tr>';
                        echo '<td>' . $nome . '</td>';
                        echo '<td>' . $prezzoBase . '€</td>';

                        if ($prezzoFinale != $prezzoBase) {
                            echo '<td>' . $prezzoFinale . '€</td>';
                        } else {
                            echo '<td>-</td>';
                        }

                        echo '<td>' . $quantita . '</td>';
                        echo '<td>' . $prezzoTotale . '€</td>';
                        echo '<td>' . $bonus . '€</td>';
                        echo '<td>' . $data . '</td>';
                        echo '<td>' . $ora . '</td>';
                        echo '</tr>';
                    }

                    echo '</table>';
                    echo '</div>';
                } else {
                    echo '<h2 class="titolo">Nessun acquisto presente per il cliente: ' . $email . '</h2>';
                }
            } else {
                echo 'ID utente non specificato.';
            }
        ?>
    </body>
</html>