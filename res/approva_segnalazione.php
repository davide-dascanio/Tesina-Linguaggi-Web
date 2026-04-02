<?php
    session_start();

    // Verifica che il form sia stato inviato
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // Verifica che tutti i campi necessari siano stati compilati
        if (isset($_POST['id_contributo'], $_POST['id_prodotto'], $_POST['action'], $_POST['autore_segnalazione'])) {
            
            $idContributo = $_POST['id_contributo'];
            $autoreSegnalazione = $_POST['autore_segnalazione'];
            $idProdotto = $_POST['id_prodotto'];
            $action = $_POST['action'];

            // Carica il file XML segnalazioni.xml
            $xmlFile = '../xml/segnalazioni.xml';
            $dom = new DOMDocument();
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->load($xmlFile);

            $segnalazioni = $dom->getElementsByTagName('segnalazione');
            $segnalazioneTarget = null;

            foreach ($segnalazioni as $segnalazione) {
                if ($segnalazione->getAttribute('id_contributo') == $idContributo &&
                    $segnalazione->getAttribute('autore_segnalazione') == $autoreSegnalazione) { //per evitare che una segnalazione rimanga appesa in attesa
                    $segnalazioneTarget = $segnalazione;
                    break;
                }
            }
            
            // Segnalazione non trovata
            if (!$segnalazioneTarget) {
                header("Location: ../php/menu_segnalazioni.php");
                exit();
            }

            $statusAttuale = $segnalazioneTarget->getAttribute('status');

            // Già approvata in precedenza -> il contributo è già stato rimosso (segnalato=1)
            if ($statusAttuale == 'Approvata') {
                $_SESSION['contributo_gia_rimosso'] = 'true';
                header("Location: ../php/menu_segnalazioni.php");
                exit();
            }

            // action == 'Rifiuta'
            if ($action == 'Rifiuta') {
                $segnalazioneTarget->setAttribute('status', 'Rifiutata');
                $dom->save($xmlFile);

                $_SESSION['segnalazione_rifiutata'] = 'true';
                header("Location: ../php/menu_segnalazioni.php");
                exit();
            }

            // action == 'Approva'
            $segnalazioneTarget->setAttribute('status', 'Approvata');
            $dom->save($xmlFile);

            // Cerca il contributo nel catalogo e metti segnalato=1
            $xmlFileCatalogo = '../xml/catalogo_prodotti.xml';
            $domCat = new DOMDocument();
            $domCat->preserveWhiteSpace = false;
            $domCat->formatOutput = true;
            $domCat->load($xmlFileCatalogo);
            $xpath = new DOMXPath($domCat);


            // Cerca tra domande, risposte e recensioni con quell'id

            // Prova tra le domande del prodotto
            $domande = $xpath->query("//domande/domanda[@id_prodotto='$idProdotto']");
            foreach ($domande as $domanda) {
                $idDom = $domanda->getElementsByTagName('id_domanda')->item(0)->nodeValue;
                if ($idDom == $idContributo) {
                    $domanda->setAttribute('segnalato', '1');
                    $domCat->save($xmlFileCatalogo);
                    $_SESSION['segnalazione_approvata'] = 'true';
                    header("Location: ../php/menu_segnalazioni.php");
                    exit();
                }
            }


            // Prova tra le risposte del prodotto
            $risposte = $xpath->query("//risposte/risposta[@id_prodotto='$idProdotto']");
            foreach ($risposte as $risposta) {
                $idRisp = $risposta->getElementsByTagName('id_risposta')->item(0)->nodeValue;
                if ($idRisp == $idContributo) {
                    $risposta->setAttribute('segnalato', '1');
                    $domCat->save($xmlFileCatalogo);
                    $_SESSION['segnalazione_approvata'] = 'true';
                    header("Location: ../php/menu_segnalazioni.php");
                    exit();
                }
            }


            // Prova tra le recensioni del prodotto
            $recensioni = $xpath->query("//recensioni/recensione[@id_prodotto='$idProdotto']");
            foreach ($recensioni as $recensione) {
                $idRec = $recensione->getElementsByTagName('id_recensione')->item(0)->nodeValue;
                if ($idRec == $idContributo) {
                    $recensione->setAttribute('segnalato', '1');
                    $domCat->save($xmlFileCatalogo);
                    $_SESSION['segnalazione_approvata'] = 'true';
                    header("Location: ../php/menu_segnalazioni.php");
                    exit();
                }
            }
        }
    }
?>
