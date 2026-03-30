<?php
    session_start();
    require_once('connessione1.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['ban'], $_POST['id'])) {

        $action = $_POST['action'];
        if ($action === 'Approva') {
            $valoreBanAttuale = $_POST['ban'];
            $id_utente = $_POST['id'];

            // Cambia il valore di "ban" in base alla logica richiesta
            if($valoreBanAttuale == 0){
                $nuovoValoreBan = 1;
            }else{
                $nuovoValoreBan = 0;
            }

            $query = "UPDATE utenti SET ban = '$nuovoValoreBan' WHERE id = $id_utente";
                
            if ($connessione->query($query) === TRUE) {
                header("Location: ../php/gestione_utenti.php");
                exit();
            } else {
                echo 'Errore durante il salvataggio delle modifiche: ' . $connessione->error;
            }
        
        } elseif ($action === 'Rifiuta') {
            // Se l'azione è "Rifiuta", non fare nulla e reindirizza alla pagina iniziale
            header("Location: ../php/gestione_utenti.php");
            exit();
        } 
    }
?>