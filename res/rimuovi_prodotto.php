<?php
    session_start();

    // Verifica che il form sia stato inviato
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // Verifica che il campo necessario sia compilato
        if (isset($_POST['nome'])){

            // Percorso del file XML
            $xmlFile = '../xml/catalogo_prodotti.xml';

            // Carica il file XML
            $dom = new DOMDocument();
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->load($xmlFile);


            // Cerca e rimuovi il prodotto
            $xpath = new DOMXPath($dom);
            $query = "/catalogo_prodotti/prodotto[nome='{$_POST['nome']}']";
            $prodottoNode = $xpath->query($query)->item(0);  //Nodo cercato
           
            
            // Salva le modifiche solo se il prodotto è stato trovato
            if ($prodottoNode) {
                $prodottoNode->parentNode->removeChild($prodottoNode);
                $dom->save($xmlFile);
                $_SESSION['successo_rimozione_prodotto'] = 'true';
                header("Location: ../php/menu_rimuovi_prodotto.php");
                exit();
            } else {
                $_SESSION['fallimento_rimozione_prodotto'] = 'true';
                header("Location: ../php/menu_rimuovi_prodotto.php");
            }
        }
    }
?>