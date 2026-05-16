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

function addRow() {
    rowId += 1;
    rowsContainer.insertAdjacentHTML('beforeend', orderRowTemplate(rowId));
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
rowsContainer.addEventListener('change', computeTotals);

function computeTotals() {
    const rows = [...rowsContainer.querySelectorAll('.order-row')];
    let total = 0;
    let fruit = 0;
    let puree = 0;

    rows.forEach(row => {
        const qty = Number(row.querySelector('.qty').value || 0);
        const type = row.querySelector('.product-type').value;
        total += qty;
        if (type === 'fruit') fruit += qty;
        if (type === 'puree') puree += qty;
    });

    totalKgEl.textContent = `${total} kg`;
    fruitKgEl.textContent = `${fruit} kg`;
    pureeKgEl.textContent = `${puree} kg`;
    optionalAmountEl.textContent = `${(total * 100).toLocaleString('fr-FR')} FCFA`;
    return { total, fruit, puree };
}

preorderForm.addEventListener('submit', async (e) => {
    e.preventDefault();

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

        const result = await response.json();
        if (result.success) {
            console.log('Précommande ID:', result.preorder_id);
            afterSubmit.classList.add('visible');
            afterSubmit.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } else {
            alert('Erreur: ' + (result.message || 'Vérifiez vos données'));
        }
    } catch (error) {
        alert('Erreur connexion: ' + error.message);
    }
});

const publicKey = window.APP_CONFIG.kkiapayPublicKey;
const callbackUrl = window.APP_CONFIG.kkiapayCallback;

payNowBtn.addEventListener('click', () => {
    if (!reservationData) {
        alert("Enregistre d'abord la réservation.");
        return;
    }

    openKkiapayWidget({
        amount: reservationData.optionalPrepayment,
        key: publicKey,
        sandbox: true,
        position: 'center',
        theme: '#c62828',
        callback: callbackUrl,
        phone: reservationData.phone || '',
        email: reservationData.email || '',
        name: `${reservationData.firstName} ${reservationData.lastName}`
    });
});


/*payNowBtn.addEventListener('click', () => {
    if (!reservationData) return;
    const amount = reservationData.optionalPrepayment;
    if (!amount || amount <= 0) {
        alert('Ajoutez d\'abord une quantité valide.');
        return;
    }

    if (typeof openKkiapayWidget !== 'function') {
        alert('Le SDK KKiaPay n\'est pas chargé.');
        return;
    }

    openKkiapayWidget({
        amount,
        key: 'VOTRE_CLE_PUBLIQUE_KKIAPAY',
        sandbox: true,
        position: 'center',
        theme: '#c62828',
        phone: reservationData.phone || '',
        email: reservationData.email || '',
        name: `${reservationData.firstName} ${reservationData.lastName}`,
        data: JSON.stringify({
            type: 'precommande-tomate',
            amount,
            reservation: reservationData
        })
    });
});*/

skipPayBtn.addEventListener('click', () => {
    alert('La réservation est déjà enregistrée. Vous pourrez relancer le paiement plus tard par email, WhatsApp ou appel.');
});


preorderForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    // Clear erreurs précédentes
    clearErrors();

    const data = { /* tes données */ };

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
            showErrors(result.errors || { error: ['Erreur serveur'] });
            return;
        }

        const result = await response.json();
        afterSubmit.classList.add('visible');

    } catch (error) {
        showErrors({ error: ['Erreur connexion'] });
    }
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
