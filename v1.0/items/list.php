<?php

include_once '../core/helper.php';
include_once '../core/security.php';
include_once '../core/action.php';
include_once '../core/database.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

class ItemsListAction extends API_Action {

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

    if (!isset($data->list_id)) {
      self::sendResponse(400, json_encode(self::getErrorWithMessage('Введите идентификатор списка.'), JSON_UNESCAPED_UNICODE));
      return false;
    }

    $list_id = $data->list_id;

    if (!is_numeric($list_id)) {
      self::sendResponse(400, json_encode(self::getErrorWithMessage('Некорректный идентификатор списка.'), JSON_UNESCAPED_UNICODE));
      return false;
    }

    $list_id = intval($list_id);

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

    if ($_user_id <= 0) {
      self::sendResponse(400, json_encode(self::getErrorWithMessage('Список не найден.'), JSON_UNESCAPED_UNICODE));
      return false;
    }

    $query = 'SELECT item_id, title, description, is_active, insert_date, update_date FROM items WHERE list_id = ?';
    $stmt = $this->db->instance->prepare($query);
    $stmt->bind_param('i', $list_id);
    $stmt->execute();

    $result = array();

    $stmt->bind_result($item_id, $title, $description, $is_active, $insert_date, $update_date);
    while ($stmt->fetch()) {
      $item = [
        'item_id' => $item_id,
        'list_id' => $list_id,
        'title' => $title,
        'description' => empty($description) ? NULL : $description,
        'is_active' => $is_active == 1,
        'insert_date' => date('c', strtotime($insert_date)),
        'update_date' => empty($update_date) ? NULL : date('c', strtotime($update_date))
      ];

      $result[] = $item;
    }

    $stmt->close();

    self::sendResponse(200, json_encode($result, JSON_UNESCAPED_UNICODE));
    return true;
  }

}

$action = new ItemsListAction;
$action->action();

?>
