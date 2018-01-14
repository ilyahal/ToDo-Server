<?php

# Подключаем файл с данными для соединения к базой данных
require_once '../config/connection.php';

class Database {

  # Экземпляр базы данных
  public $instance;

  function __construct() {

    # Устанавливаем новое соединение с сервером MySQL
    $this->instance = new mysqli(Connection::HOST, Connection::USER, Connection::PASSWORD, Connection::DATABASE);
    # Устанавливаем кодировку соединения
    $this->instance->set_charset('utf8mb4');
    # Отключаем автоматическую фиксацию изменений базы данных
    $this->instance->autocommit(FALSE);
  }

  function __destruct() {

    # Закрываем ранее открытое соединение с базой данных
    $this->instance->close();
  }

}

?>
