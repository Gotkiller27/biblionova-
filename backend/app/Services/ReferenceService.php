<?php

namespace App\Services;

use App\Models\Reference;
use App\Models\User;
use App\Models\View;
use App\Models\Download;
use App\Models\DocumentRevision;
use App\Events\ReferencePublished;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ReferenceService
{
    public function createReference(array $data, User $user): Reference
    {
        return DB::transaction(function () use ($data, $user) {
            if (isset($data['cover_image'])) {
                $data['cover_image'] = $data['cover_image']->store('covers', 'public');
            }
            if (isset($data['file_path'])) {
                $data['file_path'] = $data['file_path']->store('documents', 'public');
            }

            $data['uploaded_by'] = $user->id;
            $data['bibliothecaire_id'] = $data['bibliothecaire_id'] ?? $user->id;

            $reference = Reference::create($data);

            if (isset($data['authors'])) {
                $reference->authors()->sync($data['authors']);
            }

            if (isset($data['keywords'])) {
                $reference->keywords()->sync($data['keywords']);
            }

            DocumentRevision::create([
                'reference_id' => $reference->id,
                'bibliothecaire_id' => $user->id,
                'action' => 'create',
            ]);

            if ($data['status'] === 'published') {
                event(new ReferencePublished($reference));
            }

            activity()
                ->causedBy($user)
                ->performedOn($reference)
                ->log('Reference created');

            return $reference;
        });
    }

    public function updateReference(Reference $reference, array $data, User $user): Reference
    {
        return DB::transaction(function () use ($reference, $data, $user) {
            $previousData = $reference->toArray();

            if (isset($data['cover_image'])) {
                if ($reference->cover_image) {
                    Storage::disk('public')->delete($reference->cover_image);
                }
                $data['cover_image'] = $data['cover_image']->store('covers', 'public');
            }

            if (isset($data['file_path'])) {
                if ($reference->file_path) {
                    Storage::disk('public')->delete($reference->file_path);
                }
                $data['file_path'] = $data['file_path']->store('documents', 'public');
            }

            $wasPublished = $reference->status === 'published';
            $reference->update($data);

            if (isset($data['authors'])) {
                $reference->authors()->sync($data['authors']);
            }

            if (isset($data['keywords'])) {
                $reference->keywords()->sync($data['keywords']);
            }

            DocumentRevision::create([
                'reference_id' => $reference->id,
                'bibliothecaire_id' => $user->id,
                'action' => 'update',
                'previous_data' => $previousData,
                'new_data' => $data,
            ]);

            if (!$wasPublished && $reference->status === 'published') {
                event(new ReferencePublished($reference));
            }

            activity()
                ->causedBy($user)
                ->performedOn($reference)
                ->log('Reference updated');

            return $reference;
        });
    }

    public function deleteReference(Reference $reference, User $user): void
    {
        DB::transaction(function () use ($reference, $user) {
            if ($reference->cover_image) {
                Storage::disk('public')->delete($reference->cover_image);
            }
            if ($reference->file_path) {
                Storage::disk('public')->delete($reference->file_path);
            }

            activity()
                ->causedBy($user)
                ->performedOn($reference)
                ->log('Reference deleted');

            $reference->delete();
        });
    }

    public function recordView(Reference $reference, ?User $user): View
    {
        $view = View::create([
            'user_id' => $user?->id,
            'reference_id' => $reference->id,
            'viewed_at' => now(),
        ]);

        $reference->incrementViewCount();

        return $view;
    }

    public function recordDownload(Reference $reference, ?User $user): Download
    {
        $download = Download::create([
            'user_id' => $user?->id,
            'reference_id' => $reference->id,
            'downloaded_at' => now(),
        ]);

        $reference->incrementDownloadCount();

        return $download;
    }
}
