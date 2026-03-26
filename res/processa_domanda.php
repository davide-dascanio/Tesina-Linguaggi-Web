<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $faq_id = $_POST['faq_id'];
    $question_text = $_POST['question'];
    
    $xmlFile = '../xml/faq.xml';

    if (file_exists($xmlFile)) {
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;

        // Vogliamo output leggibile (indentato)
        $dom->formatOutput = true;

        $dom->load($xmlFile);

        $xpath = new DOMXPath($dom);
        $result = $xpath->query("//entry[@id='$faq_id']");
        
        if ($result->length > 0) {
            $faq_entry = $result->item(0);

            // Trova l'elemento question e aggiorna il suo testo
            $question = $xpath->query("question", $faq_entry);
            $question->item(0)->nodeValue = $question_text;
            
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