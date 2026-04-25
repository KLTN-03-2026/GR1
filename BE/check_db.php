<?php
    $db = new PDO('sqlite:database/database.sqlite');
    $res = $db->query("SELECT sql FROM sqlite_master WHERE type='table'")->fetchAll();
    foreach($res as $row) {
        echo $row['sql'] . "\n\n";
    }
