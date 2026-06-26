<?php

namespace App\Services;

use App\Models\DepositRequest;
use App\Models\User;
use App\Models\Reference;
use App\Events\DepositRequestCreated;
use App\Events\DepositRequestReviewed;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class DepositWorkflowService
{
    public function createDepositRequest(array $data, User $user): DepositRequest
    {
        return DB::transaction(function () use ($data, $user) {
            if (isset($data['file_path'])) {
                $data['file_path'] = $data['file_path']->store('deposit-requests', 'public');
            }

            $depositRequest = $user->depositRequests()->create($data);

            event(new DepositRequestCreated($depositRequest));

            activity()
                ->causedBy($user)
                ->performedOn($depositRequest)
                ->log('Deposit request created');

            return $depositRequest;
        });
    }

    public function reviewDepositRequest(DepositRequest $depositRequest, array $data, User $reviewer): DepositRequest
    {
        return DB::transaction(function () use ($depositRequest, $data, $reviewer) {
            $review = $depositRequest->reviews()->create([
                'reviewer_id' => $reviewer->id,
                'reviewer_role' => $reviewer->roles->first()?->name ?? 'user',
                'decision' => $data['decision'],
                'justification' => $data['justification'] ?? null,
            ]);

            $statusMap = [
                'approve' => $reviewer->hasRole('admin') ? 'approved' : 'approved_by_manager',
                'reject' => 'rejected',
                'second_review' => 'second_review',
            ];

            $depositRequest->update(['status' => $statusMap[$data['decision']]);

            event(new DepositRequestReviewed($depositRequest, $data['decision']));

            activity()
                ->causedBy($reviewer)
                ->performedOn($depositRequest)
                ->withProperties(['decision' => $data['decision']])
                ->log('Deposit request reviewed');

            return $depositRequest;
        });
    }

    public function convertToReference(DepositRequest $depositRequest, User $bibliothecaire): Reference
    {
        return DB::transaction(function () use ($depositRequest, $bibliothecaire) {
            $referenceData = [
                'title' => $depositRequest->title,
                'abstract' => $depositRequest->description,
                'category_id' => $depositRequest->category_id,
                'language' => $depositRequest->language,
                'publication_year' => $depositRequest->publication_year,
                'uploaded_by' => $bibliothecaire->id,
                'bibliothecaire_id' => $bibliothecaire->id,
                'status' => 'draft',
            ];

            if ($depositRequest->file_path) {
                $referenceData['file_path'] = $depositRequest->file_path;
            }

            $reference = Reference::create($referenceData);

            $depositRequest->update(['status' => 'published']);

            activity()
                ->causedBy($bibliothecaire)
                ->performedOn($reference)
                ->log('Reference created from deposit request');

            return $reference;
        });
    }
}
