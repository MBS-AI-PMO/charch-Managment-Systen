<?php

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_messages', function (Blueprint $t) {
            $t->boolean('is_public')->default(false)->after('replied_at');
            $t->index('is_public');
        });

        // Existing answered questions appear in public Q&A history.
        DB::table('contact_messages')
            ->whereNotNull('replied_at')
            ->update(['is_public' => true]);

        $main = Menu::where('slug', 'main')->first();
        if ($main) {
            $contact = MenuItem::where('menu_id', $main->id)
                ->where('link_value', 'site.contact')
                ->whereNull('parent_id')
                ->first();

            $sortOrder = $contact ? (int) $contact->sort_order : (int) MenuItem::where('menu_id', $main->id)->max('sort_order') + 1;

            if ($contact) {
                MenuItem::where('menu_id', $main->id)
                    ->whereNull('parent_id')
                    ->where('sort_order', '>=', $sortOrder)
                    ->increment('sort_order');
            }

            MenuItem::updateOrCreate(
                [
                    'menu_id' => $main->id,
                    'link_type' => 'route',
                    'link_value' => 'site.qa.index',
                    'parent_id' => null,
                ],
                [
                    'label' => 'Q&A',
                    'sort_order' => $sortOrder,
                    'target' => '_self',
                ]
            );
        }
    }

    public function down(): void
    {
        $main = Menu::where('slug', 'main')->first();
        if ($main) {
            MenuItem::where('menu_id', $main->id)
                ->where('link_type', 'route')
                ->where('link_value', 'site.qa.index')
                ->delete();
        }

        Schema::table('contact_messages', function (Blueprint $t) {
            $t->dropIndex(['is_public']);
            $t->dropColumn('is_public');
        });
    }
};
