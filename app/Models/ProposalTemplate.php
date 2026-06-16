<?php

namespace App\Models;

use App\Core\Model;

class ProposalTemplate extends Model {
    protected $table = 'proposal_templates';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'name',
        'description',
        'category',
        'template_content',
        'pricing_tiers',
        'terms_conditions',
        'is_active',
        'is_default',
        'usage_count',
        'created_by',
        'created_at',
        'updated_at'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function createTemplate($data, $pricingTiers = [], $sections = [], $variables = []) {
        $data['created_by'] = $_SESSION['user_id'] ?? null;
        $data['usage_count'] = 0;
        $data['template_content'] = json_encode($data['template_content'] ?? []);
        $data['pricing_tiers'] = json_encode($pricingTiers);
        
        $templateId = $this->insert($data);
        
        if ($templateId) {
            // Insert pricing tiers
            if (!empty($pricingTiers)) {
                $this->insertPricingTiers($templateId, $pricingTiers);
            }
            
            // Insert sections
            if (!empty($sections)) {
                $this->insertSections($templateId, $sections);
            }
            
            // Insert variables
            if (!empty($variables)) {
                $this->insertVariables($templateId, $variables);
            }
        }
        
        return $templateId;
    }

    public function getTemplateById($id) {
        $stmt = $this->pdo->prepare("
            SELECT pt.*, u.full_name as created_by_name
            FROM {$this->table} pt
            LEFT JOIN users u ON pt.created_by = u.id
            WHERE pt.id = :id
        ");
        $stmt->execute(['id' => $id]);
        $template = $stmt->fetch();
        
        if ($template) {
            $template['template_content'] = json_decode($template['template_content'], true) ?? [];
            $template['pricing_tiers'] = json_decode($template['pricing_tiers'], true) ?? [];
            $template['pricing_tiers_detailed'] = $this->getPricingTiers($id);
            $template['sections'] = $this->getSections($id);
            $template['variables'] = $this->getVariables($id);
        }
        
        return $template;
    }

    public function getAllTemplates($filters = []) {
        $sql = "
            SELECT pt.*, u.full_name as created_by_name
            FROM {$this->table} pt
            LEFT JOIN users u ON pt.created_by = u.id
            WHERE 1=1
        ";
        
        $params = [];
        
        if (!empty($filters['category'])) {
            $sql .= " AND pt.category = :category";
            $params['category'] = $filters['category'];
        }
        
        if (!empty($filters['is_active'])) {
            $sql .= " AND pt.is_active = :is_active";
            $params['is_active'] = $filters['is_active'];
        }
        
        if (!empty($filters['created_by'])) {
            $sql .= " AND pt.created_by = :created_by";
            $params['created_by'] = $filters['created_by'];
        }
        
        $sql .= " ORDER BY pt.is_default DESC, pt.usage_count DESC, pt.name ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $templates = $stmt->fetchAll();
        
        foreach ($templates as &$template) {
            $template['template_content'] = json_decode($template['template_content'], true) ?? [];
            $template['pricing_tiers'] = json_decode($template['pricing_tiers'], true) ?? [];
        }
        
        return $templates;
    }

    public function getActiveTemplates() {
        return $this->getAllTemplates(['is_active' => true]);
    }

    public function getTemplatesByCategory($category) {
        return $this->getAllTemplates(['category' => $category, 'is_active' => true]);
    }

    public function getDefaultTemplate() {
        $stmt = $this->pdo->prepare("
            SELECT pt.*, u.full_name as created_by_name
            FROM {$this->table} pt
            LEFT JOIN users u ON pt.created_by = u.id
            WHERE pt.is_default = TRUE AND pt.is_active = TRUE
            ORDER BY pt.usage_count DESC
            LIMIT 1
        ");
        $stmt->execute();
        $template = $stmt->fetch();
        
        if ($template) {
            $template['template_content'] = json_decode($template['template_content'], true) ?? [];
            $template['pricing_tiers'] = json_decode($template['pricing_tiers'], true) ?? [];
            $template['pricing_tiers_detailed'] = $this->getPricingTiers($template['id']);
            $template['sections'] = $this->getSections($template['id']);
            $template['variables'] = $this->getVariables($template['id']);
        }
        
        return $template;
    }

    public function updateTemplate($id, $data, $pricingTiers = null, $sections = null, $variables = null) {
        if (isset($data['template_content'])) {
            $data['template_content'] = json_encode($data['template_content']);
        }
        
        if (isset($data['pricing_tiers'])) {
            $data['pricing_tiers'] = json_encode($data['pricing_tiers']);
        }
        
        $result = $this->update($id, $data);
        
        if ($result) {
            // Update pricing tiers if provided
            if ($pricingTiers !== null) {
                $this->updatePricingTiers($id, $pricingTiers);
            }
            
            // Update sections if provided
            if ($sections !== null) {
                $this->updateSections($id, $sections);
            }
            
            // Update variables if provided
            if ($variables !== null) {
                $this->updateVariables($id, $variables);
            }
        }
        
        return $result;
    }

    public function deleteTemplate($id) {
        // Check if template is used in any proposals
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM proposals WHERE template_id = :template_id");
        $stmt->execute(['template_id' => $id]);
        $result = $stmt->fetch();
        
        if ($result['count'] > 0) {
            return false; // Cannot delete template used in proposals
        }
        
        // Delete related records
        $this->deletePricingTiers($id);
        $this->deleteSections($id);
        $this->deleteVariables($id);
        
        return $this->delete($id);
    }

    public function duplicateTemplate($id, $newName) {
        $template = $this->getTemplateById($id);
        
        if (!$template) {
            return false;
        }

        $newTemplateData = [
            'name' => $newName,
            'description' => $template['description'] . ' (Copy)',
            'category' => $template['category'],
            'template_content' => $template['template_content'],
            'pricing_tiers' => $template['pricing_tiers'],
            'terms_conditions' => $template['terms_conditions'],
            'is_active' => false, // New templates start as inactive
            'is_default' => false
        ];

        $newTemplateId = $this->createTemplate(
            $newTemplateData,
            $template['pricing_tiers_detailed'] ?? [],
            $template['sections'] ?? [],
            $template['variables'] ?? []
        );
        
        return $newTemplateId;
    }

    public function setAsDefault($id) {
        // Remove default flag from all templates
        $this->pdo->prepare("UPDATE {$this->table} SET is_default = FALSE")->execute();
        
        // Set new default
        return $this->update($id, ['is_default' => true]);
    }

    public function incrementUsage($id) {
        $stmt = $this->pdo->prepare("UPDATE {$this->table} SET usage_count = usage_count + 1 WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function generateProposalFromTemplate($templateId, $clientData, $customizations = []) {
        $template = $this->getTemplateById($templateId);
        
        if (!$template) {
            return false;
        }

        // Increment usage count
        $this->incrementUsage($templateId);

        // Generate proposal content
        $proposalContent = $this->processTemplateContent($template['template_content'], $clientData, $customizations);
        
        // Process pricing tiers
        $selectedTier = $customizations['selected_tier'] ?? null;
        $pricingData = $this->processPricingTiers($template['pricing_tiers_detailed'], $selectedTier, $customizations);

        return [
            'template_id' => $templateId,
            'template_name' => $template['name'],
            'title' => $this->generateProposalTitle($template, $clientData),
            'description' => $proposalContent['description'] ?? '',
            'content' => $proposalContent,
            'pricing' => $pricingData,
            'timeline' => $pricingData['timeline_weeks'] ?? 4,
            'total_amount' => $pricingData['total_amount'],
            'terms_conditions' => $template['terms_conditions'],
            'sections' => $template['sections'],
            'variables' => $template['variables']
        ];
    }

    public function getTemplateAnalytics($templateId = null, $filters = []) {
        $sql = "
            SELECT pa.*, pt.name as template_name, p.title as proposal_title, c.full_name as client_name
            FROM proposal_analytics pa
            LEFT JOIN proposal_templates pt ON pa.template_id = pt.id
            LEFT JOIN proposals p ON pa.proposal_id = p.id
            LEFT JOIN users c ON pa.client_id = c.id
            WHERE 1=1
        ";
        
        $params = [];
        
        if ($templateId) {
            $sql .= " AND pa.template_id = :template_id";
            $params['template_id'] = $templateId;
        }
        
        if (!empty($filters['conversion_status'])) {
            $sql .= " AND pa.conversion_status = :conversion_status";
            $params['conversion_status'] = $filters['conversion_status'];
        }
        
        if (!empty($filters['date_from'])) {
            $sql .= " AND pa.created_at >= :date_from";
            $params['date_from'] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $sql .= " AND pa.created_at <= :date_to";
            $params['date_to'] = $filters['date_to'];
        }
        
        $sql .= " ORDER BY pa.created_at DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getTemplateStats($templateId = null) {
        $sql = "
            SELECT 
                COUNT(*) as total_proposals,
                SUM(view_count) as total_views,
                AVG(time_spent) as avg_time_spent,
                SUM(CASE WHEN conversion_status = 'accepted' THEN 1 ELSE 0 END) as accepted_count,
                SUM(CASE WHEN conversion_status = 'rejected' THEN 1 ELSE 0 END) as rejected_count,
                SUM(CASE WHEN conversion_status = 'viewed' THEN 1 ELSE 0 END) as viewed_count
            FROM proposal_analytics
            WHERE 1=1
        ";
        
        $params = [];
        
        if ($templateId) {
            $sql .= " AND template_id = :template_id";
            $params['template_id'] = $templateId;
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    public function getPopularTemplates($limit = 10) {
        $stmt = $this->pdo->prepare("
            SELECT pt.*, COUNT(pa.id) as usage_count
            FROM {$this->table} pt
            LEFT JOIN proposal_analytics pa ON pt.id = pa.template_id
            WHERE pt.is_active = TRUE
            GROUP BY pt.id
            ORDER BY usage_count DESC, pt.usage_count DESC
            LIMIT :limit
        ");
        $stmt->execute(['limit' => $limit]);
        return $stmt->fetchAll();
    }

    // Pricing Tiers methods
    private function insertPricingTiers($templateId, $tiers) {
        $stmt = $this->pdo->prepare("
            INSERT INTO template_pricing_tiers 
            (template_id, tier_name, description, base_price, features, timeline_weeks, support_level, revisions_included, is_popular, sort_order)
            VALUES (:template_id, :tier_name, :description, :base_price, :features, :timeline_weeks, :support_level, :revisions_included, :is_popular, :sort_order)
        ");
        
        foreach ($tiers as $index => $tier) {
            $stmt->execute([
                'template_id' => $templateId,
                'tier_name' => $tier['tier_name'],
                'description' => $tier['description'] ?? '',
                'base_price' => $tier['base_price'],
                'features' => json_encode($tier['features'] ?? []),
                'timeline_weeks' => $tier['timeline_weeks'] ?? 4,
                'support_level' => $tier['support_level'] ?? 'basic',
                'revisions_included' => $tier['revisions_included'] ?? 2,
                'is_popular' => $tier['is_popular'] ?? false,
                'sort_order' => $tier['sort_order'] ?? $index
            ]);
        }
    }

    public function getPricingTiers($templateId) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM template_pricing_tiers 
            WHERE template_id = :template_id 
            ORDER BY sort_order ASC
        ");
        $stmt->execute(['template_id' => $templateId]);
        $tiers = $stmt->fetchAll();
        
        foreach ($tiers as &$tier) {
            $tier['features'] = json_decode($tier['features'], true) ?? [];
        }
        
        return $tiers;
    }

    private function updatePricingTiers($templateId, $tiers) {
        // Delete existing tiers
        $this->deletePricingTiers($templateId);
        
        // Insert new tiers
        $this->insertPricingTiers($templateId, $tiers);
    }

    private function deletePricingTiers($templateId) {
        $stmt = $this->pdo->prepare("DELETE FROM template_pricing_tiers WHERE template_id = :template_id");
        return $stmt->execute(['template_id' => $templateId]);
    }

    // Sections methods
    private function insertSections($templateId, $sections) {
        $stmt = $this->pdo->prepare("
            INSERT INTO template_sections 
            (template_id, section_name, section_type, content, is_required, sort_order)
            VALUES (:template_id, :section_name, :section_type, :content, :is_required, :sort_order)
        ");
        
        foreach ($sections as $index => $section) {
            $stmt->execute([
                'template_id' => $templateId,
                'section_name' => $section['section_name'],
                'section_type' => $section['section_type'] ?? 'custom',
                'content' => $section['content'] ?? '',
                'is_required' => $section['is_required'] ?? true,
                'sort_order' => $section['sort_order'] ?? $index
            ]);
        }
    }

    public function getSections($templateId) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM template_sections 
            WHERE template_id = :template_id 
            ORDER BY sort_order ASC
        ");
        $stmt->execute(['template_id' => $templateId]);
        return $stmt->fetchAll();
    }

    private function updateSections($templateId, $sections) {
        // Delete existing sections
        $this->deleteSections($templateId);
        
        // Insert new sections
        $this->insertSections($templateId, $sections);
    }

    private function deleteSections($templateId) {
        $stmt = $this->pdo->prepare("DELETE FROM template_sections WHERE template_id = :template_id");
        return $stmt->execute(['template_id' => $templateId]);
    }

    // Variables methods
    private function insertVariables($templateId, $variables) {
        $stmt = $this->pdo->prepare("
            INSERT INTO template_variables 
            (template_id, variable_name, variable_type, default_value, description, validation_rules, is_required, sort_order)
            VALUES (:template_id, :variable_name, :variable_type, :default_value, :description, :validation_rules, :is_required, :sort_order)
        ");
        
        foreach ($variables as $index => $variable) {
            $stmt->execute([
                'template_id' => $templateId,
                'variable_name' => $variable['variable_name'],
                'variable_type' => $variable['variable_type'] ?? 'text',
                'default_value' => $variable['default_value'] ?? '',
                'description' => $variable['description'] ?? '',
                'validation_rules' => json_encode($variable['validation_rules'] ?? []),
                'is_required' => $variable['is_required'] ?? false,
                'sort_order' => $variable['sort_order'] ?? $index
            ]);
        }
    }

    public function getVariables($templateId) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM template_variables 
            WHERE template_id = :template_id 
            ORDER BY sort_order ASC
        ");
        $stmt->execute(['template_id' => $templateId]);
        $variables = $stmt->fetchAll();
        
        foreach ($variables as &$variable) {
            $variable['validation_rules'] = json_decode($variable['validation_rules'], true) ?? [];
        }
        
        return $variables;
    }

    private function updateVariables($templateId, $variables) {
        // Delete existing variables
        $this->deleteVariables($templateId);
        
        // Insert new variables
        $this->insertVariables($templateId, $variables);
    }

    private function deleteVariables($templateId) {
        $stmt = $this->pdo->prepare("DELETE FROM template_variables WHERE template_id = :template_id");
        return $stmt->execute(['template_id' => $templateId]);
    }

    // Helper methods
    private function processTemplateContent($content, $clientData, $customizations) {
        $processed = $content;
        
        // Replace template variables
        foreach ($clientData as $key => $value) {
            if (is_string($processed)) {
                $processed = str_replace('{{' . $key . '}}', $value, $processed);
            }
        }
        
        return $processed;
    }

    private function processPricingTiers($tiers, $selectedTier, $customizations) {
        if (!$tiers) {
            return ['total_amount' => 0, 'timeline_weeks' => 4];
        }
        
        $selectedTierData = null;
        
        if ($selectedTier) {
            // Find the selected tier
            foreach ($tiers as $tier) {
                if ($tier['tier_name'] === $selectedTier || $tier['id'] == $selectedTier) {
                    $selectedTierData = $tier;
                    break;
                }
            }
        }
        
        // If no specific tier selected, use the first one
        if (!$selectedTierData && !empty($tiers)) {
            $selectedTierData = $tiers[0];
        }
        
        if (!$selectedTierData) {
            return ['total_amount' => 0, 'timeline_weeks' => 4];
        }
        
        // Apply customizations
        $totalAmount = $selectedTierData['base_price'];
        if (!empty($customizations['price_adjustment'])) {
            $totalAmount += $customizations['price_adjustment'];
        }
        
        return [
            'tier_name' => $selectedTierData['tier_name'],
            'total_amount' => $totalAmount,
            'timeline_weeks' => $selectedTierData['timeline_weeks'] ?? 4,
            'features' => $selectedTierData['features'] ?? [],
            'support_level' => $selectedTierData['support_level'] ?? 'basic',
            'revisions_included' => $selectedTierData['revisions_included'] ?? 2
        ];
    }

    private function generateProposalTitle($template, $clientData) {
        $clientName = $clientData['client_name'] ?? 'Client';
        $templateName = $template['name'];
        
        return "Proposal for {$clientName} - {$templateName}";
    }

    public function logTemplateUsage($templateId, $proposalId, $action, $changes = null) {
        $stmt = $this->pdo->prepare("
            INSERT INTO template_usage_log 
            (template_id, proposal_id, user_id, action, changes)
            VALUES (:template_id, :proposal_id, :user_id, :action, :changes)
        ");
        
        return $stmt->execute([
            'template_id' => $templateId,
            'proposal_id' => $proposalId,
            'user_id' => $_SESSION['user_id'] ?? null,
            'action' => $action,
            'changes' => json_encode($changes)
        ]);
    }

    public function searchTemplates($query, $filters = []) {
        $sql = "
            SELECT pt.*, u.full_name as created_by_name
            FROM {$this->table} pt
            LEFT JOIN users u ON pt.created_by = u.id
            WHERE (pt.name LIKE :query OR pt.description LIKE :query)
            AND pt.is_active = TRUE
        ";
        
        $params = ['query' => '%' . $query . '%'];
        
        if (!empty($filters['category'])) {
            $sql .= " AND pt.category = :category";
            $params['category'] = $filters['category'];
        }
        
        $sql .= " ORDER BY pt.usage_count DESC, pt.name ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
