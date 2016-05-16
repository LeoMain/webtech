<?php
include ("connect.php");

class Comment
{
	private $username = "";
	private $email = "";
	private $comment = "";
	private $usernameErr = "";
	private $emailErr = "";
	private $commentErr = "";
	public $isValid = 1;

	function __construct($username, $email, $comment)
	{
		$this->username = $this->filter($username);
		$this->email = $this->filter($email);
		$this->comment = $this->filter($comment);
		$this->check_username($this->username);
		$this->check_email($this->email);
		$this->check_comment($this->comment);
		
	}

	public function save()
	{
		if (!$this->isValid)
			return;

		Connect::database()->insert("comments", [
			"author" => $this->username,
			"email" => $this->email,
			"message" => $this->comment
		]);
	}

	public static function delete($id)
	{
		Connect::database()->delete("comments", [
			"id" => $id
		]);
	}

	

	public static function get_all()
	{
		$datas = Connect::database()->select("comments", [
			"author",
			"email",
			"message",
			"created_at"
		]);

		return $datas;
	}

	private function check_username($data)
	{
		if (empty($data)) {
			$this->usernameErr = "Укажите имя";
			$this->isValid = 0;
		}
		if (strlen($data) > 60){
			$this->usernameErr = "Превышен лимит";
			$this->isValid = 0;
		}
		if (!preg_match("/^[А-Я][а-я]*/", $data)) {
			$this->usernameErr = "Неверный формат"; 
			$this->isValid = 0;
		}
	}

	private function check_email($data)
	{
		if (empty($data)) {
			$this->emailErr = "Укажите e-mail";
			$this->isValid = 0;
		}
		if (strlen($data) > 60){
			$this->emailErr = "Превышен лимит";
			$this->isValid = 0;
		}
		if (!filter_var($data, FILTER_VALIDATE_EMAIL)) {
			$this->emailErr = "Неверный формат"; 
			$this->isValid = 0;
		}
	}

	private function check_comment($data)
	{
		if (empty($data)) {
			$this->commentErr = "Напишите отзыв";
			$this->isValid = 0;
		}
		if (strlen($data) > 500){
			$this->commentErr = "Превышен лимит";
			$this->isValid = 0;
		}
	}

	private function filter($data) 
	{
			$data = trim($data);
			$data = stripslashes($data);
			$data = htmlspecialchars($data);
		return $data;
	}

	static function get_form(Comment $com = null)
	{
		if (!is_null($com)){
			$username = $com->username;
			$email = $com->email;
			$comment = $com->comment;
			$usernameErr = $com->usernameErr;
			$emailErr = $com->emailErr;
			$commentErr = $com->commentErr;
		} else {
			$username = $email = $comment = $usernameErr = $emailErr = $commentErr = '';
		}

		return '<form novalidate method="post" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '">
			<label for="username">Имя</label>
			<input type="text" name="username" required placeholder="Андрей" pattern="[А-Я]{1}[а-я]{1,}" value="' . $username . '" maxlength="60">
			<span class="error">' . $usernameErr . '</span>
			<label for="mail">E-mail</label>
			<input type="email" name="email" required placeholder="user@example.com" value="' . $email . '" maxlength="60">
			<span class="error">' . $emailErr . '</span>
			<label for="comment">Отзыв</label>
			<textarea type="text" name="comment" required placeholder="Ваш отзыв" maxlength="500">' . $comment . '</textarea>
			<span class="error">' . $commentErr . '</span>
			<input type="hidden" name="token" value="' . $_SESSION["token"] . '" />
			<input type="submit" value="Оставить отзыв">
		</form>';
	}
}

?>