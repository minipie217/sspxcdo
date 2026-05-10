<?php

namespace App\Services;

use App\Models\EmailTemplate;

class EmailTemplateService
{
    public function render(string $key, array $data): array
    {
        $template = EmailTemplate::getByKey($key);

        if (! $template) {
            return [
                'subject' => $key,
                'body'    => implode('<br>', array_map(
                    fn($k, $v) => "{$k}: {$v}",
                    array_keys($data),
                    array_values($data)
                )),
            ];
        }

        return $template->render($data);
    }
}