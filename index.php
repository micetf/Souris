<?php
$nbCircuits = 17;
$noCircuit = (isset($_GET['c']) && in_array($_GET['c'], range(1, $nbCircuits))) ? (int)$_GET['c'] : 1;
$pseudo = (isset($_GET['p'])) ? htmlspecialchars($_GET['p'], ENT_QUOTES, 'UTF-8') : '';
$nomCircuit = 'parcours'.$noCircuit;
$sessionToken = bin2hex(random_bytes(16));

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
<!doctype html>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!-- Consider adding a manifest.appcache: h5bp.com/d/Offline -->
<!--[if gt IE 8]><!--> <html class="no-js" lang="fr"> <!--<![endif]-->
<head>
    <meta charset="utf-8">

    <!-- Use the .htaccess and remove these lines to avoid edge case issues.
             More info: h5bp.com/i/378 -->
<!--  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">-->

<title>Les rois de la souris (Parcours n°<?php echo $noCircuit; ?>)</title>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<meta name="description" content="Jeu d'entrainement à la manipulation de la souris. Parcours n°<?php echo $noCircuit; ?>" />
<meta name="keywords" content="souris, psychomotricité fine, TUIC, B2I" />


    <!-- Mobile viewport optimized: h5bp.com/viewport -->
    <meta name="viewport" content="width=device-width,user-scalable=no,initial-scale=1">
    <!-- Place favicon.ico and apple-touch-icon.png in the root directory: mathiasbynens.be/notes/touch-icons -->

    <link rel="stylesheet" href="css/circuit.css" type="text/css" media="screen" />

    <!-- More ideas for your <head> here: h5bp.com/d/head-Tips -->
    <link rel="canonical" href="http://micetf.fr/Souris"/>

    <!-- All JavaScript at the bottom, except this Modernizr build.
             Modernizr enables HTML5 elements & feature detects for optimal performance.
             Create your own custom Modernizr build: www.modernizr.com/download/ -->
    <script src="../library/js/modernizr.js"></script>
</head>

<body>

<div id="consignes">
<h1>Les rois de la souris</h1>
<p>Toi aussi, tu veux devenir un roi de la souris ?</p>
<br/>
<p>Une petite coccinelle se cache sous un rond vert.</p>
<p>Elle ne sortira que si tu places le pointeur sur ce rond.</p>
<p>Elle remplacera alors le pointeur.</p>
<p>En suivant le chemin bleu, tu dois amener cette petite coccinelle jusqu'au rond rouge.</p>
<br/>
<p>Attention ! Si tu quittes le chemin bleu, tu as perdu.</p>
<p>Essaie d'aller vite !</p>
<br/>
<a id="retour">retour</a>
</div>
<div id="getPseudo">
<h1>Les rois de la souris</h1>
<h2>Saisis ton pseudo !</h2>

<h2><input type="text" value="Anonyme"/> <input type="button" value="OK"/></h2>
<br/>
</div>

<div id="jsOK">
<div id="popup"><br/><h1></h1><p><input type="button" value="Recommencer"/></p></div>
<div id="circuit"></div>
<p id="chrono">0</p>
<p id="menu">
| <a id="aide">Aide</a> |
<?php
foreach (range(1, $nbCircuits) as $i) {
    ?>
 | <a class="changer <?php if ($noCircuit == $i) {
     echo 'parcours';
 } ?>"
        href="?c=<?php echo $i; ?>">
            <?php echo $i; ?>
     </a>
<?php
}
?>
 |
</p>
<p id="pseudo" title="Changer de pseudo">[Anonyme]</p>
<div id="records">
<p>Top 10 des records pour le circuit n°<?php echo $noCircuit; ?></p>
<ol>
<li></li>
</ol>
<ul>
<li>Il n'y a aucun record pour ce circuit.</li>
</ul>
</div>
</div>

<div id="jsKO"><h1>Vous devez activer le Javascript !</h1></div>
<p>
Créé par
<a href="http://www.micetf.fr" title="Accueil">MiCetF</a> (2011) -
<a id="contact" href="" title="Pour contacter le webmaster">contact</a>
</p>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="ZXVEXH5392YTY">
<input type="image" src="https://www.paypalobjects.com/fr_FR/FR/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - la solution de paiement en ligne la plus simple et la plus sécurisée !">
<img alt="" border="0" src="https://www.paypalobjects.com/fr_FR/i/scr/pixel.gif" width="1" height="1">
</form>


    <!-- scripts concatenated and minified via build script -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"
        integrity="sha256-lSjKY0/srUM9BE3dPm+c4fBo1dky2v27Gdjm2uoZaL0="
        crossorigin="anonymous"></script>
        <script type="text/javascript" src="../library/js/jquery.ui.touch-punch.min.js"></script>
        <script type="text/javascript" src="../library/js/jquery-contact.min.js"></script>
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
        <script type="text/javascript" src="js/circuit.min.js"></script>

</body>
</html>
