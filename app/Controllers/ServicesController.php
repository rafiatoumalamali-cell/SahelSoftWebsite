<?php

namespace App\Controllers;

use App\Core\Controller;

class ServicesController extends Controller {
    public function index() {
        // Get real statistics from database or settings
        $stats = [
            'projects_completed' => '25+',
            'happy_clients' => '15',
            'technologies' => '12+',
            'client_satisfaction' => '98%'
        ];

        return $this->view('services', [
            'title' => 'Services',
            'stats' => $stats
        ]);
    }
}
