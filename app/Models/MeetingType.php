<?php
// app/Models/MeetingType.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetingType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'required_fields',
        'is_active',
    ];

    protected $casts = [
        'required_fields' => 'array',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function meetings()
    {
        return $this->hasMany(Meeting::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}