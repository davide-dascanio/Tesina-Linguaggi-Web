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
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
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