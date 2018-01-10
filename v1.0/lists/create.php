<?php

include_once '../core/helper.php';
include_once '../core/security.php';
include_once '../core/action.php';
include_once '../core/database.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

class ListsCreateAction extends API_Action {

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

    $query = 'INSERT INTO lists (user_id, title, description, color_id, icon_id) VALUES (?, ?, ?, ?, ?)';
    $stmt = $this->db->instance->prepare($query);
    $stmt->bind_param('issii', $user_id, $title, $description, $color_id, $icon_id);
    $stmt->execute();

    $stmt->close();

    $list_id = $this->db->instance->insert_id;

    $this->db->instance->commit();

    $insert_date = date('Y-m-d H:i:s');
    $update_date = NULL;

    $result = [
      'list_id' => $list_id,
      'title' => $title,
      'description' => $description,
      'color_id' => $color_id,
      'icon_id' => $icon_id,
      'insert_date' => date('c', strtotime($insert_date)),
      'update_date' => $update_date
    ];
    self::sendResponse(200, json_encode($result, JSON_UNESCAPED_UNICODE));
    return true;
  }

}

$action = new ListsCreateAction;
$action->action();

?>
