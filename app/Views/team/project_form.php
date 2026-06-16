<?php include VIEW_PATH . '/layouts/header.php'; ?>

<style>
/* Project Form Styles */
.project-form-section {
    margin-top: 80px;
    padding: 30px 0;
    background: linear-gradient(135deg, rgba(14, 159, 110, 0.03) 0%, rgba(255, 255, 255, 0.95) 100%);
    min-height: 100vh;
}

.form-container {
    max-width: 1000px;
    margin: 0 auto;
    background: white;
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-lg);
    overflow: hidden;
    border: 1px solid rgba(14, 159, 110, 0.1);
}

.form-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    padding: 40px;
    color: white;
    position: relative;
    overflow: hidden;
}

.form-header::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
    background-size: 50px 50px;
    opacity: 0.1;
}

.form-header h2 {
    margin: 0;
    color: white;
    font-size: 2rem;
    position: relative;
    z-index: 1;
}

.form-header p {
    margin: 10px 0 0 0;
    color: rgba(255, 255, 255, 0.9);
    font-size: 1.1rem;
    position: relative;
    z-index: 1;
}

.form-content {
    padding: 40px;
}

/* Form Layout */
.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 30px;
    margin-bottom: 30px;
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
}

.form-group {
    margin-bottom: 25px;
}

.full-width {
    grid-column: 1 / -1;
}

.form-label {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
    font-weight: 600;
    color: var(--text-dark);
    font-size: 1rem;
}

.form-label i {
    color: var(--primary-color);
    font-size: 1.1rem;
}

.label-required::after {
    content: ' *';
    color: #ef4444;
}

.form-control {
    width: 100%;
    padding: 14px 16px;
    border: 2px solid var(--border-color);
    border-radius: var(--border-radius);
    font-family: var(--font-main);
    font-size: 1rem;
    color: var(--text-color);
    transition: all 0.3s ease;
    background: white;
}

.form-control:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(14, 159, 110, 0.1);
    background: white;
}

.form-control:hover {
    border-color: var(--primary-light);
}

textarea.form-control {
    resize: vertical;
    min-height: 120px;
}

/* Select Styles */
.select-wrapper {
    position: relative;
}

.select-wrapper::after {
    content: '▼';
    position: absolute;
    right: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-light);
    pointer-events: none;
    font-size: 0.8rem;
}

/* Client Search */
.client-search-container {
    position: relative;
}

.client-search-results {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 2px solid var(--border-color);
    border-top: none;
    border-radius: 0 0 var(--border-radius) var(--border-radius);
    max-height: 200px;
    overflow-y: auto;
    display: none;
    z-index: 100;
    box-shadow: var(--shadow-md);
}

.client-option {
    padding: 12px 16px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 10px;
    border-bottom: 1px solid var(--border-color);
}

.client-option:last-child {
    border-bottom: none;
}

.client-option:hover {
    background: var(--bg-light);
}

.client-avatar {
    width: 32px;
    height: 32px;
    background: var(--gradient-primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 0.9rem;
}

.client-info {
    flex: 1;
}

.client-name {
    font-weight: 600;
    color: var(--text-dark);
}

.client-email {
    font-size: 0.85rem;
    color: var(--text-light);
}

/* Features Management */
.features-container {
    background: var(--bg-light);
    padding: 20px;
    border-radius: var(--border-radius);
    margin-top: 10px;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
    padding: 10px;
    background: white;
    border-radius: var(--border-radius);
}

.feature-item input {
    flex: 1;
    padding: 10px;
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius);
    font-family: var(--font-main);
}

.remove-feature {
    background: none;
    border: none;
    color: #ef4444;
    cursor: pointer;
    font-size: 1.2rem;
    padding: 5px;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.remove-feature:hover {
    background: #fee2e2;
}

.add-feature {
    display: flex;
    align-items: center;
    gap: 10px;
    background: transparent;
    border: 2px dashed var(--border-color);
    color: var(--primary-color);
    padding: 10px 15px;
    border-radius: var(--border-radius);
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
    margin-top: 10px;
    width: 100%;
}

.add-feature:hover {
    background: rgba(14, 159, 110, 0.05);
    border-color: var(--primary-color);
}

/* Tech Stack Tags */
.tech-tags-container {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 10px;
    padding: 15px;
    background: var(--bg-light);
    border-radius: var(--border-radius);
    min-height: 60px;
    align-items: center;
}

.tech-tag {
    background: white;
    padding: 8px 15px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 500;
    color: var(--primary-dark);
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border-color);
}

.remove-tag {
    background: none;
    border: none;
    color: var(--text-light);
    cursor: pointer;
    font-size: 0.9rem;
    padding: 0;
    width: 18px;
    height: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.remove-tag:hover {
    background: #fee2e2;
    color: #ef4444;
}

.tech-input-container {
    position: relative;
    margin-top: 10px;
}

.tech-suggestions {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 2px solid var(--border-color);
    border-top: none;
    border-radius: 0 0 var(--border-radius) var(--border-radius);
    max-height: 150px;
    overflow-y: auto;
    display: none;
    z-index: 100;
    box-shadow: var(--shadow-md);
}

.tech-suggestion {
    padding: 10px 15px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.tech-suggestion:hover {
    background: var(--bg-light);
    color: var(--primary-color);
}

/* File Upload */
.file-upload-container {
    border: 2px dashed var(--border-color);
    border-radius: var(--border-radius);
    padding: 30px;
    text-align: center;
    transition: all 0.3s ease;
    cursor: pointer;
    background: var(--bg-light);
}

.file-upload-container:hover {
    border-color: var(--primary-color);
    background: rgba(14, 159, 110, 0.05);
}

.file-upload-icon {
    font-size: 3rem;
    color: var(--primary-color);
    margin-bottom: 15px;
}

.file-upload-text {
    color: var(--text-color);
    margin-bottom: 10px;
}

.file-upload-hint {
    color: var(--text-light);
    font-size: 0.9rem;
}

.file-preview {
    margin-top: 20px;
    display: none;
}

.preview-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: white;
    border-radius: var(--border-radius);
    margin-bottom: 10px;
    border: 1px solid var(--border-color);
}

.preview-icon {
    font-size: 1.5rem;
    color: var(--primary-color);
}

.preview-info {
    flex: 1;
}

.preview-name {
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 5px;
}

.preview-size {
    font-size: 0.85rem;
    color: var(--text-light);
}

.remove-file {
    background: none;
    border: none;
    color: #ef4444;
    cursor: pointer;
    padding: 5px;
    font-size: 1.2rem;
}

/* Form Actions */
.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 15px;
    margin-top: 40px;
    padding-top: 30px;
    border-top: 2px solid var(--border-color);
}

.btn-submit {
    background: var(--gradient-primary);
    color: white;
    border: none;
    padding: 15px 35px;
    border-radius: var(--border-radius);
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 10px;
}

.btn-submit:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-lg);
}

.btn-cancel {
    background: transparent;
    color: var(--text-color);
    border: 2px solid var(--border-color);
    padding: 15px 30px;
    border-radius: var(--border-radius);
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-cancel:hover {
    background: var(--bg-light);
    border-color: var(--primary-color);
    color: var(--primary-color);
}

/* Form Validation */
.form-control.error {
    border-color: #ef4444;
    background: #fef2f2;
}

.error-message {
    color: #ef4444;
    font-size: 0.85rem;
    margin-top: 5px;
    display: none;
}

.success-message {
    background: #d1fae5;
    color: #065f46;
    padding: 15px;
    border-radius: var(--border-radius);
    margin-bottom: 20px;
    display: none;
    border-left: 4px solid #10b981;
}

/* Responsive Design */
@media (max-width: 768px) {
    .form-header {
        padding: 30px 20px;
    }
    
    .form-content {
        padding: 25px;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn-submit,
    .btn-cancel {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .project-form-section {
        margin-top: 60px;
        padding: 20px 0;
    }
    
    .form-container {
        border-radius: 0;
        box-shadow: none;
        border: none;
    }
    
    .form-header h2 {
        font-size: 1.5rem;
    }
}
</style>

<section class="project-form-section">
    <div class="form-container">
        <!-- Form Header -->
        <div class="form-header">
            <h2><?= isset($project) ? '✏️ Edit Project' : '🚀 Create New Project' ?></h2>
            <p><?= isset($project) ? 
                'Update project details and manage settings' : 
                'Start a new software project with SahelSoft' ?></p>
        </div>

        <!-- Success Message (for redirects) -->
        <?php if (isset($_SESSION['form_success'])): ?>
            <div class="success-message" style="display: block;">
                <?= $_SESSION['form_success'] ?>
                <?php unset($_SESSION['form_success']); ?>
            </div>
        <?php endif; ?>

        <!-- Form Content -->
        <div class="form-content">
            <form action="<?= APP_URL ?>/team/project/<?= isset($project) ? 'update' : 'store' ?>" method="POST" id="projectForm" enctype="multipart/form-data">
                <?php if (isset($project)): ?>
                    <input type="hidden" name="id" value="<?= $project['id'] ?>">
                <?php endif; ?>

                <div class="form-grid">
                    <!-- Project Title -->
                    <div class="form-group full-width">
                        <label class="form-label label-required">
                            <i class="fas fa-heading"></i>
                            Project Title
                        </label>
                        <input type="text" name="title" class="form-control" 
                               value="<?= htmlspecialchars($project['title'] ?? '') ?>" 
                               placeholder="e.g., Niger E-commerce Platform" 
                               required>
                        <div class="error-message" id="title-error"></div>
                    </div>

                    <!-- Client Selection -->
                    <div class="form-group full-width">
                        <label class="form-label label-required">
                            <i class="fas fa-user"></i>
                            Client
                        </label>
                        <div class="client-search-container">
                            <input type="text" class="form-control" id="clientSearch" 
                                   placeholder="Search for client by name or email..."
                                   value="<?= isset($project) ? ($project['client_name'] ?? '') : '' ?>"
                                   <?= !isset($project) ? 'required' : 'disabled' ?>>
                            <input type="hidden" name="client_id" id="clientId" value="<?= $project['client_id'] ?? '' ?>">
                            <div class="client-search-results" id="clientResults"></div>
                        </div>
                        <small style="color: var(--text-light); display: block; margin-top: 5px;">
                            Type to search existing clients. New clients will be created separately.
                        </small>
                        <div class="error-message" id="client-error"></div>
                    </div>

                    <!-- Project Description -->
                    <div class="form-group full-width">
                        <label class="form-label label-required">
                            <i class="fas fa-align-left"></i>
                            Project Description
                        </label>
                        <textarea name="description" class="form-control" rows="5" 
                                  placeholder="Describe the project goals, requirements, and scope..."
                                  required><?= htmlspecialchars($project['description'] ?? '') ?></textarea>
                        <div class="error-message" id="description-error"></div>
                    </div>

                    <!-- Budget -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-money-bill-wave"></i>
                            Budget (CFA)
                        </label>
                        <input type="number" name="budget" class="form-control" 
                               value="<?= $project['budget'] ?? '' ?>" 
                               placeholder="e.g., 500000" min="0">
                        <div class="error-message" id="budget-error"></div>
                    </div>

                    <!-- Project Category -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-folder"></i>
                            Category
                        </label>
                        <div class="select-wrapper">
                            <select name="category" class="form-control">
                                <option value="">Select Category</option>
                                <option value="ecommerce" <?= ($project['category'] ?? '') == 'ecommerce' ? 'selected' : '' ?>>E-commerce Platform</option>
                                <option value="website" <?= ($project['category'] ?? '') == 'website' ? 'selected' : '' ?>>Website Development</option>
                                <option value="mobile" <?= ($project['category'] ?? '') == 'mobile' ? 'selected' : '' ?>>Mobile Application</option>
                                <option value="enterprise" <?= ($project['category'] ?? '') == 'enterprise' ? 'selected' : '' ?>>Enterprise Software</option>
                                <option value="government" <?= ($project['category'] ?? '') == 'government' ? 'selected' : '' ?>>Government Portal</option>
                                <option value="education" <?= ($project['category'] ?? '') == 'education' ? 'selected' : '' ?>>Education Platform</option>
                            </select>
                        </div>
                    </div>

                    <!-- Dates -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-calendar-plus"></i>
                            Start Date
                        </label>
                        <input type="date" name="start_date" class="form-control" 
                               value="<?= $project['start_date'] ?? '' ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-calendar-check"></i>
                            Deadline
                        </label>
                        <input type="date" name="deadline" class="form-control" 
                               value="<?= $project['deadline'] ?? '' ?>">
                    </div>

                    <!-- Status (for edit only) -->
                    <?php if (isset($project)): ?>
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-tasks"></i>
                            Status
                        </label>
                        <div class="select-wrapper">
                            <select name="status" class="form-control">
                                <option value="proposed" <?= ($project['status'] ?? '') == 'proposed' ? 'selected' : '' ?>>Proposed</option>
                                <option value="pending" <?= ($project['status'] ?? '') == 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="active" <?= ($project['status'] ?? '') == 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="on_hold" <?= ($project['status'] ?? '') == 'on_hold' ? 'selected' : '' ?>>On Hold</option>
                                <option value="completed" <?= ($project['status'] ?? '') == 'completed' ? 'selected' : '' ?>>Completed</option>
                                <option value="cancelled" <?= ($project['status'] ?? '') == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Priority -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-exclamation-circle"></i>
                            Priority
                        </label>
                        <div class="select-wrapper">
                            <select name="priority" class="form-control">
                                <option value="low" <?= ($project['priority'] ?? '') == 'low' ? 'selected' : '' ?>>Low</option>
                                <option value="medium" <?= ($project['priority'] ?? '') == 'medium' ? 'selected' : '' ?>>Medium</option>
                                <option value="high" <?= ($project['priority'] ?? '') == 'high' ? 'selected' : '' ?>>High</option>
                                <option value="urgent" <?= ($project['priority'] ?? '') == 'urgent' ? 'selected' : '' ?>>Urgent</option>
                            </select>
                        </div>
                    </div>

                    <!-- Features List -->
                    <div class="form-group full-width">
                        <label class="form-label">
                            <i class="fas fa-star"></i>
                            Key Features
                        </label>
                        <div class="features-container" id="featuresContainer">
                            <?php 
                            $features = isset($project['features']) ? json_decode($project['features'], true) : ['User Authentication', 'Dashboard'];
                            if (!is_array($features)) $features = [];
                            ?>
                            <?php foreach ($features as $index => $feature): ?>
                                <div class="feature-item">
                                    <input type="text" name="features[]" class="form-control" 
                                           value="<?= htmlspecialchars($feature) ?>" 
                                           placeholder="Enter a feature">
                                    <button type="button" class="remove-feature">&times;</button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="add-feature">
                            <i class="fas fa-plus"></i>
                            Add Feature
                        </button>
                    </div>

                    <!-- Tech Stack -->
                    <div class="form-group full-width">
                        <label class="form-label">
                            <i class="fas fa-code"></i>
                            Technology Stack
                        </label>
                        <input type="text" class="form-control" id="techInput" placeholder="Add technologies (PHP, React, MySQL...)">
                        <div class="tech-suggestions" id="techSuggestions"></div>
                        <div class="tech-tags-container" id="techTagsContainer">
                            <?php 
                            $techStack = isset($project['tech_stack']) ? json_decode($project['tech_stack'], true) : ['PHP', 'MySQL'];
                            if (!is_array($techStack)) $techStack = [];
                            ?>
                            <?php foreach ($techStack as $tech): ?>
                                <div class="tech-tag">
                                    <span><?= htmlspecialchars($tech) ?></span>
                                    <input type="hidden" name="tech_stack[]" value="<?= htmlspecialchars($tech) ?>">
                                    <button type="button" class="remove-tag">&times;</button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Team Assignment -->
                    <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'project_manager'): ?>
                    <div class="form-group full-width">
                        <label class="form-label">
                            <i class="fas fa-users"></i>
                            Assigned Team
                        </label>
                        <div class="tech-tags-container" style="min-height: auto; padding: 20px;">
                            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px; width: 100%;">
                                <?php
                                // Sample team members - In real app, fetch from database
                                $teamMembers = [
                                    ['id' => 1, 'name' => 'Ali Moussa', 'role' => 'Project Manager'],
                                    ['id' => 2, 'name' => 'Fatima Ahmed', 'role' => 'Lead Developer'],
                                    ['id' => 3, 'name' => 'Oumar Diallo', 'role' => 'Frontend Developer'],
                                    ['id' => 4, 'name' => 'Aminata Sow', 'role' => 'Backend Developer'],
                                    ['id' => 5, 'name' => 'Boubacar Traoré', 'role' => 'UI/UX Designer'],
                                ];
                                
                                $assignedTeam = isset($project['assigned_team']) ? json_decode($project['assigned_team'], true) : [];
                                if (!is_array($assignedTeam)) $assignedTeam = [];
                                ?>
                                
                                <?php foreach ($teamMembers as $member): ?>
                                    <label style="display: flex; align-items: center; gap: 10px; padding: 10px; background: white; border-radius: var(--border-radius); border: 1px solid var(--border-color); cursor: pointer; transition: all 0.3s ease;">
                                        <input type="checkbox" name="assigned_team[]" value="<?= $member['id'] ?>"
                                               <?= in_array($member['id'], $assignedTeam) ? 'checked' : '' ?>
                                               style="margin: 0;">
                                        <div style="display: flex; align-items: center; gap: 10px; flex: 1;">
                                            <div style="width: 36px; height: 36px; background: var(--gradient-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.9rem;">
                                                <?= strtoupper(substr($member['name'], 0, 1)) ?>
                                            </div>
                                            <div>
                                                <div style="font-weight: 600; font-size: 0.9rem;"><?= $member['name'] ?></div>
                                                <div style="font-size: 0.8rem; color: var(--text-light);"><?= $member['role'] ?></div>
                                            </div>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Project Documents -->
                    <div class="form-group full-width">
                        <label class="form-label">
                            <i class="fas fa-paperclip"></i>
                            Project Documents
                        </label>
                        <div class="file-upload-container" id="fileUpload">
                            <div class="file-upload-icon">
                                <i class="fas fa-cloud-upload-alt"></i>
                            </div>
                            <div class="file-upload-text">
                                <strong>Drag & drop files here</strong> or click to browse
                            </div>
                            <div class="file-upload-hint">
                                Supports: PDF, DOC, XLS, Images (Max: 10MB per file)
                            </div>
                            <input type="file" name="documents[]" multiple style="display: none;" id="fileInput">
                        </div>
                        <div class="file-preview" id="filePreview"></div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <a href="<?= APP_URL ?>/team/dashboard" class="btn-cancel">
                        Cancel
                    </a>
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save"></i>
                        <?= isset($project) ? 'Update Project' : 'Create Project' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form Validation
    const form = document.getElementById('projectForm');
    const titleInput = form.querySelector('input[name="title"]');
    const clientIdInput = document.getElementById('clientId');
    const clientSearch = document.getElementById('clientSearch');
    const clientResults = document.getElementById('clientResults');
    
    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        // Validate title
        if (!titleInput.value.trim()) {
            showError(titleInput, 'Project title is required');
            isValid = false;
        } else if (titleInput.value.trim().length < 5) {
            showError(titleInput, 'Title must be at least 5 characters');
            isValid = false;
        } else {
            clearError(titleInput);
        }
        
        // Validate client (for new projects)
        if (!clientIdInput.value && !document.querySelector('input[name="id"]')) {
            showError(clientSearch, 'Please select a client');
            isValid = false;
        } else {
            clearError(clientSearch);
        }
        
        if (!isValid) {
            e.preventDefault();
            showNotification('Please fix the errors in the form', 'error');
        }
    });
    
    function showError(input, message) {
        input.classList.add('error');
        const errorDiv = input.parentElement.querySelector('.error-message');
        if (errorDiv) {
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
        }
    }
    
    function clearError(input) {
        input.classList.remove('error');
        const errorDiv = input.parentElement.querySelector('.error-message');
        if (errorDiv) {
            errorDiv.style.display = 'none';
        }
    }
    
    // Client Search
    if (clientSearch) {
        let searchTimeout;
        
        clientSearch.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (query.length < 2) {
                clientResults.style.display = 'none';
                return;
            }
            
            searchTimeout = setTimeout(() => {
                searchClients(query);
            }, 500);
        });
        
        // Close results when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.client-search-container')) {
                clientResults.style.display = 'none';
            }
        });
    }
    
    function searchClients(query) {
        // In real app, this would be an AJAX call
        const sampleClients = [
            { id: 1, name: 'Niger Telecom', email: 'contact@nigertelecom.ne', initials: 'NT' },
            { id: 2, name: 'Bank of Africa', email: 'it@boa.ne', initials: 'BA' },
            { id: 3, name: 'Ministry of Education', email: 'info@education.gov.ne', initials: 'ME' },
            { id: 4, name: 'AgriCorp Niger', email: 'support@agricorp.ne', initials: 'AN' },
            { id: 5, name: 'MediCare Clinic', email: 'admin@medicare.ne', initials: 'MC' }
        ];
        
        const filtered = sampleClients.filter(client => 
            client.name.toLowerCase().includes(query.toLowerCase()) ||
            client.email.toLowerCase().includes(query.toLowerCase())
        );
        
        displayClientResults(filtered);
    }
    
    function displayClientResults(clients) {
        clientResults.innerHTML = '';
        
        if (clients.length === 0) {
            clientResults.innerHTML = '<div class="client-option">No clients found</div>';
        } else {
            clients.forEach(client => {
                const div = document.createElement('div');
                div.className = 'client-option';
                div.innerHTML = `
                    <div class="client-avatar">${client.initials}</div>
                    <div class="client-info">
                        <div class="client-name">${client.name}</div>
                        <div class="client-email">${client.email}</div>
                    </div>
                `;
                div.addEventListener('click', () => selectClient(client));
                clientResults.appendChild(div);
            });
        }
        
        clientResults.style.display = 'block';
    }
    
    function selectClient(client) {
        clientSearch.value = client.name;
        clientIdInput.value = client.id;
        clientResults.style.display = 'none';
        clearError(clientSearch);
    }
    
    // Features Management
    const featuresContainer = document.getElementById('featuresContainer');
    const addFeatureBtn = document.querySelector('.add-feature');
    
    addFeatureBtn.addEventListener('click', function() {
        const featureItem = document.createElement('div');
        featureItem.className = 'feature-item';
        featureItem.innerHTML = `
            <input type="text" name="features[]" class="form-control" placeholder="Enter a feature">
            <button type="button" class="remove-feature">&times;</button>
        `;
        featuresContainer.appendChild(featureItem);
        
        // Focus on new input
        const input = featureItem.querySelector('input');
        input.focus();
    });
    
    // Remove feature
    featuresContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-feature')) {
            e.target.closest('.feature-item').remove();
        }
    });
    
    // Tech Stack Management
    const techInput = document.getElementById('techInput');
    const techSuggestions = document.getElementById('techSuggestions');
    const techTagsContainer = document.getElementById('techTagsContainer');
    
    const techSuggestionsList = [
        'PHP', 'Laravel', 'Symfony', 'Python', 'Django', 'JavaScript',
        'React', 'Vue.js', 'Node.js', 'MySQL', 'PostgreSQL', 'MongoDB',
        'HTML5', 'CSS3', 'Bootstrap', 'Tailwind CSS', 'Docker', 'Git',
        'REST API', 'GraphQL', 'AWS', 'Firebase', 'React Native', 'Flutter'
    ];
    
    techInput.addEventListener('input', function() {
        const query = this.value.trim().toLowerCase();
        
        if (query.length === 0) {
            techSuggestions.style.display = 'none';
            return;
        }
        
        const filtered = techSuggestionsList.filter(tech => 
            tech.toLowerCase().includes(query) && 
            !Array.from(document.querySelectorAll('.tech-tag span')).some(span => 
                span.textContent.toLowerCase() === tech.toLowerCase()
            )
        );
        
        displayTechSuggestions(filtered);
    });
    
    function displayTechSuggestions(suggestions) {
        techSuggestions.innerHTML = '';
        
        if (suggestions.length === 0) {
            techSuggestions.innerHTML = '<div class="tech-suggestion">No suggestions</div>';
        } else {
            suggestions.forEach(tech => {
                const div = document.createElement('div');
                div.className = 'tech-suggestion';
                div.textContent = tech;
                div.addEventListener('click', () => addTechTag(tech));
                techSuggestions.appendChild(div);
            });
        }
        
        techSuggestions.style.display = 'block';
    }
    
    function addTechTag(tech) {
        // Check if tag already exists
        const existingTags = Array.from(document.querySelectorAll('.tech-tag span'));
        if (existingTags.some(span => span.textContent.toLowerCase() === tech.toLowerCase())) {
            return;
        }
        
        const tag = document.createElement('div');
        tag.className = 'tech-tag';
        tag.innerHTML = `
            <span>${tech}</span>
            <input type="hidden" name="tech_stack[]" value="${tech}">
            <button type="button" class="remove-tag">&times;</button>
        `;
        techTagsContainer.appendChild(tag);
        
        techInput.value = '';
        techSuggestions.style.display = 'none';
    }
    
    techInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const value = this.value.trim();
            if (value) {
                addTechTag(value);
            }
        }
    });
    
    // Remove tech tag
    techTagsContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-tag')) {
            e.target.closest('.tech-tag').remove();
        }
    });
    
    // Close tech suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.tech-input-container')) {
            techSuggestions.style.display = 'none';
        }
    });
    
    // File Upload
    const fileUpload = document.getElementById('fileUpload');
    const fileInput = document.getElementById('fileInput');
    const filePreview = document.getElementById('filePreview');
    
    fileUpload.addEventListener('click', () => fileInput.click());
    
    fileUpload.addEventListener('dragover', (e) => {
        e.preventDefault();
        fileUpload.style.borderColor = 'var(--primary-color)';
        fileUpload.style.background = 'rgba(14, 159, 110, 0.05)';
    });
    
    fileUpload.addEventListener('dragleave', () => {
        fileUpload.style.borderColor = '';
        fileUpload.style.background = '';
    });
    
    fileUpload.addEventListener('drop', (e) => {
        e.preventDefault();
        fileUpload.style.borderColor = '';
        fileUpload.style.background = '';
        
        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            handleFiles(e.dataTransfer.files);
        }
    });
    
    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length) {
            handleFiles(e.target.files);
        }
    });
    
    function handleFiles(files) {
        filePreview.innerHTML = '';
        filePreview.style.display = 'block';
        
        Array.from(files).forEach(file => {
            if (file.size > 10 * 1024 * 1024) { // 10MB limit
                showNotification(`File "${file.name}" exceeds 10MB limit`, 'error');
                return;
            }
            
            const previewItem = document.createElement('div');
            previewItem.className = 'preview-item';
            
            const icon = getFileIcon(file.name);
            const size = formatFileSize(file.size);
            
            previewItem.innerHTML = `
                <div class="preview-icon">${icon}</div>
                <div class="preview-info">
                    <div class="preview-name">${file.name}</div>
                    <div class="preview-size">${size}</div>
                </div>
                <button type="button" class="remove-file">&times;</button>
            `;
            
            previewItem.querySelector('.remove-file').addEventListener('click', () => {
                previewItem.remove();
                if (filePreview.children.length === 0) {
                    filePreview.style.display = 'none';
                }
            });
            
            filePreview.appendChild(previewItem);
        });
    }
    
    function getFileIcon(filename) {
        const ext = filename.split('.').pop().toLowerCase();
        const icons = {
            pdf: '📄',
            doc: '📝',
            docx: '📝',
            xls: '📊',
            xlsx: '📊',
            jpg: '🖼️',
            jpeg: '🖼️',
            png: '🖼️',
            gif: '🖼️',
            zip: '📦',
            rar: '📦'
        };
        return icons[ext] || '📁';
    }
    
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    // Date validation
    const startDate = form.querySelector('input[name="start_date"]');
    const deadline = form.querySelector('input[name="deadline"]');
    
    if (startDate && deadline) {
        startDate.addEventListener('change', function() {
            if (this.value && deadline.value && this.value > deadline.value) {
                deadline.value = this.value;
            }
        });
        
        deadline.addEventListener('change', function() {
            if (this.value && startDate.value && this.value < startDate.value) {
                showNotification('Deadline cannot be before start date', 'error');
                this.value = startDate.value;
            }
        });
    }
    
    // Notification system
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: ${type === 'success' ? 'var(--primary-color)' : 
                        type === 'error' ? '#ef4444' : '#3b82f6'};
            color: white;
            padding: 15px 25px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-lg);
            z-index: 1000;
            animation: slideIn 0.3s ease;
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }
});

// Add CSS for animations
const projectFormAnimationStyle = document.createElement('style');
projectFormAnimationStyle.textContent = `
    @keyframes slideIn {
        from {
            transform: translateY(100%);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateY(0);
            opacity: 1;
        }
        to {
            transform: translateY(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(projectFormAnimationStyle);
</script>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>