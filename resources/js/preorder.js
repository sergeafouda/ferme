const video = document.getElementById('heroVideo');
const reserveBtn = document.getElementById('reserveBtn');
const reserveHint = document.getElementById('reserveHint');
const formShell = document.getElementById('formShell');
const videoStatusText = document.getElementById('videoStatusText');
const videoStatusFlag = document.getElementById('videoStatusFlag');
const rowsContainer = document.getElementById('rowsContainer');
const addRowBtn = document.getElementById('addRowBtn');
const preorderForm = document.getElementById('preorderForm');
const afterSubmit = document.getElementById('afterSubmit');
const totalKgEl = document.getElementById('totalKg');
const fruitKgEl = document.getElementById('fruitKg');
const pureeKgEl = document.getElementById('pureeKg');
const optionalAmountEl = document.getElementById('optionalAmount');
const payNowBtn = document.getElementById('payNowBtn');
const skipPayBtn = document.getElementById('skipPayBtn');

let reservationData = null;
let rowId = 0;

function unlockReservation() {
    reserveBtn.disabled = false;
    reserveHint.textContent = 'La vidéo est terminée. Vous pouvez maintenant ouvrir le formulaire de réservation.';
    videoStatusText.textContent = 'Vidéo terminée. Vous pouvez passer à la réservation.';
    videoStatusFlag.textContent = 'Débloqué';
    videoStatusFlag.style.color = '#2e7d32';
}

video.addEventListener('ended', unlockReservation);

reserveBtn.addEventListener('click', () => {
    if (reserveBtn.disabled) return;
    formShell.classList.add('visible');
    formShell.scrollIntoView({ behavior: 'smooth', block: 'start' });
});

function orderRowTemplate(id) {
    return `
          <div class="order-row" data-row-id="${id}">
            <div class="field">
              <label>Produit</label>
              <select class="product-type" required>
                <option value="fruit">Tomate fruit</option>
                <option value="puree">Tomate en purée</option>
                <option value="lapin">Lapin</option>
                <option value="poulet_goliath">Poulet Goliath</option>
              </select>
            </div>
            <div class="field">
              <label>Quantité (kg)</label>
              <input class="qty" type="number" min="1" step="1" required placeholder="Ex. 25" />
            </div>
            <div class="field">
              <label>Date souhaitée</label>
              <input class="delivery-date" type="date" required />
            </div>
            <div class="field">
              <label>Adresse de livraison</label>
              <input class="delivery-address" type="text" required placeholder="Ex. Akpakpa, Cotonou" />
            </div>
            <button type="button" class="remove-btn" aria-label="Supprimer cette ligne">×</button>
          </div>
        `;
}

function getMinDate(type) {
    const d = new Date();
    if (type === 'fruit' || type === 'puree') {
        d.setMonth(d.getMonth() + 3);
    } else {
        d.setDate(d.getDate() + 7);
    }
    return d.toISOString().split('T')[0];
}

function updateRowDateMin(row) {
    const type = row.querySelector('.product-type').value;
    const dateInput = row.querySelector('.delivery-date');
    const minDate = getMinDate(type);
    dateInput.min = minDate;
    if (dateInput.value && dateInput.value < minDate) {
        dateInput.value = minDate;
    }
}

function addRow() {
    rowId += 1;
    rowsContainer.insertAdjacentHTML('beforeend', orderRowTemplate(rowId));
    const newRow = rowsContainer.lastElementChild;
    updateRowDateMin(newRow);
    computeTotals();
}

addRowBtn.addEventListener('click', addRow);
addRow();

rowsContainer.addEventListener('click', (e) => {
    if (!e.target.classList.contains('remove-btn')) return;
    const rows = rowsContainer.querySelectorAll('.order-row');
    if (rows.length === 1) return;
    e.target.closest('.order-row').remove();
    computeTotals();
});

rowsContainer.addEventListener('input', computeTotals);
rowsContainer.addEventListener('change', (e) => {
    if (e.target.classList.contains('product-type')) {
        updateRowDateMin(e.target.closest('.order-row'));
    }
    computeTotals();
});

function computeTotals() {
    const rows = [...rowsContainer.querySelectorAll('.order-row')];
    let total = 0;
    let fruit = 0;
    let puree = 0;
    let lapin = 0;
    let pouletGoliath = 0;

    rows.forEach(row => {
        const qty = Number(row.querySelector('.qty').value || 0);
        const type = row.querySelector('.product-type').value;
        total += qty;
        if (type === 'fruit') fruit += qty;
        if (type === 'puree') puree += qty;
        if (type === 'lapin') lapin += qty;
        if (type === 'poulet_goliath') pouletGoliath += qty;
    });

    totalKgEl.textContent = `${total} kg`;
    fruitKgEl.textContent = `${fruit} kg`;
    pureeKgEl.textContent = `${puree} kg`;
    const lapinEl = document.getElementById('lapinKg');
    if (lapinEl) lapinEl.textContent = `${lapin} kg`;
    const pouletEl = document.getElementById('pouletGoliathKg');
    if (pouletEl) pouletEl.textContent = `${pouletGoliath} kg`;

    optionalAmountEl.textContent = `${(total * 100).toLocaleString('fr-FR')} FCFA`;
    return { total, fruit, puree, lapin, pouletGoliath };
}

preorderForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    clearErrors();

    const totals = computeTotals();
    const rows = [...rowsContainer.querySelectorAll('.order-row')].map(row => ({
        productType: row.querySelector('.product-type').value,
        quantityKg: Number(row.querySelector('.qty').value) || 0,
        deliveryDate: row.querySelector('.delivery-date').value,
        deliveryAddress: row.querySelector('.delivery-address').value.trim()
    }));

    const data = {
        firstName: document.getElementById('firstName').value.trim(),
        lastName: document.getElementById('lastName').value.trim(),
        email: document.getElementById('email').value.trim(),
        phone: document.getElementById('phone').value.trim(),
        projectNote: document.getElementById('projectNote').value.trim(),
        total_kg: totals.total,
        fruit_kg: totals.fruit,
        puree_kg: totals.puree,
        lapin_kg: totals.lapin,
        poulet_goliath_kg: totals.pouletGoliath,
        deliveries: rows,
        optional_prepayment: totals.total * 100
    };

    try {
        const response = await fetch('/preorder/store', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        });

        if (!response.ok) {
            const result = await response.json();
            showErrors(result.errors || { error: ['Erreur de validation des données'] });
            const firstError = document.querySelector('.error-message');
            if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        const result = await response.json();
        if (result.success) {
            console.log('Précommande ID:', result.preorder_id);
            reservationData = {
                id: result.preorder_id,
                firstName: data.firstName,
                lastName: data.lastName,
                email: data.email,
                phone: data.phone,
                optionalPrepayment: result.total_prepay || data.optional_prepayment
            };
            afterSubmit.classList.add('visible');
            afterSubmit.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } else {
            alert('Erreur: ' + (result.message || 'Vérifiez vos données'));
        }
    } catch (error) {
        showErrors({ error: ['Erreur de connexion au serveur'] });
    }
});

const paydunyaUrl = window.APP_CONFIG.paydunyaUrl;

payNowBtn.addEventListener('click', async () => {
    if (!reservationData) {
        alert("Enregistrez d'abord la réservation.");
        return;
    }

    payNowBtn.disabled = true;
    payNowBtn.textContent = 'Création de la facture...';

    try {
        const res = await fetch(paydunyaUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ preorder_id: reservationData.id })
        });

        const result = await res.json();
        if (result.success && result.url) {
            window.location.href = result.url;
        } else {
            alert('Erreur: ' + (result.message || 'Impossible d\'initialiser le paiement PayDunya.'));
            payNowBtn.disabled = false;
            payNowBtn.textContent = 'Payer mon acompte';
        }
    } catch (err) {
        alert('Erreur de connexion au serveur.');
        payNowBtn.disabled = false;
        payNowBtn.textContent = 'Payer mon acompte';
    }
});

skipPayBtn.addEventListener('click', () => {
    if (!reservationData) return;
    window.location.href = '/preorder/thank-you?id=' + reservationData.id;
});


// Fonctions utilitaires erreurs
function clearErrors() {
    document.querySelectorAll('.error-message').forEach(el => el.remove());
}

function showErrors(errors) {
    Object.keys(errors).forEach(key => {
        const field = document.querySelector(`[name="${key}"]`) ||
            document.querySelector(`[name="deliveries[][${key}]"]`) ||
            document.querySelector(`[name="deliveries.*.${key}"]`);

        if (field) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            errorDiv.style.cssText = 'color: #ef4444; font-size: 13px; margin-top: 4px; font-weight: 500;';
            errorDiv.textContent = Array.isArray(errors[key]) ? errors[key][0] : errors[key];
            field.parentNode.appendChild(errorDiv);
        }
    });
}
