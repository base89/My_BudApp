<?php

session_start();

if ((!isset($_POST['email'])) || (!isset($_POST['password'])))
{
    header('Location: login.php');
    exit();
}

require 'connect.php';

$connection = @new mysqli($host, $db_user, $db_password, $db_name);

if ($connection->connect_errno != 0) {
    echo "Error: " . $connection->connect_errno;
} else {
    $email = htmlentities($_POST['email'], ENT_QUOTES, "UTF-8");
    $password = htmlentities($_POST['password'], ENT_QUOTES, "UTF-8");

    $sqlQuerySelectedUser = "SELECT * FROM users WHERE email='$email' AND password='$password'";

    if ($queryResult = @$connection->query($sqlQuerySelectedUser)) {

        $userNumbers = $queryResult->num_rows;
        if ($userNumbers > 0) {

            $_SESSION['isLogged'] = true;

            $dataRow = $queryResult->fetch_assoc();
            $_SESSION['id'] = $dataRow['id'];
            $_SESSION['username'] = $dataRow['username'];
            $_SESSION['password'] = $dataRow['password'];
            $_SESSION['email'] = $dataRow['email'];

            unset($_SESSION['error']);
            $queryResult->free_result();
            header('Location: index.php');
        } else {

            $_SESSION['error'] = '<span class="text-danger">Nieprawidłowy login lub hasło</span>';
            header('Location: login.php');
        }
    }

    $connection->close();
}
