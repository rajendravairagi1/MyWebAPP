<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The block editor (paragraph/heading/list JSON) is replaced by a real
 * rich-text (Quill) editor, so `content` becomes a plain HTML string
 * instead of a JSON block array - this widens the column to hold that
 * HTML and converts every existing post's stored blocks into the
 * equivalent HTML, so already-published posts keep rendering exactly
 * as before under the new renderer (see blog/show.blade.php).
 *
 * Guarded to only touch rows that still look like the old JSON block
 * format (start with [ or {) - safe to re-run if this migration only
 * partially completed, and a no-op on a database where it already
 * finished.
 */
return new class extends Migration
{
    public function up(): void
    {
        // SQLite has no real column typing to widen - a "json" column
        // there already accepts arbitrary text, so only MySQL needs the
        // column itself changed.
        if (Schema::getConnection()->getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE blog_posts MODIFY content LONGTEXT NOT NULL');
        }

        foreach (DB::table('blog_posts')->select('id', 'content')->get() as $post) {
            $raw = trim((string) $post->content);

            if ($raw === '' || ! in_array($raw[0], ['[', '{'], true)) {
                continue;
            }

            $blocks = json_decode($raw, true);

            if (! is_array($blocks)) {
                continue;
            }

            $html = collect($blocks)->map(function ($block) {
                $type = $block['type'] ?? 'paragraph';

                if ($type === 'heading') {
                    return '<h2>'.e($block['text'] ?? '').'</h2>';
                }

                if ($type === 'list') {
                    $items = collect($block['items'] ?? [])
                        ->map(fn ($item) => '<li>'.e($item).'</li>')
                        ->implode('');

                    return "<ul>{$items}</ul>";
                }

                return '<p>'.e($block['text'] ?? '').'</p>';
            })->implode('');

            DB::table('blog_posts')->where('id', $post->id)->update(['content' => $html]);
        }
    }

    public function down(): void
    {
        // Converting HTML back into the old block format would be lossy
        // (bold/italic/links/images have no block-JSON equivalent) - not
        // reversible.
    }
};
