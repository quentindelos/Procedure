<?php
require 'db.php';
require 'tcpdf/tcpdf.php';

class PDF_UTF8 extends TCPDF
{
    public function Header()
    {
        $this->SetFont('dejavusans', 'B', 15);
        $this->Cell(0, 10, 'Document de Procédure', 0, 1, 'C');
        $this->Ln(10);
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('dejavusans', 'I', 8);
        $this->Cell(0, 10, 'Procédure générée avec ProcedureFacile.fr', 0, 0, 'L');
        $this->SetX($this->GetPageWidth() - 30); // Positionne la numérotation des pages à 30 mm du bord droit
        $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'R');
    }

    public function AddStep($step_number, $step_text, $step_image = '')
    {
        $this->Ln(5); // Ajout d'un petit espace avant chaque étape pour le retour à la ligne
        $this->SetFont('dejavusans', 'BU', 14); // Gras et souligné
        $this->Cell(0, 10, 'Étape ' . $step_number . ' :', 0, 1, 'L');
        $this->SetFont('dejavusans', '', 12);
        $html = '<p style="text-align:justify;">' . $step_text . '</p>';
        $this->WriteHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, 'J', true);

        if (!empty($step_image) && file_exists($step_image)) {
            $this->Ln(3); // Ajout d'un petit espace avant l'image
            list($width, $height) = getimagesize($step_image);
            $max_width = 100;
            $ratio = $max_width / $width;
            $new_height = $height * $ratio;
            $current_y = $this->GetY();
            $this->Image($step_image, '', $current_y, $max_width, $new_height);
            $this->SetY($current_y + $new_height + 3); // Ajuste Y après l'image (taille de l'image + marge)
        }
        $this->Ln(3); // Réduction de l'espace entre les étapes
    }
}

if (isset($_GET['id'])) {
    $procedure_id = $_GET['id'];
    $sql = "SELECT * FROM procedures WHERE id = $procedure_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $title = $row['title'];
        $description = $row['description'];
        $steps_data = unserialize($row['steps_data']);

        $pdf = new PDF_UTF8(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('ProcedureFacile');
        $pdf->SetTitle($title);
        $pdf->SetSubject('Document de Procédure');
        $pdf->SetKeywords('TCPDF, PDF, procédure');

        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        $pdf->AddPage();
        $pdf->SetFont('dejavusans', 'B', 16);
        $pdf->Cell(0, 10, $title, 0, 1, 'C');
        $pdf->Ln(10);
        if (!empty($description)) {
            $pdf->SetFont('dejavusans', '', 12);
            $html_description = '<p style="text-align:justify;">' . $description . '</p>';
            $pdf->WriteHTMLCell(0, 0, '', '', $html_description, 0, 1, 0, true, 'J', true);
            $pdf->Ln(10);
        }

        foreach ($steps_data as $index => $step) {
            $pdf->AddStep($index + 1, $step['text'], $step['image']);
        }

        $pdf->Output('procedure.pdf', 'I');
    } else {
        echo "Procédure non trouvée.";
    }
} else {
    echo "ID de procédure non spécifié.";
}

$conn->close();
?>
