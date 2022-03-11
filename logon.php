<?php

session_start();

if ((!isset($_POST['email'])) || (!isset($_POST['password']))) {

    header('Location: login.php');
    exit();
}

require 'connect.php';
mysqli_report(MYSQLI_REPORT_STRICT);

try {

    $connection = new mysqli($host, $db_user, $db_password, $db_name);

    if ($connection->connect_errno != 0) {

        throw new Exception(mysqli_connect_errno());
    } else {

        $email = $_POST['email'];
        $password = $_POST['password'];

        $email = htmlentities($_POST['email'], ENT_QUOTES, "UTF-8");

        if ($queryResult = $connection->query(sprintf(
            "SELECT * FROM users WHERE email='%s'",
            mysqli_real_escape_string($connection, $email)
        ))) {

            $userNumbers = $queryResult->num_rows;
            if ($userNumbers > 0) {

                $dataRow = $queryResult->fetch_assoc();

                if (password_verify($_POST['password'], $dataRow['password'])) {

                    $_SESSION['isLogged'] = true;

                    $_SESSION['id'] = $dataRow['id'];
                    $_SESSION['username'] = $dataRow['username'];
                    $_SESSION['password'] = $dataRow['password'];
                    $_SESSION['email'] = $dataRow['email'];

                    unset($_SESSION['error']);
                    $queryResult->free_result();
                    header('Location: index.php');
                } else {

                    $_SESSION['error'] = '<span class="text-danger f-error mt-1">Nieprawidłowy login lub hasło</span>';
                    header('Location: login.php');
                }
            } else {

                $_SESSION['error'] = '<span class="text-danger f-error mt-1">Nieprawidłowy login lub hasło</span>';
                header('Location: login.php');
            }
        } else {

            throw new Exception($connection->error);
        }

        $connection->close();
    }
} catch (Exception $error) {

    echo '<span class="text-danger">Błąd serwera. Przepraszamy za trudności i prosimy zarejestrować się w innym terminie.</span>';
    echo '<br />Informacja developerska: ' . $error;
}
