<?php

include_once 'helper.php';

abstract class Security {

  # Генерация токена
  public static function generate_token($db) {
    do {
      $token = bin2hex(random_bytes(32));

      $query = 'SELECT app_id FROM access WHERE token = ?';
      $stmt = $db->instance->prepare($query);
      $stmt->bind_param('s', $token);
      $stmt->execute();

      $stmt->bind_result($app_id);
      while ($stmt->fetch()) {
        break;
      }

      $stmt->close();
    } while ($app_id > 0);

    return $token;
  }

  # Получение AppId
  public static function get_app_id() {
    $headers = apache_request_headers();
    $app_id = Helper::clean($headers['X-Auth-AppId'] ?? '');

    return $app_id;
  }

  # Получение токена
  public static function get_token() {
    $headers = apache_request_headers();
    $token = Helper::clean($headers['X-Auth-Token'] ?? '');

    return $token;
  }

  # Проверка токена
  public static function check_token($db) {
    $headers = apache_request_headers();

    $app_id = Helper::clean($headers['X-Auth-AppId'] ?? '');
    $token = Helper::clean($headers['X-Auth-Token'] ?? '');

    if (!is_numeric($app_id) || mb_strlen($token) != 64) {
      return false;
    }

    $app_id = intval($app_id);

    $query = 'SELECT user_id, token FROM access WHERE app_id = ?';
    $stmt = $db->instance->prepare($query);
    $stmt->bind_param('i', $app_id);
    $stmt->execute();

    $stmt->bind_result($user_id, $_token);
    while ($stmt->fetch()) {
      break;
    }

    $stmt->close();

    if ($user_id <= 0 || $token != $_token) {
      return false;
    }

    return $user_id;
  }

}

?>
