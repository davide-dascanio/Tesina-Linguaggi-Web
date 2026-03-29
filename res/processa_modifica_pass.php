<?php
    session_start();
    require_once('connessione1.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
        $admin = $_SESSION['ammin'];

        if($admin == 1){
            $id_utente = $_POST['id'];
            $password = $_POST['password'];
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        }else{
            //sono il cliente sicuramente
            $id_utente = $_SESSION['id'];
            $password = $_POST['password'];
            $vecchia_password = $_POST['vecchia_password'];
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $query_pass = "SELECT passwd FROM utenti WHERE id = ?";
            $stmt_pass = $connessione->prepare($query_pass);
            $stmt_pass->bind_param("i", $id_utente);
            $stmt_pass->execute();

            $risultato = $stmt_pass->get_result();
            $pass = $risultato->fetch_assoc();
            $hash_attuale = $pass['passwd'];
            $stmt_pass->close();

            // Verifica che la vecchia password sia corretta
            // password_verify confronta la vecchia password in chiaro inserita dall'utente 
            // con l'hash salvato nel DB: restituisce true se corrispondono, false altrimenti
            if (!password_verify($vecchia_password, $hash_attuale)) {
                $_SESSION['errore_vecchia_pass'] = 'true';
                header('Location: ../php/modifica_password.php');
                exit(1);
            }
        }
        

        //controllo se la password rispetta i parametri
        //~ è il carattere delimitatore dell'espressione regolare
        if (!preg_match('~^(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*])(?=.*[0-9]).{8,}$~', $password)){
            $_SESSION['errore_preg'] = 'true';
            if($admin == 1){
                header('Location: ../php/modifica_password.php?id=' . $id_utente . '');
                exit(1);
            }else{
                //sono il cliente
                header('Location: ../php/modifica_password.php');
                exit(1);
            }
        }

        $query = "UPDATE utenti SET passwd = ? WHERE id = ?";

        // Utilizza statement preparati per evitare SQL injection
        $stmt = $connessione->prepare($query);
        $stmt->bind_param("si", $hashed_password, $id_utente);

        if ($stmt->execute()) {
            if($admin == 1){
                header("Location: ../php/gestione_utenti.php");
                exit();
            }
            else {
                header("Location: ../php/gestione_profilo.php");
                exit();
            }
        }
        else {
            echo 'Errore durante il salvataggio delle modifiche: ' . $stmt->error;
        }
    }
?>