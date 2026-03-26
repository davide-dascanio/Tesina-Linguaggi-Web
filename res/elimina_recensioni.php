<?php
    session_start();

    if ($_SERVER["REQUEST_METHOD"] === "GET") {

        // Percorso del file XML
        $xmlFile = '../xml/catalogo_prodotti.xml';

        // Carica il file XML
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->load($xmlFile);

        if (isset($_GET['id_recensione'])) {

            // Specifica l'id_recensione che si desidera eliminare
            $id_recensione = $_GET['id_recensione'];
            $id_prodotto = $_GET['id_prodotto'];
            $nome = $_GET['nome'];
            $tipologia = $_GET['tipologia'];

            // Utilizza XPath per trovare il nodo da eliminare
            $xpath = new DOMXPath($dom);
            $query = "//recensione[id_recensione='{$id_recensione}']";
            $recensioneNodes = $xpath->query($query);

            // Verifica se il nodo è stato trovato
            if ($recensioneNodes->length > 0) {
                // Rimuovi il nodo trovato
                $recensioneNode = $recensioneNodes->item(0);
                $recensioneNode->parentNode->removeChild($recensioneNode);

                // Salva le modifiche nel file XML
                $dom->save($xmlFile);

                // Reindirizza alla pagina delle recensioni aggiornata
                $_SESSION['successo_eliminazione'] = 'true';
                header("Location: ../php/lista_recensioni.php?id_prodotto=" . $id_prodotto . "&nome=" . $nome . "&tipologia=" . $tipologia);
                exit();
            }
        }
    }
?>
