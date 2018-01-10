<?php

include_once '../core/helper.php';
include_once '../core/security.php';
include_once '../core/action.php';
include_once '../core/database.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

class AccountLoginAction extends API_Action {

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

    if (empty($email)) {
      self::sendResponse(400, json_encode(self::getErrorWithMessage('Некорректный адрес электронной почты.'), JSON_UNESCAPED_UNICODE));
      return false;
    }

    if (!isset($data->password)) {
      self::sendResponse(400, json_encode(self::getErrorWithMessage('Введите пароль.'), JSON_UNESCAPED_UNICODE));
      return false;
    }

    $password = Helper::clean($data->password);

    if (empty($password)) {
      self::sendResponse(400, json_encode(self::getErrorWithMessage('Некорректный пароль.'), JSON_UNESCAPED_UNICODE));
      return false;
    }

    $query = 'SELECT user_id, password FROM users WHERE email = ?';
    $stmt = $this->db->instance->prepare($query);
    $stmt->bind_param('s', $email);
    $stmt->execute();

    $stmt->bind_result($user_id, $_password);
    while ($stmt->fetch()) {
      break;
    }

    $stmt->close();

    if ($user_id <= 0) {
      self::sendResponse(400, json_encode(self::getErrorWithMessage('Пользователь с указанным электронным адресом не найден.'), JSON_UNESCAPED_UNICODE));
      return false;
    }

    $password = md5($password);

    if ($password != $_password) {
      self::sendResponse(401, json_encode(self::getErrorWithMessage('Неверный пароль.'), JSON_UNESCAPED_UNICODE));
      return false;
    }

    $token = Security::generate_token($this->db);

    $query = 'INSERT INTO access (user_id, token) VALUES (?, ?)';
    $stmt = $this->db->instance->prepare($query);
    $stmt->bind_param('is', $user_id, $token);
    $stmt->execute();

    $stmt->close();

    $app_id = $this->db->instance->insert_id;

    $this->db->instance->commit();

    $result = [
      'app_id' => $app_id,
      'token' => $token
    ];
    self::sendResponse(200, json_encode($result, JSON_UNESCAPED_UNICODE));
    return true;
  }

}

$action = new AccountLoginAction;
$action->action();

?>
