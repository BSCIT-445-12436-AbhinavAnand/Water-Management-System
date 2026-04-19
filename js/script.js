// Mobile Nav Toggle
function toggleNav() {
    document.querySelector('.nav-links').classList.toggle('show');
}

// Sidebar Toggle (Dashboard)
function toggleSidebar() {
    document.querySelector('.sidebar').classList.toggle('show');
}

// Auto Location Detection
function detectLocation() {
    const badge = document.getElementById('locationBadge');
    if (!badge) return;
    
    if (navigator.geolocation) {
        badge.innerHTML = '📍 Detecting...';
        navigator.geolocation.getCurrentPosition(
            function(pos) {
                const lat = pos.coords.latitude.toFixed(4);
                const lng = pos.coords.longitude.toFixed(4);
                badge.innerHTML = '📍 ' + lat + ', ' + lng;
                badge.classList.add('location-badge');
                
                // Try reverse geocode
                fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + lat + '&lon=' + lng)
                    .then(r => r.json())
                    .then(data => {
                        if (data.address) {
                            const area = data.address.suburb || data.address.village || data.address.town || '';
                            const district = data.address.state_district || data.address.county || '';
                            badge.innerHTML = '📍 ' + (area ? area + ', ' : '') + district;
                            
                            // Auto-fill form fields if they exist
                            const areaField = document.getElementById('area');
                            const districtField = document.getElementById('district');
                            if (areaField && !areaField.value) areaField.value = area;
                            if (districtField && !districtField.value) districtField.value = district;
                        }
                    })
                    .catch(() => {});
            },
            function() {
                badge.innerHTML = '📍 Location denied';
            }
        );
    } else {
        badge.innerHTML = '📍 Not supported';
    }
}

// Image preview on complaint form
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    if (!preview) return;
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = '<img src="' + e.target.result + '" style="max-width:200px;border-radius:8px;margin-top:8px;">';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Close alert banner
function closeAlert(el) {
    el.closest('.alert-box').style.display = 'none';
}

// Animate numbers on scroll
function animateNumbers() {
    document.querySelectorAll('.stat-value[data-count]').forEach(el => {
        const target = parseInt(el.dataset.count);
        let current = 0;
        const step = Math.ceil(target / 40);
        const timer = setInterval(() => {
            current += step;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            el.textContent = current.toLocaleString();
        }, 30);
    });
}

// Init
document.addEventListener('DOMContentLoaded', function() {
    detectLocation();
    animateNumbers();
});
