<?php
    session_start();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['autore'], $_POST['recensione'])) {
        $id_prodotto = $_POST['id_prodotto'];
        $autore = $_POST['autore'];

        // I textarea su Windows mandano le andate a capo come \r\n
        // Il DOMDocument quando salva nel XML converte il \r in &#13;
        // Rimuoviamo quindi il \r prima di assegnare il testo al nodo XML
        $recensione = str_replace("\r\n", "\n", $_POST['recensione']);
        
        $tipologia = $_POST['tipologia'];
        $nome = $_POST['nome'];
        $id_utente = $_SESSION['id'];
        $id_recensione = uniqid();
        $segnalato = 0;          

        // Carica il file XML del catalogo
        $xmlFile = '../xml/catalogo_prodotti.xml';
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->load($xmlFile);

        // Trova il prodotto nel file XML
        $xpath = new DOMXPath($dom);
        $prodottoNode = $xpath->query("//prodotto[id_prodotto=$id_prodotto]")->item(0);

        // Verifica se il nodo del prodotto esiste prima di procedere
        if ($prodottoNode) {
            // Crea o trova l'elemento 'recensioni'
            $recensioniNode = $prodottoNode->getElementsByTagName('recensioni')->item(0);
            if (!$recensioniNode) {
                $recensioniNode = $dom->createElement('recensioni');
                $prodottoNode->appendChild($recensioniNode);
            }

            // Crea l'elemento 'recensione'
            $recensioneNode = $dom->createElement('recensione');
            $recensioneNode->setAttribute('id_prodotto', $id_prodotto);
            $recensioneNode->setAttribute('id_utente', $id_utente);
            $recensioneNode->setAttribute('segnalato', $segnalato);


            // Aggiungi gli elementi 'autore', 'testo' e 'data e ora' all'elemento 'recensione'
            $autoreNode = $dom->createElement('autore', $autore);
            $testoNode = $dom->createElement('testo', $recensione);
            $dataNode = $dom->createElement('data', date('Y-m-d'));
            $oraNode = $dom->createElement('ora', date('H:i:s'));
            $idRecensioneNode = $dom->createElement('id_recensione', $id_recensione);
            $utilitaNode = $dom->createElement('utilita');
            $supportoNode = $dom->createElement('supporto');           
    
        
            $recensioneNode->appendChild($autoreNode);
            $recensioneNode->appendChild($testoNode);
            $recensioneNode->appendChild($dataNode);
            $recensioneNode->appendChild($oraNode);
            $recensioneNode->appendChild($idRecensioneNode);
            $recensioneNode->appendChild($utilitaNode);
            $recensioneNode->appendChild($supportoNode);


            // Aggiungi l'elemento 'recensione' all'elemento 'recensioni'
            $recensioniNode->appendChild($recensioneNode);


            // Salva il file XML aggiornato
            $dom->save($xmlFile);

            $_SESSION['creazione_recensione'] = 'true';
            header("Location: ../php/lista_recensioni.php?id_prodotto=$id_prodotto&nome=$nome&tipologia=$tipologia");            
        } else {
            echo 'Prodotto non trovato.';
        }
    }   
?>
