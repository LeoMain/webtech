<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/models/Comment.php');
$form = "";

session_start();

if (!isset($_SESSION['token'])) {
	$_SESSION['token'] = md5(uniqid(rand(), TRUE));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$comment = new Comment($_POST['username'], $_POST['email'], $_POST['comment']);
	if ($_POST['token'] != $_SESSION['token']) {
		$comment->isValid = 0;
	}	
	
	if (!$comment->isValid)
		$form = Comment::get_form($comment);

	$comment->save();
}

if (empty($form)){
	$form = Comment::get_form();
}

$datas = Comment::get_all();
include('view/index.html');
?>