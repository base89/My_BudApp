<?php

session_start();

if (!isset($_SESSION['isLogged'])) {

    header('Location: login.php');
    exit();
}

if (isset($_POST['inputIncomeAmount'])) {

    $isGood = true;

    $incomeAmount = $_POST['inputIncomeAmount'];
    if (is_numeric($incomeAmount)) {

        $incomeAmount = round($incomeAmount, 2);
    } else {

        $isGood = false;
        $_SESSION['error_amount'] = '<span class="text-danger f-error mt-1">Nieprawidłowa kwota przychodu</span>';
    }

    $maxAmount = 2147483647;
    if ($incomeAmount > $maxAmount) {

        $isGood = false;
        $_SESSION['error_amount'] = '<span class="text-danger f-error mt-1">Kwota jest zbyt duża</span>';
    }

    $incomeDate = $_POST['inputIncomeDate'];
    if ($incomeDate == NULL) {

        $isGood = false;
        $_SESSION['error_date'] = '<span class="text-danger f-error mt-1">Nie wprowadzono daty przychodu</span>';
    }

    $currentDate = date('Y-m-d');
    if ($incomeDate > $currentDate) {

        $isGood = false;
        $_SESSION['error_date'] = '<span class="text-danger f-error mt-1">Nieprawidłowa data przychodu</span>';
    }

    $incomeCategory = $_POST['inputIncomeCategory'];
    if ($incomeCategory == NULL) {

        $isGood = false;
        $_SESSION['error_category'] = '<span class="text-danger f-error mt-1">Nie wprowadzono kategorii przychodu</span>';
    }

    $incomeComment = $_POST['inputIncomeComment'];
    if (strlen($incomeComment) > 100) {
        $isGood = false;
        $_SESSION['error_comment'] = '<span class="text-danger f-error mt-1">Komentarz jest za długi</span>';
    }

    if ($isGood) {

        $connect = require_once "connect.php";
        $userId = $_SESSION['id'];

        try {
            
            $connection = new PDO("mysql:host={$connect['host']};dbname={$connect['db_name']};charset=utf8", $connect['db_user'], $connect['db_password'],
                        [PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    
            if ($connection) {

                $insertQuery = "INSERT INTO incomes VALUES (NULL, '$userId', (SELECT id FROM incomes_category_assigned_to_users 
                WHERE user_id ='$userId' AND name ='$incomeCategory'),'$incomeAmount','$incomeDate','$incomeComment')";

                if ($connection->query($insertQuery)) {

                $_SESSION['new_income_alert'] = '<div class="container col-xl-4 col-lg-5 col-md-6 col-sm-8 my-4 mx-auto text-center">
                <div class="alert alert-success" role="alert"><h4 class="alert-heading">Potwierdzenie</h4><p>Dodano nowy dochód!</p></div></div>';
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
    <title>Add Your New Income</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Boogaloo&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
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
                <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav_menu" aria-controls="nav_menu" aria-expanded="false" aria-label="Navigation switch">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="nav_menu">
                    <div class="navbar-nav me-auto">
                        <a class="nav-item nav-link" href="./index.php"> Menu Główne </a>
                        <a class="nav-item nav-link active" href="./add_income.php"> Dodaj przychód </a>
                        <a class="nav-item nav-link" href="./add_expence.php"> Dodaj wydatek </a>
                        <a class="nav-item nav-link" href="./display_balance.php"> Przeglądaj bilans </a>
                    </div>
                    <div class="navbar-nav ms-auto">
                        <a class="nav-item nav-link" href="#"> Ustawienia </a>
                        <a class="nav-item nav-link" href="<?php
                                                            echo 'logout.php'
                                                            ?>"> Wyloguj się </a>
                    </div>
                </div>
            </div>
        </nav>

        <section>
            <?php
            if (isset($_SESSION['new_income_alert'])) {

                echo $_SESSION['new_income_alert'];
                unset($_SESSION['new_income_alert']);
            }
            ?>
            <div class="container col-xl-4 col-lg-5 col-md-6 col-sm-8 border bg-white my-4 mx-auto px-4 text-center">
                <h2 class="text-wrap h2 h1-app fw-bold my-5 mx-4">Dodaj przychód</h2>
                <form class="row g-3" method="post">
                    <div class="row justify-content-start my-2 mx-auto">
                        <label class="col-sm-3 col-form-label text-start" for="inputIncomeAmount">Kwota:</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="number" name="inputIncomeAmount" id="inputIncomeAmount" min="0" step="0.01" placeholder="np. 99,99" required>
                        </div>
                        <?php
                        if (isset($_SESSION['error_amount'])) {

                            echo $_SESSION['error_amount'];
                            unset($_SESSION['error_amount']);
                        }
                        ?>
                    </div>
                    <div class="row justify-content-start my-2 mx-auto">
                        <label class="col-sm-3 col-form-label text-start" for="inputIncomeDate">Data:</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="date" name="inputIncomeDate" id="inputIncomeDate" min="2000-01-01" required>
                        </div>
                        <?php
                        if (isset($_SESSION['error_date'])) {

                            echo $_SESSION['error_date'];
                            unset($_SESSION['error_date']);
                        }
                        ?>
                    </div>
                    <div class="row justify-content-start my-2 mx-auto">
                        <label class="col-sm-3 col-form-label text-start" for="inputIncomeCategory">Kategoria:</label>
                        <div class="col-sm-9">
                            <select class="form-control" name="inputIncomeCategory" id="inputIncomeCategory" required>
                                <option value="" class="text-center" disabled selected>-- Wybierz kategorię: --</option>
                                <option value="Salary">Wynagrodzenie</option>
                                <option value="Interest">Inwestycje</option>
                                <option value="Allegro">Sprzedaż</option>
                                <option value="Another Incomes">Inne</option>
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
                        <label class="col-sm-2 col-form-label text-start" for="inputIncomeComment">Komentarz:</label>
                        <div>
                            <textarea class="form-control" name="inputIncomeComment" id="inputIncomeComment" cols="30" rows="3" placeholder="Opcjonalnie..."></textarea>
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


    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
</body>

</html>