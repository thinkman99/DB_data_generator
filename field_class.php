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
    
private function check_not_null() { //�������� ����������� ������������� NOT NULL ��� ���������� ���� ����.
  if (($this->nnull == 'NOT NULL') and ($r->generate_rand_data($this->type) === 'NULL')) {
    exit ("\nError! Can't generate NOT NULL.");
    return (true); 
  }
  return (true);
}    

function generate_data($mysqli, $selected_table) {
  $r = new Random_class;
  
  if ($this->auto_increment === 'AUTO_INCREMENT') return (true); //������ �� ���������� - �������������, ����� ��������� ������ SQL - ���������
  //if ($this->current_timestamp == 'CURRENT_TIMESTAMP') return (true); //������ �� ���������� - �� ��������� CURRENT_TIMESTAMP
  if (($this->key === 'PRI') or ($this->key === 'UNI')) {
    do {    //��������� ���� NOT NULL. ���� ��� ����������� '����' generate_rand_data() ������ ����� NULL - ����������� ����.
      do {
        $this->value = $mysqli->real_escape_string($r->generate_rand_data($this->type));
        $sql = "SELECT `".$this->name."` FROM `".$selected_table."` WHERE `".$this->name."` = '".$this->value."'";
        $result = $mysqli->query($sql); 
        $row = $result->fetch_row();
        if ($row[0] !== null) {
          echo '^'; //��������� ������ - ������ ��������� ���� ���� PRI ��� UNI
          //var_dump($row);
        }
      } while ($row[0] !== null);
    } while (($this->nnull == 'NOT NULL') and ($this->value == 'NULL'));
    return (true);
  }
  //FK - � �������� ��������
  if (($this->key === 'MUL') and ($this->engine_selected_table=='InnoDB')) {
    echo "Foreign key. Panic!!!\n";
    return(false);
  }
  // ���� ����� �� ����� ����� �� �� AI, PRI, UNI - �������
  do {    //��������� ���� NOT NULL. ���� ��� ����������� '����' generate_rand_data() ������ ����� NULL - ����������� ����.
    $this->value = $mysqli->real_escape_string($r->generate_rand_data($this->type));
  } while (($this->nnull == 'NOT NULL') and ($this->value == 'NULL'));              

  return (true);  
}

}
?>