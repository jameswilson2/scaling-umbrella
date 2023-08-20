<?php

interface Crust_DB_Adapter{
    function execute($sql, $values = array());
    function fetch();
    function fetchAll();
}
