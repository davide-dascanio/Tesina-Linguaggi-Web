<?php 
    session_start();
    require_once('connessione1.php');
    
    if($_SERVER["REQUEST_METHOD"] === "POST"){
        // Sanifica la stringa inserita dall’utente escapando i caratteri speciali per evitare SQL Injection 
        $email = $connessione->real_escape_string($_POST['email']);
        $password = $connessione->real_escape_string($_POST['password']);

        $sql_select = "SELECT * FROM utenti WHERE email = '$email'";
        if($result = $connessione->query($sql_select)){
            if($result->num_rows === 1){
                $row = mysqli_fetch_array($result);   // oppure $row = $result->fetch_array(MYSQLI_ASSOC);
                
                // Confronto diretto delle password (senza hashing)
                if(password_verify($password, $row['passwd'])){
                    // Verifica il campo 'ban'
                    if($row['ban'] == 1){
                        header("Location: ../php/utente_bannato.php"); // Reindirizza a pagina di errore ban
                        exit();
                    }
                    $_SESSION['loggato'] = true;
                    $_SESSION['email'] = $row['email'];
                    $_SESSION['crediti'] = $row['crediti'];
                    $_SESSION['id'] = $row['id'];
                    $_SESSION['cliente'] = $row['cliente'];
                    $_SESSION['gestore'] = $row['gestore'];
                    $_SESSION['ammin'] = $row['ammin'];
                    $_SESSION['indirizzo'] = $row['indirizzo_di_residenza'];
                    header("Location: ../php/index.php");
                    exit();
                } else {
                    $_SESSION['errore_login'] = 'true';
                    header("Location: ../php/login_cliente.php");
                    exit();
                }
            } else {
                $_SESSION['errore_login'] = 'true';
                header("Location: ../php/login_cliente.php");
                exit();
            }
        } else {
            $_SESSION['errore_login'] = 'true';
            header("Location: ../php/login_cliente.php");
        }
        $connessione->close();
    }
?>