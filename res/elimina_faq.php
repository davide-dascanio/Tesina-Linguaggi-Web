<?php
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Verifica se è stato fornito un parametro "id" (l'id dell'entry) nell'URL
    if (isset($_GET['id'])) {
        $faq_id = $_GET['id'];

        $xmlFile = '../xml/faq.xml';

        if (file_exists($xmlFile)) {
            // Carica il file XML
            $dom = new DOMDocument();
            $dom->preserveWhiteSpace = false;

            $dom->load($xmlFile);

            // Cerca l'elemento con l'id corrispondente
            $xpath = new DOMXPath($dom);
            $faq_entry = $xpath->query("//entry[@id='$faq_id']");

            // Verifica se è stato trovato un elemento
            if ($faq_entry->length > 0) {
                // Rimuovi l'elemento dal file XML
                $faq_entry->item(0)->parentNode->removeChild($faq_entry->item(0));
                $dom->normalizeDocument();
                $dom->formatOutput = true; 
                // Salva le modifiche nel file XML
                $dom->save($xmlFile);

                // Reindirizza alla pagina delle FAQ
                header('Location: ../php/faq.php');
                exit();
            } else {
                echo "Errore: FAQ non trovata.";
            }
        } else {
            echo "Errore: Il file XML delle FAQ non esiste.";
        }
    } else {
        echo "Errore: Parametro 'id' mancante.";
    }
}
?>