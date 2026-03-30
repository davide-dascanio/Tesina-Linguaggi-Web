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
        <title>Gestione Richieste Crediti</title>
        <link rel="stylesheet" href="../css/style_standard.css">
        <link rel="stylesheet" href="../css/style_header.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    </head>
    <body>
        <?php
            require_once('../res/header.php');
        ?>
        <div class="contenitore">
            <?php
                // Visualizza i messaggi di successo o errore
                if (isset($_SESSION['successo_richiesta_approvata']) && $_SESSION['successo_richiesta_approvata'] == 'true') {
                    echo '<h2 id="successo">Richiesta approvata con successo... I crediti sono stati aggiunti all\'account di "' . $_SESSION['email'] . '"</h2>';
                    unset($_SESSION['successo_richiesta_approvata']);
                }
                if (isset($_SESSION['successo_richiesta_rifiutata']) && $_SESSION['successo_richiesta_rifiutata'] == 'true') {
                    echo '<h2 id="successo">Richiesta rifiutata con successo!!!</h2>';
                    unset($_SESSION['successo_richiesta_rifiutata']);
                }
                if (isset($_SESSION['fallimento_richiesta']) && $_SESSION['fallimento_richiesta'] == 'true') {
                    echo '<h2>Errore nell\'aggiornamento dei crediti dell\'utente nel database: ' . $connessione->error . '</h2>';
                    unset($_SESSION['fallimento_richiesta']);
                }


                // Carica il file XML
                $xmlFile = '../xml/requests.xml';
                $dom = new DOMDocument();
                $dom->preserveWhiteSpace = false;
                $dom->formatOutput = true;
                $dom->load($xmlFile);

                $requests = $dom->getElementsByTagName('request');

                // Controlla se esistono richieste in attesa
                $hasPendingRequests = false;
                
                foreach ($requests as $request) {
                    if ($request->getAttribute('status') == 'In Attesa') {
                        $hasPendingRequests = true;
                        break; // inutile continuare, basta trovarne una
                    }
                }


                if ($hasPendingRequests) {

                    //C'è almeno una richiesta da mostrare che è "In Attesa"

                    echo '<h1 class="titolo">Richieste di Ricarica Crediti</h1>';

                    echo '<table>';
                    echo '<thead>';
                    echo '<tr>';
                    echo '<th>Email</th>';
                    echo '<th>Importo</th>';
                    echo '<th>Azione</th>';
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';

                    foreach ($requests as $request) {
                        $status = $request->getAttribute('status');

                        if ($status == 'In Attesa') {

                            $email = $request->getElementsByTagName('email')->item(0)->nodeValue;
                            $importo = $request->getElementsByTagName('importo')->item(0)->nodeValue;

                            echo '<tr>';
                            echo "<td>$email</td>";
                            echo "<td>$importo</td>";
                            echo '<td>';
                            echo '<form action="../res/approva_richieste_crediti.php" method="post">';
                            echo "<input type='hidden' name='email' value='$email'>";
                            echo "<input type='hidden' name='importo' value='$importo'>";
                            echo '<button class="done" type="submit" name="action" value="Approva"><span id="done" class="material-symbols-outlined">done</span></button>';
                            echo '<button class="done" type="submit" name="action" value="Rifiuta"><span id="done" class="material-symbols-outlined">close</span></button> ';
                            echo '</form>';
                            echo '</td>';
                            echo '</tr>';
                        }
                    }

                    echo '</tbody>';
                    echo '</table>';
                }else{
                    //Nessuna richiesta crediti "In Attesa"
                    echo '<p class="titolo">Nessuna richiesta di ricarica attualmente in sospeso</p>';
                }   
            ?>
        </div>
    </body> 
</html>