function updateCapacityMeter(percentage) {
    const meter = document.getElementById('capacityMeter');
    meter.style.width = percentage + '%';

    // Change color based on capacity level
    if (percentage > 80) {
        meter.style.backgroundColor = '#e74c3c'; // Red
    } else if (percentage > 50) {
        meter.style.backgroundColor = '#f39c12'; // Orange
    } else {
        meter.style.backgroundColor = '#2ecc71'; // Green
    }
}

// Form validation
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');
    const ageInput = document.getElementById('age');

    if (form) {
        form.addEventListener('submit', function (e) {
            const age = parseInt(ageInput.value);
            if (age < 16 || age > 35) {
                alert('Age must be between 16 and 35');
                e.preventDefault();
            }
        });
    }

    // Real-time capacity meter update (simulated)
    setInterval(function () {
        const meter = document.getElementById('capacityMeter');
        const currentWidth = parseFloat(meter.style.width) || 0;
        const newWidth = Math.min(currentWidth + 0.5, 100);
        updateCapacityMeter(newWidth);
    }, 5000);
});