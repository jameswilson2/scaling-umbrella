<?php

$dbcnx = @mysql_connect(DB_SERVER, DB_USER, DB_PASSWORD);
if (!$dbcnx) {
  exit('<p>Unable to connect to the ' .
      'database server at this time.</p>');
}

if (!@mysql_select_db(DB_DATABASE)) {
  exit('<p>Unable to locate the comments ' .
      'database at this time.</p>');
}

function create_pdo(){
    $host = DB_SERVER;
    $dbname = DB_DATABASE;
    $username = DB_USER;
    $password = DB_PASSWORD;
    $dsn = "mysql:dbname=$dbname;host=$host";
    return new PDO($dsn, $username, $password, array(PDO::ATTR_PERSISTENT => true));
}

function bind($statement, $values){
    foreach($values as $key => $value){
        
        if(is_numeric($key)){
            $key += 1;
        }
        
        $statement->bindValue($key, $value);
    }
}

function fetch_one($pdo, $sql, $values = array()){
    $select = $pdo->prepare($sql);
    bind($select, $values);
    if($select->execute()){
        return $select->fetch(PDO::FETCH_ASSOC);
    }
}

function fetch_all($pdo, $sql, $values = array()){
    $select = $pdo->prepare($sql);
    bind($select, $values);
    if($select->execute()){
        return $select->fetchAll(PDO::FETCH_ASSOC);
    }
}
