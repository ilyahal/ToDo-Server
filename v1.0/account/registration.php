<?php

include_once '../core/helper.php';
include_once '../core/action.php';
include_once '../core/database.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

class AccountRegistrationAction extends API_Action {

  private $db;

  function __construct() {
    $this->db = new Database;
  }

  public function action() {
    $data = json_decode(file_get_contents("php://input"));

    if (!isset($data->email)) {
      self::sendResponse(400, json_encode(self::getErrorWithMessage('Введите адрес электронной почты.'), JSON_UNESCAPED_UNICODE));
      return false;
    }

    $email = Helper::clean($data->email);
    $email_validate = filter_var(idn_to_ascii($email), FILTER_VALIDATE_EMAIL);

    if (!Helper::check_length($email, 2, 40) || !$email_validate) {
      self::sendResponse(400, json_encode(self::getErrorWithMessage('Некорректный адрес электронной почты.'), JSON_UNESCAPED_UNICODE));
      return false;
    }

    if (!isset($data->password)) {
      self::sendResponse(400, json_encode(self::getErrorWithMessage('Введите пароль.'), JSON_UNESCAPED_UNICODE));
      return false;
    }

    $password = Helper::clean($data->password);

    if (!Helper::check_length($password, 6, 40)) {
      self::sendResponse(400, json_encode(self::getErrorWithMessage('Некорректный пароль.'), JSON_UNESCAPED_UNICODE));
      return false;
    }

    $query = 'SELECT user_id FROM users WHERE email = ?';
    $stmt = $this->db->instance->prepare($query);
    $stmt->bind_param('s', $email);
    $stmt->execute();

    $stmt->bind_result($user_id);
    while ($stmt->fetch()) {
      break;
    }

    $stmt->close();

    if ($user_id > 0) {
      self::sendResponse(409, json_encode(self::getErrorWithMessage('Пользователь с указанным адресом электронной почты уже существует.'), JSON_UNESCAPED_UNICODE));
      return false;
    }

    $password = md5($password);

    $query = 'INSERT INTO users (email, password) VALUES (?, ?)';
    $stmt = $this->db->instance->prepare($query);
    $stmt->bind_param('ss', $email, $password);
    $stmt->execute();

    $stmt->close();

    $user_id = $this->db->instance->insert_id;

    $title = 'Добавить новый элемент ("+")';
    $description = 'Список создан автоматически';
    $color_id = 5;
    $icon_id = 5;

    $query = 'INSERT INTO lists (user_id, title, description, color_id, icon_id) VALUES (?, ?, ?, ?, ?)';
    $stmt = $this->db->instance->prepare($query);
    $stmt->bind_param('issii', $user_id, $title, $description, $color_id, $icon_id);
    $stmt->execute();

    $stmt->close();

    $list_id = $this->db->instance->insert_id;
    $title = 'Обучение';
    $description = 'Запись создана автоматически';
    $active = true;

    $query = 'INSERT INTO items (list_id, title, description, is_active) VALUES (?, ?, ?, ?)';
    $stmt = $this->db->instance->prepare($query);
    $stmt->bind_param('issi', $list_id, $title, $description, $active);
    $stmt->execute();

    $stmt->close();

    $this->db->instance->commit();

    self::sendResponse(200, '', 'text/html');
    return true;
  }

}

$action = new AccountRegistrationAction;
$action->action();

?>