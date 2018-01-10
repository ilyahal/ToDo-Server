<?php

include_once '../core/helper.php';
include_once '../core/security.php';
include_once '../core/action.php';
include_once '../core/database.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

class ListsEditAction extends API_Action {

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

    $query = 'SELECT user_id, insert_date FROM lists WHERE list_id = ?';
    $stmt = $this->db->instance->prepare($query);
    $stmt->bind_param('i', $list_id);
    $stmt->execute();

    $stmt->bind_result($_user_id, $insert_date);
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

    if (!isset($data->title) || !isset($data->color_id) || !isset($data->icon_id)) {
      self::sendResponse(400, json_encode(self::getErrorWithMessage('Введите необходимые данные.'), JSON_UNESCAPED_UNICODE));
      return false;
    }

    $title = Helper::clean($data->title);
    $color_id = $data->color_id;
    $icon_id = $data->icon_id;

    if (!Helper::check_length($title, 1, 40) || !is_numeric($color_id) || !is_numeric($icon_id)) {
      self::sendResponse(400, json_encode(self::getErrorWithMessage('Введены некорректные данные.'), JSON_UNESCAPED_UNICODE));
      return false;
    }

    $description = isset($data->description) ? Helper::clean($data->description) : NULL;
    $color_id = intval($color_id);
    $icon_id = intval($icon_id);
    $update_date = date('Y-m-d H:i:s');

    $query = 'UPDATE lists SET title = ?, description = ?, color_id = ?, icon_id = ?, update_date = ? WHERE list_id = ?';
    $stmt = $this->db->instance->prepare($query);
    $stmt->bind_param('ssiisi', $title, $description, $color_id, $icon_id, $update_date, $list_id);
    $stmt->execute();

    $stmt->close();

    $this->db->instance->commit();

    $result = [
      'list_id' => $list_id,
      'title' => $title,
      'description' => $description,
      'color_id' => $color_id,
      'icon_id' => $icon_id,
      'insert_date' => date('c', strtotime($insert_date)),
      'update_date' => date('c', strtotime($update_date))
    ];
    self::sendResponse(200, json_encode($result, JSON_UNESCAPED_UNICODE));
    return true;
  }

}

$action = new ListsEditAction;
$action->action();

?>
