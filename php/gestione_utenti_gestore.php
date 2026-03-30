<?php
    session_start();

    // Verifica se il gestore è loggato
    if (isset($_SESSION['id'])) {

        $gestore = $_SESSION['gestore'];
        if ($gestore == 0){
            // non siamo gestori
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
            <h1 class="titolo">PROFILI UTENTI</h1>

            <?php
                // Query per ottenere gli utenti
                $sql = "SELECT id, nome, cognome, crediti, email, data_registrazione, reputazione, ammin, gestore FROM utenti 
                ORDER BY
                CASE 
                    WHEN ammin = 1 THEN 1
                    WHEN gestore = 1 THEN 2
                    ELSE 3
                END";
                $result = $connessione->query($sql);



                if ($result->num_rows > 0) {

                    // Stampa la tabella degli utenti
                    echo '<table border="1">';
                    echo '<tr>';
                    echo '<th>Ruolo</th>';
                    echo '<th>Nome</th>';
                    echo '<th>Cognome</th>';
                    echo '<th>Email</th>';
                    echo '<th>Data Registrazione</th>';
                    echo '<th>Reputazione</th>';
                    echo '<th>Crediti</th>';
                    echo '<th>Storico Acquisti</th>';
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
                        echo '<td>' . $row['data_registrazione'] . '</td>';
                        echo '<td>' . $row['reputazione'] . '</td>';
                        echo '<td>' . $row['crediti'] . '</td>';
                        echo '<td>';

                        // Mostra il bottone storico solo per i clienti
                        if($row['ammin'] == 0 && $row['gestore'] == 0){
                            echo '<form action="storico_acquisti_gestore.php" method="post">';
                            echo '<input type="hidden" name="id" value="' . $row['id'] . '"/>';
                            echo '<input type="hidden" name="email" value="' . $row['email'] . '"/>';
                            echo '<button class="done" type="submit"><span id="done" class="material-symbols-outlined">shopping_bag</span></button>';
                            echo '</form>';
                        }else{
                            echo '-';
                        }
                        echo '</td>';
                        echo '</tr>';    
                    }
                    echo '</table>';
                } else {
                    echo '<p class="titolo">Nessun utente trovato</p>';
                }
            ?>
        </div>
    </body>
</html>