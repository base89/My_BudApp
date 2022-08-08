<?php

session_start();

if (isset($_POST['formPeriod']) || isset($_SESSION['formPeriod'])) {

    if (isset($_POST['formPeriod']))
        $_SESSION['formPeriod'] = $_POST['formPeriod'];

    if (isset($_POST['startDate']) || isset($_POST['endDate'])) {
        if ($_POST['startDate'] > $_POST['endDate']) {

            $_SESSION['error_date'] = '<span class="text-danger f-error">Niewłaściwy dobór dat!<br> Data początkowa nie może być późniejsza niż data końcowa.</span>';
            header('Location: display_balance.php');
            exit();
        }
    }
    switch ($_SESSION['formPeriod']) {
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

    unset($_SESSION['formPeriod']);
} else {

    header('Location: display_balance.php');
    exit();
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

        $userId = $_SESSION['id'];
        $startDate = $_SESSION['periodStartDate'];
        $endDate = $_SESSION['periodEndDate'];

        // income queries

        $incomeQuery = "SELECT ic.name, SUM(i.amount) FROM users u 
		INNER JOIN incomes i ON u.id = i.user_id 
		INNER JOIN incomes_category_assigned_to_users ic ON i.income_category_assigned_to_user_id = ic.id 
		WHERE u.id = $userId AND i.date_of_income >= '$startDate' 
		AND  i.date_of_income <= '$endDate' GROUP BY ic.id";

        $incomeSumQuery = "SELECT SUM(i.amount) FROM users u 
        INNER JOIN incomes i ON u.id = i.user_id 
        WHERE u.id = $userId AND i.date_of_income >= '$startDate' 
        AND  i.date_of_income <= '$endDate'";

        $incomeListQuery = "SELECT i.amount, i.date_of_income, ic.name AS category, i.income_comment FROM users u 
        INNER JOIN incomes i ON u.id = i.user_id 
        INNER JOIN incomes_category_assigned_to_users ic ON i.income_category_assigned_to_user_id = ic.id
        WHERE u.id = $userId AND i.date_of_income >= '$startDate' 
        AND  i.date_of_income <= '$endDate' ORDER BY i.date_of_income DESC";

        // expense queries

        $expenceQuery = "SELECT ec.name, SUM(e.amount) FROM users u 
		INNER JOIN expences e ON u.id = e.user_id 
		INNER JOIN expenses_category_assigned_to_users ec ON e.expense_category_assigned_to_user_id = ec.id 
		WHERE u.id = $userId AND e.date_of_expense >= '$startDate' 
		AND  e.date_of_expense <= '$endDate' GROUP BY ec.id";

        $expenceSumQuery = "SELECT SUM(e.amount) FROM users u 
        INNER JOIN expences e ON u.id = e.user_id 
        WHERE u.id = $userId AND e.date_of_expense >= '$startDate' 
        AND  e.date_of_expense <= '$endDate'";

        $expenceListQuery = "SELECT e.amount, e.date_of_expense, p.name AS payment, ec.name AS category, e.expense_comment FROM users u 
		INNER JOIN expences e ON u.id = e.user_id 
		INNER JOIN expenses_category_assigned_to_users ec ON e.expense_category_assigned_to_user_id = ec.id
		INNER JOIN payment_methods_assigned_to_users p ON e.payment_method_assigned_to_user_id = p.id
		WHERE u.id = $userId AND e.date_of_expense >= '$startDate' 
		AND  e.date_of_expense <= '$endDate' ORDER BY e.date_of_expense DESC";

        if ($connection->query($incomeQuery) && $connection->query($incomeSumQuery) && $connection->query($expenceQuery)
            && $connection->query($expenceSumQuery) && $connection->query($incomeListQuery) && $connection->query($expenceListQuery)) {

            if ($incomes = $connection->query($incomeQuery)->fetchAll()) {

                foreach ($incomes as $userIncome) {

                    $_SESSION[$userIncome[0]] = $userIncome[1];
                }
            }

            if ($incomesSum = $connection->query($incomeSumQuery)->fetch()) {

                if ($incomesSum[0] > 0 && $incomesSum[0] != NULL) {

                    $_SESSION['incomesSum'] = $incomesSum[0];
                } else {

                    $_SESSION['incomesSum'] = "0.00";
                }
            }

            if ($incomeList = $connection->query($incomeListQuery)->fetchAll()) {

                $_SESSION['incomeList'] = $incomeList;
            }

            if ($expences = $connection->query($expenceQuery)->fetchAll()) {

                foreach ($expences as $userExpence) {

                    $_SESSION[$userExpence[0]] = $userExpence[1];
                }
            }

            if ($expenceSum = $connection->query($expenceSumQuery)->fetch()) {

                if ($expenceSum[0] > 0 && $expenceSum[0] != NULL) {

                    $_SESSION['expenceSum'] = $expenceSum[0];
                } else {

                    $_SESSION['expenceSum'] = "0.00";
                }
            }

            if ($expenceList = $connection->query($expenceListQuery)->fetchAll()) {
                
                $_SESSION['expenceList'] = $expenceList;
            }

            if (isset($_SESSION['incomesSum']) && isset($_SESSION['expenceSum'])) {

                $_SESSION['balance'] = number_format($_SESSION['incomesSum'] - $_SESSION['expenceSum'], 2, '.', ' ');
            }

            header('Location: display_balance.php');
        }
    }
} catch (PDOException $error) {

    echo $error->getMessage();
    exit(' Wystąpił błąd! Spróbuj ponownie.');
}
