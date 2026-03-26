<?php
    session_start();
    
    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        $id_contributo = $_POST['id_contributo'];
        $testo_contributo = $_POST['testo_contributo'];
        $id_prodotto = $_POST['id_prodotto'];
        $autore_contributo = $_POST['autore_contributo'];
        $nome = $_POST['nome'];
        $tipologia = $_POST['tipologia'];
        $autore_segnalazione = $_POST['autore_segnalazione'];
        $testo_segnalazione = $_POST['testo_segnalazione'];


        // Carica il file XML
        $xmlFile = '../xml/segnalazioni.xml';
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->load($xmlFile);


        $root = $dom->documentElement;

        $segnalazione = $dom->createElement('segnalazione');

        $segnalazione->setAttribute('status', 'In Attesa');
        $segnalazione->setAttribute('id_contributo', $id_contributo);
        $segnalazione->setattribute('id_prodotto', $id_prodotto);
        $segnalazione->setattribute('autore_segnalazione', $autore_segnalazione);


        $testo_elemento = $dom->createElement('testo_contributo', $testo_contributo);
        $segnalazione->appendChild($testo_elemento);

        $autore_elemento = $dom->createElement('autore_contributo', $autore_contributo);
        $segnalazione->appendChild($autore_elemento);

        $segnalazione_elemento = $dom->createElement('testo_segnalazione', $testo_segnalazione);
        $segnalazione->appendChild($segnalazione_elemento);

        $root->appendChild($segnalazione);


        $dom->save($xmlFile);


        $_SESSION['successo_segnalazione'] = 'true';
        if(isset($_POST['rec']) && $_POST['rec'] = 'rec'){
            header("Location: ../php/lista_recensioni.php?id_prodotto=$id_prodotto&nome=$nome&tipologia=$tipologia");
        }else{
            header("Location: ../php/lista_domande.php?id_prodotto=$id_prodotto&nome=$nome&tipologia=$tipologia");
            exit();
        }
    }
?>
