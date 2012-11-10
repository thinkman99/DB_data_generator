#!/usr/local/bin/php

<?php
include_once("transaction_class.php");

error_reporting(1);
$a = new Transaction_class;
$action = -1;
while (true) {
  $a->show_menu();
  $action = trim(fgets(STDIN));
  if ($action == 9) exit ('Bye!');
  if ($action == 1) $a->connect(); //ввести информацию для подключения: хост, пользователь, пароль, база, таблица.
  if ($action == 2) $a->select_db(); //выбрать базу
  if ($action == 3) $a->select_table(); //выбрать таблицу
  if ($action == 4) $a->show_table_structure(); //показать информацию о таблице
  if ($action == 5) $a->insert_data(); //вставить набор данных
//показать набор данных
//вставить набор данных  
  
};

unset ($a);
?>