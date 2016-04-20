<?php
$usernameErr = $emailErr = $commentErr = "";
$username = $email = $comment = "";
$dbservername = "localhost";
$dbusername = $dbpassword = $dbname = "webtech";
$isValid = 1;

session_start();

$conn = new mysqli($dbservername, $dbusername, $dbpassword, $dbname);

if ($conn->connect_error) {
    die("Ошибка соединения с БД: " . $conn->connect_error);
}

if (!isset($_SESSION['token'])) {
	$_SESSION['token'] = md5(uniqid(rand(), TRUE));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if ($_POST['token'] != $_SESSION['token']) {
		$isValid = 0;
	}

	if (empty($_POST["username"])) {
		$usernameErr = "Укажите имя";
		$isValid = 0;
	} else {
		$username = validate($_POST["username"]);
		if (strlen($username) > 60){
			$usernameErr = "Превышен лимит";
			$isValid = 0;
		}
		if (!preg_match("/^[А-Я][а-я]*/", $username)) {
			$usernameErr = "Неверный формат"; 
			$isValid = 0;
		}
	}

	if (empty($_POST["email"])) {
		$emailErr = "Укажите e-mail";
		$isValid = 0;
	} else {
		$email = validate($_POST["email"]);
		if (strlen($email) > 60) {
			$usernameErr = "Превышен лимит";
			$isValid = 0;
		}
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$emailErr = "Неверный формат"; 
			$isValid = 0;
		}
	}

	if (empty($_POST["comment"])) {
		$commentErr = "Напишите отзыв";
		$isValid = 0;
	} else {
		$comment = validate($_POST["comment"]);
		if (strlen($comment) > 500) {
			$commentErr = "Превышен лимит";
			$isValid = 0;
		}
	}

	if ($isValid) {
		$stmt = $conn->prepare("INSERT INTO comments (author, email, message) VALUES (?, ?, ?)");
		$stmt->bind_param("sss", $username, $email, $comment);
		$stmt->execute();
	}
}

function validate($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}

$result = $conn->query('SELECT * FROM comments');	

include('index.html');

$conn->close();
?>