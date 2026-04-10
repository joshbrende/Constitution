<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CertificateTemplate extends Model
{
    protected $table = 'certificate_templates';

    protected $fillable = ['name', 'path'];

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'certificate_template_id');
    }

    /** Whether the path is under public (e.g. FTP-uploaded to public/asset/). */
    public function isPublicPath(): bool
    {
        return $this->path && (str_starts_with($this->path, 'asset/') || str_starts_with($this->path, 'asset\\'));
    }

    /** Full filesystem path to the template PDF. */
    public function getFullPathAttribute(): string
    {
        if ($this->isPublicPath()) {
            return public_path(str_replace('\\', '/', $this->path));
        }
        return storage_path('app/' . $this->path);
    }

    public function fileExists(): bool
    {
        return $this->path && is_file($this->getFullPathAttribute()) && is_readable($this->getFullPathAttribute());
    }
}
