// HMIS Practical JavaScript
// Demonstrates DOM selection, events, fetch API, form validation, and calculations.

document.addEventListener('DOMContentLoaded', () => {
    const navToggle = document.querySelector('.nav-toggle');
    const mainNav = document.querySelector('.main-nav');

    if (navToggle && mainNav) {
        navToggle.addEventListener('click', () => {
            mainNav.classList.toggle('open');
        });
    }

    document.querySelectorAll('[data-confirm]').forEach((button) => {
        button.addEventListener('click', (event) => {
            const message = button.dataset.confirm || 'Are you sure?';
            if (!confirm(message)) {
                event.preventDefault();
            }
        });
    });

    const alertBox = document.querySelector('.alert');
    if (alertBox) {
        setTimeout(() => {
            alertBox.style.opacity = '0.75';
        }, 2500);
    }

    const liveSearch = document.querySelector('#livePatientSearch');
    const liveResults = document.querySelector('#livePatientResults');

    if (liveSearch && liveResults) {
        let timer;
        liveSearch.addEventListener('input', () => {
            clearTimeout(timer);
            const keyword = liveSearch.value.trim();

            if (keyword.length < 2) {
                liveResults.innerHTML = '<p class="muted">Type at least 2 characters.</p>';
                return;
            }

            timer = setTimeout(async () => {
                liveResults.innerHTML = '<p class="muted">Searching...</p>';
                try {
                    const response = await fetch(`api/patient_search.php?q=${encodeURIComponent(keyword)}`);
                    const patients = await response.json();
                    if (!patients.length) {
                        liveResults.innerHTML = '<p class="muted">No patients found.</p>';
                        return;
                    }
                    liveResults.innerHTML = patients.map((patient) => `
                        <div class="search-result">
                            <strong>${patient.patient_no}</strong> - ${patient.full_name}<br>
                            <span class="muted">${patient.phone || 'No phone'} | ${patient.gender}</span><br>
                            <a class="btn light" href="patient_view.php?id=${patient.id}">Open record</a>
                        </div>
                    `).join('');
                } catch (error) {
                    liveResults.innerHTML = '<p class="muted">Search failed. Check Apache/PHP.</p>';
                }
            }, 350);
        });
    }

    const appointmentDate = document.querySelector('#appointment_date');
    if (appointmentDate) {
        appointmentDate.addEventListener('change', () => {
            const selected = new Date(appointmentDate.value);
            const now = new Date();
            if (selected < now) {
                alert('The appointment date is in the past. Please choose a future date/time.');
            }
        });
    }

    const billForm = document.querySelector('#billForm');
    if (billForm) {
        const totalOutput = document.querySelector('#billTotal');
        const recalculate = () => {
            let total = 0;
            billForm.querySelectorAll('.service-check').forEach((checkbox) => {
                const serviceId = checkbox.value;
                const qtyInput = billForm.querySelector(`[name="quantities[${serviceId}]"]`);
                const quantity = Number(qtyInput?.value || 1);
                const price = Number(checkbox.dataset.price || 0);
                if (checkbox.checked) {
                    total += price * quantity;
                }
            });
            totalOutput.textContent = `KES ${total.toLocaleString()}`;
        };

        billForm.querySelectorAll('.service-check, .service-qty').forEach((input) => {
            input.addEventListener('input', recalculate);
            input.addEventListener('change', recalculate);
        });
        recalculate();
    }
});
