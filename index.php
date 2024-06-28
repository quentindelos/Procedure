<!DOCTYPE html>
<html lang="fr_FR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/index.css">
    <link rel="shortcut icon" href="styles/images/favicon.ico" type="image/x-icon">
    <title>Générateur de Procédure</title>
</head>
<body>
    <div class="container">
        <h1>Générateur de Procédure</h1>
        <form id="procedure-form" action="includes/upload.php" method="post" enctype="multipart/form-data">
            <!-- Step 1: Title -->
            <div class="form-step" id="step-1">
                <label for="title">Titre:</label>
                <input type="text" id="title" name="title" required><br>
                <button type="button" onclick="nextStep(1)">Suivant</button>
            </div>
            <!-- Step 2: Description -->
            <div class="form-step" id="step-2" style="display:none;">
                <label for="description">Description (facultative):</label>
                <textarea id="description" name="description" rows="4" cols="50"></textarea><br>
                <button type="button" onclick="prevStep(2)">Précédent</button>
                <button type="button" onclick="nextStep(2)">Suivant</button>
            </div>
            <!-- Step 3: Add steps -->
            <div class="form-step" id="step-3" style="display:none;">
                <div id="steps-container">
                    <div class="step">
                        <label for="step-text-1">Texte de l'étape 1:</label>
                        <textarea id="step-text-1" name="step-text[]" rows="2" cols="50" required></textarea><br>
                        <label for="step-image-1">Image de l'étape 1:</label>
                        <input type="file" id="step-image-1" name="step-image[]" accept="image/*" required><br>
                    </div>
                </div>
                <button type="button" onclick="addStep()">Ajouter une étape</button><br>
                <button type="button" onclick="prevStep(3)">Précédent</button>
                <button type="submit">Générer</button>
            </div>
        </form>
        <div id="procedure-output">
            <?php include 'includes/generate.php'; ?>
        </div>
    </div>
    <script src="scripts/index.js"></script>
</body>
</html>