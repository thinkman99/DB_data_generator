<?php
include_once("row_class.php");

class Transaction_class {

public $mysqli = null; //������ � ������������ �����
//public $row = array();  //��� ������� ����������� ����� �������� ����� ��� ������ - ������� ������ �������.
//public $num_rows = 0;   //������� ������� ������� ���������� ������� 
public $row = null;       //��� ������������� $row ������ ��������
public $connected_host = null;
public $connected_db = null;
public $selected_table = null;
public $engine_selected_table = null;

private function if_table_exist ($input) {  //
  $sql="SHOW TABLE STATUS IN `$this->connected_db` LIKE '$input'";
  if (!$result = $this->mysqli->query($sql)) echo $this->mysqli->error;
  if ($row = $result->fetch_row()) {
    $this->engine_selected_table = $row[1];
    $this->selected_table = $input;
    return (true);
  } else {
    echo "Table '$input' not found.";
    $this->selected_table = null;
    return (false);
  }
  return (false);
}

private function show_num_line($table) {    //
    $sql="SELECT COUNT(*) FROM `$table`";
    if (!$result = $this->mysqli->query($sql)) echo $this->mysqli->error;
    $row = $result->fetch_row();
    return ($row[0]);
}

function show_menu() {
    //����� ����
    echo "\n\n";
    $up = '+'; for ($i = 0; $i <= 10; $i++) $up.='-'; $up.="   MENU   "; for ($i = 0; $i <= 10; $i++) $up.='-'; $up.='+'."\n";
    echo $up;
    echo '|'."(1) Connect to host.\t\t ".'|'."\n";
    echo '|'."(2) Select database.\t\t ".'|'."\n";
    echo '|'."(3) Select table.\t\t ".'|'."\n";
    echo '|'."(4) Show table structure.\t ".'|'."\n";
    echo '|'."(5) Insert data to table.\t ".'|'."\n";
    echo '|'."(9) Exit.\t\t\t ".'|'."\n";
    $up = '+'; for ($i = 0; $i <= 31; $i++) $up.='-'; $up.='+';
    echo $up;
    //����� �������������� ����������
    if (isset($this->connected_host)) echo "\nConnected::".$this->mysqli->host_info;
    if (isset($this->connected_db)) echo "\nDatabase::".$this->connected_db;	
    if (isset($this->selected_table)) echo "\nTable::".$this->selected_table.' ('.$this->show_num_line($this->selected_table).' lines, '.$this->engine_selected_table.')';
    echo "\nSelect action: ";
}

function connect() {
  $this->connected_db = null;
  $this->selected_table = null;
  //���������� � ������������
  
  //����
  echo "\nEnter database URL or press Enter for 'localhost': ";
  $input = trim(fgets(STDIN));
  if (strlen($input)<=0) $db_url = 'localhost'; 
    else $db_url = $input;
  //������������
  echo 'Enter \'username\': ';
  $db_user = trim(fgets(STDIN));
  //������
  echo 'Enter \'password\': ';
  $db_password = trim(fgets(STDIN));
  //������� ������������
  $this->mysqli = new mysqli( $db_url, $db_user, $db_password);
  if ($this->mysqli->connect_errno) {
    printf("Error: %s\n", $this->mysqli->connect_error);
    $this->connected_host = null;
    return (false);
  }
  $this->connected_host = true;
  return (true);
}

function select_db() {
  //�������� ����������� � �����, ���� ��� - ����� ������
  if (!isset($this->mysqli->host_info)) {
    echo "Error! Can't select database. You need connect to host.\n";
    return (false);
  }
  //����� ������������ ��
  $sql='SHOW DATABASES';
  $result = $this->mysqli->query($sql);
  if ($this->mysqli->error) {
    echo $this->mysqli->error;
    return (false);
  }
  echo "\nFound the following databases:\n";
  $i=0;
  $f = true;
  while ($row = $result->fetch_row()) {
    $f = true;
    $i++;
    printf ("($i)\t%s\n", $row[0]);
    //������ 30 ������� �������� ����� ������������.
    if ($i%30 == 0) {
      echo "Press 'Enter' to continue, or enter a database name: ";
      $input = trim(fgets(STDIN));
      if (strlen($input)>0) {
        if (!$this->mysqli->select_db($input)) {
          echo "Database '$input' not found.";
          $this->connected_db = null;
          return (false);
        } else $this->connected_db = $input;
      }
      //��� ���������� ���������� ������ ����� ������ �� ����� while
      $f = false;
    }
  }
  //���� �������� ��������� ���� ���� ��� �������� - �����
  if ($f == false) return (false);
  //�������� ����� ������������
  echo "Press 'Enter' to continue, or enter a database name: ";
  $input = trim(fgets(STDIN));
  if (strlen($input)>0)
  if (!$this->mysqli->select_db($input)) {
    echo "Database '$input' not found.";
    $this->connected_db = null;
    return (false);
  } else $this->connected_db = $input;
	return (true);
}

function select_table() {
  $this->selected_table = null;
  //���� ������ �������?
  if (!isset($this->connected_db)) {
    echo "Error! Can't select table. You need select database.\n";
    return (false);
  }
  //�������� ������������ ������
  $sql="SHOW TABLES FROM `$this->connected_db`";
  $result = $this->mysqli->query($sql);
  if ($this->mysqli->error) {
    echo $this->mysqli->error;
    return (false);
  }
  echo "\nFound the following tables (rows):\n";
  $i=0;
  $f = true;
  while ($row = $result->fetch_row()) {
    $f = true;
    $i++;
    printf ("($i) %s (%s)\n", $row[0], $this->show_num_line($row[0]));
    if ($i%30==0) {
      echo "Press 'Enter' to continue, or enter a table name: ";
      $input = trim(fgets(STDIN));
      if (strlen($input)>0) 
        if ($this->if_table_exist($input)) return (true);  
          else return (false);
      $f = false;
    }
  }
  if ($f===false) return (false);
  echo "Press 'Enter' to continue, or enter a table name: ";
  $input = trim(fgets(STDIN));
  if (strlen($input)>0)
    if (strlen($input)>0) 
      if ($this->if_table_exist($input)) return (true);
        else return (false);
  return (true);
}

function show_table_structure() {   //
  //������� �������?
  if (!isset($this->selected_table)) {
    echo "Error! Can't show structures of table. You need select table.\n";
    return (false);
  }
  //����� ���������� � ����� �������
	$sql="SHOW FULL COLUMNS FROM `$this->selected_table`";
  $result = $this->mysqli->query($sql);
  if ($this->mysqli->error) {
    echo $this->mysqli->error;
    return (false);
  }
  if ($result = $this->mysqli->query($sql)) {
    echo "\nStructures of table:\n";
    $i=0;
    while ($row = $result->fetch_row()) {
      $i++;
      echo '<'.$row[0].'>';
      echo ' - '.strtoupper($row[1]).',';
      if ($row[3] == 'YES') echo ' NOT NULL,';
      if ($row[4] != null) echo ' '.strtoupper($row[4]).',';
      if ($row[5] != null) echo ' '.strtoupper($row[5]).',';
      if ($row[6] != null) echo ' '.strtoupper($row[6]).',';
      echo "\n";
      //������ 30 ����� ������� ������� �� ����
      if ($i%30==0) {
		    echo "Press 'Enter' to continue...";
			  trim(fgets(STDIN));
		  }
    }
  }

}

function insert_data() {
  //������� �������?
  if (!isset($this->selected_table)) {
    echo "Error! Can't generate and insert data to table. You need select table.\n";
    return (false);
  }
  $oneline = false;
  $eachline = false;
  echo 'How much lines? > ';
  $input = trim(fgets(STDIN));
  echo 'Show one generated line? <N> ';
  if ((stripos(trim(fgets(STDIN)), 'Y')) !== false) $oneline = true;
  echo 'Show each generated line? <N> ';
  if ((stripos(trim(fgets(STDIN)), 'Y')) !== false) $eachline = true;
  
  $this->row = new Row_class; //��� ��� ��� ������� ����� ������� 1 ���������
  for ($i=1; $i<=$input; $i++) { 
    if ($i%100 == 0) echo '.';
    if ($i%1000 == 0) echo $i;
    if (!$this->row->generate_row($this->mysqli, $this->selected_table)) {
      echo 'Error! Data for inserting not found!';
      return (false);
    } else { 
      if ($oneline===true or $eachline===true) {
        $oneline = false;
        //����� ��������������� ������� SQL
        echo "\n".$this->row->sql;
        echo "\nOk? <Y> ";
        //������ � �������? ���������.
        //��� - ��������� ����������.
        if ((stripos(trim(fgets(STDIN)), 'N')) !== false) return (false);
        if (!$result = $this->mysqli->query($this->row->sql)) {
          //���� �� ����������� ������� ������ ������� ���������
          echo $this->mysqli->error;
          return (false);
        }
      } else {
        //������������ �� ����� ������������� ������ ����� ��������.
        if (!$result = $this->mysqli->query($this->row->sql)) {
          echo $this->mysqli->error;
          return (false);
        }
      }
    }
  }
}

}
?>