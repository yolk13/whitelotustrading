<?php
echo "Method: " . $_SERVER['REQUEST_METHOD'] . "\n";
echo "POST data: ";
print_r($_POST);
echo "Content-Type: " . ($_SERVER['CONTENT_TYPE'] ?? 'not set') . "\n";
echo "Raw input: " . file_get_contents('php://input') . "\n";
