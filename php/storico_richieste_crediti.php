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
        <title>Storico Richieste Crediti</title>
        <link rel="stylesheet" href="../css/style_standard.css">
        <link rel="stylesheet" href="../css/style_header.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    </head>
    <body>
        <?php
            require_once('../res/header.php');

            $id_utente_sessione = $_SESSION['id'];
            $xmlFile = '../xml/requests.xml';
            $dom = new DOMDocument();
            $dom->load($xmlFile);

            $requests = $dom->getElementsByTagName('request');

            // Verifica se c'è storico richieste crediti per il cliente
            $richiesteCrediti = [];
            foreach ($requests as $request) {
                $id_utente = $request->getAttribute('id_utente');

                if ($id_utente == $id_utente_sessione) {
                    $richiesteCrediti[] = $request;
                }
            }

            echo '<div class="contenitore">';

            if (count($richiesteCrediti) > 0) { 
                echo '<h1 class="titolo">Storico Richieste Crediti</h1>';

                // Inizio della tabella
                echo '<table border="1">';
                echo '<tr>';
                echo '<th>Importo</th>';
                echo '<th>Stato</th>';
                echo '</tr>';

                // Loop attraverso le richieste
                foreach ($richiesteCrediti as $request) {
                    $id_utente = $request->getAttribute('id_utente');
                    $importo = $request->getElementsByTagName('importo')->item(0)->nodeValue;
                    $status = $request->getAttribute('status');

                    // Stampa le informazioni della richiesta all'interno di una riga della tabella
                    echo '<tr>';
                    echo "<td>$importo</td>";
                    echo "<td>$status</td>";
                    echo '</tr>';
                }

                // Chiusura della tabella
                echo '</table>';
                echo '</div>';
            }else{
                echo '<p class="titolo">Nessuna richiesta di ricarica effettuata</p>';
                echo '</div>';
            }
        ?>
    </body>
</html>
