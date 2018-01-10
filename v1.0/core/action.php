<?php

abstract class API_Action {

  # Создание ответа, содержащего ошибку
  protected function getErrorWithMessage($message) {
    $result = [
      "message" => $message
    ];

    return $result;
  }

  # Получение сообщения для кода ответа
  private function getStatusCodeMessage($status) {
    $codes = [
      100 => 'Continue',
      101 => 'Switching Protocols',
      102 => 'Processing',
      200 => 'OK',
      201 => 'Created',
      202 => 'Accepted',
      203 => 'Non-Authoritative Information',
      204 => 'No Content',
      205 => 'Reset Content',
      206 => 'Partial Content',
      207 => 'Multi-Status',
      208 => 'Already Reported',
      226 => 'IM Used',
      300 => 'Multiple Choices',
      301 => 'Moved Permanently',
      302 => 'Found',
      303 => 'See Other',
      304 => 'Not Modified',
      305 => 'Use Proxy',
      306 => '(Unused)',
      307 => 'Temporary Redirect',
      308 => 'Permanent Redirect',
      400 => 'Bad Request',
      401 => 'Unauthorized',
      402 => 'Payment Required',
      403 => 'Forbidden',
      404 => 'Not Found',
      405 => 'Method Not Allowed',
      406 => 'Not Acceptable',
      407 => 'Proxy Authentication Required',
      408 => 'Request Timeout',
      409 => 'Conflict',
      410 => 'Gone',
      411 => 'Length Required',
      412 => 'Precondition Failed',
      413 => 'Request Entity Too Large',
      414 => 'Request-URI Too Long',
      415 => 'Unsupported Media Type',
      416 => 'Requested Range Not Satisfiable',
      417 => 'Expectation Failed',
      418 => 'I\'m a teapot',
      421 => 'Misdirected Request',
      422 => 'Unprocessable Entity',
      423 => 'Locked',
      424 => 'Failed Dependency',
      426 => 'Upgrade Required',
      428 => 'Precondition Required',
      429 => 'Too Many Requests',
      431 => 'Request Header Fields Too Large',
      449 => 'Retry With',
      451 => 'Unavailable For Legal Reasons',
      500 => 'Internal Server Error',
      501 => 'Not Implemented',
      502 => 'Bad Gateway',
      503 => 'Service Unavailable',
      504 => 'Gateway Timeout',
      505 => 'HTTP Version Not Supported',
      506 => 'Variant Also Negotiates',
      507 => 'Insufficient Storage',
      508 => 'Lood Detected',
      509 => 'Bandwidth Limit Exceeded',
      510 => 'Not Extended',
      511 => 'Network Authentication Required',
      520 => 'Unknown Error',
      521 => 'Web Server Is Down',
      522 => 'Connection Timed Out',
      523 => 'Origin Is Unreachable',
      524 => 'A Timeout Occurred',
      525 => 'SSL Handshake Failed',
      526 => 'Invalid SSL Certificate'
    ];

    return $codes[$status] ?? '';
  }

  # Отправить ответ
  protected function sendResponse($status = 200, $body = '', $content_type = 'application/json; charset=UTF-8') {
    $status_header = 'HTTP/1.1 ' . $status . ' ' . self::getStatusCodeMessage($status);
    header($status_header);
    header('Content-type: ' . $content_type);

    echo $body;
  }

  # Действие
  abstract public function action();

}

?>
