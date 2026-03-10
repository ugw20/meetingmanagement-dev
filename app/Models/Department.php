<?php
// app/Models/Department.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function meetings()
    {
        return $this->hasMany(Meeting::class);
    }

    public function actionItems()
    {
        return $this->hasMany(ActionItem::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Methods
    public function getCompletedActionItemsCountAttribute()
    {
        return $this->actionItems()->where('status', 'completed')->count();
    }

    public function getPendingActionItemsCountAttribute()
    {
        return $this->actionItems()->whereIn('status', ['pending', 'in_progress'])->count();
    }
}