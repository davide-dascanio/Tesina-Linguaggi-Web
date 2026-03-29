<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Verifica se 'faq_question' e 'faq_answer' sono impostati
    if (isset($_POST['faq_question']) && isset($_POST['faq_answer'])) {

        // I textarea su Windows mandano le andate a capo come \r\n
        // Il DOMDocument quando salva nel XML converte il \r in &#13;
        // Rimuoviamo quindi il \r prima di assegnare il testo al nodo XML
        $faq_question = str_replace("\r\n", "\n", $_POST['faq_question']);
        $faq_answer = str_replace("\r\n", "\n", $_POST['faq_answer']);

        
        $xmlFile = '../xml/faq.xml';

        if (file_exists($xmlFile)) {
            // Carica il file XML
            $dom = new DOMDocument();
            $dom->preserveWhiteSpace = false;
            
            // Vogliamo output leggibile (indentato)
            $dom->formatOutput = true;

            $dom->load($xmlFile);

            // Genera un ID univoco per la FAQ
            $faq_id = uniqid();

            // Crea l'elemento <entry> per la nuova FAQ
            $newFaq = $dom->createElement('entry');
            $newFaq->setAttribute('id', $faq_id);

            // Crea l'elemento <question> e aggiungi il testo
            $questionNode = $dom->createElement('question', $faq_question);
            $newFaq->appendChild($questionNode);

            // Crea l'elemento <answer> e aggiungi il testo
            $answerNode = $dom->createElement('answer', $faq_answer);
            $newFaq->appendChild($answerNode);

            $primaFaq = $dom->documentElement->firstChild;

            // Aggiungi la nuova FAQ al documento XML ma in testa al documento
            $dom->documentElement->insertBefore($newFaq,$primaFaq);

            // Garantisce che la struttura sia pulita e coerente
            $dom->normalizeDocument();
            
            // Salva le modifiche nel file XML
            $dom->save($xmlFile);

            header('Location: ../php/faq.php');
            exit();
        } else {
            echo "Errore: Il file XML delle FAQ non esiste.";
        }
    } else {
        echo "Errore: Dati mancanti nella richiesta.";
    }
}
?>