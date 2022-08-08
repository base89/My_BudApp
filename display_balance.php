<?php

session_start();

if (!isset($_SESSION['isLogged'])) {

    header('Location: login.php');
    exit();
}

if (!isset($_SESSION['periodStartDate']) && !isset($_SESSION['periodEndDate'])) {

    $_SESSION['formPeriod'] = "currentMonth";

    header('Location: balance_form.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Your Finance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Boogaloo&family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
</head>

<body>

    <main>

        <header>
            <div class="container my-4 text-center d-none d-md-block">
                <h1 class="fw-bold bud_app_logo">My BudApp!</h1>
            </div>
        </header>

        <nav class="navbar navbar-dark bg-dark navbar-expand-md sticky-top">
            <div class="container-fluid mx-lg-5 mx-sm-3">
                <a href="./index.html" class="navbar-brand d-block d-md-none fw-bold bud_app_logo-bar">My BudApp!</a>
                <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav_menu" aria-controls="nav_menu" aria-expanded="false" aria-label="Navigation switch">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="nav_menu">
                    <div class="navbar-nav me-auto">
                        <a class="nav-item nav-link" href="./index.php"> Menu Główne </a>
                        <a class="nav-item nav-link" href="./add_income.php"> Dodaj przychód </a>
                        <a class="nav-item nav-link" href="./add_expence.php"> Dodaj wydatek </a>
                        <a class="nav-item nav-link active" href="./display_balance.php"> Przeglądaj bilans </a>
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

        <section class="row d-flex flex-row-reverse">
            <div class="container-fluid col-lg-3 col-sm-6 col-9 mx-auto p-0">
                <div class="container border border-app bg-white my-4 mx-auto px-4 text-center">
                    <form class="form-inline g-3" action="balance_form.php" method="post">
                        <label class="fs-5 fs-5-app fw-bold mt-4 mb-3 mx-auto">Wybierz okres bilansu:</label>
                        <div class="dropdown">
                            <button class="btn btn-dark dropdown-toggle font-form-app mb-4 mx-auto" type="button" id="menuPeriodBalance" data-bs-toggle="dropdown" aria-expanded="false">Wybierz
                                okres</button>
                            <ul class="dropdown-menu" aria-labelledby="menuPeriodBalance">
                                <li><button class="dropdown-item font-form-app" type="submit" name="formPeriod" value="currentMonth">Bieżący miesiąc</button></li>
                                <li><button class="dropdown-item font-form-app" type="submit" name="formPeriod" value="previousMonth">Poprzedni miesiąc</button></li>
                                <li><button class="dropdown-item font-form-app" type="submit" name="formPeriod" value="currentYear">Bieżący rok</button></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><button class="dropdown-item font-form-app" type="button" data-bs-toggle="modal" data-bs-target="#modalMenuDate">Niestandardowy</button></li>
                            </ul>
                        </div>
                        <div class="modal fade" id="modalMenuDate" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalMenuDateLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <label class="modal-title h4 h4-app fw-bold ms-1 my-2" id="modalMenuDateLabel">Podaj daty okresu</label>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body px-4">
                                        <form class="row mx-auto g-3">
                                            <label class="fs-6 fs-6-app text-start ps-0 my-1 mx-auto" for="startDate">Data początkowa</label>
                                            <input class="form-control my-1 mx-auto font-form-app" id="startDate" name="startDate" type="date" min="2000-01-01" max="<?php echo date('Y-m-d');
                                                                                                                                                                        ?>" value="<?php echo date('Y-m-d');
                                                                                                                                                                                    ?>" required>
                                            <label class="fs-6 fs-6-app text-start ps-0 my-1 mx-auto" for="endDate">Data końcowa</label>
                                            <input class="form-control my-1 mx-auto font-form-app" id="endDate" name="endDate" type="date" min="2000-01-01" max="<?php echo date('Y-m-d');
                                                                                                                                                                    ?>" value="<?php echo date('Y-m-d');
                                                                                                                                                                                ?>" required>
                                            <div class="row justify-content-center g-3 my-2 mx-auto mb-3">
                                                <button class="btn bg-btn-app font-form-app col-sm-5 col-8 mx-1 py-1" type="submit" name="formPeriod" value="selectedPeriod">Wyświetl bilans</button>
                                                <button class="btn btn-danger font-form-app col-sm-5 col-8 mx-1 py-1" data-bs-dismiss="modal" type="button">Anuluj</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                        if (isset($_SESSION['error_date']))
                            echo $_SESSION['error_date'];
                        unset($_SESSION['error_date']);
                        ?>
                    </form>
                </div>
            </div>
            <div class="container row col-xl-7 col-lg-8 col-sm-10 border bg-white my-4 mx-auto px-4 pb-4 text-center">
                <h2 class="text-wrap h2 h2-app fw-bold my-5 mx-auto">Przegląd bilansu<br><?php if (isset($_SESSION['periodStartDate']) && isset($_SESSION['periodEndDate'])) {
                                                                                                echo "od " . $_SESSION['periodStartDate'] . " do " . $_SESSION['periodEndDate'];
                                                                                                unset($_SESSION['periodStartDate']);
                                                                                                unset($_SESSION['periodEndDate']);
                                                                                            }
                                                                                            ?></h2>
                <div class="container-fluid col-md-5 p-0 me-2">
                    <table class="table table-borderless table-hover table-striped caption-top rounded-table-app">
                        <caption class="fs-5 fw-bold fs-5-app bg-table-app text-dark text-center mb-1">BILANS PRZYCHODÓW</caption>
                        <thead class="table-primary">
                            <tr>
                                <th scope="col">Kategoria</th>
                                <th scope="col">Przychód</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (isset($_SESSION['Salary'])) {
                                echo '<tr>
                                <th scope="row">Wynagrodzenie</th>
                                <td>' . $_SESSION["Salary"];
                                unset($_SESSION["Salary"]);
                                echo '</td>
                                </tr>';
                            }
                            if (isset($_SESSION['Interest'])) {
                                echo '<tr>
                                <th scope="row">Odsetki bankowe</th>
                                <td>' . $_SESSION["Interest"];
                                unset($_SESSION["Interest"]);
                                echo '</td>
                                </tr>';
                            }
                            if (isset($_SESSION['Allegro'])) {
                                echo '<tr>
                                <th scope="row">Sprzedaż na allegro</th>
                                <td>' . $_SESSION["Allegro"];
                                unset($_SESSION["Allegro"]);
                                echo '</td>
                                </tr>';
                            }
                            if (isset($_SESSION['Another Incomes'])) {
                                echo '<tr>
                                <th scope="row">Inne</th>
                                <td>' . $_SESSION["Another Incomes"];
                                unset($_SESSION["Another Incomes"]);
                                echo '</td>
                                </tr>';
                            }
                            ?>
                        </tbody>
                        <tfoot class="table-warning">
                            <tr>
                                <th class="border-bottom-0" scope="row">SUMA</th>
                                <td class="border-bottom-0"><?php if (isset($_SESSION['incomesSum'])) {
                                                                echo number_format($_SESSION['incomesSum'], 2, '.', ' ');;
                                                                unset($_SESSION['incomesSum']);
                                                            } else {
                                                                echo "0.00";
                                                            }
                                                            ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="container-fluid col-md-6 p-0">
                    <table class="table table-borderless table-hover table-striped caption-top rounded-table-app">
                        <caption class="fs-5 fw-bold fs-5-app bg-table-app text-dark text-center mb-1">BILANS WYDATKÓW</caption>
                        <thead class="table-primary">
                            <tr>
                                <th scope="col">Kategoria</th>
                                <th scope="col">Wydatek</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (isset($_SESSION['Food'])) {
                                echo '<tr>
                                <th scope="row">Jedzenie</th>
                                <td>' . $_SESSION["Food"];
                                unset($_SESSION["Food"]);
                                echo '</td>
                                </tr>';
                            }
                            if (isset($_SESSION['Apartment'])) {
                                echo '<tr>
                                <th scope="row">Mieszkanie</th>
                                <td>' . $_SESSION["Apartment"];
                                unset($_SESSION["Apartment"]);
                                echo '</td>
                                </tr>';
                            }
                            if (isset($_SESSION['Transport'])) {
                                echo '<tr>
                                <th scope="row">Transport</th>
                                <td>' . $_SESSION["Transport"];
                                unset($_SESSION["Transport"]);
                                echo '</td>
                                </tr>';
                            }
                            if (isset($_SESSION['Telecommunication'])) {
                                echo '<tr>
                                <th scope="row">Telekomunikacja</th>
                                <td>' . $_SESSION["Telecommunication"];
                                unset($_SESSION["Telecommunication"]);
                                echo '</td>
                                </tr>';
                            }
                            if (isset($_SESSION['Healthcare'])) {
                                echo '<tr>
                                <th scope="row">Opieka zdrowotna</th>
                                <td>' . $_SESSION["Healthcare"];
                                unset($_SESSION["Healthcare"]);
                                echo '</td>
                                </tr>';
                            }
                            if (isset($_SESSION['Clothes'])) {
                                echo '<tr>
                                <th scope="row">Ubranie</th>
                                <td>' . $_SESSION["Clothes"];
                                unset($_SESSION["Clothes"]);
                                echo '</td>
                                </tr>';
                            }
                            if (isset($_SESSION['Hygiene'])) {
                                echo '<tr>
                                <th scope="row">Higiena</th>
                                <td>' . $_SESSION["Hygiene"];
                                unset($_SESSION["Hygiene"]);
                                echo '</td>
                                </tr>';
                            }
                            if (isset($_SESSION['Children'])) {
                                echo '<tr>
                                <th scope="row">Dzieci</th>
                                <td>' . $_SESSION["Children"];
                                unset($_SESSION["Children"]);
                                echo '</td>
                                </tr>';
                            }
                            if (isset($_SESSION['Entertainment'])) {
                                echo '<tr>
                                <th scope="row">Rozrywka</th>
                                <td>' . $_SESSION["Entertainment"];
                                unset($_SESSION["Entertainment"]);
                                echo '</td>
                                </tr>';
                            }
                            if (isset($_SESSION['Trip'])) {
                                echo '<tr>
                                <th scope="row">Wycieczki</th>
                                <td>' . $_SESSION["Trip"];
                                unset($_SESSION["Trip"]);
                                echo '</td>
                                </tr>';
                            }
                            if (isset($_SESSION['Training'])) {
                                echo '<tr>
                                <th scope="row">Szkolenia</th>
                                <td>' . $_SESSION["Training"];
                                unset($_SESSION["Training"]);
                                echo '</td>
                                </tr>';
                            }
                            if (isset($_SESSION['Books'])) {
                                echo '<tr>
                                <th scope="row">Książki</th>
                                <td>' . $_SESSION["Books"];
                                unset($_SESSION["Books"]);
                                echo '</td>
                                </tr>';
                            }
                            if (isset($_SESSION['Savings'])) {
                                echo '<tr>
                                <th scope="row">Oszczędności</th>
                                <td>' . $_SESSION["Savings"];
                                unset($_SESSION["Savings"]);
                                echo '</td>
                                </tr>';
                            }
                            if (isset($_SESSION['Retirement'])) {
                                echo '<tr>
                                <th scope="row">Na emeryturę</th>
                                <td>' . $_SESSION["Retirement"];
                                unset($_SESSION["Retirement"]);
                                echo '</td>
                                </tr>';
                            }
                            if (isset($_SESSION['Repayment'])) {
                                echo '<tr>
                                <th scope="row">Spłata długów</th>
                                <td>' . $_SESSION["Repayment"];
                                unset($_SESSION["Repayment"]);
                                echo '</td>
                                </tr>';
                            }
                            if (isset($_SESSION['Donation'])) {
                                echo '<tr>
                                <th scope="row">Darowizna</th>
                                <td>' . $_SESSION["Donation"];
                                unset($_SESSION["Donation"]);
                                echo '</td>
                                </tr>';
                            }
                            if (isset($_SESSION['Another Expenses'])) {
                                echo '<tr>
                                <th scope="row">Inne wydatki</th>
                                <td>' . $_SESSION["Another Expenses"];
                                unset($_SESSION["Another Expenses"]);
                                echo '</td>
                                </tr>';
                            }
                            ?>
                        </tbody>
                        <tfoot class="table-warning">
                            <tr>
                                <th class="border-bottom-0" scope="row">SUMA</th>
                                <td class="border-bottom-0"><?php if (isset($_SESSION['expenceSum'])) {
                                                                echo number_format($_SESSION['expenceSum'], 2, '.', ' ');
                                                                unset($_SESSION['expenceSum']);
                                                            } else {
                                                                echo "0.00";
                                                            }
                                                            ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="row mx-auto p-0 mt-4">
                    <div class="container col-md-8 p-0">
                        <table class="table table-borderless caption-top rounded-table-app">
                            <caption class="fs-5 fw-bold fs-5-app bg-warning text-dark text-center mb-1">BILANS PRZYCHODÓW I WYDATKÓW</caption>
                            <thead class="table-warning">
                                <tr>
                                    <th class="border-bottom-0" scope="row">Podsumowanie</th>
                                    <td><?php if (isset($_SESSION['balance']))
                                            echo $_SESSION['balance'];
                                        ?></td>
                                </tr>
                            </thead>
                            <tfoot class="table-success">
                                <tr>
                                    <th colspan="2" class="f-lett-space-min-app <?php if (isset($_SESSION['balance'])) {
                                                                                    if ($_SESSION['balance'] > 0) {
                                                                                        echo "bg-green-app";
                                                                                    } else {
                                                                                        echo "bg-red-app";
                                                                                    }
                                                                                }
                                                                                ?>"><?php if (isset($_SESSION['balance'])) {
                                                                                        if ($_SESSION['balance'] > 0) {
                                                                                            echo "Gratulacje. Świetnie zarządzasz finansami!";
                                                                                        } else {
                                                                                            echo "Wydajesz więcej niż zarobiłeś! Popracuj nad finansami.";
                                                                                        }
                                                                                    }
                                                                                    unset($_SESSION['balance']);
                                                                                    ?></th>
                                </tr>
                            </tfoot>
                        </table>

                    </div>
                </div>

                <div class="row mx-auto mt-4">
                    <div class="container p-0">
                        <h3 class="h4 fw-bold h4-app my-4">Moje Wydatki</h3>
                        <div class="pie-chart-app"></div>
                    </div>
                </div>
                <h3 class="text-wrap h3 h3-app fw-bold mt-5 mb-3 mx-auto">Zestawienie operacji finansowych</h3>
                <div class="container-fluid col-md-5 p-0 me-2">
                    <h4 class="fs-5 fw-bold fs-5-app bg-table-app text-dark text-center mb-3 py-2">PRZYCHODY</h4>
                    <?php
                    if (isset($_SESSION['incomeList'])) {

                        foreach ($_SESSION['incomeList'] as $incomeUserFromList) {

                            echo '<table class="table table-borderless table-hover table-striped caption-top rounded-table-app">
                            <tbody class="table-info">
                            <tr>
                                <th class="text-center" scope="col">Data</th>
                                <td class="text-center">';
                            if (isset($incomeUserFromList['date_of_income']))
                                echo $incomeUserFromList['date_of_income'];
                            echo '</td>
                            </tr>
                            <tr>
                                <th class="text-center" scope="col">Kwota</th>
                                <td class="text-center">';
                            if (isset($incomeUserFromList['amount']))
                                echo $incomeUserFromList['amount'];
                            echo '</td>
                            </tr>
                            <tr>
                                <th class="text-center" scope="col">Kategoria</th>
                                <td class="text-center">';
                            if (isset($incomeUserFromList['category'])) {

                                switch ($incomeUserFromList['category']) {
                                    case "Salary":
                                        echo "Wynagrodzenie";
                                        break;
                                    case "Interest":
                                        echo "Inwestycje";
                                        break;
                                    case "Allegro":
                                        echo "Sprzedaż";
                                        break;
                                    case "Another Incomes":
                                        echo "Inne";
                                        break;
                                }
                            }
                            echo '</td>
                            </tr>';
                            if (isset($incomeUserFromList['income_comment']) && strlen(trim($incomeUserFromList['income_comment'])) != 0) {

                                echo '<tr>
                                <th class="text-center" scope="col">Komentarz</th>
                                <td class="text-center">';
                                echo $incomeUserFromList['income_comment'];
                                echo '</td>
                            </tr>';
                            }
                            echo '</tbody>
                            </table>';
                        }
                    }
                    ?>
                </div>
                <div class="container-fluid col-md-6 p-0">
                    <h4 class="fs-5 fw-bold fs-5-app bg-table-app text-dark text-center mb-3 py-2">WYDATKI</h4>
                    <?php
                    if (isset($_SESSION['expenceList'])) {

                        foreach ($_SESSION['expenceList'] as $expenceUserFromList) {

                            echo '<table class="table table-borderless table-hover table-striped caption-top rounded-table-app">
                            <tbody class="table-info">
                            <tr>
                                <th class="text-center" scope="col">Data</th>
                                <td class="text-center">';
                            if (isset($expenceUserFromList['date_of_expense']))
                                echo $expenceUserFromList['date_of_expense'];
                            echo '</td>
                            </tr>
                            <tr>
                                <th class="text-center" scope="col">Kwota</th>
                                <td class="text-center">';
                            if (isset($expenceUserFromList['amount']))
                                echo $expenceUserFromList['amount'];
                            echo '</td>
                                </tr>
                                <tr>
                                    <th class="text-center" scope="col">Metoda płatności</th>
                                    <td class="text-center">';
                            if (isset($expenceUserFromList['payment'])) {

                                switch ($expenceUserFromList['payment']) {
                                    case "Cash":
                                        echo "Gotówka";
                                        break;
                                    case "Debit_Card":
                                        echo "Karta debetowa";
                                        break;
                                    case "Credit_Card":
                                        echo "Karta kredytowa";
                                        break;
                                }
                            }
                            echo '</td>
                            </tr>
                            <tr>
                                <th class="text-center" scope="col">Kategoria</th>
                                <td class="text-center">';
                            if (isset($expenceUserFromList['category'])) {

                                switch ($expenceUserFromList['category']) {
                                    case "Food":
                                        echo "Jedzenie";
                                        break;
                                    case "Apartment":
                                        echo "Mieszkanie";
                                        break;
                                    case "Transport":
                                        echo "Transport";
                                        break;
                                    case "Telecommunication":
                                        echo "Telekomunikacja";
                                        break;
                                    case "Healthcare":
                                        echo "Opieka zdrowotna";
                                        break;
                                    case "Clothes":
                                        echo "Ubranie";
                                        break;
                                    case "Hygiene":
                                        echo "Higiena";
                                        break;
                                    case "Children":
                                        echo "Dzieci";
                                        break;
                                    case "Entertainment":
                                        echo "Rozrywka";
                                        break;
                                    case "Trip":
                                        echo "Wycieczka";
                                        break;
                                    case "Training":
                                        echo "Szkolenia";
                                        break;
                                    case "Books":
                                        echo "Książki";
                                        break;
                                    case "Savings":
                                        echo "Oszczędności";
                                        break;
                                    case "Retirement":
                                        echo "Na emeryturę";
                                        break;
                                    case "Repayment":
                                        echo "Spłata kredytu";
                                        break;
                                    case "Donation":
                                        echo "Darowizna";
                                        break;
                                    case "Another Expenses":
                                        echo "Inne";
                                        break;
                                }
                            }
                            echo '</td>
                            </tr>';
                            if (isset($expenceUserFromList['expense_comment']) && strlen(trim($expenceUserFromList['expense_comment'])) != 0) {

                                echo '<tr>
                                <th class="text-center" scope="col">Komentarz</th>
                                <td class="text-center">';
                                echo $expenceUserFromList['expense_comment'];
                                echo '</td>
                            </tr>';
                            }
                            echo '</tbody>
                            </table>';
                        }
                    }
                    ?>
                </div>
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