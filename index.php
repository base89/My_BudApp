<?php

session_start();

if (!isset($_SESSION['isLogged']))
{
    header('Location: login.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Your Finances</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Boogaloo&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
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
                <a href="#" class="navbar-brand d-block d-md-none fw-bold bud_app_logo-bar">My BudApp!</a>
                <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav_menu" aria-controls="nav_menu" aria-expanded="false" aria-label="Navigation switch">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="nav_menu">
                    <div class="navbar-nav me-auto">
                        <a class="nav-item nav-link active" href="./index.php"> Menu Główne </a>
                        <a class="nav-item nav-link" href="./add_income.php"> Dodaj przychód </a>
                        <a class="nav-item nav-link" href="./add_expence.html"> Dodaj wydatek </a>
                        <a class="nav-item nav-link" href="./display_balance.html"> Przeglądaj bilans </a>
                    </div>
                    <div class="navbar-nav ms-auto">
                        <a class="nav-item nav-link" href="#"> Ustawienia </a>
                        <a class="nav-item nav-link" href="<?php
                                                            echo 'logout.php'
                                                            ?>">
                            Wyloguj się </a>
                    </div>
                </div>
            </div>
        </nav>

        <section>
            <div class="container col-xl-4 col-lg-5 col-md-6 col-sm-8 border bg-white my-4 mx-auto text-center">
                <h2 class="text-wrap h1 h1-app fw-bold my-5 mx-4"><?php
                                                                    echo "Witaj " . $_SESSION['username'] . "!"
                                                                    ?>
                </h2>
                <h3 class="h4 h4-app fw-bold text-muted">Co chcesz zrobić?</h3>
                <ul class="list-unstyled mb-5">
                    <li>
                        <a href="./add_income.php" class="btn w-50 bg-btn-app mt-3 my-1" tabindex="-1" role="button" aria-disabled="true">Dodaj przychód</a>
                    </li>
                    <li>
                        <a href="./add_expence.html" class="btn w-50 bg-btn-app my-1" tabindex="-1" role="button" aria-disabled="true">Dodaj
                            wydatek</a>
                    </li>
                    <li>
                        <a href="./display_balance.html" class="btn w-50 bg-btn-app my-1" tabindex="-1" role="button" aria-disabled="true">Przeglądaj bilans</a>
                    </li>
                </ul>
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
                    <span class="col-5 mx-auto text-muted text-center f-footer-b">© 2022 My BudApp</span>
                </div>
            </div>

        </footer>

    </main>



    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
</body>

</html>