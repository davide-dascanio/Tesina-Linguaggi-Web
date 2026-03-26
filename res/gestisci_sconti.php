<?php
    session_start();

    // Verifica che il form sia stato inviato
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        if (isset($_POST['id_prodotto'], $_POST['tipologia'], $_POST['tipo_sconto'], $_POST['azione'])) {

            $id_prodotto = $_POST['id_prodotto'];
            $tipologia = $_POST['tipologia'];
            $tipo_sconto = $_POST['tipo_sconto'];
            $azione = $_POST['azione'];

            // Carica il file XML segnalazioni.xml
            $xmlFile = '../xml/catalogo_prodotti.xml';
            $dom = new DOMDocument();
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->load($xmlFile);

            $xpath = new DOMXPath($dom);
            $prodottoNode = $xpath->query("//prodotto[id_prodotto='$id_prodotto']")->item(0);

            if (!$prodottoNode) {
                header("Location: ../php/gestisci_sconti_form.php?id_prodotto=$id_prodotto&tipologia=$tipologia");
                exit();
            }

            $sb = $prodottoNode->getElementsByTagName('sconti_bonus')->item(0);

            if ($azione == 'disattiva') {

                if ($tipo_sconto == 'sconto_generico') {
                    $nodo = $sb->getElementsByTagName('sconto_generico')->item(0);
                    $nodo->setAttribute('attivo', '0');
                    $nodo->nodeValue = '0';

                } elseif ($tipo_sconto == 'bonus_generico') {
                    $nodo = $sb->getElementsByTagName('bonus_generico')->item(0);
                    $nodo->setAttribute('attivo', '0');
                    $nodo->nodeValue = '0';

                } elseif ($tipo_sconto == 'sconto_personalizzato') {
                    $nodo = $sb->getElementsByTagName('sconto_personalizzato')->item(0);
                    $criterio = $nodo->getElementsByTagName('criterio')->item(0);

                    $nodo->setAttribute('attivo', '0');
                    $nodo->getElementsByTagName('percentuale')->item(0)->nodeValue = '0';
                    $criterio->setAttribute('tipo', 'nessuno');
                    $criterio->getElementsByTagName('soglia')->item(0)->nodeValue = '0';
                    $criterio->getElementsByTagName('data_riferimento')->item(0)->nodeValue = '';

                } elseif ($tipo_sconto == 'bonus_personalizzato') {
                    $nodo     = $sb->getElementsByTagName('bonus_personalizzato')->item(0);
                    $criterio = $nodo->getElementsByTagName('criterio')->item(0);

                    $nodo->setAttribute('attivo', '0');
                    $nodo->getElementsByTagName('crediti')->item(0)->nodeValue = '0';
                    $criterio->setAttribute('tipo', 'nessuno');
                    $criterio->getElementsByTagName('soglia')->item(0)->nodeValue = '0';
                    $criterio->getElementsByTagName('data_riferimento')->item(0)->nodeValue = '';
                }

                $_SESSION['successo_sconto'] = 'Sconto/Bonus disattivato con successo!';

            } elseif ($azione == 'attiva') {

                if ($tipo_sconto == 'sconto_generico') {
                    $nodo = $sb->getElementsByTagName('sconto_generico')->item(0);
                    $nodo->setAttribute('attivo', '1');
                    $nodo->nodeValue = $_POST['percentuale'];

                } elseif ($tipo_sconto == 'bonus_generico') {
                    $nodo = $sb->getElementsByTagName('bonus_generico')->item(0);
                    $nodo->setAttribute('attivo', '1');
                    $nodo->nodeValue = $_POST['crediti'];

                } elseif ($tipo_sconto == 'sconto_personalizzato') {
                    $nodo = $sb->getElementsByTagName('sconto_personalizzato')->item(0);
                    $criterio = $nodo->getElementsByTagName('criterio')->item(0);

                    $nodo->setAttribute('attivo', '1');
                    $nodo->getElementsByTagName('percentuale')->item(0)->nodeValue = $_POST['percentuale'];
                    $criterio->setAttribute('tipo', $_POST['tipo_criterio']);
                    $criterio->getElementsByTagName('soglia')->item(0)->nodeValue = $_POST['soglia'];
                    $criterio->getElementsByTagName('data_riferimento')->item(0)->nodeValue = isset($_POST['data_riferimento']) ? $_POST['data_riferimento'] : '';

                } elseif ($tipo_sconto == 'bonus_personalizzato') {
                    $nodo = $sb->getElementsByTagName('bonus_personalizzato')->item(0);
                    $criterio = $nodo->getElementsByTagName('criterio')->item(0);

                    $nodo->setAttribute('attivo', '1');
                    $nodo->getElementsByTagName('crediti')->item(0)->nodeValue = $_POST['crediti'];
                    $criterio->setAttribute('tipo', $_POST['tipo_criterio']);
                    $criterio->getElementsByTagName('soglia')->item(0)->nodeValue = $_POST['soglia'];
                    $criterio->getElementsByTagName('data_riferimento')->item(0)->nodeValue = isset($_POST['data_riferimento']) ? $_POST['data_riferimento'] : '';
                }

                $_SESSION['successo_sconto'] = 'Sconto/Bonus attivato con successo!';
            }

            $dom->save($xmlFile);
            header("Location: ../php/gestisci_sconti_form.php?id_prodotto=$id_prodotto&tipologia=$tipologia");
            exit();
        }
    }
?>
