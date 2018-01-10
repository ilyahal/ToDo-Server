<?php

include_once '../core/helper.php';
include_once '../core/security.php';
include_once '../core/action.php';
include_once '../core/database.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

class ItemsCreateAction extends API_Action {

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

    if (!isset($data->title) || !isset($data->is_active)) {
      self::sendResponse(400, json_encode(self::getErrorWithMessage('Введите необходимые данные.'), JSON_UNESCAPED_UNICODE));
      return false;
    }

    $title = Helper::clean($data->title);
    $is_active = $data->is_active == true;

    if (!Helper::check_length($title, 1, 40) || !is_bool($is_active)) {
      self::sendResponse(400, json_encode(self::getErrorWithMessage('Введены некорректные данные.'), JSON_UNESCAPED_UNICODE));
      return false;
    }

    $description = isset($data->description) ? Helper::clean($data->description) : NULL;
    $is_active = boolval($is_active);

    $query = 'INSERT INTO items (list_id, title, description, is_active) VALUES (?, ?, ?, ?)';
    $stmt = $this->db->instance->prepare($query);
    $stmt->bind_param('issi', $list_id, $title, $description, $is_active);
    $stmt->execute();

    $stmt->close();

    $item_id = $this->db->instance->insert_id;

    $this->db->instance->commit();

    $insert_date = date('Y-m-d H:i:s');
    $update_date = NULL;

    $result = [
      'item_id' => $item_id,
      'list_id' => $list_id,
      'title' => $title,
      'description' => $description,
      'is_active' => $is_active,
      'insert_date' => date('c', strtotime($insert_date)),
      'update_date' => $update_date
    ];
    self::sendResponse(200, json_encode($result, JSON_UNESCAPED_UNICODE));
    return true;
  }

}

$action = new ItemsCreateAction;
$action->action();

?>
