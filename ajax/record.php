<?php

if (!function_exists('file_put_contents')) {
    function file_put_contents($filename, $data)
    {
        $f = fopen($filename, 'w');
        if (!$f) {
            return false;
        } else {
            $bytes = fwrite($f, $data);
            fclose($f);
            return $bytes;
        }
    }
}

$parcours = (isset($_POST['parcours'])) ? $_POST['parcours'] : exit();
$pseudo = (isset($_POST['pseudo'])) ? $_POST['pseudo'] : 'Anonyme';
$chrono = (isset($_POST['chrono']) && $_POST['chrono'] > 0) ? $_POST['chrono'] : 360000;
$key = (isset($_POST['key'])) ? $_POST['key'] : '';
$fichier = '../records/'.$parcours.'.txt';

$newRecord = '';
$nRecord = 0;
$ajoute = false;


if (!file_exists($fichier)) {
    file_put_contents($fichier, '');
}
if (md5("MiCetF".$chrono) === $key && $chrono != 360000) {
    $enregs = file($fichier);
    foreach ($enregs as $cle => $enreg) {
        $infos = explode(',', $enreg);
        if (!$ajoute && $infos[1] >= ($chrono / 100)) {
            $nRecord++;
            $newRecord .= $pseudo.','.($chrono / 100).PHP_EOL;
            $ajoute = true;
        }
        if ($nRecord > 9) {
            break;
        }
        $nRecord++;
        $newRecord .= $enreg;
    }
    if ($nRecord < 10 && !$ajoute) {
        $newRecord .= $pseudo.','.($chrono / 100).PHP_EOL;
    }
    file_put_contents($fichier, $newRecord);
}

$liste = '';
$enregs = file($fichier);
foreach ($enregs as $cle => $enreg) {
    $enreg = explode(',', trim($enreg));
    $liste .= '<li>'.htmlspecialchars($enreg[0]).' : '.htmlspecialchars($enreg[1]).'</li>';
}
echo $liste;
