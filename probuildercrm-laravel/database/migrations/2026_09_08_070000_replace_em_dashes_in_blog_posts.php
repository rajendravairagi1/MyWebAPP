<?php

use App\Models\BlogPost;
use Illuminate\Database\Migrations\Migration;

/**
 * The launch posts were originally written with em dashes ("—"), a
 * telltale sign of AI-written copy. This scrubs already-seeded rows to
 * plain hyphens — the source seed migration was fixed the same way, but
 * Laravel never re-runs a migration that already executed, so existing
 * data needs its own pass.
 */
return new class extends Migration
{
    public function up(): void
    {
        BlogPost::all()->each(function (BlogPost $post) {
            $post->title = $this->clean($post->title);
            $post->excerpt = $this->clean($post->excerpt);
            $post->content = $this->cleanContentValue($post->content);
            $post->save();
        });
    }

    public function down(): void
    {
        // Not reversible — the original em-dash text isn't preserved.
    }

    private function clean(?string $text): ?string
    {
        return $text === null ? null : str_replace('—', '-', $text);
    }

    private function cleanContent(?array $content): ?array
    {
        if (! $content) {
            return $content;
        }

        foreach ($content as &$block) {
            if (isset($block['text'])) {
                $block['text'] = $this->clean($block['text']);
            }
            if (isset($block['items']) && is_array($block['items'])) {
                $block['items'] = array_map(fn ($item) => $this->clean($item), $block['items']);
            }
        }

        return $content;
    }

    /**
     * content is a plain HTML string by the time this runs on a database
     * seeded by the now-fixed 050000 migration - the array-block form
     * above only still applies to a database whose content predates that
     * fix and was never converted.
     */
    private function cleanContentValue(string|array|null $content): string|array|null
    {
        return is_array($content) ? $this->cleanContent($content) : $this->clean($content);
    }
};
