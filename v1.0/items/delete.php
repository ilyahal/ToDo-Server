<?php

include_once '../core/helper.php';
include_once '../core/security.php';
include_once '../core/action.php';
include_once '../core/database.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

class ItemsDeleteAction extends API_Action {

  private $db;

  function __construct() {
    $this->db = new Database;
  }

  public function action() {
    $user_id = Security::check_token($this->db);

    if ($user_id <= 0) {
      self::sendResponse(401, json_encode(self::getErrorWithMessage('Неверный токен.'), JSON_UNESCAPED_UNICODE));
      return false;
    }

    $data = json_decode(file_get_contents("php://input"));

    if (!isset($data->item_id)) {
      self::sendResponse(400, json_encode(self::getErrorWithMessage('Введите идентификатор записи.'), JSON_UNESCAPED_UNICODE));
      return false;
    }

    $item_id = $data->item_id;

    if (!is_numeric($item_id)) {
      self::sendResponse(400, json_encode(self::getErrorWithMessage('Некорректный идентификатор записи.'), JSON_UNESCAPED_UNICODE));
      return false;
    }

    $item_id = intval($item_id);

    $query = 'SELECT list_id FROM items WHERE item_id = ?';
    $stmt = $this->db->instance->prepare($query);
    $stmt->bind_param('i', $item_id);
    $stmt->execute();

    $stmt->bind_result($list_id);
    while ($stmt->fetch()) {
      break;
    }

    $stmt->close();

    if ($list_id <= 0) {
      self::sendResponse(400, json_encode(self::getErrorWithMessage('Запись не найдена.'), JSON_UNESCAPED_UNICODE));
      return false;
    }

    $query = 'SELECT user_id FROM lists WHERE list_id = ?';
    $stmt = $this->db->instance->prepare($query);
    $stmt->bind_param('i', $list_id);
    $stmt->execute();

    $stmt->bind_result($_user_id);
    while ($stmt->fetch()) {
      break;
    }

    $stmt->close();

    if ($user_id != $_user_id && $_user_id > 0) {
      self::sendResponse(403, json_encode(self::getErrorWithMessage('Нет доступа.'), JSON_UNESCAPED_UNICODE));
      return false;
    }

    $query = 'DELETE FROM items WHERE item_id = ?';
    $stmt = $this->db->instance->prepare($query);
    $stmt->bind_param('i', $item_id);
    $stmt->execute();

    $stmt->close();

    $this->db->instance->commit();

    self::sendResponse(200, '', 'text/html');
    return true;
  }

}

$action = new ItemsDeleteAction;
$action->action();

?>
