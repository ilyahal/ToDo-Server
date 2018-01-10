<?php

include_once '../core/helper.php';
include_once '../core/security.php';
include_once '../core/action.php';
include_once '../core/database.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

class ListsListAction extends API_Action {

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

    $query = 'SELECT list_id, title, description, color_id, icon_id, insert_date, update_date FROM lists WHERE user_id = ?';
    $stmt = $this->db->instance->prepare($query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();

    $result = array();

    $stmt->bind_result($list_id, $title, $description, $color_id, $icon_id, $insert_date, $update_date);
    while ($stmt->fetch()) {
      $list = [
        'list_id' => $list_id,
        'title' => $title,
        'description' => empty($description) ? NULL : $description,
        'color_id' => $color_id,
        'icon_id' => $icon_id,
        'insert_date' => date('c', strtotime($insert_date)),
        'update_date' => empty($update_date) ? NULL : date('c', strtotime($update_date))
      ];

      $result[] = $list;
    }

    $stmt->close();

    self::sendResponse(200, json_encode($result, JSON_UNESCAPED_UNICODE));
    return true;
  }

}

$action = new ListsListAction;
$action->action();

?>
