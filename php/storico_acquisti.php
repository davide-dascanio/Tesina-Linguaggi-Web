<?php
    session_start();

    // Verifica se il cliente è loggato
    if (isset($_SESSION['id'])) {

        $cliente = $_SESSION['cliente'];
        if ($cliente == 0){
            // non siamo clienti
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
        <title>Login</title>
        <link rel="stylesheet" href="../css/style_standard.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
        <link rel="stylesheet" href="../css/style_header.css">
    </head>
    <body>
        <?php
            require_once('../res/header.php');

            $xmlPath = '../xml/storico_acquisti.xml';

            $dom = new DomDocument;
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->load($xmlPath);

            $id_utente_sessione = $_SESSION['id'];
            $contatore = 0;

            $acquisti = $dom->getElementsByTagName('acquisto');

            // Verifica se ci sono acquisti per il cliente
            $acquistiCliente = [];
            foreach ($acquisti as $acquisto) {
                $id_utente = $acquisto->getAttribute('id_utente');

                if ($id_utente == $id_utente_sessione) {
                    $acquistiCliente[] = $acquisto;
                }
            }

            echo '<div class="contenitore">';

            if (count($acquistiCliente) > 0) { 
                echo '<h1 class="titolo">Storico Acquisti</h1>';
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
                    $prezzo_base = $acquisto->getElementsByTagName('prezzo_unitario')->item(0)->nodeValue;
                    $prezzo_finale = $acquisto->getElementsByTagName('prezzo_scontato')->item(0)->nodeValue;
                    $quantita = $acquisto->getElementsByTagName('quantita')->item(0)->nodeValue;
                    $prezzo_totale = $acquisto->getElementsByTagName('prezzo_totale')->item(0)->nodeValue;
                    $bonus = $acquisto->getElementsByTagName('bonus')->item(0)->nodeValue;
                    $data_acquisto = $acquisto->getElementsByTagName('data')->item(0)->nodeValue;
                    $ora_acquisto = $acquisto->getElementsByTagName('ora')->item(0)->nodeValue;

                    echo '<tr>';
                    echo '<td>' . $nome . '</td>';
                    echo '<td>' . $prezzo_base . '€</td>';

                    if ($prezzo_finale != $prezzo_base) {
                        echo '<td>' . $prezzo_finale . '€</td>';
                    } else {
                        echo '<td>-</td>';
                    }
                
                    echo '<td>' . $quantita . '</td>';
                    echo '<td>' . $prezzo_totale . '€</td>';
                    echo '<td>' . $bonus . '€</td>';
                    echo '<td>' . $data_acquisto . '</td>';
                    echo '<td>' . $ora_acquisto . '</td>';
                    echo '</tr>';
                }

                echo '</table>';
                echo '</div>';
            }else{
                echo '<p class="titolo">Non hai effettuato acquisti per il momento...</p>';
                echo '</div>';
            }
        ?>
    </body>
</html>