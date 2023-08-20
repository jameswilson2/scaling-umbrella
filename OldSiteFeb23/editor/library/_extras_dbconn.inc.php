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

?>