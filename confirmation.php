<?php

session_start();

if (!isset($_SESSION['isRegistered'])) {

    header('Location: registration.php');
    exit();
} else {

    unset($_SESSION['isRegistered']);
}

if (isset($_SESSION['input_username'])) unset($_SESSION['input_username']);
if (isset($_SESSION['input_email'])) unset($_SESSION['input_email']);
if (isset($_SESSION['input_password'])) unset($_SESSION['input_password']);
if (isset($_SESSION['input_password2'])) unset($_SESSION['input_password2']);

if (isset($_SESSION['error_username'])) unset($_SESSION['error_username']);
if (isset($_SESSION['error_email'])) unset($_SESSION['error_email']);
if (isset($_SESSION['error_password'])) unset($_SESSION['error_password']);
if (isset($_SESSION['error_bot'])) unset($_SESSION['error_bot']);

?>

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Confirmation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Boogaloo&family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
</head>

<body>

    <main>

        <section class="row">
            <div class="row mx-auto">
                <div class="container col-xl-4 col-lg-5 col-md-6 col-sm-7 border bg-white text-center mx-auto mt-5 p-3 pb-1">
                    <div class="row my-3 mx-auto">
                        <h1 class="fw-bold bud_app_logo mb-3">My BudApp!</h1>
                        <h2 class="h4 h4-app fw-bolder text-gray-app mb-0">Zarządzaj Swoim Budżetem <span class="row justify-content-center mx-auto">- bezpłatnie!</span></h2>
                    </div>
                    <div class="row mx-auto">
                        <div class="alert alert-success" role="alert">
                            <h4 class="alert-heading">Potwierdzenie rejestracji!</h4>
                            <p>Twoja rejestracja w naszym serwisie przebiegła pomyślnie.</p>
                            <hr>
                            <p>Aby rozpocząć korzystanie z aplikacji kliknij
                                <a href="./login.html" class="alert-link">Zaloguj się!</a> używając podanego adresu e-mail oraz hasła.
                            </p>
                        </div>

                    </div>
                </div>
            </div>
            <div class="row mx-auto">
                <div class="container col-xl-4 col-lg-5 col-md-6 col-sm-7 border bg-white text-center mx-auto my-2">
                    <p class="fs-6 fs-6-app fw-light my-3">Posiadasz już konto.
                        <a class="text-decoration-none text-primary fw-light" href="./login.php">Zaloguj się!</a>
                    </p>
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