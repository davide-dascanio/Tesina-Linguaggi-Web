<?php
    session_start();
    
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $email = $_SESSION['email'];
        $importo = $_POST['importo'];

        $xmlFile = '../xml/requests.xml';
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;

        // Vogliamo output leggibile (indentato)
        $dom->formatOutput = true; 
        $dom->load($xmlFile);

        $root = $dom->documentElement;

        $request = $dom->createElement('request');
        $request->setAttribute('status', 'In Attesa');
        $request->setAttribute('id_utente', $_SESSION['id']);

        $emailElement = $dom->createElement('email', $email);
        $request->appendChild($emailElement);
        
        
        $importoElement = $dom->createElement('importo', number_format(($importo), 2, '.', ''));
        $request->appendChild($importoElement);


        $root->appendChild($request);

        // Salva le modifiche nel file XML
        $dom->save($xmlFile);


        $_SESSION['successo_richiesta'] = 'true';
        header("Location: ../php/richiesta_crediti.php");
    }
?>