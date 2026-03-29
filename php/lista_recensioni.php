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
    <title>Lista Recensioni</title>
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
    if(isset($_SESSION['successo_eliminazione']) && $_SESSION['successo_eliminazione'] == 'true'){
        echo '<h2 id="successo">Eliminazione effettuata con successo.</h2>';
        unset($_SESSION['successo_eliminazione']);
    }
    if(isset($_SESSION['creazione_recensione']) && $_SESSION['creazione_recensione'] == 'true'){
        echo '<h2 id="successo">Recensione inviata con successo.</h2>';
        unset($_SESSION['creazione_recensione']);
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

    // Trova tutti gli elementi 'recensione' nel file XML relativi all'id_prodotto desiderato
    $xpath = new DOMXPath($dom);
    $recensioni = $xpath->query("//recensioni/recensione[@id_prodotto='$id_prodotto']");


    // ═════════════════════════════════════════════════════════════════════
    // BLOCCO CLIENTE
    // Può: visualizzare le recensioni di altri clienti, votarle e segnalarle. 
    //      Può eliminare le proprie recensioni.
    // ═════════════════════════════════════════════════════════════════════

    if($cliente == 1){ 

        echo '<a class="go-back" href="../php/catalogo_' . $tipologia . '.php">
                <span class="material-symbols-outlined" style="vertical-align:middle;">arrow_back</span>
                Torna al catalogo ' . $tipologia . '</a>';

        // Mostra le recensioni in una tabella
        if ($recensioni->length > 0) {
            echo '<h1 class="titolo">Recensioni del prodotto: ' . $nome . '</h1>';
            echo '<table>';

            $ciSonoRecensioniNonSegnalate = false;  // partiamo assumendo: tutte le recensioni sono segnalate

            foreach ($recensioni as $recensione) {  
                $id_utente_recensione = $recensione->getAttribute("id_utente");
                $post_segnalato = $recensione->getAttribute("segnalato");

                if($post_segnalato != 0)
                    continue;
                
                // se siamo qui ci sono recensioni non segnalate da stampare
                $ciSonoRecensioniNonSegnalate = true;


                $utilitaNode = $xpath->query("utilita/valore[@id_utente='$id_utente']", $recensione)->item(0);  //contiene un nodo XML oppure null
                $supportoNode = $xpath->query("supporto/valore[@id_utente='$id_utente']", $recensione)->item(0);

                // Ottieni i valori di utilità e supporto o imposta "N/A" se non presenti
                $utilitaValue = ($utilitaNode!=null) ? $utilitaNode->nodeValue : "N/A";
                $supportoValue = ($supportoNode!=null) ? $supportoNode->nodeValue : "N/A";


                $id_recensione = $recensione->getElementsByTagName("id_recensione")->item(0)->nodeValue;
                $autoreRecensione = $recensione->getElementsByTagName("autore")->item(0)->nodeValue;
                $testoRecensione = $recensione->getElementsByTagName("testo")->item(0)->nodeValue;
                $dataRecensione = $recensione->getElementsByTagName("data")->item(0)->nodeValue;
                $oraRecensione = $recensione->getElementsByTagName("ora")->item(0)->nodeValue;


                // Header colonne   
                echo '<tr>';
                echo '<th>Gestisci Recensione</th><th>Autore Recensione</th><th>Recensione</th><th>Voto Utilità</th><th>Voto Supporto</th><th>Valutazione</th>';
                echo '</tr>';
                
                echo '<tr>';

                // Colonna "Gestisci": elimina se propria, segnala se di un altro cliente
                echo '<td>';
                if($id_utente == $id_utente_recensione ){
                    echo '<a title="Elimina" href="../res/elimina_recensioni.php?id_recensione=' . $id_recensione . '&nome=' . $nome . '&id_prodotto=' . $id_prodotto . '&tipologia=' . $tipologia . '">';
                    echo '<span id="simbolo_cestino" class="material-symbols-outlined">delete</span></a>';
                } else {
                    echo '<a title="Segnala" href="segnalazione.php?id_recensione=' . $id_recensione . '&nome=' . $nome . '&testo_recensione=' . urlencode($testoRecensione) . '&id_prodotto=' . $id_prodotto . '&autore_recensione=' . urlencode($autoreRecensione) . '&tipologia=' . $tipologia . '">';
                    echo '<span id="simbolo_segnala" class="material-symbols-outlined">report</span></a>';
                }
                echo '</td>';

                echo '<td>Il cliente <strong>' . $autoreRecensione . '</strong> ha pubblicato la recensione il ' . $dataRecensione . ' alle ' . $oraRecensione . '</td>';
                echo nl2br('<td>' . $testoRecensione . '</td>');


                // Voti: non disponibili per la propria recensione
                if ($id_utente == $id_utente_recensione) {
                    echo '<td>---</td><td>---</td><td>---</td>';
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
                        // Colonna per i pulsanti di voto
                        echo '<td>';
                        echo '<form action="../res/recensioni_utilita_supporto.php" method="post">';
                        echo '<input type="hidden" name="id_recensione" value="' . $id_recensione . '"/>';
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

            echo '</table>';
            if (!$ciSonoRecensioniNonSegnalate) {
                echo '<p style="margin-top:10vh;" class="titolo">Nessuna recensione disponibile</p>';
            }
        } else {
            echo '<p class="titolo">Nessuna recensione disponibile</p>';
        }

    // ═════════════════════════════════════════════════════════════════════
    // BLOCCO GESTORE 
    // Può: visualizzare le recensioni dei clienti, votarle ed eliminarle
    // ═════════════════════════════════════════════════════════════════════

    }elseif($gestore == 1){

        echo '<a class="go-back" href="../php/catalogo_' . $tipologia . '.php">
                <span class="material-symbols-outlined" style="vertical-align:middle;">arrow_back</span>
                Torna al catalogo ' . $tipologia . '</a>';

        // Mostra le recensioni in una tabella
        if ($recensioni->length > 0) {
            echo '<h1 class="titolo">Recensioni del prodotto: ' . $nome . '</h1>';
            echo '<table>';
        
            $ciSonoRecensioniNonSegnalate = false;  // partiamo assumendo: tutte le recensioni sono segnalate

            foreach ($recensioni as $recensione) {  
                $id_utente_recensione = $recensione->getAttribute("id_utente");
                $post_segnalato = $recensione->getAttribute("segnalato");

                if($post_segnalato != 0)
                    continue;

                // se siamo qui ci sono recensioni non segnalate da stampare
                $ciSonoRecensioniNonSegnalate = true;


                $utilitaNode = $xpath->query("utilita/valore[@id_utente='$id_utente']", $recensione)->item(0);  //contiene un nodo XML oppure null
                $supportoNode = $xpath->query("supporto/valore[@id_utente='$id_utente']", $recensione)->item(0);

                // Ottieni i valori di utilità e supporto o imposta "N/A" se non presenti
                $utilitaValue = ($utilitaNode!=null) ? $utilitaNode->nodeValue : "N/A";
                $supportoValue = ($supportoNode!=null) ? $supportoNode->nodeValue : "N/A";


                $id_recensione = $recensione->getElementsByTagName("id_recensione")->item(0)->nodeValue;
                $autoreRecensione = $recensione->getElementsByTagName("autore")->item(0)->nodeValue;
                $testoRecensione = $recensione->getElementsByTagName("testo")->item(0)->nodeValue;
                $dataRecensione = $recensione->getElementsByTagName("data")->item(0)->nodeValue;
                $oraRecensione = $recensione->getElementsByTagName("ora")->item(0)->nodeValue;


                // Header colonne   
                echo '<tr>';
                echo '<th>Gestisci Recensione</th><th>Autore Recensione</th><th>Recensione</th><th>Voto Utilità</th><th>Voto Supporto</th><th>Valutazione</th>';
                echo '</tr>';
                
                echo '<tr>';

                // Colonna "Gestisci": elimina le recensioni dei clienti
                echo '<td>';
                echo '<a title="Elimina" href="../res/elimina_recensioni.php?id_recensione=' . $id_recensione . '&nome=' . $nome . '&id_prodotto=' . $id_prodotto . '&tipologia=' . $tipologia . '">';
                echo '<span id="simbolo_cestino" class="material-symbols-outlined">delete</span></a>';
                echo '</td>';

                echo '<td>Il cliente <strong>' . $autoreRecensione . '</strong> ha pubblicato la recensione il ' . $dataRecensione . ' alle ' . $oraRecensione . '</td>';
                echo nl2br('<td>' . $testoRecensione . '</td>');
                echo '<td>' . $utilitaValue . '</td>';
                echo '<td>' . $supportoValue . '</td>';

                // Ottieni l'id_utente dai nodi "valore" all'interno degli elementi "utilita" e "supporto"0
                $utilitaIdUtente = ($utilitaNode!=null) ? $utilitaNode->getAttribute("id_utente")  : "N/A";
                $supportoIdUtente = ($supportoNode!=null) ? $supportoNode->getAttribute("id_utente") : "N/A";

                if ($utilitaIdUtente == $id_utente || $supportoIdUtente == $id_utente) {
                    // Già votato
                    echo '<td><p><span class="material-symbols-outlined">verified</span></p></td>';
                } else {
                    // Colonna per i pulsanti di voto
                    echo '<td>';
                    echo '<form action="../res/recensioni_utilita_supporto.php" method="post">';
                    echo '<input type="hidden" name="id_recensione" value="' . $id_recensione . '"/>';
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

                echo '</tr>';

            }

            echo '</table>';
            if (!$ciSonoRecensioniNonSegnalate) {
                echo '<p style="margin-top:10vh;" class="titolo">Nessuna recensione disponibile</p>';
            }
        } else {
            echo '<p class="titolo">Nessuna recensione disponibile</p>';
        }   

    // ═════════════════════════════════════════════════════════════════════
    // BLOCCO AMMINISTRATORE
    // Può: "visualizzare le recensioni dei clienti ed eliminarle
    // ═════════════════════════════════════════════════════════════════════

    }elseif($admin == 1){

        echo '<a class="go-back" href="../php/catalogo_' . $tipologia . '.php">
                <span class="material-symbols-outlined" style="vertical-align:middle;">arrow_back</span>
                Torna al catalogo ' . $tipologia . '</a>';

        // Mostra le recensioni in una tabella
        if ($recensioni->length > 0) {
            echo '<h1 class="titolo">Recensioni del prodotto: ' . $nome . '</h1>';
            echo '<table>';
        
            $ciSonoRecensioniNonSegnalate = false;  // partiamo assumendo: tutte le recensioni sono segnalate

            foreach ($recensioni as $recensione) {  
                $id_utente_recensione = $recensione->getAttribute("id_utente");
                $post_segnalato =  $recensione->getAttribute("segnalato");

                if($post_segnalato != 0)
                    continue;

                // se siamo qui ci sono recensioni non segnalate da stampare
                $ciSonoRecensioniNonSegnalate = true;


                $utilitaNode = $xpath->query("utilita/valore[@id_utente='$id_utente']", $recensione)->item(0);  //contiene un nodo XML oppure null
                $supportoNode = $xpath->query("supporto/valore[@id_utente='$id_utente']", $recensione)->item(0);

                // Ottieni i valori di utilità e supporto o imposta "N/A" se non presenti
                $utilitaValue = ($utilitaNode!=null) ? $utilitaNode->nodeValue : "N/A";
                $supportoValue = ($supportoNode!=null) ? $supportoNode->nodeValue : "N/A";


                $id_recensione = $recensione->getElementsByTagName("id_recensione")->item(0)->nodeValue;
                $autoreRecensione = $recensione->getElementsByTagName("autore")->item(0)->nodeValue;
                $testoRecensione = $recensione->getElementsByTagName("testo")->item(0)->nodeValue;
                $dataRecensione = $recensione->getElementsByTagName("data")->item(0)->nodeValue;
                $oraRecensione = $recensione->getElementsByTagName("ora")->item(0)->nodeValue;


                // Header colonne   
                echo '<tr>';
                echo '<th>Gestisci Recensione</th><th>Autore Recensione</th><th>Recensione</th>';
                echo '</tr>';
                
                echo '<tr>';

                // Colonna "Gestisci": elimina le recensioni dei clienti
                echo '<td>';
                echo '<a title="Elimina" href="../res/elimina_recensioni.php?id_recensione=' . $id_recensione . '&nome=' . $nome . '&id_prodotto=' . $id_prodotto . '&tipologia=' . $tipologia . '">';
                echo '<span id="simbolo_cestino" class="material-symbols-outlined">delete</span></a>';
                echo '</td>';

                echo '<td>Il cliente <strong>' . $autoreRecensione . '</strong> ha pubblicato la recensione il ' . $dataRecensione . ' alle ' . $oraRecensione . '</td>';
                echo nl2br('<td>' . $testoRecensione . '</td>');
                
                echo '</tr>';

            }

            echo '</table>';
            if (!$ciSonoRecensioniNonSegnalate) {
                echo '<p style="margin-top:10vh;" class="titolo">Nessuna recensione disponibile</p>';
            }
        } else {
            echo '<p class="titolo">Nessuna recensione disponibile</p>';
        }   
    }
    ?>
    </div>
</body>
</html>