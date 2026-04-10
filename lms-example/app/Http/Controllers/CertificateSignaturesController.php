<?php

namespace App\Http\Controllers;

use App\Models\CertificateSignature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;

final class CertificateSignaturesController extends Controller
{
    private const ALLOWED_TYPES = ['board_of_faculty', 'supervisor', 'facilitator'];
    private const STORAGE_DIR = 'certificate/signatures';

    /**
     * Admin: page to upload Board of Faculty and Supervisor signatures.
     */
    public function index()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only admins can manage certificate signatures.');
        }

        $board = CertificateSignature::where('type', 'board_of_faculty')->whereNull('user_id')->first();
        $supervisor = CertificateSignature::where('type', 'supervisor')->whereNull('user_id')->first();

        return view('admin.certificate-signatures', [
            'boardOfFaculty' => $board,
            'supervisor' => $supervisor,
        ]);
    }

    /**
     * Admin: store Board of Faculty or Supervisor signature.
     */
    public function store(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only admins can upload certificate signatures.');
        }

        $request->validate([
            'type' => 'required|in:board_of_faculty,supervisor',
            'signature' => 'required|file|image|max:2048',
        ]);

        $type = $request->input('type');
        $file = $request->file('signature');
        $path = $this->storeFile($file, $type, null);

        CertificateSignature::updateOrCreate(
            ['type' => $type, 'user_id' => null],
            ['path' => $path]
        );

        return redirect()
            ->route('admin.certificate-signatures.index')
            ->with('message', $type === 'board_of_faculty' ? 'Board of Faculty signature saved.' : 'Supervisor signature saved.');
    }

    /**
     * Admin: preview uploaded Board of Faculty or Supervisor signature image.
     */
    public function preview(string $type): Response
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }
        if (!in_array($type, ['board_of_faculty', 'supervisor'], true)) {
            abort(404);
        }
        $sig = CertificateSignature::where('type', $type)->whereNull('user_id')->first();
        if (!$sig || !$sig->path) {
            abort(404);
        }
        $fullPath = storage_path('app/' . $sig->path);
        if (!is_file($fullPath) || !is_readable($fullPath)) {
            abort(404);
        }
        $mime = match (strtolower(pathinfo($fullPath, PATHINFO_EXTENSION))) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            default => 'application/octet-stream',
        };
        return response()->file($fullPath, ['Content-Type' => $mime]);
    }

    /**
     * Facilitator: page to upload own facilitator signature.
     */
    public function facilitatorForm()
    {
        if (!Auth::user()->canEditCourses()) {
            abort(403, 'Only facilitators can upload their signature.');
        }

        $sig = CertificateSignature::where('type', 'facilitator')->where('user_id', Auth::id())->first();

        return view('facilitator.certificate-signature', ['signature' => $sig]);
    }

    /**
     * Facilitator: store own facilitator signature.
     */
    public function storeFacilitator(Request $request)
    {
        if (!Auth::user()->canEditCourses()) {
            abort(403, 'Only facilitators can upload their signature.');
        }

        $request->validate([
            'signature' => 'required|file|image|max:2048',
        ]);

        $file = $request->file('signature');
        $path = $this->storeFile($file, 'facilitator', (string) Auth::id());

        CertificateSignature::updateOrCreate(
            ['type' => 'facilitator', 'user_id' => Auth::id()],
            ['path' => $path]
        );

        return redirect()
            ->route('instructor.certificate-signature')
            ->with('message', 'Your facilitator signature has been saved. It will appear on certificates for courses you instruct.');
    }

    private function storeFile($file, string $type, ?string $userSuffix): string
    {
        $name = $userSuffix ? "{$type}_{$userSuffix}" : $type;
        $ext = $file->getClientOriginalExtension() ?: 'png';
        $filename = $name . '.' . $ext;
        $path = $file->storeAs(self::STORAGE_DIR, $filename, 'local');
        return $path;
    }
}
