document.addEventListener('DOMContentLoaded', () => {
    const timerDisplay = document.getElementById('timer');
    const testForm = document.getElementById('testForm');
    
    if (!timerDisplay || !testForm) {
        return; // Not on the test page
    }

    // Get the duration in minutes from a hidden input or data attribute
    const durationMinutes = parseInt(testForm.dataset.durationMinutes || '30');
    let timeLeft = durationMinutes * 60; // Convert to seconds

    const interval = setInterval(() => {
        timeLeft--;

        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;

        timerDisplay.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

        if (timeLeft <= 300) { // 5 minutes warning
            timerDisplay.classList.add('alert-danger');
            timerDisplay.classList.remove('alert-warning');
        } else if (timeLeft <= 600) { // 10 minutes warning
            timerDisplay.classList.add('alert-warning');
            timerDisplay.classList.remove('alert-danger');
        }

        if (timeLeft <= 0) {
            clearInterval(interval);
            timerDisplay.textContent = '00:00';
            // Auto-submit the form
            alert('Time is up! Submitting your test automatically.');
            testForm.submit();
        }
    }, 1000); // Update every second
});