<?php

$chrono = (isset($_POST['chrono']) && $_POST['chrono'] > 0) ? $_POST['chrono'] : 360000;
$token = isset($_POST['token']) ? $_POST['token'] : '';
echo hash_hmac('sha256', "MiCetF".$chrono, $token);
