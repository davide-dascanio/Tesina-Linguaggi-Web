<?php
    session_start();
    require_once('connessione1.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vota'], $_POST['id_recensione'], $_POST['votoUtilita'], $_POST['votoSupporto'])) {

        $xmlFile = '../xml/catalogo_prodotti.xml';

        // Carica il file XML
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->load($xmlFile);

        $id_recensione = $_POST['id_recensione'];
        $votoUtilita = $_POST['votoUtilita'];
        $votoSupporto = $_POST['votoSupporto'];
        $id_prodotto = $_POST['id_prodotto'];
        $tipologia = $_POST['tipologia'];
        $nome = $_POST['nome'];
        $id_utente = $_SESSION['id'];

        // Trova la recensione con l'id_recensione specificato nel file XML
        $xpath = new DOMXPath($dom);
        $query = "//recensione[id_recensione='$id_recensione']";
        $recensioneNode = $xpath->query($query)->item(0);

        // Verifica se la recensione esiste prima di procedere
        if ($recensioneNode) {
            $id_utente_recensione = $recensioneNode->getAttribute("id_utente"); 

            // Ottieni la reputazione attuale del votante
            $sql = "SELECT reputazione FROM utenti WHERE id = $id_utente";
            $result = $connessione->query($sql);

            if ($result->num_rows == 1) {
                // Ottieni la riga risultante dalla query
                $row = $result->fetch_assoc();

                // Ottieni il valore della reputazione dall'array associativo
                $reputazioneVotante = $row['reputazione'];
            

                // Ottieni o crea i nodi "utilita" e "supporto"
                $utilitaNode = $recensioneNode->getElementsByTagName("utilita")->item(0);
                if (!$utilitaNode) {
                    $utilitaNode = $recensioneNode->appendChild($dom->createElement("utilita"));
                }

                $supportoNode = $recensioneNode->getElementsByTagName("supporto")->item(0);
                if (!$supportoNode) {
                    $supportoNode = $recensioneNode->appendChild($dom->createElement("supporto"));
                }

                // Aggiungi un nuovo nodo "valore" per "utilita"
                $valoreUtilitaNode = $utilitaNode->appendChild($dom->createElement("valore"));

                // Imposta l'attributo "id_utente" per "utilita"
                $valoreUtilitaNode->setAttribute("id_utente", $id_utente);
                $valoreUtilitaNode->setAttribute("reputazione_Vot", $reputazioneVotante);

                // Imposta il valore di "valore" per "utilita"
                $valoreUtilitaNode->nodeValue = $votoUtilita;

                // Aggiungi un nuovo nodo "valore" per "supporto"
                $valoreSupportoNode = $supportoNode->appendChild($dom->createElement("valore"));

                // Imposta l'attributo "id_utente" per "supporto"
                $valoreSupportoNode->setAttribute("id_utente", $id_utente);
                $valoreSupportoNode->setAttribute("reputazione_Vot", $reputazioneVotante);

                // Imposta il valore di "valore" per "supporto"
                $valoreSupportoNode->nodeValue = $votoSupporto;

                // Salva il documento XML aggiornato
                $dom->save($xmlFile);


                // ── Ricalcola la reputazione dell'autore della recensione (sicuramente un cliente) 
                // considerando TUTTI i suoi voti ricevuti ────
                // Itera su tutti i voti ricevuti applicando la formula ponderata:
                // Reputazione = (10/8) * ( Σ (utilità_i + supporto_i) * rep_i ) / Σ rep_i

                // Inizializzazione delle variabili
                $sommaVotiUtilitaSupporto = 0;
                $sommaReputazioni = 0;


                // 1) Voti ricevuti sulle DOMANDE dell'autore
                $domandeDellAutore = $xpath->query("//domanda[@id_utente='$id_utente_recensione']");
                foreach ($domandeDellAutore as $d) {
                    $utilitaNode = $d->getElementsByTagName('utilita')->item(0);
                    $supportoNode = $d->getElementsByTagName('supporto')->item(0);
                    if ($utilitaNode && $supportoNode) {
                        $votiU = $utilitaNode->getElementsByTagName('valore');
                        $votiS = $supportoNode->getElementsByTagName('valore');
                        for ($i = 0; $i < $votiU->length; $i++) {
                            $u = intval($votiU->item($i)->nodeValue);
                            $s = intval($votiS->item($i)->nodeValue);
                            $rep = intval($votiU->item($i)->getAttribute('reputazione_Vot'));
                            $sommaVotiUtilitaSupporto += ($u + $s) * $rep;
                            $sommaReputazioni += $rep;
                        }
                    }
                }

                // 2) Voti ricevuti sulle RECENSIONI dell'autore
                $recensioniDellAutore = $xpath->query("//recensione[@id_utente='$id_utente_recensione']");
                foreach ($recensioniDellAutore as $r) {
                    $utilitaNode = $r->getElementsByTagName('utilita')->item(0);
                    $supportoNode = $r->getElementsByTagName('supporto')->item(0);
                    if ($utilitaNode && $supportoNode) {
                        $votiU = $utilitaNode->getElementsByTagName('valore');
                        $votiS = $supportoNode->getElementsByTagName('valore');
                        for ($i = 0; $i < $votiU->length; $i++) {
                            $u = intval($votiU->item($i)->nodeValue);
                            $s = intval($votiS->item($i)->nodeValue);
                            $rep = intval($votiU->item($i)->getAttribute('reputazione_Vot'));
                            $sommaVotiUtilitaSupporto += ($u + $s) * $rep;
                            $sommaReputazioni += $rep;
                        }
                    }
                }

                // 3) Voti ricevuti sulle RISPOSTE dell'autore
                $risposteDellAutore = $xpath->query("//risposta[@id_utente='$id_utente_recensione']");
                foreach ($risposteDellAutore as $ri) {
                    $utilitaNode = $ri->getElementsByTagName('utilita')->item(0);
                    $supportoNode = $ri->getElementsByTagName('supporto')->item(0);
                    if ($utilitaNode && $supportoNode) {
                        $votiU = $utilitaNode->getElementsByTagName('valore');
                        $votiS = $supportoNode->getElementsByTagName('valore');
                        for ($i = 0; $i < $votiU->length; $i++) {
                            $u = intval($votiU->item($i)->nodeValue);
                            $s = intval($votiS->item($i)->nodeValue);
                            $rep = intval($votiU->item($i)->getAttribute('reputazione_Vot'));
                            $sommaVotiUtilitaSupporto += ($u + $s) * $rep;
                            $sommaReputazioni += $rep;
                        }
                    }
                }


                // Calcola la nuova reputazione del cliente che ha lasciato la recensione 
                $nuovaReputazione = (10/8) * ($sommaVotiUtilitaSupporto / $sommaReputazioni);
                
                // ── Sicuramente l'autore è un cliente perchè solo questi possono lasciare recensioni, quindi aggiorniamo ────────────────
                $updateQuery = "UPDATE utenti SET reputazione = $nuovaReputazione WHERE id = $id_utente_recensione";
                $connessione->query($updateQuery);
                header("Location: ../php/lista_recensioni.php?id_prodotto=$id_prodotto&nome=$nome&tipologia=$tipologia");
                exit();
            }
        }
    }
?>