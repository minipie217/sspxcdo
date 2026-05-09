<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class HomepageLayoutService
{
    private const PATH = 'home.json';

    public function read(): array
    {
        if (! Storage::disk('local')->exists(self::PATH)) {
            $this->write($this->defaultLayout());
        }

        $decoded = json_decode(Storage::disk('local')->get(self::PATH), true);

        if (! is_array($decoded)) {
            return $this->defaultLayout();
        }

        return $this->normalize($decoded);
    }

    public function write(array $layout): void
    {
        Storage::disk('local')->put(
            self::PATH,
            json_encode($this->normalize($layout), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
    }

    public function defaultLayout(): array
    {
        return [
            'sections' => collect($this->availableSections())
                ->map(fn (string $label, string $key) => [
                    'key' => $key,
                    'label' => $label,
                    'visible' => true,
                ])
                ->values()
                ->all(),
        ];
    }

    public function availableSections(): array
    {
        return [
            'hero' => 'Hero Banner',
            'features' => 'Features',
            'workflow' => 'Workflow',
            'section_system' => 'Section System',
            'testimonials' => 'Testimonials',
            'final_cta' => 'Final CTA',
        ];
    }

    public function normalize(array $layout): array
    {
        $available = $this->availableSections();
        $seen = [];
        $sections = [];

        foreach (($layout['sections'] ?? []) as $section) {
            $key = is_array($section) ? ($section['key'] ?? null) : null;

            if (! is_string($key) || ! array_key_exists($key, $available) || in_array($key, $seen, true)) {
                continue;
            }

            $seen[] = $key;
            $sections[] = [
                'key' => $key,
                'label' => $available[$key],
                'visible' => filter_var($section['visible'] ?? true, FILTER_VALIDATE_BOOL),
            ];
        }

        foreach ($available as $key => $label) {
            if (! in_array($key, $seen, true)) {
                $sections[] = [
                    'key' => $key,
                    'label' => $label,
                    'visible' => true,
                ];
            }
        }

        return ['sections' => $sections];
    }
}
