<?php 
    include APP_ROOT . '/app/Views/layouts/header.php'; 
    $isAdmin = ($_SESSION['role'] === 'admin');
?>

<div class="invoice-view-wrapper">
    <div class="invoice-actions no-print">
        <div class="actions-left">
            <a href="<?= APP_URL ?><?= $isAdmin ? '/admin/invoices' : '/client/invoices' ?>" class="btn-back">
                <i class="fas fa-arrow-left"></i>
                <span>Back to Invoices</span>
            </a>
        </div>
        <div class="actions-right">
            <button onclick="window.print()" class="btn-tool" title="Print Invoice">
                <i class="fas fa-print"></i>
            </button>
            <button onclick="window.print()" class="btn-tool" title="Download PDF">
                <i class="fas fa-file-pdf"></i>
            </button>
            
            <?php if ($isAdmin && $invoice['status'] === 'draft'): ?>
                <form action="<?= APP_URL ?>/admin/invoices/send" method="POST" class="d-inline">
                    <input type="hidden" name="id" value="<?= $invoice['id'] ?>">
                    <button type="submit" class="btn-premium-action info">
                        <i class="fas fa-paper-plane"></i>
                        <span>Dispatch to Client</span>
                    </button>
                </form>
            <?php endif; ?>

            <?php if ($isAdmin && !empty($invoice['payments'])): ?>
                <?php foreach ($invoice['payments'] as $payment): ?>
                    <?php if ($payment['status'] === 'pending'): ?>
                        <form action="<?= APP_URL ?>/admin/invoices/approve-payment" method="POST" class="d-inline">
                            <input type="hidden" name="invoice_id" value="<?= $invoice['id'] ?>">
                            <input type="hidden" name="payment_id" value="<?= $payment['id'] ?>">
                            <button type="submit" class="btn-premium-action success">
                                <i class="fas fa-check-double"></i>
                                <span>Verify & Approve Payment</span>
                            </button>
                        </form>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Main Invoice Document -->
    <div class="invoice-document shadow-lg">
        <div class="invoice-brand-strip"></div>
        
        <div class="invoice-content">
            <!-- Header: Logo and Info -->
            <div class="invoice-header">
                <div class="brand-side">
                    <div class="invoice-logo">
                        <span class="logo-text">Sahel<span>Soft</span></span>
                    </div>
                    <div class="company-details">
                        <p class="company-address">Niamey, Niger</p>
                        <p class="company-contact">contact@sahelsoft.ne | +227 00 00 00 00</p>
                    </div>
                </div>
                <div class="invoice-meta">
                    <h1 class="document-title">INVOICE</h1>
                    <div class="meta-grid">
                        <div class="meta-item">
                            <span class="meta-label">Invoice Number</span>
                            <span class="meta-value"><?= $invoice['invoice_number'] ?></span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Status</span>
                            <span class="meta-value status-text-<?= strtolower($invoice['status']) ?>"><?= strtoupper($invoice['status']) ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="invoice-bill-grid">
                <div class="bill-box">
                    <h4 class="box-title">Bill To</h4>
                    <div class="client-details">
                        <p class="client-name"><?= htmlspecialchars($invoice['client_name']) ?></p>
                        <p class="client-email"><?= htmlspecialchars($invoice['client_email']) ?></p>
                        <p class="project-ref">Ref: <?= htmlspecialchars($invoice['title']) ?></p>
                    </div>
                </div>
                <div class="date-box">
                    <div class="date-item">
                        <span class="date-label">Issue Date</span>
                        <span class="date-value"><?= date('M d, Y', strtotime($invoice['issue_date'])) ?></span>
                    </div>
                    <div class="date-item">
                        <span class="date-label">Due Date</span>
                        <span class="date-value text-danger"><?= date('M d, Y', strtotime($invoice['due_date'])) ?></span>
                    </div>
                </div>
            </div>

            <!-- Line Items -->
            <div class="line-items-section">
                <table class="line-items-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        foreach ($invoice['items'] as $item): 
                        ?>
                        <tr>
                            <td>
                                <div class="item-name"><?= htmlspecialchars($item['name']) ?></div>
                                <div class="item-desc"><?= htmlspecialchars($item['description'] ?? '') ?></div>
                            </td>
                            <td class="text-center"><?= $item['quantity'] ?></td>
                            <td class="text-end"><?= number_format($item['unit_price']) ?></td>
                            <td class="text-end fw-bold"><?= number_format($item['quantity'] * $item['unit_price']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Financial Summary -->
            <div class="invoice-summary">
                <div class="summary-notes">
                    <h4 class="box-title">Terms & Notes</h4>
                    <p class="notes-text"><?= nl2br(htmlspecialchars($invoice['notes'] ?? 'Please pay by the due date. Thank you for your business!')) ?></p>
                </div>
                <div class="summary-calculations">
                    <div class="calc-row">
                        <span>Subtotal</span>
                        <span><?= number_format($invoice['subtotal']) ?> <small>XOF</small></span>
                    </div>
                    <div class="calc-row">
                        <span>Tax (<?= $invoice['tax_rate'] ?>%)</span>
                        <span><?= number_format($invoice['tax_amount']) ?> <small>XOF</small></span>
                    </div>
                    <div class="calc-total">
                        <span>Grand Total</span>
                        <span><?= number_format($invoice['total_amount']) ?> <small>XOF</small></span>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="invoice-footer">
                <p>Payment Instructions: Bank Transfer to <strong>SahelSoft Niger SARL</strong></p>
                <div class="footer-stamp">
                    <img src="<?= APP_URL ?>/assets/img/stamp_placeholder.png" alt="" class="stamp-img" loading="lazy">
                    <p>Digitally Signed by SahelSoft</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Proof Upload (Client Only) -->
    <?php if (!$isAdmin && $invoice['status'] !== 'paid'): ?>
        <div class="payment-upload-card no-print">
            <div class="card-header-premium">
                <h3>Submit Payment Confirmation</h3>
                <p>Upload your transfer receipt to notify our team.</p>
            </div>
            <form action="<?= APP_URL ?>/client/invoices/submit-payment" method="POST" enctype="multipart/form-data" class="upload-form">
                <input type="hidden" name="invoice_id" value="<?= $invoice['id'] ?>">
                <div class="form-grid">
                    <div class="input-group">
                        <label>Payment Method</label>
                        <select name="payment_method" required>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="mobile_money">Mobile Money (Alizza/Aman)</option>
                            <option value="cash">Cash Payment</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label>Transaction Reference</label>
                        <input type="text" name="reference" placeholder="e.g. TXN12345678" required>
                    </div>
                    <div class="input-group full-width">
                        <label>Receipt Image / PDF</label>
                        <div class="file-drop-area">
                            <input type="file" name="receipt" required id="receiptFile">
                            <div class="drop-message">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span>Click or Drag receipt here</span>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn-premium-accent w-100 mt-4">
                    <i class="fas fa-check"></i> Submit Proof
                </button>
            </form>
        </div>
    <?php endif; ?>
</div>

<style>
.invoice-view-wrapper {
    padding-top: 150px;
    padding-bottom: 80px;
    max-width: 900px;
    margin: 0 auto;
    padding-left: 20px;
    padding-right: 20px;
}

.invoice-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.btn-back {
    display: flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    color: var(--text-light);
    font-weight: 600;
    transition: var(--transition);
}

.btn-back:hover { color: var(--primary-color); }

.actions-right { display: flex; gap: 12px; align-items: center; }

.btn-tool {
    width: 45px;
    height: 45px;
    border-radius: 12px;
    border: 1px solid var(--border-color);
    background: white;
    color: var(--text-dark);
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
}

.btn-tool:hover { background: var(--bg-light); border-color: var(--primary-color); color: var(--primary-color); }

.btn-premium-action {
    padding: 12px 24px;
    border-radius: 12px;
    border: none;
    color: white;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 10px;
    transition: var(--transition);
    box-shadow: var(--shadow-sm);
}

.btn-premium-action.info { background: #3b82f6; }
.btn-premium-action.success { background: var(--primary-color); }

.btn-premium-action:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); opacity: 0.9; }

/* Document Styling */
.invoice-document {
    background: white;
    border-radius: 24px;
    overflow: hidden;
    margin-bottom: 50px;
}

.invoice-brand-strip {
    height: 8px;
    background: var(--gradient-flag);
}

.invoice-content { padding: 60px; }

.invoice-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 60px;
}

.logo-text { font-size: 2.2rem; font-weight: 800; color: var(--text-dark); letter-spacing: -1px; }
.logo-text span { color: var(--primary-color); }

.company-details { margin-top: 15px; color: var(--text-light); font-size: 0.9rem; }

.document-title {
    font-size: 3rem;
    font-weight: 900;
    color: #f3f4f6;
    margin: 0;
    line-height: 1;
    text-align: right;
}

.meta-grid { display: flex; gap: 30px; margin-top: 20px; justify-content: flex-end; }

.meta-label { display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-light); text-transform: uppercase; margin-bottom: 5px; }
.meta-value { font-weight: 800; color: var(--text-dark); font-size: 1.1rem; }

.status-text-paid { color: #059669; }
.status-text-sent { color: #2563eb; }
.status-text-overdue { color: #dc2626; }

.invoice-bill-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
    padding: 40px 0;
    border-top: 1px solid #f3f4f6;
    border-bottom: 1px solid #f3f4f6;
    margin-bottom: 40px;
}

.box-title { font-size: 0.85rem; font-weight: 700; color: var(--text-light); text-transform: uppercase; margin-bottom: 15px; letter-spacing: 1px; }

.client-name { font-size: 1.25rem; font-weight: 800; color: var(--text-dark); margin: 0; }
.client-email { color: var(--text-light); margin: 5px 0; }
.project-ref { font-weight: 600; color: var(--primary-color); margin: 10px 0 0 0; }

.date-box { display: flex; flex-direction: column; gap: 20px; align-items: flex-end; }
.date-item { text-align: right; }
.date-label { display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-light); text-transform: uppercase; margin-bottom: 5px; }
.date-value { font-weight: 700; color: var(--text-dark); }

/* Table Styling */
.line-items-section { margin-bottom: 40px; }
.line-items-table { width: 100%; border-collapse: collapse; }
.line-items-table th { padding: 15px 0; text-align: left; border-bottom: 2px solid var(--text-dark); font-size: 0.85rem; font-weight: 700; color: var(--text-dark); }
.line-items-table td { padding: 25px 0; border-bottom: 1px solid #f3f4f6; }

.item-name { font-weight: 800; color: var(--text-dark); font-size: 1.05rem; }
.item-desc { font-size: 0.85rem; color: var(--text-light); margin-top: 5px; }

/* Summary */
.invoice-summary { display: grid; grid-template-columns: 1.5fr 1fr; gap: 60px; }

.notes-text { font-size: 0.9rem; color: var(--text-light); line-height: 1.6; font-style: italic; }

.summary-calculations { display: flex; flex-direction: column; gap: 15px; }
.calc-row { display: flex; justify-content: space-between; font-weight: 600; color: var(--text-light); }
.calc-total { display: flex; justify-content: space-between; font-weight: 900; color: var(--text-dark); font-size: 1.5rem; padding-top: 15px; border-top: 2px solid var(--text-dark); margin-top: 10px; }

/* Footer */
.invoice-footer { margin-top: 60px; padding-top: 30px; border-top: 1px solid #f3f4f6; text-align: center; color: var(--text-light); font-size: 0.85rem; }
.footer-stamp { margin-top: 30px; opacity: 0.6; }
.stamp-img { width: 100px; margin-bottom: 10px; filter: grayscale(1); }

/* Payment Upload */
.payment-upload-card { background: white; border-radius: 20px; box-shadow: var(--shadow-md); border: 1px solid var(--border-color); overflow: hidden; }
.card-header-premium { padding: 30px; background: var(--bg-light); border-bottom: 1px solid var(--border-color); }
.card-header-premium h3 { margin: 0; font-size: 1.25rem; font-weight: 800; color: var(--text-dark); }
.card-header-premium p { margin: 5px 0 0 0; color: var(--text-light); }

.upload-form { padding: 30px; }
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.full-width { grid-column: span 2; }

.input-group label { display: block; font-size: 0.85rem; font-weight: 700; color: var(--text-dark); margin-bottom: 10px; }
.input-group select, .input-group input { width: 100%; padding: 14px; border: 1px solid var(--border-color); border-radius: 12px; font-family: inherit; }

.file-drop-area { border: 2px dashed var(--border-color); border-radius: 12px; padding: 40px; text-align: center; position: relative; transition: var(--transition); cursor: pointer; }
.file-drop-area:hover { border-color: var(--primary-color); background: rgba(14, 159, 110, 0.05); }
.file-drop-area input { position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; }
.drop-message { color: var(--text-light); display: flex; flex-direction: column; gap: 10px; }
.drop-message i { font-size: 2rem; color: var(--primary-color); }

/* Print Logic */
@media print {
    .no-print, .site-header, .site-footer, .whatsapp-float, .back-to-top { display: none !important; }
    body { background: white !important; margin: 0; padding: 0; font-size: 12pt; }
    @page { margin: 0.5cm; }
    .invoice-view-wrapper { padding: 0 !important; margin: 0 !important; max-width: 100% !important; width: 100% !important; }
    .invoice-document { box-shadow: none !important; border-radius: 0 !important; border: none !important; width: 100% !important; margin: 0 !important; }
    .invoice-content { padding: 20px !important; }
    .invoice-header { margin-bottom: 30px !important; }
    .invoice-bill-grid { margin-bottom: 20px !important; padding: 20px 0 !important; }
    .line-items-table td { padding: 15px 0 !important; }
    .invoice-summary { gap: 30px !important; }
    .invoice-footer { margin-top: 30px !important; }
    .invoice-brand-strip { height: 8px !important; }
}

@media (max-width: 768px) {
    .invoice-header { flex-direction: column; gap: 30px; }
    .document-title { text-align: left; }
    .meta-grid { justify-content: flex-start; }
    .invoice-bill-grid { grid-template-columns: 1fr; }
    .date-box { align-items: flex-start; text-align: left; }
    .invoice-summary { grid-template-columns: 1fr; }
    .form-grid { grid-template-columns: 1fr; }
    .full-width { grid-column: span 1; }
}
</style>

<?php include APP_ROOT . '/app/Views/layouts/footer.php'; ?>
