<?php
require_once 'library/security/_access.inc.php';
require_once 'library/_page.class.php';

Email::dequeue(create_pdo());
