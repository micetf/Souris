<?php

$chrono = (isset($_POST['chrono']) && $_POST['chrono'] > 0) ? $_POST['chrono'] : 360000;
echo md5("MiCetF".$chrono);
