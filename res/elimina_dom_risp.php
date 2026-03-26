<?php
    session_start();

    if ($_SERVER["REQUEST_METHOD"] === "GET") {

        // Carica il file XML
        $xmlFile = '../xml/catalogo_prodotti.xml';
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->load($xmlFile);
        

        if (isset($_GET['id_domanda']) && !isset($_GET['id_risposta'])) {

            // ── ELIMINAZIONE DOMANDA ──────────────────────────────────────
            $id_domanda = $_GET['id_domanda'];
            $id_prodotto = $_GET['id_prodotto'];
            $nome = $_GET['nome'];
            $tipologia = $_GET['tipologia'];

            // Usiamo XPath per trovare la domanda da eliminare
            // XPath: cerca in TUTTO il documento (//) un elemento <domanda>
            // che abbia un figlio <id_domanda> con valore uguale a $id_domanda
            // Struttura XML cercata:
            //   <domanda>
            //       <id_domanda>65ecf1ae5376c</id_domanda>  ← questo
            //       ...
            //   </domanda>
            $xpath = new DOMXPath($dom);
            $query = "//domanda[id_domanda='{$id_domanda}']";
            $domandaNodes = $xpath->query($query);

            // Verifichiamo che il nodo sia stato trovato
            if ($domandaNodes->length > 0) {
                
                // .item(0) serve per estrarre il nodo dalla lista
                $domandaNode = $domandaNodes->item(0);

                // removeChild vuole il nodo PADRE come riferimento,
                // quindi risaliamo con ->parentNode (cioè <domande>)
                // e da lì rimuoviamo il figlio trovato
                $domandaNode->parentNode->removeChild($domandaNode);

                // Salva sul file XML
                $dom->save($xmlFile);

                $_SESSION['successo_eliminazione'] = 'true';
                header("Location: ../php/lista_domande.php?id_prodotto=" . $id_prodotto . "&nome=" . $nome . "&tipologia=" . $tipologia);
                exit();
            }

        } elseif (isset($_GET['id_risposta'])) {

            // ── ELIMINAZIONE RISPOSTA ─────────────────────────────────────
            $id_risposta = $_GET['id_risposta'];
            $id_prodotto = $_GET['id_prodotto'];
            $nome = $_GET['nome'];
            $tipologia = $_GET['tipologia'];
            
            // Usiamo XPath per trovare la risposta da eliminare
            $xpath = new DOMXPath($dom);

            // PASSO 1 — trova la <domanda> PADRE che contiene questa risposta.
            // XPath: cerca una <domanda> che abbia, dentro il percorso
            // ./risposte/risposta, un figlio <id_risposta> con quel valore.
            // Struttura XML cercata:
            //   <domanda>                           ← vogliamo questo nodo
            //       <risposte>
            //           <risposta>
            //               <id_risposta>xyz</id_risposta>  ← condizione
            //           </risposta>
            //       </risposte>
            //   </domanda>
            $domandaXPath = "//domanda[./risposte/risposta[id_risposta='{$id_risposta}']]";
            $domandaNodeList = $xpath->query($domandaXPath);


            if ($domandaNodeList->length > 0) {

                // PASSO 2 — ora che abbiamo la <domanda> giusta, cerchiamo
                // la <risposta> esatta AL SUO INTERNO (secondo parametro di
                // query = nodo di contesto da cui partire, invece di tutto il doc)
                // Il ./ davanti significa "a partire da questo nodo"
                $risposteXPath = "./risposte/risposta[id_risposta='{$id_risposta}']";
                $rispostaNodeList = $xpath->query($risposteXPath, $domandaNodeList->item(0));

                if ($rispostaNodeList->length > 0) {

                    $rispostaNode = $rispostaNodeList->item(0);

                    // Risale al padre <risposte> e rimuove il figlio <risposta>
                    $rispostaNode->parentNode->removeChild($rispostaNode);


                    $dom->save($xmlFile);

                    $_SESSION['successo_eliminazione'] = 'true';
                    header("Location: ../php/lista_domande.php?id_prodotto=" . $id_prodotto . "&nome=" . $nome . "&tipologia=" . $tipologia);
                    exit();
                } else {
                    echo "La risposta con id_risposta '{$id_risposta}' non trovata associata a quella domanda";
                }
            } else { 
                echo "Domanda contenente la risposta con id_risposta '{$id_risposta}' non trovata";
            }
        }
    }
?>
