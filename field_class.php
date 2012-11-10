<?php
include_once ('random_class.php');

class Field_class {
  public $name = null;
  public $type = null;
  public $nnull = null;
  public $key = null;
  public $current_timestamp = null;
  public $auto_increment = null;
  public $value = null;
    
private function check_not_null() { //проверка возможности сгенерировать NOT NULL для требуемого типа поля.
  if (($this->nnull == 'NOT NULL') and ($r->generate_rand_data($this->type) === 'NULL')) {
    exit ("\nError! Can't generate NOT NULL.");
    return (true); 
  }
  return (true);
}    

function generate_data($mysqli, $selected_table) {
  $r = new Random_class;
  
  if ($this->auto_increment === 'AUTO_INCREMENT') return (true); //ничего не генерируем - автоинкремент, когда формируем запрос SQL - исключаем
  //if ($this->current_timestamp == 'CURRENT_TIMESTAMP') return (true); //ничего не генерируем - по умолчанию CURRENT_TIMESTAMP
  if (($this->key === 'PRI') or ($this->key === 'UNI')) {
    do {    //проверить поле NOT NULL. если нет обработчика 'типа' generate_rand_data() вернет опять NULL - бесконечный цикл.
      do {
        $this->value = $mysqli->real_escape_string($r->generate_rand_data($this->type));
        $sql = "SELECT `".$this->name."` FROM `".$selected_table."` WHERE `".$this->name."` = '".$this->value."'";
        $result = $mysqli->query($sql); 
        $row = $result->fetch_row();
        if ($row[0] !== null) {
          echo '^'; //индикатор работы - повтор генерации если поле PRI или UNI
          //var_dump($row);
        }
      } while ($row[0] !== null);
    } while (($this->nnull == 'NOT NULL') and ($this->value == 'NULL'));
    return (true);
  }
  //FK - в будующем появится
  if (($this->key === 'MUL') and ($this->engine_selected_table=='InnoDB')) {
    echo "Foreign key. Panic!!!\n";
    return(false);
  }
  // если дошли до этого места то не AI, PRI, UNI - создать
  do {    //проверить поле NOT NULL. если нет обработчика 'типа' generate_rand_data() вернет опять NULL - бесконечный цикл.
    $this->value = $mysqli->real_escape_string($r->generate_rand_data($this->type));
  } while (($this->nnull == 'NOT NULL') and ($this->value == 'NULL'));              

  return (true);  
}

}
?>