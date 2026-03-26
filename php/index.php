<?php
    session_start();
?>

<?xml version = "1.0"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Home</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
        <link rel="stylesheet" href="../css/style_index.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
        <link rel="stylesheet" href="../css/style_header.css">
    </head>
    <body>
        <?php
            require_once('../res/header.php');
        ?>
        <footer>
            <div class="col">
                <h4>Informazioni sul sito</h4>
                <li>Servizi e supporto</li>
                <li>Supporto tecnico</li>
                <li>Consulenza</li>
                <li>Servizio clienti</li>
            </div>
                
            <div class="col">
                <h4>Servizi</h4>
                <li>About Us</li> 
                <li>Chi siamo?</li>
                <li>Dove siamo?</li>
                <li>Contatti</li>
            </div>
                
            <div class="col">
                <h4>Supporto</h4>
                <li>Traccia il tuo ordine</li>
                <li>Ritiro usato</li>
                <li>Verifica validità</li>
            </div>
            
            <div class="col">
                <h4>Seguici su</h4>
                <div class="social-media">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-github"></i></a>
                    <a href="#"><i class="fab fa-whatsapp"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>
        </footer>
    </body>
</html>