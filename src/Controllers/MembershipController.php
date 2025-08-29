<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\RateLimiter;
use App\Models\MembershipApplication;

class MembershipController extends Controller
{
    public function saveDraft(): void
    {
        // Rate limiting
        $rateLimiter = new RateLimiter();
        if (!$rateLimiter->allow('membership_draft_' . $this->getClientIp(), 60)) {
            $this->json(['error' => 'Too many requests'], 429);
            return;
        }
        
        $this->requireCsrf();
        
        $input = $this->getJsonInput();
        $draftId = $input['draft_id'] ?? null;
        $data = $input['data'] ?? [];
        
        if (empty($data)) {
            $this->json(['error' => 'No data provided'], 400);
            return;
        }
        
        $applicationData = [
            'draft_data' => json_encode($data),
            'created_ip' => $this->getClientIp()
        ];
        
        if ($draftId) {
            // Update existing draft
            $existing = MembershipApplication::find((int) $draftId);
            if (!$existing || $existing['status'] !== 'draft') {
                $this->json(['error' => 'Draft not found'], 404);
                return;
            }
            
            MembershipApplication::update((int) $draftId, $applicationData);
        } else {
            // Create new draft
            $applicationData['status'] = 'draft';
            $draftId = MembershipApplication::create($applicationData);
        }
        
        $this->json([
            'ok' => true,
            'draft_id' => (int) $draftId,
            'saved_at' => date('c')
        ]);
    }
}