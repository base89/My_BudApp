<?php

session_start();

if ((!isset($_POST['email'])) || (!isset($_POST['password']))) {

    header('Location: login.php');
    exit();
}

$connect = require_once 'connect.php';

try {

    $connection = new PDO("mysql: host = {$connect['host']}; dbname = {$connect['db_name']}; charset = utf8", $connect['db_user'], $connect['db_password'],
                [PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    if ($connection) {

        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'];        

        $queryResult = $connection->prepare('SELECT * FROM users WHERE email = :email');
        $queryResult->bindValue(':email', $email, PDO::PARAM_STR);

        if ($queryResult->execute()) {

            $userNumbers = $queryResult->rowCount();
            if ($userNumbers > 0) {

                $dataRow = $queryResult->fetch();

                if (password_verify($_POST['password'], $dataRow['password'])) {

                    $_SESSION['isLogged'] = true;
                    $_SESSION['id'] = $dataRow['id'];
                    $_SESSION['username'] = $dataRow['username'];
                    $_SESSION['password'] = $dataRow['password'];
                    $_SESSION['email'] = $dataRow['email'];

                    unset($_SESSION['error']);
                    header('Location: index.php');
                } else {

                    $_SESSION['error'] = '<span class="text-danger f-error mt-1">Nieprawidłowy login lub hasło</span>';
                    header('Location: login.php');
                }
            } else {

                $_SESSION['error'] = '<span class="text-danger f-error mt-1">Nieprawidłowy login lub hasło</span>';
                header('Location: login.php');
            }
        }
    }
} catch (PDOException $error) {

    echo $error->getMessage();
    exit(' Wystąpił błąd podczas logowania! Spróbuj ponownie.');
}
