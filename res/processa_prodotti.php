<?php
    session_start();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['autore'], $_POST['domanda'])) {
        $id_prodotto = $_POST['id_prodotto'];
        $autore = $_POST['autore'];
        $domanda = $_POST['domanda'];
        $tipologia = $_POST['tipologia'];
        $nome = $_POST['nome'];
        $id_utente = $_SESSION['id'];
        $id_domanda = uniqid();
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
            // Crea o trova l'elemento 'domande'
            $domandeNode = $prodottoNode->getElementsByTagName('domande')->item(0);
            if (!$domandeNode) {
                $domandeNode = $dom->createElement('domande');
                $prodottoNode->appendChild($domandeNode);
            }

            // Crea l'elemento 'domanda'
            $domandaNode = $dom->createElement('domanda');
            $domandaNode->setAttribute('id_prodotto', $id_prodotto);
            $domandaNode->setAttribute('id_utente', $id_utente);
            $domandaNode->setAttribute('segnalato', $segnalato);

            // Aggiungi gli elementi 'autore', 'testo' e 'data e ora' all'elemento 'domanda'
            $autoreNode = $dom->createElement('autore', $autore);
            $testoNode = $dom->createElement('testo', $domanda);
            $idDomandaNode = $dom->createElement('id_domanda', $id_domanda);
            $dataNode = $dom->createElement('data', date('Y-m-d'));
            $oraNode = $dom->createElement('ora', date('H:i:s'));
            $utilitaNode = $dom->createElement('utilita');
            $supportoNode = $dom->createElement('supporto');
            $risposteNode = $dom->createElement('risposte');



            $domandaNode->appendChild($autoreNode);
            $domandaNode->appendChild($testoNode);
            $domandaNode->appendChild($idDomandaNode);
            $domandaNode->appendChild($dataNode);
            $domandaNode->appendChild($oraNode);
            $domandaNode->appendChild($utilitaNode);
            $domandaNode->appendChild($supportoNode);
            $domandaNode->appendChild($risposteNode);
            

            // Aggiungi l'elemento 'domanda' all'elemento 'domande'
            $domandeNode->appendChild($domandaNode);


            // Salva il file XML aggiornato
            $dom->save($xmlFile);

            $_SESSION['creazione_domanda'] = 'true';
            header("Location: ../php/lista_domande.php?id_prodotto=$id_prodotto&nome=$nome&tipologia=$tipologia");
        } else {
            echo 'Prodotto non trovato.';
        }
    }
?>

