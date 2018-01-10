<?php

abstract class Helper {

  # Очистить строку
  public static function clean($value = '') {
    $value = trim($value); // Удаление пробелов из начала и конца
    $value = stripslashes($value); // Удаление экранированных символов
    $value = strip_tags($value); // Удаление HTML и PHP тегов

    return $value;
  }

  # Проверка длины
  public static function check_length($value = '', $min, $max) {
    $result = (mb_strlen($value) < $min || mb_strlen($value) > $max);
    return !$result;
  }

}

?>
