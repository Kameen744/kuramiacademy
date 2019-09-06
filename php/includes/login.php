<?php
    if (isset($_POST['User_Login'])) {
        include_once 'database.php';
        $db = new Database();

        $User_Name = $db->validation($_POST['User_Name']);
        $User_Password = $db->validation($_POST['User_Password']);

        $getUser = $db->getRow('SELECT * FROM `admin` WHERE `User_Name` = ?', [$User_Name]);
        if (password_verify($User_Password, $getUser['Password'])) {
            session_start();
            $_SESSION['Admin_Name'] = $getUser['User_Name'];
            header("Location: ../admin.php");
        } else {
            echo "<script>alert('Access denied. Wrong username or password'); window.location = '../../index.html';</script>";
        }
    } 