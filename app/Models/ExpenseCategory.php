<?php
// app/Models/ExpenseCategory.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'color',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'category_id');
    }

    public static function getDefaultCategories()
    {
        return [
            ['name' => 'Utilities', 'color' => '#EF4444', 'description' => 'Electricity, water, gas bills'],
            ['name' => 'Supplies', 'color' => '#F59E0B', 'description' => 'Food ingredients, cleaning supplies'],
            ['name' => 'Equipment', 'color' => '#8B5CF6', 'description' => 'Kitchen equipment, furniture'],
            ['name' => 'Maintenance', 'color' => '#06B6D4', 'description' => 'Repairs, maintenance work'],
            ['name' => 'Marketing', 'color' => '#10B981', 'description' => 'Advertising, promotions'],
            ['name' => 'Other', 'color' => '#6B7280', 'description' => 'Miscellaneous expenses'],
        ];
    }
}