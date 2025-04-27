<?php
$nbCircuits = 17;
$noCircuit = (isset($_GET['c']) && in_array($_GET['c'], range(1, $nbCircuits))) ? (int)$_GET['c'] : 1;
$pseudo = (isset($_GET['p'])) ? htmlspecialchars($_GET['p'], ENT_QUOTES, 'UTF-8') : '';
$nomCircuit = 'parcours'.$noCircuit;
$sessionToken = bin2hex(openssl_random_pseudo_bytes(16));

$img = imagecreatefrompng('images/'.$nomCircuit.'.png');
$bitmap = array();
foreach (range(0, imagesx($img) - 1) as $x) {
    $bitmap[$x] = array();
    foreach (range(0, imagesy($img) - 1) as $y) {
        $rgb = imagecolorat($img, $x, $y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;
        if ($r == 255 && $g == 255 && $b == 255) {
            $bitmap[$x][$y] = 0;
        } elseif ($r == 255 && $g == 0 && $b == 0) {
            $bitmap[$x][$y] = 3;
        } elseif ($r == 0 && $g == 255 && $b == 0) {
            $bitmap[$x][$y] = 1;
        } else {
            $bitmap[$x][$y] = 2;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Les rois de la souris (Parcours n°<?php echo $noCircuit; ?>)</title>
    <meta name="description" content="Jeu d'entrainement à la manipulation de la souris. Parcours n°<?php echo $noCircuit; ?>" />
    <meta name="keywords" content="souris, psychomotricité fine, TUIC, B2I" />
    <link rel="canonical" href="http://micetf.fr/Souris"/>
    
    <!-- Styles et polices -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/circuit.css" type="text/css" media="screen" />
    
    <!-- Modernizr pour détection des fonctionnalités -->
    <script src="../library/js/modernizr.js"></script>
</head>

<body>
    <!-- Section consignes -->
    <div id="consignes">
        <h1>Les rois de la souris</h1>
        <div class="instructions">
            <p>Toi aussi, tu veux devenir un roi de la souris ?</p>
            <p>Une petite coccinelle se cache sous un rond vert. Elle ne sortira que si tu places le pointeur sur ce rond.</p>
            <p>Elle remplacera alors le pointeur. En suivant le chemin bleu, tu dois amener cette petite coccinelle jusqu'au rond rouge.</p>
            <p>Attention ! Si tu quittes le chemin bleu, tu as perdu.</p>
            <p>Essaie d'aller vite !</p>
        </div>
        <a id="retour" class="btn-primary"><i class="fas fa-arrow-left"></i> Retour au jeu</a>
    </div>

    <!-- Section saisie pseudo -->
    <div id="getPseudo">
        <h1>Les rois de la souris</h1>
        <h2>Saisis ton pseudo !</h2>
        <h2>
            <input type="text" value="Anonyme"/> 
            <input type="button" value="OK" class="btn-primary"/>
        </h2>
    </div>

    <!-- Section principale du jeu -->
    <div id="jsOK">
        <div id="popup">
            <br/>
            <h1></h1>
            <p><input type="button" value="Recommencer" class="btn-primary"/></p>
        </div>
        
        <div id="circuit"></div>
        
        <p id="chrono">0</p>
        
        <p id="personal-record" style="display:none;"></p>
        
        <p id="menu">
            | <a id="aide" href="#"><i class="fas fa-question-circle"></i> Aide</a> |
            
            <?php foreach (range(1, $nbCircuits) as $i): ?>
                <a class="changer <?php echo ($noCircuit == $i) ? 'parcours' : ''; ?>" 
                   href="?c=<?php echo $i; ?>">
                    <?php echo $i; ?>
                </a> |
            <?php endforeach; ?>
        </p>
        
        <p id="pseudo" title="Changer de pseudo">[Anonyme]</p>
        
        <div id="records">
            <p><i class="fas fa-trophy"></i> Top 10 des records pour le circuit n°<?php echo $noCircuit; ?></p>
            <ol>
                <li></li>
            </ol>
            <ul>
                <li>Il n'y a aucun record pour ce circuit.</li>
            </ul>
        </div>
    </div>

    <!-- Message d'erreur si JavaScript désactivé -->
    <div id="jsKO">
        <h1>Vous devez activer le Javascript !</h1>
    </div>

    <p>
        Créé par <a href="http://www.micetf.fr" title="Accueil">MiCetF</a> (2011) -
        <a id="contact" href="#" title="Pour contacter le webmaster">contact</a>
    </p>
    
    <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
        <input type="hidden" name="cmd" value="_s-xclick">
        <input type="hidden" name="hosted_button_id" value="ZXVEXH5392YTY">
        <input type="image" src="https://www.paypalobjects.com/fr_FR/FR/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - la solution de paiement en ligne la plus simple et la plus sécurisée !">
        <img alt="" border="0" src="https://www.paypalobjects.com/fr_FR/i/scr/pixel.gif" width="1" height="1">
    </form>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js" integrity="sha256-lSjKY0/srUM9BE3dPm+c4fBo1dky2v27Gdjm2uoZaL0=" crossorigin="anonymous"></script>
    <script src="../library/js/jquery.ui.touch-punch.min.js"></script>
    <script src="../library/js/jquery-contact.min.js"></script>
    <script type="text/javascript">
        var parcours = '<?php echo $nomCircuit; ?>',
            pilote = '<?php echo $pseudo; ?>',
            sessionToken = '<?php echo $sessionToken; ?>',
            zBitmap = new Array();
        <?php
        foreach ($bitmap as $x => $ys) {
            echo "zBitmap[$x]=new Array(".implode(',', $ys).");";
        }
?>
    </script>
    <script src="js/circuit.min.js"></script>
</body>
</html>