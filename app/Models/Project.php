<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'client',
        'category',
        'tech_stack',
        'summary',
        'results',
        'cover_image',
        'is_published',
    ];

    protected $casts = [
        'tech_stack' => 'array',
        'is_published' => 'boolean',
    ];

    public function images()
    {
        return $this->hasMany(ProjectImage::class)->orderBy('display_order');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'project_tag');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)->orderByDesc('created_at');
    }
}
