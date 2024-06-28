<?php
require 'db.php';
require 'fpdf/fpdf.php';

if (isset($_GET['id'])) {
    $procedure_id = $_GET['id'];
    $sql = "SELECT * FROM procedures WHERE id = $procedure_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $title = $row['title'];
        $description = $row['description'];
        $steps_data = unserialize($row['steps_data']);

        // Create PDF
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, $title, 0, 1, 'C');
        $pdf->Ln(10);
        if (!empty($description)) {
            $pdf->SetFont('Arial', '', 12);
            $pdf->MultiCell(0, 10, $description);
            $pdf->Ln(10);
        }

        foreach ($steps_data as $index => $step) {
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->Cell(0, 10, 'Étape ' . ($index + 1), 0, 1);
            $pdf->SetFont('Arial', '', 12);
            $pdf->MultiCell(0, 10, $step['text']);
            $pdf->Ln(10);
            if (!empty($step['image']) && file_exists($step['image'])) {
                $pdf->Image($step['image'], null, null, 100);
                $pdf->Ln(10);
            } else {
                $pdf->Cell(0, 10, 'Image non trouvée', 0, 1);
            }
        }

        $pdf->Output();
    } else {
        echo "Procédure non trouvée.";
    }
} else {
    echo "ID de procédure non spécifié.";
}

$conn->close();
?>
