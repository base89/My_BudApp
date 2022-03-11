<?php

session_start();

if (isset($_POST['email'])) {

    $isGood = true;

    $username = $_POST['username'];
    if ((strlen($username) < 3) || (strlen($username) > 20)) {

        $isGood = false;
        $_SESSION['error_username'] = '<span class="text-danger f-error">Nazwa użytkownika musi posiadać od 3 do 20 znaków</span>';
    }

    if (!ctype_alnum($username)) {

        $isGood = false;
        $_SESSION['error_username'] = '<span class="text-danger f-error mt-1">Nazwa użytkownika może składać się tylko z liter i cyfr (bez polskich znaków)</span>';
    }

    $email = $_POST['email'];
    $secureEmail = filter_var($email, FILTER_SANITIZE_EMAIL);
    if (!(filter_var($secureEmail, FILTER_VALIDATE_EMAIL)) || ($secureEmail != $email)) {

        $isGood = false;
        $_SESSION['error_email'] = '<span class="text-danger f-error mt-1">Podaj poprawny adres e-mail</span>';
    }

    $password = $_POST['password'];
    $password2 = $_POST['password2'];
    if ((strlen($password) < 8) || (strlen($password) > 20)) {

        $isGood = false;
        $_SESSION['error_password'] = '<span class="text-danger f-error mt-1">Hasło musi posiadać od 8 do 20 znaków</span>';
    }

    if ($password != $password2) {

        $isGood = false;
        $_SESSION['error_password2'] = '<span class="text-danger f-error mt-1">Hasła nie są identyczne</span>';
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $captchaKey = "6LeoFr0eAAAAAGX05jS28QsBLxHaILNqbj6yVsKv";

    $checkKey = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $captchaKey . '&response=' . $_POST['g-recaptcha-response']);

    $responseCaptcha = json_decode($checkKey);

    if (!($responseCaptcha->success)) {

        $isGood = false;
        $_SESSION['error_bot'] = '<span class="text-danger f-error mt-1">Potwierdź, że nie jesteś botem</span>';
    }

    $_SESSION['input_username'] = $username;
    $_SESSION['input_password'] = $password;
    $_SESSION['input_password2'] = $password2;
    $_SESSION['input_email'] = $email;

    require_once "connect.php";
    mysqli_report(MYSQLI_REPORT_STRICT);

    try {

        $connection = new mysqli($host, $db_user, $db_password, $db_name);
        if ($connection->connect_errno != 0) {

            throw new Exception(mysqli_connect_errno());
        } else {

            $queryResult = $connection->query("SELECT id FROM users WHERE email='$email'");
            if (!$queryResult)
                throw new Exception($connection->error);

            $emailNumbers = $queryResult->num_rows;
            if ($emailNumbers > 0) {
                $isGood = false;
                $_SESSION['error_email'] = '<span class="text-danger f-error mt-1">Istnieje konto przypisane do tego adresu e-mail</span>';
            }

            $queryResult = $connection->query("SELECT id FROM users WHERE username='$username'");
            if (!$queryResult)
                throw new Exception($connection->error);

            $usernameNumbers = $queryResult->num_rows;
            if ($usernameNumbers > 0) {
                $isGood = false;
                $_SESSION['error_username'] = '<span class="text-danger f-error mt-1">Istnieje konto o takiej nazwie</span>';
            }

            if ($isGood) {

                if ($connection->query("INSERT INTO users VALUES (NULL, '$username', '$hashedPassword', '$email')")) {

                    $expenseQuery = "INSERT INTO expenses_category_assigned_to_users(user_id, name) 
                    SELECT users.id AS user_id, de.name FROM users CROSS JOIN expenses_category_default AS de WHERE users.username='$username'";
                    $incomeQuery = "INSERT INTO incomes_category_assigned_to_users(user_id, name) 
                    SELECT users.id AS user_id, de.name FROM users CROSS JOIN incomes_category_default AS de WHERE users.username='$username'";
                    $paymentQuery = "INSERT INTO payment_methods_assigned_to_users(user_id, name) 
                    SELECT users.id AS user_id, de.name FROM users CROSS JOIN payment_methods_default AS de WHERE users.username='$username'";

                    if ($connection->query($expenseQuery) && $connection->query($incomeQuery) && $connection->query($paymentQuery)) {

                        $connection->commit();
                        $_SESSION['isRegistered'] = true;
                        header('Location: confirmation.php');
                    }
                } else {

                    throw new Exception($connection->error);
                }
            }

            $connection->close();
        }
    } catch (Exception $error) {

        echo '<span class="text-danger">Błąd serwera. Przepraszamy za trudności i prosimy zarejestrować się w innym terminie.</span>';
        echo '<br />Informacja developerska: ' . $error;
    }
}

?>

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sing Up!</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Boogaloo&family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>

<body>

    <main>

        <section class="row">
            <div class="row mx-auto">
                <div class="container col-xl-4 col-lg-5 col-md-6 col-sm-7 border bg-white text-center mx-auto mt-5 p-3 pb-1">
                    <div class="row my-3 mx-auto">
                        <h1 class="fw-bold bud_app_logo mb-3">My BudApp!</h1>
                        <h2 class="h4 h4-app fw-bolder text-gray-app mb-0">Zacznij zarządzać Swoim Budżetem <span class="row justify-content-center mx-auto">- bezpłatnie!</span></h2>
                    </div>
                    <div class="row mx-auto">
                        <form class="row g-3 px-xl-5 px-lg-4 px-md-3 my-0 mx-auto" method="post">
                            <input class="form-control fs-6 fs-6-app bg-l-gray-app" type="text" name="username" placeholder="Imię" value="<?php
                                                                                                                                            if (isset($_SESSION['input_username'])) {

                                                                                                                                                echo $_SESSION['input_username'];
                                                                                                                                                unset($_SESSION['input_username']);
                                                                                                                                            }
                                                                                                                                            ?>" required>
                            <?php
                            if (isset($_SESSION['error_username'])) {

                                echo $_SESSION['error_username'];
                                unset($_SESSION['error_username']);
                            }
                            ?>
                            <input class="form-control fs-6 fs-6-app bg-l-gray-app" type="email" name="email" placeholder="Adres e-mail" value="<?php
                                                                                                                                                if (isset($_SESSION['input_email'])) {

                                                                                                                                                    echo $_SESSION['input_email'];
                                                                                                                                                    unset($_SESSION['input_email']);
                                                                                                                                                }
                                                                                                                                                ?>" required>
                            <?php
                            if (isset($_SESSION['error_email'])) {

                                echo $_SESSION['error_email'];
                                unset($_SESSION['error_email']);
                            }
                            ?>
                            <input class="form-control fs-6 fs-6-app bg-l-gray-app" type="password" name="password" placeholder="Hasło" value="<?php
                                                                                                                                                if (isset($_SESSION['input_password'])) {

                                                                                                                                                    echo $_SESSION['input_password'];
                                                                                                                                                    unset($_SESSION['input_password']);
                                                                                                                                                }
                                                                                                                                                ?>" required>
                            <?php
                            if (isset($_SESSION['error_password'])) {

                                echo $_SESSION['error_password'];
                                unset($_SESSION['error_password']);
                            }
                            ?>
                            <input class="form-control fs-6 fs-6-app bg-l-gray-app" type="password" name="password2" placeholder="Powtórz hasło" value="<?php
                                                                                                                                                        if (isset($_SESSION['input_password2'])) {

                                                                                                                                                            echo $_SESSION['input_password2'];
                                                                                                                                                            unset($_SESSION['input_password2']);
                                                                                                                                                        }
                                                                                                                                                        ?>" required>
                            <?php
                            if (isset($_SESSION['error_password2'])) {

                                echo $_SESSION['error_password2'];
                                unset($_SESSION['error_password2']);
                            }
                            ?>
                            <div class="text-center">
                                <div class="g-recaptcha captcha" data-sitekey="6LeoFr0eAAAAANrUzQIhp3BmcKEHEqbVTv1SWdBg"></div>
                                <?php
                                if (isset($_SESSION['error_bot'])) {

                                    echo $_SESSION['error_bot'];
                                    unset($_SESSION['error_bot']);
                                }
                                ?>
                            </div>
                            <div class="row mx-auto my-4">
                                <button class="fs-6 fs-6-app fw-bolder btn bg-btn-app w-75 mx-auto">Zarejestruj się</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="row mx-auto">
                <div class="container col-xl-4 col-lg-5 col-md-6 col-sm-7 border bg-white text-center mx-auto my-2">
                    <p class="fs-6 fs-6-app fw-light my-3">Masz konto?
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