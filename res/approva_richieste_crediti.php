<?php
    session_start();
    require_once('connessione1.php');

    // Verifica che il form sia stato inviato
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'], $_POST['importo'], $_POST['action'])) {

        $email = $_POST['email'];
        $importo = $_POST['importo'];
        $action = $_POST['action'];

        if ($action=="Approva") {
            $xmlFile = '../xml/requests.xml';
            $dom = new DOMDocument();
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;

            $dom->load($xmlFile);

            $requests = $dom->getElementsByTagName('request');

            foreach ($requests as $request) {
                $statusElement = $request->getAttribute('status');

                if ($statusElement == 'In Attesa') {
                    $emailElement = $request->getElementsByTagName('email')->item(0);
                    $importoElement = $request->getElementsByTagName('importo')->item(0);

                    $requestEmail = $emailElement->nodeValue;
                    $requestImporto = $importoElement->nodeValue;

                    if ($requestEmail == $email && $requestImporto == $importo) {
                        // Aggiorna lo stato della richiesta nel file XML
                        $request->setAttribute('status', 'Approvata');

                        $dom->save($xmlFile);

                        // Aggiorna i crediti dell'utente nel database
                        $sql_credit_update = "UPDATE utenti SET crediti = crediti + $importo WHERE email = '$email'";
                        if ($connessione->query($sql_credit_update) === TRUE) {
                            $_SESSION['successo_richiesta_approvata'] = 'true';
                            $_SESSION['email'] = $email;
                            header("Location: ../php/menu_richieste_crediti.php");
                        } 
                        else {
                            $_SESSION['fallimento_richiesta'] = 'true';
                            header("Location: ../php/menu_richieste_crediti.php");
                        }

                        $connessione->close();
                        exit();
                    }
                }
            } 
        }
        elseif ($action=="Rifiuta") {

            $xmlFile = '../xml/requests.xml';
            $dom = new DOMDocument();
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            
            $dom->load($xmlFile);

            $requests = $dom->getElementsByTagName('request');

            foreach ($requests as $request) {
                $statusElement = $request->getAttribute('status');

                if ($statusElement == 'In Attesa') {
                    $emailElement = $request->getElementsByTagName('email')->item(0);
                    $importoElement = $request->getElementsByTagName('importo')->item(0);

                    $requestEmail = $emailElement->nodeValue;
                    $requestImporto = $importoElement->nodeValue;

                    if ($requestEmail == $email && $requestImporto == $importo) {
                        // Aggiorna lo stato della richiesta nel file XML a 'Rifiutata'
                        $request->setAttribute('status', 'Rifiutata');

                        $dom->save($xmlFile);

                        $_SESSION['successo_richiesta_rifiutata'] = 'true';
                        header("Location: ../php/menu_richieste_crediti.php");
                        $connessione->close();
                        exit();
                    }
                }
            }
        }
        else {
            echo 'Errore: richiesta non trovata...';
        }
    }
?>
