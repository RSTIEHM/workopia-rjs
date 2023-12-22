<?php
$config = require basePath("config/db.php");

use Framework\Database;

$db = new Database($config);

$id = $_GET['id'] ?? '';

$params = [
    'id' => $id
];

$listing = $db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();


loadView('listings/show', ['listing' => $listing]);
