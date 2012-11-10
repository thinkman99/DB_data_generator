<?php
include_once("field_class.php");

class Row_class {

public $fields = array();
public $num_fields = 0;
public $sql = null;

function insert_row() {
  return (true);
}

function show_data() {
  return (true);
}

function generate_row($mysqli, $selected_table) {
  //прочитать данные о типе полей из таблицы, сформировать шаблон строки для вставки
  $sql="SHOW FULL COLUMNS FROM `$selected_table`";
  $result = $mysqli->query($sql);
  if ($mysqli->error) {
    echo $mysqli->error;
    return (false);
  }
  $this->num_fields = 0;
  while ($row = $result->fetch_row()) {
    $this->fields[$this->num_fields] = new Field_class;
    $this->fields[$this->num_fields]->name = $row[0]; //name field
    $this->fields[$this->num_fields]->type = strtoupper($row[1]); //type field
    if ($row[3] != 'YES') $this->fields[$this->num_fields]->nnull = 'NULL';
      else $this->fields[$this->num_fields]->nnull = 'NOT NULL';
    if (stripos($row[4], 'PRI') !== false) $this->fields[$this->num_fields]->key = 'PRI';
    if (stripos($row[4], 'MUL') !== false) $this->fields[$this->num_fields]->key = 'MUL';
    if (stripos($row[4], 'UNI') !== false) $this->fields[$this->num_fields]->key = 'UNI';
    if (stripos($row[5], 'current_timestamp') !== false) $this->fields[$this->num_fields]->current_timestamp = 'CURRENT_TIMESTAMP';
    if (stripos($row[6], 'auto_increment') !== false) $this->fields[$this->num_fields]->auto_increment = 'AUTO_INCREMENT'; 
    if (!$this->fields[$this->num_fields]->generate_data($mysqli, $selected_table)) {
      echo "\nError! Can't generate random data.";
      return (false);
    }
    $this->num_fields += 1;
  }
  //создаем запрос SQL для вставки строки
  $name_columns = '';
  $values = '';
  for ($i = 0; $i <= ($this->num_fields - 1); $i++) {
    if ($this->fields[$i]->auto_increment != 'AUTO_INCREMENT') {
      $name_columns .= "`".strval($this->fields[$i]->name)."`,";
      $values .= "'".strval($this->fields[$i]->value)."',";
    }
  }
  $name_columns = rtrim($name_columns, ',');
  $values = rtrim($values, ',');
  $this->sql = "INSERT INTO `$selected_table` ($name_columns) VALUES ($values)";
  return (true);
}    
    
}
?>