<?php include APP_ROOT . '/app/Views/layouts/header.php'; ?>

<div class="invoice-creation-wrapper">
    <div class="creation-container">
        <!-- Page Header -->
        <div class="page-header-premium">
            <div class="header-info">
                <h1 class="dashboard-title">Invoice Generation</h1>
                <p class="dashboard-subtitle">Create a professional bill for your clients using our automated system.</p>
            </div>
            <div class="header-actions">
                <a href="<?= APP_URL ?>/admin/invoices" class="btn-back-soft">
                    <i class="fas fa-times"></i>
                    <span>Cancel & Exit</span>
                </a>
            </div>
        </div>

        <form action="<?= APP_URL ?>/admin/invoices/create" method="POST" id="invoiceForm" class="premium-form-layout">
            <?= csrf_field() ?>
            
            <div class="form-main-grid">
                <!-- Left Column: Primary Details -->
                <div class="form-column-main">
                    <div class="premium-card">
                        <div class="card-header-soft">
                            <i class="fas fa-user-tie"></i>
                            <h3>Recipient Information</h3>
                        </div>
                        <div class="card-body-premium">
                            <div class="form-group-premium">
                                <label>Target Client</label>
                                <select name="client_id" required class="select-premium">
                                    <option value="">Select a client...</option>
                                    <?php foreach ($clients as $client): ?>
                                        <option value="<?= $client['id'] ?>" <?= ($selectedClientId == $client['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($client['full_name']) ?> (<?= htmlspecialchars($client['email']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group-premium">
                                <label>Associated Project (Optional)</label>
                                <select name="project_id" class="select-premium">
                                    <option value="">General Service / No Project</option>
                                    <?php foreach ($projects as $project): ?>
                                        <option value="<?= $project['id'] ?>" <?= ($selectedProjectId == $project['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($project['title']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="premium-card mt-4">
                        <div class="card-header-soft">
                            <i class="fas fa-list-ol"></i>
                            <h3>Invoice Line Items</h3>
                        </div>
                        <div class="card-body-premium">
                            <div id="itemsContainer">
                                <div class="invoice-item-row" data-index="0">
                                    <div class="item-main-fields">
                                        <div class="input-field">
                                            <input type="text" name="items[0][name]" placeholder="Item Name (e.g. Website Design)" required>
                                        </div>
                                        <div class="input-field quantity">
                                            <input type="number" name="items[0][quantity]" value="1" min="1" step="0.1" class="qty-input" required>
                                        </div>
                                        <div class="input-field price">
                                            <input type="number" name="items[0][unit_price]" placeholder="Price" class="price-input" required>
                                        </div>
                                        <div class="item-row-total">0.00</div>
                                        <button type="button" class="btn-remove-item" title="Remove Item"><i class="fas fa-trash"></i></button>
                                    </div>
                                    <textarea name="items[0][description]" placeholder="Optional description..." class="item-description-box"></textarea>
                                </div>
                            </div>
                            <button type="button" id="addItemBtn" class="btn-add-item">
                                <i class="fas fa-plus-circle"></i>
                                <span>Add Another Line Item</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Settings & Summary -->
                <div class="form-column-side">
                    <div class="premium-card sticky-sidebar">
                        <div class="card-header-soft">
                            <i class="fas fa-cog"></i>
                            <h3>Invoice Settings</h3>
                        </div>
                        <div class="card-body-premium">
                            <div class="form-group-premium">
                                <label>Invoice Title</label>
                                <input type="text" name="title" value="Software Development Services" class="input-premium">
                            </div>
                            <div class="form-grid-2">
                                <div class="form-group-premium">
                                    <label>Currency</label>
                                    <select name="currency" class="select-premium">
                                        <option value="XOF">XOF (CFA)</option>
                                        <option value="USD">USD ($)</option>
                                        <option value="EUR">EUR (€)</option>
                                    </select>
                                </div>
                                <div class="form-group-premium">
                                    <label>Tax Rate (%)</label>
                                    <input type="number" name="tax_rate" id="taxRate" value="0" min="0" max="100" class="input-premium">
                                </div>
                            </div>
                            <div class="form-group-premium">
                                <label>Payment Due Date</label>
                                <input type="date" name="due_date" value="<?= date('Y-m-d', strtotime('+14 days')) ?>" class="input-premium">
                            </div>
                            
                            <div class="invoice-summary-sidebar">
                                <div class="summary-line">
                                    <span>Subtotal</span>
                                    <span id="subtotalDisplay">0.00</span>
                                </div>
                                <div class="summary-line">
                                    <span>Tax Amount</span>
                                    <span id="taxDisplay">0.00</span>
                                </div>
                                <div class="summary-total">
                                    <span>Total Due</span>
                                    <span id="totalDisplay">0.00</span>
                                </div>
                            </div>

                            <button type="submit" class="btn-generate-invoice">
                                <i class="fas fa-file-invoice"></i>
                                <span>Generate & Save Draft</span>
                            </button>
                        </div>
                    </div>

                    <div class="premium-card mt-4">
                        <div class="card-header-soft">
                            <i class="fas fa-sticky-note"></i>
                            <h3>Internal Notes</h3>
                        </div>
                        <div class="card-body-premium">
                            <textarea name="notes" placeholder="These notes will be visible on the invoice footer..." class="textarea-premium"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
.invoice-creation-wrapper {
    padding-top: 100px;
    padding-bottom: 80px;
    background: var(--bg-light);
    min-height: 100vh;
}

.creation-container {
    max-width: var(--container-width);
    margin: 0 auto;
    padding: 0 20px;
}

.page-header-premium {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 40px;
}

.btn-back-soft {
    display: flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    color: var(--text-light);
    background: white;
    padding: 12px 20px;
    border-radius: 12px;
    font-weight: 600;
    border: 1px solid var(--border-color);
    transition: var(--transition);
}

.btn-back-soft:hover { background: #fee2e2; color: #dc2626; border-color: #fecaca; }

.form-main-grid {
    display: grid;
    grid-template-columns: 1.5fr 1fr;
    gap: 30px;
    align-items: flex-start;
}

.premium-card {
    background: white;
    border-radius: 20px;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border-color);
    overflow: hidden;
}

.card-header-soft {
    padding: 20px 25px;
    background: #f9fafb;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    gap: 12px;
}

.card-header-soft i { color: var(--primary-color); font-size: 1.1rem; }
.card-header-soft h3 { margin: 0; font-size: 1rem; font-weight: 700; color: var(--text-dark); text-transform: uppercase; letter-spacing: 0.5px; }

.card-body-premium { padding: 25px; }

.form-group-premium { margin-bottom: 20px; }
.form-group-premium label { display: block; font-size: 0.85rem; font-weight: 700; color: var(--text-dark); margin-bottom: 10px; }

.select-premium, .input-premium, .textarea-premium {
    width: 100%;
    padding: 14px 18px;
    border: 1px solid var(--border-color);
    border-radius: 12px;
    background: var(--bg-light);
    color: var(--text-color);
    font-family: inherit;
    transition: var(--transition);
}

.select-premium:focus, .input-premium:focus, .textarea-premium:focus {
    border-color: var(--primary-color);
    outline: none;
    box-shadow: 0 0 0 4px rgba(14, 159, 110, 0.1);
    background: white;
}

.form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }

.sticky-sidebar { position: sticky; top: 100px; }

/* Item Rows */
.invoice-item-row {
    background: #f9fafb;
    padding: 20px;
    border-radius: 15px;
    margin-bottom: 20px;
    border: 1px solid var(--border-color);
}

.item-main-fields {
    display: flex;
    gap: 15px;
    align-items: center;
    margin-bottom: 15px;
}

.input-field { flex-grow: 1; }
.input-field.quantity { width: 80px; flex-grow: 0; }
.input-field.price { width: 150px; flex-grow: 0; }

.item-main-fields input {
    width: 100%;
    padding: 12px;
    border: 1px solid var(--border-color);
    border-radius: 8px;
}

.item-row-total {
    width: 120px;
    font-weight: 800;
    text-align: right;
    color: var(--text-dark);
}

.btn-remove-item {
    background: transparent;
    border: none;
    color: #ef4444;
    cursor: pointer;
    font-size: 1.1rem;
    opacity: 0.6;
    transition: var(--transition);
}

.btn-remove-item:hover { opacity: 1; transform: scale(1.1); }

.item-description-box {
    width: 100%;
    min-height: 80px;
    padding: 12px;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    font-size: 0.9rem;
    resize: vertical;
}

.btn-add-item {
    width: 100%;
    padding: 15px;
    border: 2px dashed var(--border-color);
    background: transparent;
    color: var(--primary-color);
    border-radius: 12px;
    cursor: pointer;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: var(--transition);
}

.btn-add-item:hover { border-color: var(--primary-color); background: rgba(14, 159, 110, 0.05); }

/* Summary Sidebar */
.invoice-summary-sidebar {
    background: #1e293b;
    border-radius: 15px;
    padding: 20px;
    color: white;
    margin-bottom: 25px;
}

.summary-line {
    display: flex;
    justify-content: space-between;
    font-size: 0.9rem;
    margin-bottom: 10px;
    opacity: 0.8;
}

.summary-total {
    display: flex;
    justify-content: space-between;
    font-size: 1.4rem;
    font-weight: 800;
    padding-top: 15px;
    border-top: 1px solid rgba(255,255,255,0.1);
    margin-top: 10px;
    color: var(--primary-light);
}

.btn-generate-invoice {
    width: 100%;
    background: var(--gradient-primary);
    color: white;
    border: none;
    padding: 18px;
    border-radius: 12px;
    font-weight: 800;
    font-size: 1.1rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    transition: var(--transition);
    box-shadow: 0 10px 20px rgba(14, 159, 110, 0.2);
}

.btn-generate-invoice:hover { transform: translateY(-3px); box-shadow: 0 15px 30px rgba(14, 159, 110, 0.3); }

@media (max-width: 992px) {
    .form-main-grid { grid-template-columns: 1fr; }
    .sticky-sidebar { position: static; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('itemsContainer');
    const addItemBtn = document.getElementById('addItemBtn');
    const taxRateInput = document.getElementById('taxRate');
    let itemIndex = 1;

    function calculateTotals() {
        let subtotal = 0;
        document.querySelectorAll('.invoice-item-row').forEach(row => {
            const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            const total = qty * price;
            row.querySelector('.item-row-total').textContent = total.toLocaleString(undefined, {minimumFractionDigits: 2});
            subtotal += total;
        });

        const taxRate = parseFloat(taxRateInput.value) || 0;
        const taxAmount = subtotal * (taxRate / 100);
        const totalAmount = subtotal + taxAmount;

        document.getElementById('subtotalDisplay').textContent = subtotal.toLocaleString(undefined, {minimumFractionDigits: 2});
        document.getElementById('taxDisplay').textContent = taxAmount.toLocaleString(undefined, {minimumFractionDigits: 2});
        document.getElementById('totalDisplay').textContent = totalAmount.toLocaleString(undefined, {minimumFractionDigits: 2});
    }

    addItemBtn.addEventListener('click', function() {
        const row = document.createElement('div');
        row.className = 'invoice-item-row animate__animated animate__fadeInUp';
        row.setAttribute('data-index', itemIndex);
        row.innerHTML = `
            <div class="item-main-fields">
                <div class="input-field">
                    <input type="text" name="items[${itemIndex}][name]" placeholder="Item Name" required>
                </div>
                <div class="input-field quantity">
                    <input type="number" name="items[${itemIndex}][quantity]" value="1" min="1" step="0.1" class="qty-input" required>
                </div>
                <div class="input-field price">
                    <input type="number" name="items[${itemIndex}][unit_price]" placeholder="Price" class="price-input" required>
                </div>
                <div class="item-row-total">0.00</div>
                <button type="button" class="btn-remove-item"><i class="fas fa-trash"></i></button>
            </div>
            <textarea name="items[${itemIndex}][description]" placeholder="Optional description..." class="item-description-box"></textarea>
        `;
        container.appendChild(row);
        
        row.querySelector('.btn-remove-item').addEventListener('click', () => {
            row.remove();
            calculateTotals();
        });

        row.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', calculateTotals);
        });

        itemIndex++;
    });

    container.addEventListener('input', function(e) {
        if (e.target.classList.contains('qty-input') || e.target.classList.contains('price-input')) {
            calculateTotals();
        }
    });

    taxRateInput.addEventListener('input', calculateTotals);

    // Initial event listeners for first row
    document.querySelector('.qty-input').addEventListener('input', calculateTotals);
    document.querySelector('.price-input').addEventListener('input', calculateTotals);
    document.querySelector('.btn-remove-item').addEventListener('click', function() {
        if (document.querySelectorAll('.invoice-item-row').length > 1) {
            this.closest('.invoice-item-row').remove();
            calculateTotals();
        }
    });
});
</script>

<?php include APP_ROOT . '/app/Views/layouts/footer.php'; ?>
