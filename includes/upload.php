<?php
include 'db.php';
require 'tcpdf/tcpdf.php';

function convertPngToJpeg($filePath, $quality = 90) {
    $image = imagecreatefrompng($filePath);
    if ($image === false) {
        return false;
    }
    $tempPath = tempnam(sys_get_temp_dir(), 'jpg') . '.jpg';
    imagejpeg($image, $tempPath, $quality);
    imagedestroy($image);
    return $tempPath;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = isset($_POST['title']) ? mysqli_real_escape_string($conn, $_POST['title']) : '';
    $description = isset($_POST['description']) ? mysqli_real_escape_string($conn, $_POST['description']) : '';
    $steps_text = isset($_POST['step-text']) ? $_POST['step-text'] : [];
    $image_paths = [];

    if (isset($_FILES['step-image'])) {
        $step_images = $_FILES['step-image'];

        $upload_dir = "../imagesData/";  // Chemin corrigé
        if (!file_exists($upload_dir)) {
            if (!mkdir($upload_dir, 0777, true)) {
                die("Échec de la création du répertoire de téléchargement");
            }
        }

        for ($i = 0; $i < count($step_images['name']); $i++) {
            $tmpFilePath = $step_images['tmp_name'][$i];
            if ($tmpFilePath != "") {
                $originalFilePath = $upload_dir . basename($step_images['name'][$i]);
                if (move_uploaded_file($tmpFilePath, $originalFilePath)) {
                    // Conversion des images PNG en JPEG
                    if (mime_content_type($originalFilePath) == 'image/png') {
                        $jpegFilePath = convertPngToJpeg($originalFilePath);
                        if ($jpegFilePath !== false) {
                            unlink($originalFilePath); // Supprime le fichier PNG original
                            $newFilePath = $upload_dir . basename($jpegFilePath);
                            rename($jpegFilePath, $newFilePath);
                        } else {
                            $newFilePath = $originalFilePath;
                        }
                    } else {
                        $newFilePath = $originalFilePath;
                    }
                    $image_paths[] = $newFilePath;
                }
            }
        }
    }

    $steps_data = [];
    for ($i = 0; $i < count($steps_text); $i++) {
        $steps_data[] = [
            'text' => mysqli_real_escape_string($conn, $steps_text[$i]),
            'image' => isset($image_paths[$i]) ? mysqli_real_escape_string($conn, $image_paths[$i]) : ''
        ];
    }
    $steps_data_serialized = mysqli_real_escape_string($conn, serialize($steps_data));

    $sql = "INSERT INTO procedures (title, description, steps_data) VALUES ('$title', '$description', '$steps_data_serialized')";
    if ($conn->query($sql) === TRUE) {
        $procedure_id = $conn->insert_id;
        header("Location: generate_pdf.php?id=$procedure_id");
        exit;
    } else {
        echo "Erreur: " . $sql . "<br>" . $conn->error;
    }
}
?>
