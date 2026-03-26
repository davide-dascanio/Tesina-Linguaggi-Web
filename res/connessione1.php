<?php
    //dati relativi al db e alle tabelle da usare negli script che includono questo file
    $db_name = "home_av_store";
    $host = "localhost";
    $username = "root";
    $password = "root";
    $utenti_table_name = "utenti";

    //esecuzione del tentativo di connessione al DB creato
    $connessione = new mysqli($host, $username, $password, $db_name);
    
    //controllo della connessione
    if (mysqli_connect_errno()) {
        printf("Problemi con la connessione al db: %s\n", mysqli_connect_error($connessione));
        exit();
    }
?>