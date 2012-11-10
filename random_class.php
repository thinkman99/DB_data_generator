<?php
class Random_class {

private function gen_big($min, $max) {
    $_max = mt_getrandmax();
    if ((floatval($max) <= $_max) and (floatval($min) > -$_max)) return mt_rand($min, $max);
    if (floatval($min)==0) {  //Если $min = 0, то UNSIGNED 
        $len = mt_rand(0, strlen($max));
        do {
            $gen = '';
            for ($i = 1; $i <= $len-1; $i++) $gen .= chr(rand(48, 57));
            //if (!isset($gen)) $gen = '0';
            //if (isset($gen[0])=='0' and isset($gen[1])) $gen[0] = '1';
        } while (bccomp($gen, $max) > 0);
    }
    if (floatval($min) < 0) {  //Если $min < 0, то SIGNED 
        $len = mt_rand(0, strlen($max));
        do {
            if (rand(1, 10)>=5) $gen = '';
                else $gen = '-';
            for ($i = 1; $i <= $len-1; $i++) $gen .= chr(rand(48, 57));
        } while ((bccomp($gen, $max) > 0) or (bccomp($gen, $min) < 0));
    }
    while (isset($gen{0}) and $gen{0}=='0') $gen = ltrim ($gen, '0');
    if (isset($gen{0}) and $gen{0}=='-') 
        while (isset($gen{1}) and $gen{1}=='0') $gen = substr_replace ($gen, '', 1, 1);
    if (!isset($gen{1})) $gen = '0';
    return $gen;
}

public function generate_rand_data ($type) {
    if (isset($type) === false) return (false);
    
    /// CHAR, VARCHAR 
    if (stripos($type, 'CHAR') !== false) {
        $size = substr($type, strpos($type, '(')+1, strpos($type, ')')-strpos($type, '(')-1);
        $str = '';
        for ($i=1; $i<=$size; $i++) $str .= chr(mt_rand(32, 126));
        return $str;
    }
    /// BINARY, VARBINARY 
    if (stripos($type, 'BINARY') !== false) {
        $size = substr($type, strpos($type, '(')+1, strpos($type, ')')-strpos($type, '(')-1);
        $str = '';
        for ($i=1; $i<=$size; $i++) $str .= chr(mt_rand(32, 126));
        return $str;
    }
    ///  TINYBLOB 256
    if ($type==='TINYBLOB') {
        $str = '';
        $j = mt_rand(0, 1000);
        for ($i=1; $i<=$j; $i++) $str .= chr(mt_rand(0, 255));
        return $str;
    }

    /// BLOB 65 536
    if ($type==='BLOB') {
        $str = '';
        $j = mt_rand(0, 1000);
        for ($i=1; $i<=$j; $i++) $str .= chr(mt_rand(0, 255));
        return $str;
    }

    /// MEDIUMBLOB 16 777 216
    if ($type==='MEDIUMBLOB') {
        $str = '';
        $j = mt_rand(0, 1000); // $str очень долго генерится на полный диапазон
        for ($i=1; $i<=$j; $i++) $str .= chr(mt_rand(0, 255));
        return $str;
    }
    /// LONGBLOB 4 294 967 296
    if ($type==='LONGBLOB') {
        $str = '';
        $j = mt_rand(0, 1000); // $str очень долго генерится на полный диапазон
        for ($i=1; $i<=$j; $i++) $str .= chr(mt_rand(0, 255));
        return $str;
    }
    
    ///  TINYTEXT 256
    if ($type==='TINYTEXT') {
        $str = '';
        $j = mt_rand(0, 1000);
        for ($i=1; $i<=$j; $i++) $str .= chr(mt_rand(32, 126));
        return $str;
    }

    /// TEXT 65 536
    if ($type==='TEXT') {
        $str = '';
        $j = mt_rand(0, 1000);
        for ($i=1; $i<=$j; $i++) $str .= chr(mt_rand(32, 126));
        return $str;
    }

    /// MEDIUMTEXT 16 777 216
    if ($type==='MEDIUMTEXT') {
        $str = '';
        $j = mt_rand(0, 1000); // $str очень долго генерится на полный диапазон gen_big()
        for ($i=1; $i<=$j; $i++) $str .= chr(mt_rand(32, 126));
        return $str;
    }
    /// LONGTEXT 4 294 967 296 
    if ($type==='LONGTEXT') {
        $str = '';
        $j = mt_rand(0, 1000); // $str очень долго генерится на полный диапазон gen_big()
        for ($i=1; $i<=$j; $i++) $str .= chr(mt_rand(32, 126));
        return $str;
    }
    
    //Численные типы - ЦЕЛОЧИСЛЕННЫЕ, если в таблице установлены ограничения по длине меньше допустимых - отдаем на обрезку MySQL
    //BOOL - идет как TINYINT
     
    if (stripos($type, 'TINYINT') !== false)
        if (stripos($type, 'UNSIGNED') !== false) return mt_rand(0, 254);
            else return mt_rand(-254/2, 254/2);
    if (stripos($type, 'SMALLINT') !== false)
        if (stripos($type, 'UNSIGNED') !== false) return mt_rand(0, 65534);
            else return mt_rand(-65534/2, 65534/2);
    if (stripos($type, 'MEDIUMINT') !== false)
        if (stripos($type, 'UNSIGNED') !== false) return mt_rand(0, 16777214);
            else return mt_rand(-16777214/2, 16777214/2);
    if ((stripos($type, 'INT') !== false) and (strpos($type, '(')==3)) 
        if (stripos($type, 'UNSIGNED') !== false) return $this->gen_big(0, '4294967294');
            else return $this->gen_big('-2147483648', '2147483647');
    if (stripos($type, 'BIGINT') !== false) 
        if (stripos($type, 'UNSIGNED') !== false) return $this->gen_big(0, '18446744073709551614');
            else return $this->gen_big('-9223372036854775808', '9223372036854775807');
   
    //DECIMAL, FLOAT, DOUBLE /// NUMERIC,REAL
    if ($type === 'DECIMAL') return mt_rand(0, mt_getrandmax());
    if (stripos($type, 'DECIMAL') !== false) {
      $size_1 = substr($type, strpos($type, '(')+1, strpos($type, ',')-strpos($type, '(')-1);
      $size_2 = substr($type, strpos($type, ',')+1, strpos($type, ')')-strpos($type, ',')-1);
      $ret = round(mt_rand(1, (pow(10,($size_1-$size_2))-1))*sin(mt_rand(1, mt_getrandmax())), $size_2);
      if (stripos($type, 'UNSIGNED') === false) return $ret;
        else return abs($ret);
    }
    //
    if ($type === 'DOUBLE') return mt_rand(0, mt_getrandmax());
    if (stripos($type, 'DOUBLE') !== false) {
      $size_1 = substr($type, strpos($type, '(')+1, strpos($type, ',')-strpos($type, '(')-1);
      $size_2 = substr($type, strpos($type, ',')+1, strpos($type, ')')-strpos($type, ',')-1);
      $ret = round(mt_rand(1, (pow(10,($size_1-$size_2))-1))*sin(mt_rand(1, mt_getrandmax())), $size_2);
      if (stripos($type, 'UNSIGNED') === false) return $ret;
        else return abs($ret);
    }
    //
    if ($type === 'FLOAT') return mt_rand(0, 999999);
    if (stripos($type, 'FLOAT') !== false) {
      $size_1 = substr($type, strpos($type, '(')+1, strpos($type, ',')-strpos($type, '(')-1);
      $size_2 = substr($type, strpos($type, ',')+1, strpos($type, ')')-strpos($type, ',')-1);
      $ret = round(mt_rand(1, (pow(10,($size_1-$size_2))-1))*sin(mt_rand(1, mt_getrandmax())), $size_2);
      if (stripos($type, 'UNSIGNED') === false) return $ret;
        else return abs($ret);
    }
    
    
    //BIT
    if (stripos($type, 'BIT') !== false) return mt_rand(0, 65535);
    
    ///DATE DATETIME TIMESTAMP TIME YEAR[2 4]
    
    if ($type == 'DATE') return date('Y-m-d', mt_rand(0, mt_getrandmax()));
    if ($type == 'DATETIME') return date('Y-m-d H:i:s', mt_rand(0, mt_getrandmax()));
    if ($type == 'TIMESTAMP') return mt_rand(0, mt_getrandmax());
    if ($type == 'TIME') return date( 'H:i:s', mt_rand(0, mt_getrandmax()));
    if ((stripos($type, 'YEAR') !== false) and (strpos($type, '4') !== false)) return date('Y', mt_rand(0, mt_getrandmax()));
    if ((stripos($type, 'YEAR') !== false) and (strpos($type, '2') !== false)) return date( 'y', mt_rand(0, mt_getrandmax()));
    
    /// ENUM , SET--- проблема,
    
    ///

    echo "\nUnknown data type $type set in NULL\n";
    return 'NULL';
 }
 //end
 }
 ?>