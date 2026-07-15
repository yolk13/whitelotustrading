<?php

require_once __DIR__ . '/../core/init.php';

if (!Session::get('user_id')) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['file'])) {
    http_response_code(400);
    echo json_encode(['error' => 'No file uploaded']);
    exit;
}

$filename = Security::saveUpload($_FILES['file']);

if (!$filename) {
    http_response_code(400);
    echo json_encode(['error' => 'File upload failed. Allowed: JPEG, PNG, GIF, WebP (max 2MB)']);
    exit;
}

$url = SITE_URL . 'uploads/' . $filename;

echo json_encode(['location' => $url]);
