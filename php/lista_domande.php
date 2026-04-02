<?php
    session_start();

    // Verifica se il l'utente è loggato
    if (!isset($_SESSION['loggato'])) {
        // Reindirizza alla pagina di accesso se non è loggato
        header("Location: login_cliente.php");
        exit();
    }

    // Verifica se sono stati passati i parametri GET
    if (!isset($_GET['id_prodotto']) || !isset($_GET['tipologia']) || !isset($_GET['nome'])) {
        header("Location: ../php/index.php");
        exit();
    }
?>


<?xml version = "1.0"?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
       "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">


<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista Domande</title>
    <link rel="stylesheet" href="../css/style_standard.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="stylesheet" href="../css/style_header.css">
</head>
<body>
    <?php
    require_once('../res/header.php');
    ?>
    <div class="cont">
    <?php
    // ── Messaggi di sessione ──────────────────────────────────────────────
    if(isset($_SESSION['successo_segnalazione']) && $_SESSION['successo_segnalazione'] == 'true'){
        echo '<h2 id="successo">Segnalazione inviata con successo. Attendere l\'approvazione del gestore...</h2>';
        unset($_SESSION['successo_segnalazione']);
    }
    if(isset($_SESSION['errore_segnalazione']) && $_SESSION['errore_segnalazione'] == 'true'){
        echo '<h2>Hai già segnalato questo contributo. Attendere l\'approvazione del gestore...</h2>';
        unset($_SESSION['errore_segnalazione']);
    }
    if(isset($_SESSION['successo_eliminazione']) && $_SESSION['successo_eliminazione'] == 'true'){
        echo '<h2 id="successo">Eliminazione effettuata con successo.</h2>';
        unset($_SESSION['successo_eliminazione']);
    }
    if(isset($_SESSION['creazione_domanda']) && $_SESSION['creazione_domanda'] == 'true'){
        echo '<h2 id="successo">Domanda inviata con successo.</h2>';
        unset($_SESSION['creazione_domanda']);
    }
    if(isset($_SESSION['creazione_risposta']) && $_SESSION['creazione_risposta'] == 'true'){
        echo '<h2 id="successo">Risposta inviata con successo.</h2>';
        unset($_SESSION['creazione_risposta']);
    }


    // ── Parametri GET ─────────────────────────────────────────────────────
    $tipologia = $_GET['tipologia'];
    $id_prodotto = $_GET['id_prodotto'];
    $nome = $_GET['nome'];

    // ── Variabili di sessione ─────────────────────────────────────────────
    $id_utente = $_SESSION['id'];
    $email = $_SESSION['email'];
    $gestore = $_SESSION['gestore'];
    $admin = $_SESSION['ammin'];
    $cliente = $_SESSION['cliente'];

    // ── Carica XML ────────────────────────────────────────────────────────
    $xmlFile = '../xml/catalogo_prodotti.xml';
    $dom = new DOMDocument();
    $dom->load($xmlFile);
    $xpath = new DOMXPath($dom);

    // Trova tutti gli elementi 'domande' nel file XML relativi all'id_prodotto desiderato
    $domande = $xpath->query("//domande/domanda[@id_prodotto='$id_prodotto']");


    // ═════════════════════════════════════════════════════════════════════
    // BLOCCO CLIENTE
    // Può: rispondere alle domande di altri clienti, votare contributi altrui, 
    //      segnalare contributi altrui, eliminare i propri contributi.
    // ═════════════════════════════════════════════════════════════════════

    if($cliente == 1){

        echo '<a class="go-back" href="../php/catalogo_' . $tipologia . '.php">
                <span class="material-symbols-outlined" style="vertical-align:middle;">arrow_back</span>
                Torna al catalogo ' . $tipologia . '</a>';

        // Mostra le domande e i form di risposta in una tabella
        if ($domande->length > 0) {
            echo '<h1 class="titolo">Domande del prodotto: ' . $nome . '</h1>';
            echo '<table>';

            $ciSonoDomandeNonSegnalate = false;  // partiamo assumendo: tutte le domande sono segnalate

            $primaDomanda = true;

            foreach ($domande as $domanda) {    
                $id_utente_domanda = $domanda->getAttribute("id_utente");
                $post_segnalato = $domanda->getAttribute("segnalato");
                
                if ($post_segnalato != 0) 
                    continue;
                
                // se siamo qui ci sono domande non segnalate da stampare
                $ciSonoDomandeNonSegnalate = true;


                // Separatore visivo tra blocchi domanda->risposte
                $separatore = $primaDomanda ? '' : 'border-top: 4px solid rgba(74, 74, 138, 0.5);';
                $primaDomanda = false;

                // Header colonne con il bordo applicato
                echo '<tr style="' . $separatore . '">';
                echo '</tr>';


                $utilitaNode = $xpath->query("utilita/valore[@id_utente='$id_utente']", $domanda)->item(0);  //contiene un nodo XML oppure null
                $supportoNode = $xpath->query("supporto/valore[@id_utente='$id_utente']", $domanda)->item(0);

                // Ottieni i valori di utilità e supporto o imposta "N/A" se non presenti
                $utilitaValue = ($utilitaNode!=null) ? $utilitaNode->nodeValue : "N/A";
                $supportoValue = ($supportoNode!=null) ? $supportoNode->nodeValue : "N/A";

                $id_domanda = $domanda->getElementsByTagName("id_domanda")->item(0)->nodeValue;
                $autoreDomanda = $domanda->getElementsByTagName("autore")->item(0)->nodeValue;
                $testoDomanda = $domanda->getElementsByTagName("testo")->item(0)->nodeValue;
                $dataDomanda = $domanda->getElementsByTagName("data")->item(0)->nodeValue;
                $oraDomanda = $domanda->getElementsByTagName("ora")->item(0)->nodeValue;


                // Header colonne   
                echo '<tr>';
                echo '<th>Gestisci Domanda</th><th>Autore Domanda</th><th>Domanda</th><th>Voto Utilità</th><th>Voto Supporto</th><th>Valutazione</th><th>Rispondi</th>';
                echo '</tr>';
                
                echo '<tr>';
                
                // Colonna "Gestisci": elimina se propria, segnala se altrui
                echo '<td>';
                if($id_utente == $id_utente_domanda ){
                    echo '<a title="Elimina" href="../res/elimina_dom_risp.php?id_domanda=' . $id_domanda . '&nome=' . $nome . '&id_prodotto=' . $id_prodotto . '&tipologia=' . $tipologia . '">';
                    echo '<span id="simbolo_cestino" class="material-symbols-outlined">delete</span></a>';
                } else {
                    echo '<a title="Segnala" href="segnalazione.php?id_domanda=' . $id_domanda . '&nome=' . $nome . '&testo_domanda=' . urlencode($testoDomanda) . '&id_prodotto=' . $id_prodotto . '&autore_domanda=' . urlencode($autoreDomanda) . '&tipologia=' . $tipologia . '">';
                    echo '<span id="simbolo_segnala" class="material-symbols-outlined">report</span></a>';
                }
                echo '</td>';

                echo '<td>Il cliente <strong>' . $autoreDomanda . '</strong> ha pubblicato la domanda il ' . $dataDomanda . ' alle ' . $oraDomanda . '</td>';
                echo nl2br('<td>' . $testoDomanda . '</td>');

                // Voti / risposta: non disponibili per la propria domanda
                if ($id_utente == $id_utente_domanda) {
                    echo '<td>---</td><td>---</td><td>---</td><td>---</td>';
                } else {
                    echo '<td>' . $utilitaValue . '</td>';
                    echo '<td>' . $supportoValue . '</td>';


                    // Ottieni l'id_utente dai nodi "valore" all'interno degli elementi "utilita" e "supporto"
                    $utilitaIdUtente = ($utilitaNode!=null)  ? $utilitaNode->getAttribute("id_utente")  : "N/A";
                    $supportoIdUtente = ($supportoNode!=null) ? $supportoNode->getAttribute("id_utente") : "N/A";

                    if ($utilitaIdUtente == $id_utente || $supportoIdUtente == $id_utente) {
                        // Già votato
                        echo '<td><p><span class="material-symbols-outlined">verified</span></p></td>';
                    } else {
                        // Colonna per i pulsanti di voto delle domande
                        echo '<td>';
                        echo '<form action="../res/domande_utilita_supporto.php" method="post">';
                        echo '<input type="hidden" name="id_domanda" value="' . $id_domanda . '"/>';
                        echo '<input type="hidden" name="id_prodotto" value="' . $id_prodotto . '"/>';
                        echo '<input type="hidden" name="tipologia" value="' . $tipologia . '"/>';
                        echo '<input type="hidden" name="nome" value="' . $nome . '"/>';

                        echo '<label>Utilità (da 1 a 5): </label>';
                        echo '<input class="input" style="width:40px;" type="number" name="votoUtilita" min="1" max="5" required/><br>';

                        echo '<label>Supporto (da 1 a 3): </label>';
                        echo '<input class="input" style="width:40px;" type="number" name="votoSupporto" min="1" max="3" required/><br>';
                        
                        echo '<button class="done" type="submit" name="vota">CONFERMA<span id="done" class="material-symbols-outlined">done_outline</span></button>';
                        echo '</form>';
                        echo '</td>';
                    }

                    // Colonna "Rispondi"
                    echo '<td>';
                    echo '<form action="../res/risposte.php" method="post">';
                    echo '<input type="hidden" name="id_prodotto" value="' . $id_prodotto . '"/>';
                    echo '<input type="hidden" name="tipologia" value="' . $tipologia . '"/>';
                    echo '<input type="hidden" name="nome" value="' . $nome . '"/>';
                    echo '<input type="hidden" name="autore" value="' . $email . '"/>';
                    echo '<input type="hidden" name="id_domanda" value="' . $id_domanda . '"/>';

                    echo '<textarea style="height:70px;width:200px;resize:none;vertical-align:top;" class="input" name="risposta" placeholder="Inserisci la risposta..." required></textarea>';
                    echo '<button class="btn" type="submit">Invia risposta</button>';
                    echo '</form>';
                    echo '</td>';
                }

                echo '</tr>';

                // ── Mostra Risposte Di Quella Domanda ──────────────────────────────────────────────
                $risposte = $domanda->getElementsByTagName('risposta');
                if ($risposte->length > 0) {
                    foreach ($risposte as $risposta) {
                        $id_utente_risposta = $risposta->getAttribute('id_utente');
                        $risp_segnalato = $risposta->getAttribute('segnalato');

                        if ($risp_segnalato != 0) 
                            continue;

                        
                        // Cerca il ruolo nel DB
                        $sql = "SELECT gestore, cliente FROM utenti WHERE id = '$id_utente_risposta'";
                        $result = $connessione->query($sql);
                        $row = $result->fetch_assoc();

                        $ruolo_autore = '';
                        if ($row['gestore'] == 1) {
                            $ruolo_autore = 'Il gestore';
                        } else {
                            $ruolo_autore = 'Il cliente';
                        }


                        $utilitaNode = $xpath->query("utilita/valore[@id_utente='$id_utente']", $risposta)->item(0);
                        $supportoNode = $xpath->query("supporto/valore[@id_utente='$id_utente']", $risposta)->item(0);

                        // Ottieni i valori di utilità e supporto o imposta "N/A" se non presenti
                        $utilitaValue = ($utilitaNode!=null)  ? $utilitaNode->nodeValue  : "N/A";
                        $supportoValue = ($supportoNode!=null) ? $supportoNode->nodeValue : "N/A";

                        $id_risposta = $risposta->getElementsByTagName("id_risposta")->item(0)->nodeValue;
                        $autoreRisposta = $risposta->getElementsByTagName("autore")->item(0)->nodeValue;
                        $dataRisposta = $risposta->getElementsByTagName("data")->item(0)->nodeValue;
                        $oraRisposta = $risposta->getElementsByTagName("ora")->item(0)->nodeValue;
                        $testoRisposta = $risposta->getElementsByTagName("testo")->item(0)->nodeValue;

                        echo '<tr>';
                        echo '<th>Gestisci Risposta</th><th>Autore Risposta</th><th>Risposta</th><th>Voto Utilità</th><th>Voto Supporto</th><th>Valutazione</th>';
                        echo '</tr>';

                        echo '<tr>';

                        // Colonna "Gestisci": elimina se propria, segnala se altrui
                        echo '<td>';
                        if ($id_utente_risposta == $id_utente) {
                            echo '<a title="Elimina" href="../res/elimina_dom_risp.php?id_prodotto=' . $id_prodotto . '&nome=' . $nome . '&id_risposta=' . $id_risposta . '&tipologia=' . $tipologia . '">';
                            echo '<span id="simbolo_cestino" class="material-symbols-outlined">delete</span></a>';
                        } else {
                            echo '<a title="Segnala" href="segnalazione.php?id_prodotto=' . $id_prodotto . '&nome=' . $nome . '&id_risposta=' . $id_risposta . '&testo_risposta=' . urlencode($testoRisposta) . '&autore_risposta=' . urlencode($autoreRisposta) . '&tipologia=' . $tipologia . '">';
                            echo '<span id="simbolo_segnala" class="material-symbols-outlined">report</span></a>';
                        }
                        echo '</td>';


                        echo '<td>' . $ruolo_autore . ' <strong>' . $autoreRisposta . '</strong> ha risposto il ' . $dataRisposta . ' alle ' . $oraRisposta . '</td>';
                        echo nl2br('<td>' . $testoRisposta . '</td>');

                        // Voti: non disponibili per la propria risposta
                        if ($id_utente_risposta == $id_utente) {
                            echo '<td>---</td><td>---</td><td>---</td>';
                        } else {
                            echo '<td>' . $utilitaValue . '</td>';
                            echo '<td>' . $supportoValue . '</td>';

                            // Ottieni l'id_utente dai nodi "valore" all'interno degli elementi "utilita" e "supporto"
                            $utilitaIdUtente  = ($utilitaNode!=null)  ? $utilitaNode->getAttribute("id_utente")  : "N/A";
                            $supportoIdUtente = ($supportoNode!=null) ? $supportoNode->getAttribute("id_utente") : "N/A";

                            if ($utilitaIdUtente == $id_utente || $supportoIdUtente == $id_utente) {
                                // Già votato
                                echo '<td><p><span class="material-symbols-outlined">verified</span></p></td>';
                            } else {
                                // Colonna per i pulsanti di voto delle risposte
                                echo '<td>';
                                echo '<form action="../res/risposte_utilita_supporto.php" method="post">';
                                echo '<input type="hidden" name="id_domanda" value="' . $id_domanda . '"/>';
                                echo '<input type="hidden" name="id_risposta" value="' . $id_risposta . '"/>';
                                echo '<input type="hidden" name="id_prodotto" value="' . $id_prodotto . '"/>';
                                echo '<input type="hidden" name="tipologia" value="' . $tipologia . '"/>';
                                echo '<input type="hidden" name="nome" value="' . $nome . '"/>';

                                echo '<label>Utilità (da 1 a 5): </label>';
                                echo '<input class="input" style="width:40px;" type="number" name="votoUtilita" min="1" max="5" required/><br>';

                                echo '<label>Supporto (da 1 a 3): </label>';
                                echo '<input class="input" style="width:40px;" type="number" name="votoSupporto" min="1" max="3" required/><br>';

                                echo '<button class="done" type="submit" name="vota">CONFERMA<span id="done" class="material-symbols-outlined">done_outline</span></button>';
                                echo '</form>';

                                echo '</td>';
                            }
                        }
                        
                        echo '</tr>'; 
                    }
                }
            }

            echo '</table>';
            if (!$ciSonoDomandeNonSegnalate) {
                echo '<p style="margin-top:10vh;" class="titolo">Nessuna domanda disponibile</p>';
            }
        } else {
            echo '<p class="titolo">Nessuna domanda disponibile</p>';
        }

    // ═════════════════════════════════════════════════════════════════════
    // BLOCCO GESTORE
    // Può: rispondere alle domande dei clienti, votare contributi dei clienti e risposte di altri gestori (no effetto su reputazione),
    //      eliminare qualsiasi contributo.
    // NON può: votare le proprie risposte.
    // ═════════════════════════════════════════════════════════════════════
           
    }elseif($gestore == 1){

        echo '<a class="go-back" href="../php/catalogo_' . $tipologia . '.php">
                <span class="material-symbols-outlined" style="vertical-align:middle;">arrow_back</span>
                Torna al catalogo ' . $tipologia . '</a>';

        // Mostra le domande e i form di risposta in una tabella
        if ($domande->length > 0) {
            echo '<h1 class="titolo">Domande del prodotto: ' . $nome . '</h1>';
            echo '<table>';

            $ciSonoDomandeNonSegnalate = false;  // partiamo assumendo: tutte le domande sono segnalate

            $primaDomanda = true;

            foreach ($domande as $domanda) { 
                $id_utente_domanda = $domanda->getAttribute("id_utente");
                $post_segnalato = $domanda->getAttribute("segnalato");

                if ($post_segnalato != 0) 
                    continue;

                // se siamo qui ci sono domande non segnalate da stampare
                $ciSonoDomandeNonSegnalate = true;

                // Separatore visivo tra blocchi domanda->risposte
                $separatore = $primaDomanda ? '' : 'border-top: 4px solid rgba(74, 74, 138, 0.5);';
                $primaDomanda = false;

                // Header colonne con il bordo applicato
                echo '<tr style="' . $separatore . '">';
                echo '</tr>';

                
                $utilitaNode = $xpath->query("utilita/valore[@id_utente='$id_utente']", $domanda)->item(0);  //contiene un nodo XML oppure null
                $supportoNode = $xpath->query("supporto/valore[@id_utente='$id_utente']", $domanda)->item(0);

                // Ottieni i valori di utilità e supporto o imposta "N/A" se non presenti
                $utilitaValue = ($utilitaNode!=null)  ? $utilitaNode->nodeValue  : "N/A";
                $supportoValue = ($supportoNode!=null) ? $supportoNode->nodeValue : "N/A";

                $id_domanda = $domanda->getElementsByTagName("id_domanda")->item(0)->nodeValue;
                $autoreDomanda = $domanda->getElementsByTagName("autore")->item(0)->nodeValue;
                $testoDomanda = $domanda->getElementsByTagName("testo")->item(0)->nodeValue;
                $dataDomanda = $domanda->getElementsByTagName("data")->item(0)->nodeValue;
                $oraDomanda = $domanda->getElementsByTagName("ora")->item(0)->nodeValue;

                // Header colonne 
                echo '<tr>';
                echo '<th>Gestisci Domanda</th><th>Autore Domanda</th><th>Domanda</th><th>Voto Utilità</th><th>Voto Supporto</th><th>Valutazione</th><th>Rispondi</th>';
                echo '</tr>';

                echo '<tr>';

                // Colonna "Gestisci": elimina (le domande sono sempre dei clienti)
                echo '<td>';
                echo '<a title="Elimina" href="../res/elimina_dom_risp.php?id_domanda=' . $id_domanda . '&nome=' . $nome . '&id_prodotto=' . $id_prodotto . '&tipologia=' . $tipologia . '">';
                echo '<span id="simbolo_cestino" class="material-symbols-outlined">delete</span></a>';
                echo '</td>';

                echo '<td>Il cliente <strong>' . $autoreDomanda . '</strong> ha pubblicato la domanda il ' . $dataDomanda . ' alle ' . $oraDomanda . '</td>';
                echo nl2br('<td>' . $testoDomanda . '</td>');
                echo '<td>' . $utilitaValue . '</td>';
                echo '<td>' . $supportoValue . '</td>';

                // Ottieni l'id_utente dai nodi "valore" all'interno degli elementi "utilita" e "supporto"0
                $utilitaIdUtente = ($utilitaNode!=null) ? $utilitaNode->getAttribute("id_utente")  : "N/A";
                $supportoIdUtente = ($supportoNode!=null) ? $supportoNode->getAttribute("id_utente") : "N/A";

                if ($utilitaIdUtente == $id_utente || $supportoIdUtente == $id_utente) {
                    // Già votato
                    echo '<td><p><span class="material-symbols-outlined">verified</span></p></td>';
                } else {
                    // Colonna per i pulsanti di voto delle domande
                    echo '<td>';
                    echo '<form action="../res/domande_utilita_supporto.php" method="post">';
                    echo '<input type="hidden" name="id_domanda" value="' . $id_domanda . '"/>';
                    echo '<input type="hidden" name="id_prodotto" value="' . $id_prodotto . '"/>';
                    echo '<input type="hidden" name="tipologia" value="' . $tipologia . '"/>';
                    echo '<input type="hidden" name="nome" value="' . $nome . '"/>';

                    echo '<label>Utilità (da 1 a 5): </label>';
                    echo '<input class="input" style="width:40px;" type="number" name="votoUtilita" min="1" max="5" required/><br>';

                    echo '<label>Supporto (da 1 a 3): </label>';
                    echo '<input class="input" style="width:40px;" type="number" name="votoSupporto" min="1" max="3" required/><br>';

                    echo '<button class="done" type="submit" name="vota">CONFERMA<span id="done" class="material-symbols-outlined">done_outline</span></button>';
                    echo '</form>';
                    echo '</td>';
                }

                // Colonna "Rispondi"
                echo '<td>';
                echo '<form action="../res/risposte.php" method="post">';
                echo '<input type="hidden" name="id_prodotto" value="' . $id_prodotto . '"/>';
                echo '<input type="hidden" name="tipologia" value="' . $tipologia . '"/>';
                echo '<input type="hidden" name="nome" value="' . $nome . '"/>';
                echo '<input type="hidden" name="autore" value="' . $email . '"/>';
                echo '<input type="hidden" name="id_domanda" value="' . $id_domanda . '"/>';

                echo '<textarea style="height:70px;width:200px;resize:none;vertical-align:top;" class="input" name="risposta" placeholder="Inserisci la risposta..." required></textarea>';
                echo '<button class="btn" type="submit">Invia risposta</button>';
                echo '</form>';
                echo '</td>';

                echo '</tr>';

                // ── Mostra Risposte Di Quella Domanda ──────────────────────────────────────────────
                $risposte = $domanda->getElementsByTagName('risposta');
                if ($risposte->length > 0) {
                    foreach ($risposte as $risposta) {
                        $id_utente_risposta = $risposta->getAttribute('id_utente');
                        $risp_segnalato = $risposta->getAttribute('segnalato');

                        if ($risp_segnalato != 0) 
                            continue;


                        // Cerca il ruolo nel DB
                        $sql = "SELECT gestore, cliente FROM utenti WHERE id = '$id_utente_risposta'";
                        $result = $connessione->query($sql);
                        $row = $result->fetch_assoc();

                        $ruolo_autore = '';
                        if ($row['gestore'] == 1) {
                            $ruolo_autore = 'Il gestore';
                        } else {
                            $ruolo_autore = 'Il cliente';
                        }


                        $utilitaNode = $xpath->query("utilita/valore[@id_utente='$id_utente']", $risposta)->item(0);
                        $supportoNode = $xpath->query("supporto/valore[@id_utente='$id_utente']", $risposta)->item(0);

                        // Ottieni i valori di utilità e supporto o imposta "N/A" se non presenti
                        $utilitaValue = ($utilitaNode!=null)  ? $utilitaNode->nodeValue  : "N/A";
                        $supportoValue = ($supportoNode!=null) ? $supportoNode->nodeValue : "N/A";

                        $id_risposta = $risposta->getElementsByTagName("id_risposta")->item(0)->nodeValue;
                        $autoreRisposta = $risposta->getElementsByTagName("autore")->item(0)->nodeValue;
                        $dataRisposta = $risposta->getElementsByTagName("data")->item(0)->nodeValue;
                        $oraRisposta = $risposta->getElementsByTagName("ora")->item(0)->nodeValue;
                        $testoRisposta = $risposta->getElementsByTagName("testo")->item(0)->nodeValue;

                        echo '<tr>';
                        echo '<th>Gestisci Risposta</th><th>Autore Risposta</th><th>Risposta</th><th>Voto Utilità</th><th>Voto Supporto</th><th>Valutazione</th>';
                        echo '</tr>';

                        echo '<tr>';

                        // Gestisci: risposta propria o altrui -> elimina
                        echo '<td>';
                        echo '<a title="Elimina" href="../res/elimina_dom_risp.php?id_prodotto=' . $id_prodotto . '&nome=' . $nome . '&id_risposta=' . $id_risposta . '&tipologia=' . $tipologia . '">';
                        echo '<span id="simbolo_cestino" class="material-symbols-outlined">delete</span></a>';
                        echo '</td>';


                        echo '<td>' . $ruolo_autore . ' <strong>' . $autoreRisposta . '</strong> ha risposto il ' . $dataRisposta . ' alle ' . $oraRisposta . '</td>';
                        echo nl2br('<td>' . $testoRisposta . '</td>');
                        
                        // Voti: il gestore non può votare la propria risposta
                        if ($id_utente_risposta == $id_utente) {
                            echo '<td>---</td><td>---</td><td>---</td>';
                        } else {
                            echo '<td>' . $utilitaValue . '</td>';
                            echo '<td>' . $supportoValue . '</td>';

                            // Ottieni l'id_utente dai nodi "valore" all'interno degli elementi "utilita" e "supporto"
                            $utilitaIdUtente  = ($utilitaNode!=null)  ? $utilitaNode->getAttribute("id_utente")  : "N/A";
                            $supportoIdUtente = ($supportoNode!=null) ? $supportoNode->getAttribute("id_utente") : "N/A";

                            if ($utilitaIdUtente == $id_utente || $supportoIdUtente == $id_utente) {
                                // Già votato
                                echo '<td><p><span class="material-symbols-outlined">verified</span></p></td>';
                            } else {
                                // Colonna per i pulsanti di voto delle risposte
                                echo '<td>';
                                echo '<form action="../res/risposte_utilita_supporto.php" method="post">';
                                echo '<input type="hidden" name="id_domanda" value="' . $id_domanda . '"/>';
                                echo '<input type="hidden" name="id_risposta" value="' . $id_risposta . '"/>';
                                echo '<input type="hidden" name="id_prodotto" value="' . $id_prodotto . '"/>';
                                echo '<input type="hidden" name="tipologia" value="' . $tipologia . '"/>';
                                echo '<input type="hidden" name="nome" value="' . $nome . '"/>';

                                echo '<label>Utilità (da 1 a 5): </label>';
                                echo '<input class="input" style="width:40px;" type="number" name="votoUtilita" min="1" max="5" required/><br>';
                                
                                echo '<label>Supporto (da 1 a 3): </label>';
                                echo '<input class="input" style="width:40px;" type="number" name="votoSupporto" min="1" max="3" required/><br>';

                                echo '<button class="done" type="submit" name="vota">CONFERMA<span id="done" class="material-symbols-outlined">done_outline</span></button>';
                                echo '</form>';
                                echo '</td>';
                            }
                        }

                        echo '</tr>'; 
                    }
                }
            }

            echo '</table>';
            if (!$ciSonoDomandeNonSegnalate) {
                echo '<p style="margin-top:10vh;" class="titolo">Nessuna domanda disponibile</p>';
            }
        } else {
            echo '<p class="titolo">Nessuna domanda disponibile</p>';
        }

    // ═════════════════════════════════════════════════════════════════════
    // BLOCCO AMMINISTRATORE
    // Può: "non può scriverne di propri così come non può giudicare
    //         nessun contributo" — solo visualizzazione + moderazione (elimina)
    // ═════════════════════════════════════════════════════════════════════
    } elseif ($admin == 1) {

        echo '<a class="go-back" href="../php/catalogo_' . $tipologia . '.php">
                <span class="material-symbols-outlined" style="vertical-align:middle;">arrow_back</span>
                Torna al catalogo ' . $tipologia . '</a>';

        // Mostra le domande e i form di risposta in una tabella
        if ($domande->length > 0) {
            echo '<h1 class="titolo">Domande del prodotto: ' . $nome . '</h1>';
            echo '<table>';

            $ciSonoDomandeNonSegnalate = false;  // partiamo assumendo: tutte le domande sono segnalate

            $primaDomanda = true;

            foreach ($domande as $domanda) {
                $post_segnalato = $domanda->getAttribute("segnalato");

                if ($post_segnalato != 0) 
                    continue;

                // se siamo qui ci sono domande non segnalate da stampare
                $ciSonoDomandeNonSegnalate = true;

                // Separatore visivo tra blocchi domanda->risposte
                $separatore = $primaDomanda ? '' : 'border-top: 4px solid rgba(74, 74, 138, 0.5);';
                $primaDomanda = false;

                // Header colonne con il bordo applicato
                echo '<tr style="' . $separatore . '">';
                echo '</tr>';


                $id_domanda = $domanda->getElementsByTagName("id_domanda")->item(0)->nodeValue;
                $autoreDomanda = $domanda->getElementsByTagName("autore")->item(0)->nodeValue;
                $testoDomanda = $domanda->getElementsByTagName("testo")->item(0)->nodeValue;
                $dataDomanda = $domanda->getElementsByTagName("data")->item(0)->nodeValue;
                $oraDomanda = $domanda->getElementsByTagName("ora")->item(0)->nodeValue;

                // Header colonne
                echo '<tr><th>Gestisci Domanda</th><th>Autore Domanda</th><th>Domanda</th></tr>';

                echo '<tr>';

                // Colonna "Gestisci": elimina (le domande sono sempre dei clienti)
                echo '<td>';
                echo '<a title="Elimina" href="../res/elimina_dom_risp.php?id_domanda=' . $id_domanda . '&nome=' . $nome . '&id_prodotto=' . $id_prodotto . '&tipologia=' . $tipologia . '">';
                echo '<span id="simbolo_cestino" class="material-symbols-outlined">delete</span></a>';
                echo '</td>';

                echo '<td>Il cliente <strong>' . $autoreDomanda . '</strong> ha pubblicato la domanda il ' . $dataDomanda . ' alle ' . $oraDomanda . '</td>';
                echo nl2br('<td>' . $testoDomanda . '</td>');

                echo '</tr>';

                // ── Mostra Risposte Di Quella Domanda ──────────────────────────────────────────────
                $risposte = $domanda->getElementsByTagName('risposta');
                if ($risposte->length > 0) {
                    foreach ($risposte as $risposta) {
                        $id_utente_risposta = $risposta->getAttribute('id_utente');
                        $risp_segnalato = $risposta->getAttribute('segnalato');
                        
                        if ($risp_segnalato != 0) 
                            continue;


                        // Cerca il ruolo nel DB
                        $sql = "SELECT gestore, cliente FROM utenti WHERE id = '$id_utente_risposta'";
                        $result = $connessione->query($sql);
                        $row = $result->fetch_assoc();

                        $ruolo_autore = '';
                        if ($row['gestore'] == 1) {
                            $ruolo_autore = 'Il gestore';
                        } else {
                            $ruolo_autore = 'Il cliente';
                        }


                        $id_risposta = $risposta->getElementsByTagName("id_risposta")->item(0)->nodeValue;
                        $autoreRisposta = $risposta->getElementsByTagName("autore")->item(0)->nodeValue;
                        $dataRisposta = $risposta->getElementsByTagName("data")->item(0)->nodeValue;
                        $oraRisposta = $risposta->getElementsByTagName("ora")->item(0)->nodeValue;
                        $testoRisposta = $risposta->getElementsByTagName("testo")->item(0)->nodeValue;

                        echo '<tr><th>Gestisci Risposta</th><th>Autore Risposta</th><th>Risposta</th></tr>';

                        echo '<tr>';

                        // Colonna "Gestisci": elimina (moderazione)
                        echo '<td>';
                        echo '<a title="Elimina" href="../res/elimina_dom_risp.php?id_prodotto=' . $id_prodotto . '&nome=' . $nome . '&id_risposta=' . $id_risposta . '&tipologia=' . $tipologia . '">';
                        echo '<span id="simbolo_cestino" class="material-symbols-outlined">delete</span></a>';
                        echo '</td>';

                        echo '<td>' . $ruolo_autore . ' <strong>' . $autoreRisposta . '</strong> ha risposto il ' . $dataRisposta . ' alle ' . $oraRisposta . '</td>';
                        echo nl2br('<td>' . $testoRisposta . '</td>');

                        echo '</tr>';
                    }
                }
            }

            echo '</table>';
            if (!$ciSonoDomandeNonSegnalate) {
                echo '<p style="margin-top:10vh;" class="titolo">Nessuna domanda disponibile</p>';
            }
        } else {
            echo '<p class="titolo">Nessuna domanda disponibile</p>';
        }
    }
    ?>
    </div>
</body>
</html>