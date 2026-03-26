<?php

    $host = "localhost";
    $username = "root";
    $password = "root";
    $db_name = "home_av_store";

    //effettuazione della connessione al database
    $connessione = new mysqli($host, $username, $password);

    //controllo della connessione
    if (mysqli_connect_errno()) {
        printf("Problemi con la connessione al db: %s\n", mysqli_connect_error());
        exit();
    }


    // creazione del database
    $queryCreazioneDatabase = "CREATE DATABASE IF NOT EXISTS $db_name";

    // il risultato della query va in $resultQ
    $resultQ = mysqli_query($connessione, $queryCreazioneDatabase);
    if ($resultQ) {
        printf("Database creato <br />\n");
    }
    else{
        printf("Errore nella creazione del database <br />\n");
        exit();
    }

    //chiudiamo la connessione
    $connessione->close();

    //e la riapriamo con il collegamento alla base di dati
    require_once("connessione1.php")

?>