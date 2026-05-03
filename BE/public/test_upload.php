<?php
file_put_contents('upload_debug.log', "POST: " . json_encode($_POST) . "\nFILES: " . json_encode($_FILES) . "\n", FILE_APPEND);
echo json_encode(['status' => 'ok']);
