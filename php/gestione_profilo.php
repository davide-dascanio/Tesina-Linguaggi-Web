<?php
    session_start();

    // Verifica se il cliente è loggato
    if (isset($_SESSION['id'])) {

        $cliente = $_SESSION['cliente'];
        if ($cliente == 0){
            // non siamo clienti
            header("Location: accesso_negato.php");
            exit();
        }
    }else{
        // Reindirizza alla pagina di accesso se non è loggato
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
        <title>Gestione Profilo</title>
        <link rel="stylesheet" href="../css/style_standard.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
        <link rel="stylesheet" href="../css/style_header.css">
    </head>
    <body>
        <?php
            require_once('../res/header.php');
        ?>
        <div class="contenitore">
            <?php

                $id_utente = $_SESSION['id'];

                // Esegui la query per ottenere i dati del cliente
                $result = $connessione->query("SELECT * FROM utenti WHERE id = $id_utente");

                if ($result->num_rows == 1) {
                    $cliente = $result->fetch_assoc();
            ?>
                    <h1 class="titolo">Gestione Profilo</h1>
                    <table>
                        <tr>
                            <th>Nome:</th>
                            <td><?php echo $cliente['nome']; ?></td>
                            <td><a href="modifica_profilo.php"><span id="edit" class="material-symbols-outlined">edit</span></a></td>
                        </tr>
                        <tr>
                            <th>Cognome:</th>
                            <td><?php echo $cliente['cognome']; ?></td>
                            <td><a href="modifica_profilo.php"><span id="edit" class="material-symbols-outlined">edit</span></a></td>
                        </tr>
                        <tr>
                            <th>Codice fiscale:</th>
                            <td><?php echo $cliente['codice_fiscale']; ?></td>
                        </tr>
                        <tr>
                            <th>Data di nascita:</th>
                            <td><?php echo $cliente['data_di_nascita']; ?></td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td><?php echo $cliente['email']; ?></td>
                        </tr>
                        <tr>
                            <th>Password:</th>
                            <td>* * * * * * * * * *</td>
                            <td><a href="modifica_password.php"><span id="edit" class="material-symbols-outlined">key</span></a></td>

                        </tr>
                        <tr>
                            <th>Crediti:</th>
                            <td><?php echo $cliente['crediti']; ?></td>
                            <td><a href="richiesta_crediti.php"><span id="edit" class="material-symbols-outlined">add</span></a></td>
                        </tr>
                        <tr>
                            <th>Reputazione:</th>
                            <td><?php echo $cliente['reputazione']; ?></td>
                        </tr>
                        <tr>
                            <th>Cellulare:</th>
                            <td><?php echo $cliente['cellulare']; ?></td>
                            <td><a href="modifica_profilo.php"><span id="edit" class="material-symbols-outlined">edit</span></a></td>
                        </tr>
                        <tr>
                            <th>Indirizzo di residenza:</th>
                            <td><?php echo $cliente['indirizzo_di_residenza']; ?></td>
                            <td><a href="modifica_profilo.php"><span id="edit" class="material-symbols-outlined">edit</span></a></td>
                        </tr>
                    </table>
                    <?php
                    
                } else {
                    echo 'Utente non trovato';
                }
            ?>
        </div>
    </body>
</html>