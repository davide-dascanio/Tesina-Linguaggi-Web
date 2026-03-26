<?php
    session_start();
    require_once('connessione1.php');

    if($_SERVER["REQUEST_METHOD"] === "POST"){

        // Sanifica la stringa inserita dall’utente escapando i caratteri speciali per evitare SQL Injection 
        $email = $connessione->real_escape_string($_POST['email']);
        $nome = $connessione->real_escape_string($_POST['nome']);
        $cognome = $connessione->real_escape_string($_POST['cognome']);
        $data_di_nascita = $connessione->real_escape_string($_POST['data_di_nascita']);
        $cellulare = $connessione->real_escape_string($_POST['cellulare']);
        $indirizzo_di_residenza = $connessione->real_escape_string($_POST['indirizzo_di_residenza']);
        $codice_fiscale = $connessione->real_escape_string($_POST['codice_fiscale']);
        $password = $connessione->real_escape_string($_POST['password']);
        $data = $connessione->real_escape_string($_POST['data_registrazione']);

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $cliente=1;
        $admin=0;
        $gestore=0;
        $crediti=0;
        $reputazione=1;
        $ban=0;
        
        $_SESSION['form_email'] = $email;
        $_SESSION['form_nome'] = $nome;
        $_SESSION['form_cognome'] = $_POST['cognome'];
        $_SESSION['form_data_di_nascita'] = $data_di_nascita;
        $_SESSION['form_cellulare'] = $cellulare;
        $_SESSION['form_indirizzo_di_residenza'] = $indirizzo_di_residenza;
        $_SESSION['form_codice_fiscale'] = $codice_fiscale;


        
        //controllo se l'email è già esistente
        $controllo_email = "SELECT * FROM utenti u WHERE u.email = '$email'";
        $ris_email = mysqli_query($connessione, $controllo_email);

        if(mysqli_num_rows($ris_email) > 0){
            $_SESSION['errore_email'] = 'true';
            $_SESSION['email_errata'] = $email;
            header('Location: ../php/registrazione_cliente.php');
            exit(1);
        }

        //controllo se il numero di cellulare è già esistente
        $controllo_cellulare = "SELECT* FROM utenti u WHERE u.cellulare = '$cellulare'";
        $ris_cellulare = mysqli_query($connessione, $controllo_cellulare);

        if(mysqli_num_rows($ris_cellulare) > 0){
            $_SESSION['errore_cellulare'] = 'true';
            $_SESSION['cellulare_errato'] = $cellulare;
            header('Location: ../php/registrazione_cliente.php');
            exit(1);
        }

        //controllo se il codice fiscale è già esistente
        $controllo_codice_fiscale = "SELECT* FROM utenti u WHERE u.codice_fiscale = '$codice_fiscale'";
        $ris_codice_fiscale = mysqli_query($connessione, $controllo_codice_fiscale);

        if(mysqli_num_rows($ris_codice_fiscale) > 0){
            $_SESSION['errore_codice_fiscale'] = 'true';
            $_SESSION['codice_fiscale_errato'] = $codice_fiscale;
            header('Location: ../php/registrazione_cliente.php');
            exit(1);
        }
        
        //controllo se la password rispetta i parametri
        //~ è il carattere delimitatore dell'espressione regolare
        if (!preg_match('~^(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*])(?=.*[0-9]).{8,}$~', $password)){
            $_SESSION['errore_preg'] = 'true';
            header('Location: ../php/registrazione_cliente.php');
            exit(1);
        }
        
        $sql = "INSERT INTO utenti (email, nome, cognome, data_di_nascita, cellulare, indirizzo_di_residenza, codice_fiscale, passwd, crediti, cliente, ammin, gestore, reputazione, ban, data_registrazione) 
                VALUES ('$email','$nome','$cognome','$data_di_nascita','$cellulare','$indirizzo_di_residenza', '$codice_fiscale','$hashed_password', '$crediti', '$cliente', '$admin', '$gestore', '$reputazione', '$ban', '$data')";
        
        try {
            $connessione->query($sql);

            //unsetto tutte le variabili di sessione utilizzate prima visto che il form è andato a buon fine
            unset($_SESSION['form_email']);
            unset($_SESSION['form_nome']);
            unset($_SESSION['form_cognome']);
            unset($_SESSION['form_data_di_nascita']);
            unset($_SESSION['form_cellulare']);
            unset($_SESSION['form_indirizzo_di_residenza']);
            unset($_SESSION['form_codice_fiscale']);

            $_SESSION['registrazione_ok'] = 'true';
            header("Location: ../php/login_cliente.php");
            exit();
        }
        catch (Exception $e) {
            $_SESSION['errore_registrazione'] = 'true';
            header("Location: ../php/registrazione_cliente.php");
            exit();
        }
    }
?>