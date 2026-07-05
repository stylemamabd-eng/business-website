<?php
session_start();
require_once __DIR__ . '/../../includes/db.php';

if (empty($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

function uploadImage($fileField, $folder = '../uploads/') {
    if (empty($_FILES[$fileField]['name'])) return null;
    $allowed = ['jpg','jpeg','png','webp','gif'];
    $ext = strtolower(pathinfo($_FILES[$fileField]['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) return null;

    $cloud_name = getenv('CLOUDINARY_CLOUD_NAME');
    $api_key = getenv('CLOUDINARY_API_KEY');
    $api_secret = getenv('CLOUDINARY_API_SECRET');

    if ($cloud_name && $api_key && $api_secret) {
        // Upload to Cloudinary using secure HTTP endpoint
        try {
            $filePath = $_FILES[$fileField]['tmp_name'];
            $timestamp = time();
            $signature = sha1("timestamp=" . $timestamp . $api_secret);
            
            // Build multipart request using CURLFile (PHP 5.5+)
            $postFields = [
                'file' => new CURLFile($filePath),
                'timestamp' => $timestamp,
                'api_key' => $api_key,
                'signature' => $signature
            ];
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.cloudinary.com/v1_1/" . $cloud_name . "/image/upload");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);
            
            if ($response) {
                $data = json_decode($response, true);
                if (!empty($data['secure_url'])) {
                    return $data['secure_url'];
                }
            }
        } catch (Exception $e) {
            // fallback to local on failure
        }
    }

    // Local upload fallback
    $newName = uniqid('img_') . '.' . $ext;
    move_uploaded_file($_FILES[$fileField]['tmp_name'], $folder . $newName);
    return $newName;
}
