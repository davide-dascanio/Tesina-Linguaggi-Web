<?php
    session_start();

    // Verifica se l'utente è loggato
    if (isset($_SESSION['id'])) {

        $gestore = $_SESSION['gestore'];
        if ($gestore == 1){
            header("Location: accesso_negato.php");
            exit();
        }
    }else{
        // Reindirizza l'utente alla pagina di accesso se non è loggato
        header("Location: login_cliente.php");
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
        <title>Modifica Password</title>
        <link rel="stylesheet" href="../css/style_standard.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
        <link rel="stylesheet" href="../css/style_header.css">
    </head>
    <body>
        <?php
            require_once('../res/header.php');

            $cliente = $_SESSION['cliente'];

            if(isset($_SESSION['errore_preg']) && $_SESSION['errore_preg'] == 'true'){
                echo "<h2>La password non rispetta i criteri di sicurezza!</h2>";
                unset($_SESSION['errore_preg']);
            } 
            if(isset($_SESSION['errore_vecchia_pass']) && $_SESSION['errore_vecchia_pass'] == 'true'){
                echo "<h2>La vecchia password inserita non è corretta!</h2>";
                unset($_SESSION['errore_vecchia_pass']);
            }
        ?>
        <div class="cont">
        <?php
            // CASO 1: cliente - modifica la propria password
            if (($cliente == 1)) {
                $id_utente = $_SESSION['id'];

                // Esegui una query per ottenere i dati del cliente
                $query = "SELECT * FROM utenti WHERE id = $id_utente";
                $result = $connessione->query($query);

                if ($result->num_rows == 1) {
                    $cliente = $result->fetch_assoc();
        ?>
                    <h1 class="titolo">
                        <div class="tooltip">
                            <span class="tooltiptext">LA PASSWORD DEVE SODDISFARE I SEGUENTI REQUISITI:
                                <ol>
                                    <li>Deve essere lunga almeno 8 caratteri;</li>
                                    <li>Deve contenere almeno una lettera maiuscola e una minuscola;</li>
                                    <li>Deve contenere almeno un numero;</li>
                                    <li>Deve contenere almeno un carattere speciale (!,@,#,$,%,^,&,*).</li>
                                </ol>
                            </span>
                            <i id="simbolo" class="material-symbols-outlined">info</i>
                        </div>
                        Modifica Password
                    </h1>
                    <table>
                        <tr>
                            <td>
                                <form class="form" action="../res/processa_modifica_pass.php" method="post">
                                    <input style="width:300px;" class="input" type="password" name="vecchia_password" placeholder="INSERISCI LA VECCHIA PASSWORD" required><br><br>
                                    <input style="width:300px;" class="input" type="password" name="password" placeholder="INSERISCI NUOVA PASSWORD!" required><br>
                                    <br><br><br>
                                    <input class="btn" type="submit" value="Salva Password">
                                </form>
                            </td>
                        </tr>
                    </table>
                    <?php
                }
                else {
                    echo '<h2>Utente non trovato</h2>';
                }
            }
            // CASO 2: admin - modifica la password di un altro utente
            elseif (isset($_GET['id']) && is_numeric($_GET['id'])) {
                $id_utente = $_GET['id'];

                // Esegui una query per ottenere i dati dell'utente
                $query = "SELECT * FROM utenti WHERE id = $id_utente";
                $result = $connessione->query($query);

                if ($result->num_rows == 1) {
                    $utente = $result->fetch_assoc();
                    $email = $utente['email'];   // Ottieni l'email dall'array dell'utente
                    ?>
                    <h1 class="titolo">
                        <div class="tooltip">
                            <span class="tooltiptext">LA PASSWORD DEVE SODDISFARE I SEGUENTI REQUISITI:
                                <ol>
                                    <li>Deve essere lunga almeno 8 caratteri;</li>
                                    <li>Deve contenere almeno una lettera maiuscola e una minuscola;</li>
                                    <li>Deve contenere almeno un numero;</li>
                                    <li>Deve contenere almeno un carattere speciale (!,@,#,$,%,^,&,*).</li>
                                </ol>    
                            </span>
                            <i id="simbolo" class="material-symbols-outlined">info</i>
                        </div>
                    Modifica Password dell'account '<?php echo $email ?>'
                    </h1>
                    <table>
                        <tr>
                            <td>
                                <form class="form" action="../res/processa_modifica_pass.php" method="post">
                                    <input class="input" type="hidden" name="id" value="<?php echo $utente['id']; ?>">
                                    <input style="width:300px;" class="input" type="password" name="password" placeholder="INSERISCI NUOVA PASSWORD!" required><br>
                                    <br><br><br>
                                    <input class="btn" type="submit" value="Salva Password">
                                </form>
                            </td>
                        </tr>
                    </table>
                    <?php
                } 
                else {
                    echo '<h2>Utente non trovato</h2>';
                }
            }else{
                echo '<h2>ID utente non valido</h2>';
            }
            ?>
        </div>
    </body>
</html>