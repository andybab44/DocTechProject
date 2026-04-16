<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkJobAttachmentRequest;
use App\Models\WorkJob;
use App\Models\WorkJobAttachment;
use App\Services\WorkJobService;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WorkJobAttachmentController extends Controller
{
    public function __construct(private readonly WorkJobService $workJobService) {}

    /**
     * Upload a new attachment to a work job.
     * Accessible to all authenticated users who can view the job.
     */
    public function store(StoreWorkJobAttachmentRequest $request, WorkJob $workJob): RedirectResponse
    {
        $this->authorizeAccess($workJob);

        $this->workJobService->addAttachment($workJob, $request->user(), $request->file('file'));

        return redirect()
            ->route('work-jobs.show', $workJob)
            ->with('success', 'File uploaded successfully.');
    }

    /**
     * Stream the attachment file to the browser for download.
     */
    public function download(WorkJob $workJob, WorkJobAttachment $attachment): StreamedResponse
    {
        $this->authorizeAccess($workJob);

        abort_if($attachment->work_job_id !== $workJob->id, 404);

        return response()->streamDownload(function () use ($attachment) {
            $stream = \Illuminate\Support\Facades\Storage::disk('local')->readStream($attachment->stored_name);
            fpassthru($stream);
            fclose($stream);
        }, $attachment->original_name, ['Content-Type' => $attachment->mime_type]);
    }

    /**
     * Delete an attachment.
     * Only the uploader, the job's doctor, or an admin may delete.
     */
    public function destroy(WorkJob $workJob, WorkJobAttachment $attachment): RedirectResponse
    {
        $user = auth()->user();

        abort_if($attachment->work_job_id !== $workJob->id, 404);

        $canDelete = $user->isAdmin()
            || $attachment->uploaded_by === $user->id
            || ($user->isDoctor() && $workJob->doctor_id === $user->id);

        abort_unless($canDelete, 403);

        $this->workJobService->deleteAttachment($attachment);

        return redirect()
            ->route('work-jobs.show', $workJob)
            ->with('success', 'Attachment deleted.');
    }

    // -------------------------------------------------------------------------

    private function authorizeAccess(WorkJob $workJob): void
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return;
        }

        if ($user->isDoctor() && $workJob->doctor_id === $user->id) {
            return;
        }

        if ($user->isTechnician() && $workJob->technician_id === $user->id) {
            return;
        }

        abort(403);
    }
}
