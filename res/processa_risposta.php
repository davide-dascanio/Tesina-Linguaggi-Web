<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $faq_id = $_POST['faq_id'];
    $answer_text = $_POST['answer'];

    $xmlFile = '../xml/faq.xml';

    if (file_exists($xmlFile)) {
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;

        $dom->load($xmlFile);

        $xpath = new DOMXPath($dom);
        $result = $xpath->query("//entry[@id='$faq_id']");

        if ($result->length > 0) {
            $faq_entry = $result->item(0);

            // Trova l'elemento answer e aggiorna il suo testo
            $answer = $xpath->query("answer", $faq_entry);
            $answer->item(0)->nodeValue = $answer_text;
            
            // Salva le modifiche nel file XML
            $dom->save($xmlFile);
            
            header('Location: ../php/faq.php');
            exit();
        } else {
            echo "Errore: FAQ non trovata.";
        }
    } else {
        echo "Errore: Il file XML delle FAQ non esiste.";
    }
}
?>
