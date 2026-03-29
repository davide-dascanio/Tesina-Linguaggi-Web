<?php
    session_start();

    // Verifica che il form sia stato inviato
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // Verifica che tutti i campi necessari siano stati compilati
        if (isset($_POST['nome'], $_POST['descrizione'], $_POST['prezzo'], $_FILES['immagine'], $_POST['tipologia'])) {

            // Percorso del file XML
            $xmlFile = '../xml/catalogo_prodotti.xml';

            // Carica il file XML
            $dom = new DOMDocument();
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->load($xmlFile);
        
            // Trova ID dell'ultimo prodotto inserito nel catalogo
            $ultimoID = 0;
            $prodottoList = $dom->getElementsByTagName('prodotto');
        
            // Flag per verificare se il nome è già presente
            $nomePresente = false;
        
            foreach ($prodottoList as $prodottoNode) {
                $nomeNode = $prodottoNode->getElementsByTagName('nome')->item(0);
                $nomeEsistente = $nomeNode->nodeValue;
        
                // Verifica se il nome è già presente
                if ($_POST['nome'] == $nomeEsistente) {
                    $_SESSION['errore_nome_esistente'] = 'true';
                    header('Location:../php/menu_aggiungi_prodotto.php');
                    exit(); // Esce dal ciclo se il nome è già presente
                }else{
                    $idNode = $prodottoNode->getElementsByTagName('id_prodotto')->item(0);
                    $id = (int)$idNode->nodeValue;
                    if ($id > $ultimoID) {
                        $ultimoID = $id;
                    }
                }
            }

            // Calcola il prossimo ID disponibile
            $prossimoID = $ultimoID + 1;

            // Aggiungi un nuovo prodotto con l'ID incrementato       
            $prodotto = $dom->createElement('prodotto');
            $dom->documentElement->appendChild($prodotto);
                    
            $id_prodotto = $dom->createElement('id_prodotto', $prossimoID);
            $prodotto->appendChild($id_prodotto);

            $nome = $dom->createElement('nome', trim($_POST['nome']));
            $prodotto->appendChild($nome);

            $descrizione = $dom->createElement('descrizione', str_replace("\r\n", "\n", $_POST['descrizione']));
            $prodotto->appendChild($descrizione);

            $prezzo = $dom->createElement('prezzo', number_format(($_POST['prezzo']), 2, '.', ''));
            $prodotto->appendChild($prezzo);

            $tipologia = $dom->createElement('tipologia', $_POST['tipologia']);
            $prodotto->appendChild($tipologia);


            // Gestione dell'immagine
            $immaginePath = '../img/' . basename($_FILES['immagine']['name']);  // "foto.jpg"   → nome che ha scelto l'utente

            // Controlla sul file temporaneo PRIMA di spostarlo
            $immagineInfo = getimagesize($_FILES['immagine']['tmp_name']);   // dove sta il file ORA sul server

            if ($immagineInfo !== false) {
                // È un'immagine valida → ora la spostiamo
                if (move_uploaded_file($_FILES['immagine']['tmp_name'], $immaginePath)) {

                    $immagine = $dom->createElement('immagine', $immaginePath);
                    $prodotto->appendChild($immagine);
                }
            }else{
                // Non è un'immagine → errore
                $_SESSION['errore_immagine'] = 'true';
                header("Location: ../php/menu_aggiungi_prodotto.php");
                exit();
            }


            $sconti_bonus = $dom->createElement('sconti_bonus');
            $prodotto->appendChild($sconti_bonus);

            // Sconto generico 
            $sconto_generico = $dom->createElement('sconto_generico', '0');
            $sconto_generico->setAttribute('attivo', '0');
            $sconti_bonus->appendChild($sconto_generico);

            // Sconto personalizzato
            $sconto_personalizzato = $dom->createElement('sconto_personalizzato');
            $sconto_personalizzato->setAttribute('attivo', '0');
            $sconti_bonus->appendChild($sconto_personalizzato);

            $percentuale = $dom->createElement('percentuale', '0');
            $sconto_personalizzato->appendChild($percentuale);

            $criterio = $dom->createElement('criterio');
            $criterio->setAttribute('tipo', 'nessuno');
            $sconto_personalizzato->appendChild($criterio);

            $soglia = $dom->createElement('soglia', '0');
            $criterio->appendChild($soglia);

            $data_riferimento = $dom->createElement('data_riferimento');
            $criterio->appendChild($data_riferimento);

            // Bonus generico
            $bonus_generico = $dom->createElement('bonus_generico', '0');
            $bonus_generico->setAttribute('attivo', '0');
            $sconti_bonus->appendChild($bonus_generico);

            // Bonus personalizzato
            $bonus_personalizzato = $dom->createElement('bonus_personalizzato');
            $bonus_personalizzato->setAttribute('attivo', '0');
            $sconti_bonus->appendChild($bonus_personalizzato);

            $crediti = $dom->createElement('crediti', '0');
            $bonus_personalizzato->appendChild($crediti);

            $criterio = $dom->createElement('criterio');
            $criterio->setAttribute('tipo', 'nessuno');
            $bonus_personalizzato->appendChild($criterio);

            $soglia = $dom->createElement('soglia', '0');
            $criterio->appendChild($soglia);

            $data_riferimento = $dom->createElement('data_riferimento');
            $criterio->appendChild($data_riferimento);

            // Domande
            $domande = $dom->createElement('domande');
            $prodotto->appendChild($domande);

            // Recensioni
            $recensioni = $dom->createElement('recensioni');
            $prodotto->appendChild($recensioni);

            
            // Salva le modifiche
            $dom->save($xmlFile);

            $_SESSION['successo_aggiunta_prodotto'] = 'true';
            header("Location: ../php/menu_aggiungi_prodotto.php");
            exit();
        }
    }
?>