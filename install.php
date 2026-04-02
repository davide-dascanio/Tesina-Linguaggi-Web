<?php
    error_reporting(E_ALL);

    //dati relativi al db
    require_once("res/datigenerali.php");

    // Effettuazione della connessione al database
    $connessione = new mysqli($host, $username, $password);

    // Controllo della connessione
    if (mysqli_connect_errno()) {
        printf("Problemi con la connessione al db: %s\n", mysqli_connect_error());
        exit();
    }


    // Creazione del database
    $queryCreazioneDatabase = "CREATE DATABASE IF NOT EXISTS $db_name";

    // Il risultato della query va in $resultQ
    $resultQ = mysqli_query($connessione, $queryCreazioneDatabase);
    if ($resultQ) {
        printf("Database creato <br />\n");
    }
    else{
        printf("Errore nella creazione del database <br />\n");
        exit();
    }



    // Selezione del database
    $connessione->select_db($db_name);


    // Creazione tabella 'utenti'
    $sqlQuery = "CREATE TABLE if not exists $utenti_table_name ( 
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(50) NOT NULL,
        cognome VARCHAR(50) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        passwd VARCHAR(255) NOT NULL,
        crediti FLOAT DEFAULT 0,
        data_di_nascita DATE NOT NULL,
        indirizzo_di_residenza VARCHAR(255) NOT NULL,
        codice_fiscale VARCHAR(16) NOT NULL UNIQUE,
        cellulare VARCHAR(20) NOT NULL UNIQUE,
        cliente INT NOT NULL,
        gestore INT NOT NULL,
        ammin INT NOT NULL,
        reputazione INT NOT NULL,
        ban INT DEFAULT 0,
        data_registrazione DATE NOT NULL 
    )"; 


    // Verifica creazione tabella 'utenti'
    if ($resultQ = mysqli_query($connessione, $sqlQuery)){
        printf("La tabella 'utenti' è stata creata <br />\n");
        header("Location:php/index.php");
    }else {
        printf("Errore nella creazione della tabella 'utenti'! <br />\n");
        exit();
    }


    // Popolamento della tabella 'utenti'
    $sql = "INSERT INTO $utenti_table_name (`id`,`nome`,`cognome`,`email`,`passwd`,`crediti`,`data_di_nascita`,`indirizzo_di_residenza`,`codice_fiscale`,`cellulare`,`cliente`,`ammin`,`gestore`,`reputazione`,`ban`,`data_registrazione`) VALUES
    ('1', 'Davide', 'D\'Ascanio', 'davidedascanio@gmail.com', '" . password_hash('Davide1234!', PASSWORD_DEFAULT) . "','0','1998-06-14','Via Muzio Clementi','DSCDVD95H14H501R','3339553001','0','1','0','11','0','2022-01-14'),
    ('2','Marco','Bianchi','marcobianchi@gmail.com', '" . password_hash('Marco1234!', PASSWORD_DEFAULT) . "','0','2000-03-22','Via Roma 10','BNCMRC00C22H501X','3381234567','0','0','1','11','0','2021-02-10'),
    ('3','Mario','Rossi','mariorossi@gmail.com', '" . password_hash('Mario1234!', PASSWORD_DEFAULT) . "','0','1998-04-11','Via A.Stradivari 4','RSSMRA98D11H501K','3293321366','0','0','1','11','0','2022-06-22'),
    ('4','Paolo','Verdi','paoloverdi@gmail.com', '" . password_hash('Paolo1234!', PASSWORD_DEFAULT) . "','0','2001-06-20','Via Ugo La Malfa 4','VRDPLA01H20H501Y','3339553256','1','0','0','1','0','2021-01-14'),
    ('5','Giulia','Neri','giulianeri@gmail.com', '" . password_hash('Giulia1234!', PASSWORD_DEFAULT) . "','0','2003-03-15','Via Caligola','NREGLI03C55H501N','3472218934','1','0','0','1','0','2025-09-20'),
    ('6','Luca','Paoli','luca@gmail.com', '" . password_hash('Luca1234!', PASSWORD_DEFAULT) . "','0','2002-07-20','Via Andrea Doria','PLALCU02L20H501W','3339553123','1','0','0','1','0','2022-04-03')";



    // Verifica popolamento tabella 'utenti'
    if ($resultQ = mysqli_query($connessione, $sql))
        printf("Tabella 'utenti' popolata correttamente <br />\n");
    else {
        printf("Errore durante il popolamento della tabella 'utenti': <br />\n");
        exit();
    }


    //chiudiamo la connessione
    $connessione->close();

?>