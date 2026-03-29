<?php
    session_start();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['autore'], $_POST['risposta'], $_POST['id_domanda'])) {
        $id_prodotto = $_POST['id_prodotto'];
        $autore = $_POST['autore'];

        // I textarea su Windows mandano le andate a capo come \r\n
        // Il DOMDocument quando salva nel XML converte il \r in &#13;
        // Rimuoviamo quindi il \r prima di assegnare il testo al nodo XML
        $risposta = str_replace("\r\n", "\n", $_POST['risposta']);
        
        $id_domanda = $_POST['id_domanda'];
        $tipologia = $_POST['tipologia'];
        $nome = $_POST['nome'];
        $id_utente = $_SESSION['id'];
        $id_risposta = uniqid();
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
            // Trova la domanda nel file XML
            $domandaNode = $xpath->query("//domande/domanda[id_domanda='$id_domanda']")->item(0);

            // Verifica se il nodo della domanda esiste prima di procedere
            if ($domandaNode) {
                // Crea l'elemento 'risposta'
                $rispostaNode = $dom->createElement('risposta');

                $rispostaNode->setAttribute('id_prodotto', $id_prodotto);
                $rispostaNode->setAttribute('id_utente', $id_utente);
                $rispostaNode->setAttribute('segnalato', $segnalato);

                // Crea gli elementi 'id_risposta', 'autore', 'data', 'ora', 'testo'
                $idRispostaNode = $dom->createElement('id_risposta', $id_risposta);
                $idDomandaNode = $dom->createElement('id_domanda', $id_domanda);

                $autoreRispostaNode = $dom->createElement('autore', $autore);
                $dataRispostaNode = $dom->createElement('data', date('Y-m-d'));
                $oraRispostaNode = $dom->createElement('ora', date('H:i:s'));
                $testoRispostaNode = $dom->createElement('testo', $risposta);

                $utilitaRispostaNode = $dom->createElement('utilita');
                $supportoRispostaNode = $dom->createElement('supporto');

                // Aggiungi gli elementi all'elemento 'risposta'
                $rispostaNode->appendChild($idRispostaNode);
                $rispostaNode->appendChild($idDomandaNode);
                $rispostaNode->appendChild($autoreRispostaNode);
                $rispostaNode->appendChild($dataRispostaNode);
                $rispostaNode->appendChild($oraRispostaNode);
                $rispostaNode->appendChild($testoRispostaNode);
                $rispostaNode->appendChild($utilitaRispostaNode);
                $rispostaNode->appendChild($supportoRispostaNode);


                // Trova o crea l'elemento 'risposte' all'interno della domanda
                $risposteNode = $domandaNode->getElementsByTagName('risposte')->item(0);
                if (!$risposteNode) {
                    $risposteNode = $dom->createElement('risposte');
                    $domandaNode->appendChild($risposteNode);
                }

                // Aggiungi l'elemento 'risposta' alle risposte della domanda
                $risposteNode->appendChild($rispostaNode);

                // Salva il file XML aggiornato
                $dom->save($xmlFile);

                $_SESSION['creazione_risposta'] = 'true';
                header("Location: ../php/lista_domande.php?id_prodotto=$id_prodotto&nome=$nome&tipologia=$tipologia");
            } else {
                echo 'Domanda non trovata.';
            }
        }else{
            echo 'Prodotto non trovato.';
        }
    }
?>