<?php

namespace App\Models;

use App\Core\Model;

class Contact extends Model {
    protected $table = 'contacts';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id',
        'name',
        'organization',
        'email',
        'phone',
        'project_type',
        'budget',
        'description',
        'attachments',
        'status',
        'admin_notes',
        'business_type',
        'website_purpose',
        'target_audience',
        'existing_website',
        'existing_url',
        'design_style',
        'branding_colors',
        'branding_fonts',
        'competitor_urls',
        'required_features',
        'timeline_start',
        'timeline_deadline',
        'hosting_requirements',
        'domain_name',
        'seo_requirements',
        'payment_integration',
        'payment_integrations',
        'cms_preference',
        'ongoing_support_needed',
        'support_level',
        'training_needed',
        'training_details',
        'current_marketing_tools',
        'integrations_needed',
        'mobile_responsive',
        'multilingual',
        'languages',
        'analytics_tracking',
        'additional_notes',
        'industry',
        'created_at',
        'updated_at'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
