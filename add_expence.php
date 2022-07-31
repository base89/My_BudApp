<?php

session_start();

if (!isset($_SESSION['isLogged'])) {

    header('Location: login.php');
    exit();
}

if (isset($_POST['inputExpenceAmount'])) {

    $isGood = true;

    $expenceAmount = $_POST['inputExpenceAmount'];
    if (is_numeric($expenceAmount)) {

        $expenceAmount = round($expenceAmount, 2);
    } else {

        $isGood = false;
        $_SESSION['error_amount'] = '<span class="text-danger f-error mt-1">Nieprawidłowa kwota wydatku</span>';
    }

    $maxAmount = 2147483647;
    if ($expenceAmount > $maxAmount) {

        $isGood = false;
        $_SESSION['error_amount'] = '<span class="text-danger f-error mt-1">Kwota jest zbyt duża</span>';
    }

    $expenceDate = $_POST['inputExpenceDate'];
    if ($expenceDate == NULL) {

        $isGood = false;
        $_SESSION['error_date'] = '<span class="text-danger f-error mt-1">Nie wprowadzono daty wydatku</span>';
    }

    $currentDate = date('Y-m-d');
    if ($expenceDate > $currentDate) {

        $isGood = false;
        $_SESSION['error_date'] = '<span class="text-danger f-error mt-1">Nieprawidłowa data wydatku</span>';
    }

    $expencePaymentMethod = $_POST['inputPaymentMethod'];
    if ($expencePaymentMethod == NULL) {

        $isGood = false;
        $_SESSION['error_method'] = '<span class="text-danger f-error mt-1">Nie wprowadzono sposobu płatności</span>';
    }

    $expenceCategory = $_POST['inputExpenceCategory'];
    if ($expenceCategory == NULL) {

        $isGood = false;
        $_SESSION['error_category'] = '<span class="text-danger f-error mt-1">Nie wprowadzono kategorii wydatku</span>';
    }

    $expenceComment = $_POST['inputExpenceComment'];
    if (strlen($expenceComment) > 100) {
        $isGood = false;
        $_SESSION['error_comment'] = '<span class="text-danger f-error mt-1">Komentarz jest zbyt długi</span>';
    }

    if ($isGood) {

        $connect = require_once "connect.php";
        $userId = $_SESSION['id'];

        try {
            
            $connection = new PDO("mysql:host={$connect['host']};dbname={$connect['db_name']};charset=utf8", $connect['db_user'], $connect['db_password'],
                        [PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    
            if ($connection) {

                $insertQuery = "INSERT INTO expences VALUES (NULL, '$userId', (SELECT id FROM expenses_category_assigned_to_users 
                WHERE user_id ='$userId' AND name ='$expenceCategory'), (SELECT id FROM payment_methods_assigned_to_users 
                WHERE user_id ='$userId' AND name ='$expencePaymentMethod'),'$expenceAmount','$expenceDate','$expenceComment')";

                if ($connection->query($insertQuery)) {

                $_SESSION['new_expence_alert'] = '<div class="container col-xl-4 col-lg-5 col-md-6 col-sm-8 my-4 mx-auto text-center">
                <div class="alert alert-success" role="alert"><h4 class="alert-heading">Potwierdzenie</h4><p>Dodano nowy wydatek!</p></div></div>';
                }
            }

        } catch (PDOException $error) {

            echo $error->getMessage();
            exit(' Wystąpił błąd! Spróbuj ponownie.');
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Your New Expence</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Boogaloo&family=Roboto:wght@300;400;700&display=swap"
        rel="stylesheet">
    <script type="text/javascript" src="script.js"></script>
</head>

<body onload="setCurrentDate()">

    <main>

        <header>
            <div class="container my-4 text-center d-none d-md-block">
                <h1 class="fw-bold bud_app_logo">My BudApp!</h1>
            </div>
        </header>

        <nav class="navbar navbar-dark bg-dark navbar-expand-md sticky-top">
            <div class="container-fluid mx-lg-5 mx-sm-3">
                <a href="#" class="navbar-brand d-block d-md-none fw-bold bud_app_logo-bar">My BudApp!</a>
                <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav_menu"
                    aria-controls="nav_menu" aria-expanded="false" aria-label="Navigation switch">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="nav_menu">
                    <div class="navbar-nav me-auto">
                        <a class="nav-item nav-link" href="./index.php"> Menu Główne </a>
                        <a class="nav-item nav-link" href="./add_income.php"> Dodaj przychód </a>
                        <a class="nav-item nav-link active" href="./add_expence.php"> Dodaj wydatek </a>
                        <a class="nav-item nav-link" href="./display_balance.php"> Przeglądaj bilans </a>
                    </div>
                    <div class="navbar-nav ms-auto">
                        <a class="nav-item nav-link" href="#"> Ustawienia </a>
                        <a class="nav-item nav-link" href="#"> Wyloguj się </a>
                    </div>
                </div>
            </div>
        </nav>

        <section>
            <?php
            if (isset($_SESSION['new_expence_alert'])) {

                echo $_SESSION['new_expence_alert'];
                unset($_SESSION['new_expence_alert']);
            }
            ?>
            <div class="container col-xl-4 col-lg-5 col-md-6 col-sm-8 border bg-white my-4 mx-auto px-4 text-center">
                <h2 class="text-wrap h2 h1-app fw-bold my-5 mx-4">Dodaj wydatek</h2>
                <form class="row g-3" method="post">
                    <div class="row justify-content-start my-2 mx-auto">
                        <label class="col-sm-3 col-form-label text-start" for="inputExpenceAmount">Kwota:</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="number" name="inputExpenceAmount" id="inputExpenceAmount"
                                min="0" step="0.01" placeholder="np. 99,99" required>
                        </div>
                        <?php
                        if (isset($_SESSION['error_amount'])) {

                            echo $_SESSION['error_amount'];
                            unset($_SESSION['error_amount']);
                        }
                        ?>
                    </div>
                    <div class="row justify-content-start my-2 mx-auto">
                        <label class="col-sm-3 col-form-label text-start" for="inputExpenceDate">Data:</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="date" name="inputExpenceDate" id="inputExpenceDate"
                                min="2000-01-01" required>
                        </div>
                        <?php
                        if (isset($_SESSION['error_date'])) {

                            echo $_SESSION['error_date'];
                            unset($_SESSION['error_date']);
                        }
                        ?>
                    </div>
                    <div class="row justify-content-start my-2 mx-auto">
                        <label class="col-sm-3 col-form-label text-start" for="inputPaymentMethod">Sposób
                            płatności:</label>
                        <div class="col-sm-9 my-auto">
                            <select class="form-control" name="inputPaymentMethod" id="inputPaymentMethod" required>
                                <option value="" class="text-center" disabled selected>-- Wybierz płatność: --</option>
                                <option value="cash">Gotówka</option>
                                <option value="debit_card">Karta debetowa</option>
                                <option value="credit_card">Karta kredytowa</option>
                            </select>
                        </div>
                        <?php
                        if (isset($_SESSION['error_method'])) {

                            echo $_SESSION['error_method'];
                            unset($_SESSION['error_method']);
                        }
                        ?>
                    </div>
                    <div class="row justify-content-start my-2 mx-auto">
                        <label class="col-sm-3 col-form-label text-start" for="inputExpenceCategory">Kategoria:</label>
                        <div class="col-sm-9">
                            <select class="form-control" name="inputExpenceCategory" id="inputExpenceCategory" required>
                                <option value="" class="text-center" disabled selected>-- Wybierz kategorię: --</option>
                                <option value="salary">Wynagrodzenie</option>
                                <option value="food">Żywność</option>
                                <option value="apartment">Mieszkanie</option>
                                <option value="transport">Transport</option>
                                <option value="telecommunication">Telekomunikacja</option>
                                <option value="healthcare">Opieka zdrowotna</option>
                                <option value="cloth">Ubranie</option>
                                <option value="hygiene">Higiena</option>
                                <option value="children">Dzieci</option>
                                <option value="entertainment">Rozrywka</option>
                                <option value="tour">Wycieczka</option>
                                <option value="training">Szkolenia</option>
                                <option value="books">Książki</option>
                                <option value="savings">Oszczędności</option>
                                <option value="retirement">Na złotą jesień, czyli emeryturę</option>
                                <option value="repayment">Spłata długów</option>
                                <option value="donation">Darowizna</option>
                                <option value="other">Inne</option>
                            </select>
                        </div>
                        <?php
                        if (isset($_SESSION['error_category'])) {

                            echo $_SESSION['error_category'];
                            unset($_SESSION['error_category']);
                        }
                        ?>
                    </div>
                    <div class="row justify-content-start my-2 mx-auto">
                        <label class="col-sm-2 col-form-label text-start" for="inputExpenceComment">Komentarz:</label>
                        <div>
                            <textarea class="form-control" name="inputExpenceComment" id="inputExpenceComment" cols="30"
                                rows="3" placeholder="Opcjonalnie..."></textarea>
                        </div>
                        <?php
                        if (isset($_SESSION['error_comment'])) {

                            echo $_SESSION['error_comment'];
                            unset($_SESSION['error_comment']);
                        }
                        ?>
                    </div>
                    <div class="row justify-content-center g-3 my-2 mx-auto mb-5">
                        <button class="btn bg-btn-app col-sm-4 col-8 mx-1" type="submit">Dodaj</button>
                        <button class="btn btn-danger col-sm-4 col-8 mx-1" type="reset">Anuluj</button>
                    </div>
                </form>
            </div>
        </section>

        <footer>

            <div class="container-fluid">
                <ul class="list-unstyled list-inline text-sm-end text-center">
                    <li class="list-inline-item">
                        <a href="#" class="text-decoration-none mx-2 text-muted f-footer-t">O nas</a>
                    </li>
                    <li class="list-inline-item">
                        <a href="#" class="text-decoration-none mx-2 text-muted f-footer-t">Pomoc</a>
                    </li>
                    <li class="list-inline-item">
                        <a href="#" class="text-decoration-none mx-2 text-muted f-footer-t">Kontakt</a>
                    </li>
                </ul>
                <div class="row">
                    <span class="col-5 mx-auto mb-4 text-muted text-center f-footer-b">© 2022 My BudApp</span>
                </div>
            </div>

        </footer>

    </main>


    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"
        integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"
        integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13"
        crossorigin="anonymous"></script>
</body>

</html>