<?php

include_once '../core/action.php';
include_once '../core/database.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

class ColorsListAction extends API_Action {

  private $db;

  function __construct() {
    $this->db = new Database;
  }

  public function action() {
    $query = 'SELECT color_id, red, green, blue, insert_date, update_date FROM colors';
    $stmt = $this->db->instance->prepare($query);
    $stmt->execute();

    $result = array();

    $stmt->bind_result($color_id, $red, $green, $blue, $insert_date, $update_date);
    while ($stmt->fetch()) {
      $icon = [
        'color_id' => $color_id,
        'red' => $red,
        'green' => $green,
        'blue' => $blue,
        'insert_date' => date('c', strtotime($insert_date)),
        'update_date' => empty($update_date) ? NULL : date('c', strtotime($update_date))
      ];

      $result[] = $icon;
    }

    $stmt->close();

    self::sendResponse(200, json_encode($result, JSON_UNESCAPED_UNICODE));
    return true;
  }

}

$action = new ColorsListAction;
$action->action();

?>
