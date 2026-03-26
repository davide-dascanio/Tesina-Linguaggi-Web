<?php
    session_start();
    require_once('connessione1.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Ricevi i dati del modulo inviati tramite POST
        // L'id viene preso dalla sessione
        $id_utente = $_SESSION['id'];
        $nome = $_POST['nome'];
        $cognome = $_POST['cognome'];
        $indirizzo = $_POST['indirizzo'];
        $cellulare = $_POST['cellulare'];


        // Esegui una query per verificare se il numero di cellulare è già presente nel database
        $query_cellulare_esistente = "SELECT id FROM utenti WHERE cellulare = ? AND id != ?";
        $stmt_cellulare_esistente = $connessione->prepare($query_cellulare_esistente);
        $stmt_cellulare_esistente->bind_param("si", $cellulare, $id_utente);
        $stmt_cellulare_esistente->execute();
        $ris_cellulare_esistente = $stmt_cellulare_esistente->get_result();

        // Verifica se esiste già un utente con lo stesso numero di cellulare (escluso l'utente attuale)
        if ($ris_cellulare_esistente->num_rows > 0) {
            // Numero di cellulare già presente nel database
            $_SESSION['errore_cellulare_ex'] = true;
            $_SESSION['cellulare_errato'] = $cellulare;
            header("Location: ../php/modifica_profilo.php");
            exit();
        }


        // Se non ci sono duplicati, procedi con l'aggiornamento dei dati del cliente
        $query_aggiornamento = "UPDATE utenti SET nome = ?, cognome = ?, indirizzo_di_residenza = ?, cellulare = ? WHERE id = ?";
        $stmt_aggiornamento = $connessione->prepare($query_aggiornamento);

        // bind_param associa i valori reali solo a query già compilata (? come segnaposto)
        // La stringa "ssssssi" indica i tipi nell'ordine: s = string, i = integer
        $stmt_aggiornamento->bind_param("ssssi", $nome, $cognome, $indirizzo, $cellulare, $id_utente);


    
        if ($stmt_aggiornamento->execute()) {
            // Aggiornamento dei dati avvenuto con successo
            header("Location: ../php/gestione_profilo.php");
            exit();
        } else {
            // Errore durante l'aggiornamento dei dati
            $_SESSION['errore_query'] = 'true';
            header("Location: ../php/modifica_profilo.php");
            exit();
        }
    }

?>