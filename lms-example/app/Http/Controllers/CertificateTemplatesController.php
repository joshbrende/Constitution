<?php

namespace App\Http\Controllers;

use App\Models\CertificateTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

final class CertificateTemplatesController extends Controller
{
    private const STORAGE_DIR = 'certificate/templates';

    /** Directory under public/ to scan for FTP-uploaded PDFs (relative to public_path). */
    private const PUBLIC_PDF_DIR = 'asset';

    /**
     * Admin: list certificate templates and form to add (upload or select from server).
     */
    public function index()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only admins can manage certificate templates.');
        }

        $templates = CertificateTemplate::orderBy('name')->get();
        $existingPdfs = $this->listPublicPdfs();

        return view('admin.certificate-templates', compact('templates', 'existingPdfs'));
    }

    /**
     * List PDF files in public/asset/ for "select from server" (e.g. FTP-uploaded).
     *
     * @return array<string, string> basename => path relative to public (e.g. asset/foo.pdf)
     */
    private function listPublicPdfs(): array
    {
        $dir = public_path(self::PUBLIC_PDF_DIR);
        if (!File::isDirectory($dir)) {
            return [];
        }
        $paths = [];
        foreach (File::files($dir) as $file) {
            if (strtolower($file->getExtension()) === 'pdf') {
                $relative = self::PUBLIC_PDF_DIR . '/' . $file->getFilename();
                $paths[$file->getFilename()] = $relative;
            }
        }
        ksort($paths);
        return $paths;
    }

    /**
     * Admin: store a new certificate template (upload PDF or select existing file on server).
     */
    public function store(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only admins can upload certificate templates.');
        }

        $existingPath = $request->input('existing_file');
        $useExisting = is_string($existingPath) && $existingPath !== '' && str_starts_with($existingPath, self::PUBLIC_PDF_DIR . '/');

        if ($useExisting) {
            $request->validate([
                'name' => 'required|string|max:255',
                'existing_file' => 'required|string|max:500',
            ]);
            $path = $request->input('existing_file');
            // Ensure path is under public/asset/ and exists
            $allowedDir = self::PUBLIC_PDF_DIR . '/';
            if (!str_starts_with($path, $allowedDir) || preg_match('/[.\/\\\\]{2}/', $path)) {
                return redirect()->back()->withErrors(['existing_file' => 'Invalid file selection.'])->withInput();
            }
            $fullPath = public_path($path);
            if (!is_file($fullPath) || !is_readable($fullPath)) {
                return redirect()->back()->withErrors(['existing_file' => 'File not found or not readable.'])->withInput();
            }
            CertificateTemplate::create([
                'name' => $request->input('name'),
                'path' => $path,
            ]);
        } else {
            $request->validate([
                'name' => 'required|string|max:255',
                'template' => 'required|file|mimes:pdf|max:102400',
            ]);
            $file = $request->file('template');
            $template = CertificateTemplate::create([
                'name' => $request->input('name'),
                'path' => '',
            ]);
            $ext = $file->getClientOriginalExtension() ?: 'pdf';
            $filename = $template->id . '.' . $ext;
            $path = $file->storeAs(self::STORAGE_DIR, $filename, 'local');
            $template->update(['path' => $path]);
        }

        return redirect()
            ->route('admin.certificate-templates.index')
            ->with('message', 'Certificate template "' . e($request->input('name')) . '" added. Assign it to a course from the course edit page.');
    }

    /**
     * Admin: delete a certificate template (and remove file only if it was uploaded via form).
     */
    public function destroy(CertificateTemplate $certificateTemplate)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only admins can delete certificate templates.');
        }

        if ($certificateTemplate->path && !$certificateTemplate->isPublicPath() && Storage::disk('local')->exists($certificateTemplate->path)) {
            Storage::disk('local')->delete($certificateTemplate->path);
        }
        $certificateTemplate->courses()->update(['certificate_template_id' => null]);
        $certificateTemplate->delete();

        return redirect()
            ->route('admin.certificate-templates.index')
            ->with('message', 'Certificate template removed.');
    }
}
