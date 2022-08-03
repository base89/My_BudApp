<?php

session_start();

if (isset($_POST['formPeriod'])) {

    $period = $_POST['formPeriod'];

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
