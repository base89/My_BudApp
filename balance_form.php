<?php

session_start();

if (!isset($_POST['formPeriod'])) {

    header('Location: display_balance.php');
    exit();
} else {

    $period = $_POST['formPeriod'];

    if (isset($_POST['startDate']) || isset($_POST['endDate'])) {
        if ($_POST['startDate'] > $_POST['endDate']) {

            $_SESSION['error_date'] = '<span class="text-danger f-error">Niewłaściwy dobór dat!<br> Data początkowa nie może być późniejsza niż data końcowa.</span>';
            header('Location: display_balance.php');
            exit();
        }
    }
    switch ($period) {
        case "currentMonth":
            $_SESSION['periodStartDate'] = date('Y-m-d', strtotime("first day of this month"));
            $_SESSION['periodEndDate'] = date('Y-m-d', strtotime("now"));
            break;
        case "previousMonth":
            $_SESSION['periodStartDate'] = date('Y-m-d', strtotime("first day of previous month"));
            $_SESSION['periodEndDate'] = date('Y-m-d', strtotime("last day of previous month"));
            break;
        case "currentYear":
            $_SESSION['periodStartDate'] = date('Y-m-d', strtotime("1 January this year"));
            $_SESSION['periodEndDate'] = date('Y-m-d', strtotime("now"));
            break;
        case "selectedPeriod":
            $_SESSION['periodStartDate'] = $_POST['startDate'];
            $_SESSION['periodEndDate'] = $_POST['endDate'];
            break;
    }
}

$connect = require_once 'connect.php';

try {

    $connection = new PDO(
        "mysql:host={$connect['host']};dbname={$connect['db_name']};charset=utf8",
        $connect['db_user'],
        $connect['db_password'],
        [PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    if ($connection) {

        // $userId = $_SESSION['id'];
        $userId = 1;
        $startDate = $_SESSION['periodStartDate'];
        $endDate = $_SESSION['periodEndDate'];

        $incomeQuery = "SELECT ic.name, SUM(i.amount) FROM users u 
		INNER JOIN incomes i ON u.id = i.user_id 
		INNER JOIN incomes_category_assigned_to_users ic ON i.income_category_assigned_to_user_id = ic.id 
		WHERE u.id = $userId AND i.date_of_income >= '$startDate' 
		AND  i.date_of_income <= '$endDate' GROUP BY ic.id";

        $incomeSumQuery = "SELECT SUM(i.amount) FROM users u 
        INNER JOIN incomes i ON u.id = i.user_id 
        WHERE u.id = $userId AND i.date_of_income >= '$startDate' 
        AND  i.date_of_income <= '$endDate'";

        $expenceQuery = "SELECT ec.name, SUM(e.amount) FROM users u 
		INNER JOIN expences e ON u.id = e.user_id 
		INNER JOIN expenses_category_assigned_to_users ec ON e.expense_category_assigned_to_user_id = ec.id 
		WHERE u.id = $userId AND e.date_of_expense >= '$startDate' 
		AND  e.date_of_expense <= '$endDate' GROUP BY ec.id";

        $expenceSumQuery = "SELECT SUM(e.amount) FROM users u 
        INNER JOIN expences e ON u.id = e.user_id 
        WHERE u.id = $userId AND e.date_of_expense >= '$startDate' 
        AND  e.date_of_expense <= '$endDate'";

        if ($connection->query($incomeQuery) && $connection->query($incomeSumQuery) && $connection->query($expenceQuery) && $connection->query($expenceSumQuery)) {

            $incomes = $connection->query($incomeQuery)->fetchAll();

            foreach ($incomes as $userIncome) {

                $_SESSION[$userIncome['ic.name']] = $userIncome['SUM(i.amount)'];
            }

            $incomesSum = $connection->query($incomeSumQuery)->fetch();
            $_SESSION['incomesSum'] = $incomesSum;

            $expences = $connection->query($expenceQuery)->fetchAll();

            foreach ($expences as $userExpence) {

                $_SESSION[$userExpence['ec.name']] = $userExpence['SUM(e.amount)'];
            }

            $expenceSum = $connection->query($expenceSumQuery)->fetch();
            $_SESSION['expenceSum'] = $expenceSum;

            header('Location: display_balance.php');
        }
    }
} catch (PDOException $error) {

    echo $error->getMessage();
    exit(' Wystąpił błąd! Spróbuj ponownie.');
}
