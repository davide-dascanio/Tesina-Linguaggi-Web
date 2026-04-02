<?php
    session_start();

    // Verifica se l'amministratore è loggato
    if (isset($_SESSION['id'])) {

        $admin = $_SESSION['ammin'];
        if ($admin == 0){
            // non siamo amministratori
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
        <title>Gestione Utenti</title>
        <link rel="stylesheet" href="../css/style_standard.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
        <link rel="stylesheet" href="../css/style_header.css">
    </head>
    <body>
        <?php
            require_once('../res/header.php');
        ?>

        <div class="cont">
            <h1 class="titolo">GESTIONE UTENTI</h1>

            <?php
                // Query per ottenere gli utenti
                $sql = "SELECT id, nome, cognome, email, passwd, crediti, indirizzo_di_residenza, codice_fiscale, data_di_nascita, cellulare, ban, reputazione, ammin, gestore FROM utenti 
                ORDER BY 
                CASE 
                    WHEN ammin = 1 THEN 1
                    WHEN gestore = 1 THEN 2
                    ELSE 3
                END;";
                $result = $connessione->query($sql);


                if ($result->num_rows > 0) {

                    // Stampa la tabella degli utenti
                    echo '<table border="1">';
                    echo '<tr>';
                    echo '<th>Ruolo</th>';
                    echo '<th>Nome</th>';
                    echo '<th>Cognome</th>';
                    echo '<th>Email</th>';
                    echo '<th>Crediti</th>';
                    echo '<th>Reputazione</th>';
                    echo '<th>Indirizzo di residenza</th>';
                    echo '<th>Codice Fiscale</th>';
                    echo '<th>Data Nascita</th>';
                    echo '<th>Cellulare</th>';
                    echo '<th>Modifica Dati</th>';
                    echo '<th>Modifica Password</th>';
                    echo '<th>Ban</th>';
                    echo '</tr>';

                    while ($row = $result->fetch_assoc()) {
                        $ruolo = '';
                        if ($row['ammin'] == 1) {
                            $ruolo = 'Admin';
                        } elseif ($row['gestore'] == 1) {
                            $ruolo = 'Gestore';
                        } else {
                            $ruolo = 'Cliente';
                        }

                        // Evidenzia l'utente loggato
                        $sonoIo = ($row['id'] == $_SESSION['id']) ? '<span id="icon" class="material-symbols-outlined">how_to_reg</span> ' : '';
                            
                        echo '<tr>';
                        echo '<td><strong>' . $sonoIo . $ruolo . '</strong></td>';
                        echo '<td>' . $row['nome'] . '</td>';
                        echo '<td>' . $row['cognome'] . '</td>';
                        echo '<td>' . $row['email'] . '</td>';
                        echo '<td>' . $row['crediti'] . '</td>';
                        echo '<td>' . $row['reputazione'] . '</td>';
                        echo '<td>' . $row['indirizzo_di_residenza'] . '</td>';
                        echo '<td>' . $row['codice_fiscale'] . '</td>';
                        echo '<td style="width: 80px;">' . $row['data_di_nascita'] . '</td>';
                        echo '<td>' . $row['cellulare'] . '</td>';
                        echo '<td><a href="modifica_utente.php?id=' . $row['id'] . '"><span id="edit" class="material-symbols-outlined">edit</span></a></td>';

                        if (($row['id'] == $_SESSION['id']) && $row['ammin'] == 1){
                            echo '<td><a href="modifica_password.php?id=' . $row['id'] . '"><span id="edit" class="material-symbols-outlined">key</span></a></td>';
                        }elseif ($row['ammin'] == 1){
                            echo '<td>---</td>';
                        }else{
                            echo '<td><a href="modifica_password.php?id=' . $row['id'] . '"><span id="edit" class="material-symbols-outlined">key</span></a></td>';
                        }
                        
                        
                        if ($row['ammin'] == 1) {
                            echo '<td>---</td>';
                        }else {
                            if ($row['ban'] == 1) {
                                // Utente disattivato
                                echo '<td style="width: 50px;"><a class="go-back" href="conferma_ban.php?id=' . $row['id'] . '&ban=' . $row['ban'] . '"><span id="done" class="material-symbols-outlined">visibility_off</span> Attiva</a></td>';
                            } else {
                                // Utente attivato
                                echo '<td style="width: 50px;"><a class="go-back" href="conferma_ban.php?id=' . $row['id'] . '&ban=' . $row['ban'] . '"><span id="done" class="material-symbols-outlined">visibility</span> Disattiva</a></td>';
                            }
                        }
                        echo '</tr>';
                    }
                    echo '</table>';
                }else{
                    echo '<p class="titolo">Nessun utente trovato</p>';
                }
            ?>
        </div>
    </body>
</html>