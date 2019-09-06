<?php 

if (isset($_POST['keTxt'])) {
    $txt = $_POST['keTxt'];
    echo  password_hash($txt, PASSWORD_DEFAULT);
}