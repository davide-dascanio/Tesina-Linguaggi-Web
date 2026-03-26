<?php
    session_start();
    // Verifica che il form sia stato inviato
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // Verifica che tutti i campi necessari siano stati compilati
        if (isset($_POST['nome'], $_POST['descrizione'], $_POST['prezzo'])) {

            $id_prodotto = $_POST['id_prodotto'];
            $tipologia = $_POST['tipologia'];
            $nome = $_POST['nome'];
            $descrizione = $_POST['descrizione'];
            $prezzo = $_POST['prezzo'];
            $immagine = $_FILES['immagine'];

            // Percorso del file XML
            $xmlFile = '../xml/catalogo_prodotti.xml';
            
            // Carica il file XML
            $dom = new DOMDocument();
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->load($xmlFile);

            $prodotti = $dom->getElementsByTagName('prodotto');

            // Controlliamo se il nome è già usato da un altro prodotto
            foreach ($prodotti as $prodotto) {
                $nomeNode = $prodotto->getElementsByTagName('nome')->item(0);
                $nomeEsistente = $nomeNode->nodeValue;

                // Controlla se il nome è già usato da UN ALTRO prodotto (non dal prodotto che si sta modificando)
                if ($nome == $nomeEsistente && $_SESSION['nome_prodotto_attuale'] != $nomeEsistente) {
                    $_SESSION['errore_nome_esistente'] = 'true';
                    header("Location: ../php/modifica_prodotti_form.php?id_prodotto=$id_prodotto&tipologia=$tipologia");
                    exit(); // Esce dal ciclo se il nome è già presente
                }
            }


            // Identifica il prodotto da modificare
            $prodottoDaModificare = null;
            foreach ($prodotti as $prodotto) {
                $id = (int)$prodotto->getElementsByTagName('id_prodotto')->item(0)->nodeValue;
                if ($id == $id_prodotto) {
                    $prodottoDaModificare = $prodotto;
                    break;
                }
            }


            // Verifica se il prodotto è stato trovato
            if ($prodottoDaModificare) {
                // Modifica le caratteristiche del prodotto
                $prodottoDaModificare->getElementsByTagName('nome')->item(0)->nodeValue = $nome;
                $prodottoDaModificare->getElementsByTagName('descrizione')->item(0)->nodeValue = $descrizione;
                $prodottoDaModificare->getElementsByTagName('prezzo')->item(0)->nodeValue = number_format(($prezzo), 2, '.', '');
            
                // Gestione dell'immagine solo se è stato caricato un nuovo file
                if (!empty($_FILES['immagine']['name'])) {
                    $immaginePath = '../img/' . basename($_FILES['immagine']['name']);
            
                    // Verifica che l'upload dell'immagine sia avvenuto con successo
                    if (move_uploaded_file($_FILES['immagine']['tmp_name'], $immaginePath)) {
                        $immagineInfo = getimagesize($immaginePath);

                        if ($immagineInfo !== false) {
                            // Rimuovi l'immagine esistente (se presente)
                            $immagineNode = $prodottoDaModificare->getElementsByTagName('immagine')->item(0);
                            if ($immagineNode) {
                                $prodottoDaModificare->removeChild($immagineNode);
                            }
            
                            // Aggiungi l'immagine nuova
                            $newImmagineNode = $dom->createElement('immagine', $immaginePath);
                            $prodottoDaModificare->appendChild($newImmagineNode);

                        } else {
                            // Messaggio di errore
                            $_SESSION['errore_immagine'] = 'true';
                            header("Location: ../php/modifica_prodotti_form.php?id_prodotto=$id_prodotto&tipologia=$tipologia");
                            exit();
                        }
                    }
                }

                // Salva le modifiche
                $dom->save($xmlFile);
                header('Location: ../php/catalogo_' . $tipologia . '.php');
                exit();
            }
        }
    }
?>