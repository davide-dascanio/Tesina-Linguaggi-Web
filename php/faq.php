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
        <title>Faq</title>
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
            if(isset($_SESSION['loggato'])){
                $cliente = $_SESSION['cliente'];
            ?>
          <?php if($cliente == 1){ ?>
                    <h1 class="titolo">Tutte le FAQ</h1>
                    <?php
                    $xmlFile = '../xml/faq.xml';
                    if (file_exists($xmlFile)) {
                        $dom = new DOMDocument();
                        $dom->preserveWhiteSpace = false;  //come fa trim() che rimuove gli spazi vuoti dal file xml (appiattisce)
                        $dom->load($xmlFile);
                        //$entries conterrà la lista di tutti i tag <entry> trovati all'interno del file faq.xml
                        $entries = $dom->getElementsByTagName('entry');
                    ?>
                        <table style="width:80vw;">
                            <thead>
                                <tr>
                                    <th>Domanda</th>
                                    <th>Risposta</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($entries as $entry) {
                                    $id = $entry->getAttribute('id');
                                    //$question conterrà la lista di tutti i tag <question> trovati all'interno della generica $entry
                                    //(che sappiamo in realtà essere uno solo)
                                    $question = $entry->getElementsByTagName('question');
                                    //$answer conterrà la lista di tutti i tag <answer> trovati all'interno della generica $entry
                                    //(che sappiamo in realtà essere una solo)
                                    $answer = $entry->getElementsByTagName('answer');
                                ?>
                                    <tr>
                                        <!--Si usa item(0) perché normalmente in ogni entry c'è una sola domanda e una sola risposta, 
                                            quindi si prende direttamente il primo (e unico) elemento-->
                                        <td><strong><?php echo nl2br($question->item(0)->nodeValue); ?></strong></td>
                                        <td><p><strong><?php echo nl2br($answer->item(0)->nodeValue); ?></strong></p></td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    <?php
                    }else {
                        echo "Errore: Il file XML delle FAQ non esiste.";
                    }
                } else{
                    //siamo gestori o amministratori
                    ?>
                    <h1 class="titolo">Inserisci una nuova FAQ</h1>
                    <table style="width:80vw;" class="up">
                        <tr>
                            <th>Invia una domanda e una risposta FAQ</th>
                        </tr>
                        <tr>
                            <td>
                                <form action="../res/processa_faq.php" method="post">
                                    <textarea style="width:500px; height:100px; resize:none; vertical-align:top;" class="input" name="faq_question" placeholder="Inserisci la tua domanda..." required></textarea>
                                    <textarea style="width:500px; height:100px; resize:none; vertical-align:top;" class="input" name="faq_answer" placeholder="Inserisci la tua risposta..." required></textarea>
                                    <button class="btn" type="submit">Invia FAQ</button>
                                </form>
                            </td>
                        </tr>
                    </table>
                    <h1 class="titolo">Tutte le FAQ</h1>
                    <?php
                    $xmlFile = '../xml/faq.xml';
                    if (file_exists($xmlFile)) {
                        $dom = new DOMDocument();
                        $dom->load($xmlFile);
                        $entries = $dom->getElementsByTagName('entry');
                    ?>
                        <table style="width:80vw;">
                            <thead>
                                <tr>
                                    <th>Elimina</th>
                                    <th>Domanda</th>
                                    <th>Risposta</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($entries as $entry) {
                                    $id = $entry->getAttribute('id');
                                    $question = $entry->getElementsByTagName('question');
                                    $answer = $entry->getElementsByTagName('answer');
                                ?>
                                    <tr>
                                        <td>
                                            <a href="../res/elimina_faq.php?id=<?php echo $id; ?>"><span id="done" class="material-symbols-outlined">delete</span></a>
                                        </td>
                                        <td>
                                            <p><strong><?php echo nl2br($question->item(0)->nodeValue); ?></strong></p>
                                            <form action="../res/processa_domanda.php" method="post">
                                                <input type='hidden' name='faq_id' value='<?php echo $id; ?>'>
                                                <textarea style="width:500px; height:100px; resize:none; vertical-align:top;" class="input" name="question" placeholder="Modifica domanda..."required></textarea>
                                                <button class="btn" type="submit">Modifica</button>
                                            </form>
                                        </td>
                                        <td>                                   
                                            <p><strong><?php echo nl2br($answer->item(0)->nodeValue); ?></strong></p>
                                            <form action="../res/processa_risposta.php" method="post">
                                                <input type='hidden' name='faq_id' value='<?php echo $id; ?>'>
                                                <textarea style="width:500px; height:100px; resize:none; vertical-align:top;" class="input" name="answer" placeholder="Modifica risposta..."required></textarea>
                                                <button class="btn" type="submit">Modifica</button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                        <?php
                    } else {
                        echo "Errore: Il file XML delle FAQ non esiste.";
                    }
                } 
            } else {
                //siamo visitatori
                ?>  
                <h1 class="titolo">Tutte le FAQ</h1>
                <?php
                $xmlFile = '../xml/faq.xml';
                if (file_exists($xmlFile)) {
                    $dom = new DOMDocument();
                    $dom->load($xmlFile);
                    $entries = $dom->getElementsByTagName('entry');
                ?>
                    <table style="width:80vw;">
                        <thead>
                            <tr>
                                <th>Domanda</th>
                                <th>Risposta</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($entries as $entry) {
                                $id = $entry->getAttribute('id');
                                $question = $entry->getElementsByTagName('question');
                                $answer = $entry->getElementsByTagName('answer');
                            ?>
                                <tr>
                                    <td><strong><?php echo nl2br($question->item(0)->nodeValue); ?></strong></td>
                                    <td><p><strong><?php echo nl2br($answer->item(0)->nodeValue); ?></strong></p></td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                    <?php
                } else {
                    echo "Errore: Il file XML delle FAQ non esiste.";
                }
            }
            ?>
        </div>
    </body>
</html>