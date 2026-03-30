<?php
    session_start();
    require_once('connessione1.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Ricevi i dati del modulo inviati tramite POST
        $id_utente = $_POST['id'];
        $nome = $_POST['nome'];
        $cognome = $_POST['cognome'];
        $indirizzo_di_residenza = $_POST['indirizzo'];
        $codice_fiscale = $_POST['fiscale'];
        $data_di_nascita = $_POST['nascita'];
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
            header("Location: ../php/modifica_utente.php?id=" . $id_utente);
            exit();
        }



        // Esegui una query per verificare se il codice fiscale è già presente nel database
        $query_cellulare_esistente = "SELECT id FROM utenti WHERE codice_fiscale = ? AND id != ?";
        $stmt_cellulare_esistente = $connessione->prepare($query_cellulare_esistente);
        $stmt_cellulare_esistente->bind_param("si", $codice_fiscale, $id_utente);
        $stmt_cellulare_esistente->execute();
        $ris_codfiscale_esistente = $stmt_cellulare_esistente->get_result();

        // Verifica se esiste già un utente con lo stesso codice fiscale (escluso l'utente attuale)
        if ($ris_codfiscale_esistente->num_rows > 0) {
            // Codice fiscale già presente nel database
            $_SESSION['errore_codfiscale_ex'] = true;
            $_SESSION['codfiscale_errato'] = $codice_fiscale;
            header("Location: ../php/modifica_utente.php?id=" . $id_utente);
            exit();
        }


        // Se il numero di cellulare e il codice fiscale non sono già presenti nel database, procedi con l'aggiornamento dei dati dell'utente
        $query_aggiornamento = "UPDATE utenti SET nome = ?, cognome = ?, indirizzo_di_residenza = ?, cellulare = ?, codice_fiscale = ?, data_di_nascita = ? WHERE id = ?";
        $stmt_aggiornamento = $connessione->prepare($query_aggiornamento);
        $stmt_aggiornamento->bind_param("ssssssi", $nome, $cognome, $indirizzo_di_residenza, $cellulare, $codice_fiscale, $data_di_nascita, $id_utente);


        if ($stmt_aggiornamento->execute()) {
            // Aggiornamento dei dati avvenuto con successo
            $stmt_aggiornamento->close();
            header("Location: ../php/gestione_utenti.php");
            exit();
        }else{
            // Errore durante l'aggiornamento dei dati
            $_SESSION['errore_query'] = 'true';
            $stmt_aggiornamento->close();
            header("Location: ../php/modifica_utente.php?id=" . $id_utente);
            exit();
        }
    }
?>