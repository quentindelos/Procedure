let currentStep = 1;

function nextStep(step) {
    document.getElementById('step-' + step).style.display = 'none';
    document.getElementById('step-' + (step + 1)).style.display = 'block';
}

function prevStep(step) {
    document.getElementById('step-' + step).style.display = 'none';
    document.getElementById('step-' + (step - 1)).style.display = 'block';
}

function addStep() {
    const stepsContainer = document.getElementById('steps-container');
    const stepCount = stepsContainer.getElementsByClassName('step').length + 1;
    const stepDiv = document.createElement('div');
    stepDiv.className = 'step';
    stepDiv.innerHTML = `
        <label for="step-text-${stepCount}">Texte de l'étape ${stepCount}:</label>
        <textarea id="step-text-${stepCount}" name="step-text[]" rows="2" cols="50" required></textarea><br>
        <label for="step-image-${stepCount}">Image de l'étape ${stepCount}:</label>
        <input type="file" id="step-image-${stepCount}" name="step-image[]" accept="image/*" required><br>
    `;
    stepsContainer.appendChild(stepDiv);
}