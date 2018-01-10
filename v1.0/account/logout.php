<?php

include_once '../core/helper.php';
include_once '../core/security.php';
include_once '../core/action.php';
include_once '../core/database.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

class AccountLogoutAction extends API_Action {

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

    $app_id = Security::get_app_id();

    $query = 'DELETE FROM access WHERE app_id = ?';
    $stmt = $this->db->instance->prepare($query);
    $stmt->bind_param('i', $app_id);
    $stmt->execute();

    $stmt->close();

    $this->db->instance->commit();

    self::sendResponse(200, '', 'text/html');
    return true;
  }

}

$action = new AccountLogoutAction;
$action->action();

?>
