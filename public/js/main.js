/**
 * PANIER - JavaScript principal
 * Gestion des interactions côté client
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('🛒 Panier - App chargée');

    // Gestion des checkboxes pour cocher les articles
    const toggleItems = document.querySelectorAll('.toggle-item');
    toggleItems.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const itemId = this.dataset.itemId;
            const listId = this.dataset.listId;
            const isChecked = this.checked;

            // Optionnel: faire une requête AJAX
            console.log(`Article ${itemId} dans liste ${listId}: ${isChecked ? 'coché' : 'décoché'}`);

            // Pour l'instant, juste une animation visuelle
            this.closest('.item-row').classList.toggle('item-checked', isChecked);
        });
    });

    // Confirmations de suppression
    const deleteLinks = document.querySelectorAll('a[onclick*="confirm"]');
    deleteLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Le onclick handle déjà la confirmation
        });
    });

    // Activer/désactiver les boutons selon les sélections
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        const inputs = form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.addEventListener('input', validateForm);
        });
    });
});

/**
 * Valider un formulaire
 */
function validateForm(e) {
    const form = e.target.closest('form');
    if (!form) return;

    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            isValid = false;
            field.classList.add('error');
        } else {
            field.classList.remove('error');
        }
    });

    // Mettre à jour l'état du bouton submit
    const submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = !isValid;
    }
}

/**
 * Recalculer le coût total (optionnel AJAX)
 */
function recalculateTotal() {
    const items = document.querySelectorAll('.item-row');
    let total = 0;

    items.forEach(item => {
        const priceText = item.querySelector('.price')?.textContent;
        const quantityText = item.querySelector('.quantity')?.textContent;

        // À implémenter selon votre format
    });

    const totalElement = document.querySelector('.total-amount');
    if (totalElement) {
        totalElement.textContent = formatCurrency(total);
    }
}

/**
 * Formater une valeur en currency
 */
function formatCurrency(value) {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
    }).format(value);
}

/**
 * Afficher une notification
 */
function notify(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;
    alertDiv.style.cssText = `
        position: fixed;
        top: 1rem;
        right: 1rem;
        z-index: 999;
        animation: slideIn 0.3s ease;
    `;

    document.body.appendChild(alertDiv);

    // Auto-dismiss après 4s
    setTimeout(() => {
        alertDiv.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => alertDiv.remove(), 300);
    }, 4000);
}

// Animation styles
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }

    input.error {
        border-color: #dc2626 !important;
        background-color: #fee2e2 !important;
    }
`;
document.head.appendChild(style);

console.log('✅ JavaScript chargé et prêt');
