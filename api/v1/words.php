<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

ini_set('log_errors', 'On');
ini_set('error_log', '/path/to/php-error.log');

$bad_words = array(
    "", ""
);


$rate_limit_max = 2;
$rate_limit_window = 1;

session_start();

function checkRateLimit($rate_limit_max, $rate_limit_window) {
    if (!isset($_SESSION['requests'])) {
        $_SESSION['requests'] = [];
    }

    $current_time = time();
    $_SESSION['requests'] = array_filter($_SESSION['requests'], function($timestamp) use ($current_time, $rate_limit_window) {
        return ($current_time - $timestamp) < $rate_limit_window;
    });

    if (count($_SESSION['requests']) >= $rate_limit_max) {
        return false;
    }

    $_SESSION['requests'][] = $current_time;
    return true;
}

function sanitizeMessage($message) {
    return htmlspecialchars(strip_tags(trim($message)), ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    if (!checkRateLimit($rate_limit_max, $rate_limit_window)) {
        header('Content-Type: application/json', true, 429);
        echo json_encode(["error" => "レートリミットを超えました。しばらくしてから再試行してください。"]);
        exit;
    }

    if (isset($_GET['message'])) {
        $message = $_GET['message'];
        $message = sanitizeMessage($message);

        $contains_bad_words = false;
        foreach ($bad_words as $bad_word) {
            if (strpos($message, $bad_word) !== false) {
                $contains_bad_words = true;
                break;
            }
        }

        header('Content-Type: application/json');
        echo json_encode([
            "contains_bad_words" => $contains_bad_words
        ]);
    } else {
        header('Content-Type: application/json', true, 400);
        echo json_encode(["error" => "メッセージが必要です"]);
    }
} else {
    header('Content-Type: application/json', true, 405);
    echo json_encode(["error" => "不正なリクエストメソッドです"]);
}
?>
