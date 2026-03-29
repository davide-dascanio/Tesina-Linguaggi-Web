<?php
    //dati relativi al db
    require_once("datigenerali.php");

    //esecuzione del tentativo di connessione al DB creato
    $connessione = new mysqli($host, $username, $password, $db_name);
    
    //controllo della connessione
    if (mysqli_connect_errno()) {
        printf("Problemi con la connessione al db: %s\n", mysqli_connect_error($connessione));
        exit();
    }
?>