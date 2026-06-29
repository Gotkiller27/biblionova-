<?php

namespace App\Services;

use App\Models\DepositRequest;
use App\Models\DepositRequestAttachment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class DepositRequestService
{
    public function submitDepositRequest(DepositRequest $depositRequest): DepositRequest
    {
        if (!$depositRequest->isDraft()) {
            throw new \Exception('Cette demande a déjà été soumise');
        }

        $depositRequest->submit();
        return $depositRequest->load(['applicant', 'reviews']);
    }

    public function cancelDepositRequest(DepositRequest $depositRequest, ?string $reason = null): DepositRequest
    {
        if ($depositRequest->isCancelled()) {
            throw new \Exception('Cette demande est déjà annulée');
        }

        $depositRequest->cancel($reason);
        return $depositRequest->load(['applicant', 'reviews']);
    }

    public function addAttachment(
        DepositRequest $depositRequest,
        UploadedFile $file,
        string $fileType,
        ?string $description = null
    ): DepositRequestAttachment {
        $filePath = $file->store('deposit-requests/' . $depositRequest->id, 'public');

        return DepositRequestAttachment::create([
            'deposit_request_id' => $depositRequest->id,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $filePath,
            'file_type' => $fileType,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'description' => $description,
        ]);
    }

    public function deleteAttachment(
        DepositRequest $depositRequest,
        DepositRequestAttachment $attachment
    ): void {
        if ($attachment->deposit_request_id !== $depositRequest->id) {
            throw new \Exception('Cette pièce jointe n\'appartient pas à cette demande');
        }

        Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();
    }

    public function getDrafts(int $userId)
    {
        return DepositRequest::with(['applicant', 'assignedManager', 'reviews.reviewer'])
            ->draft()
            ->notCancelled()
            ->where('applicant_id', $userId)
            ->latest();
    }
}
