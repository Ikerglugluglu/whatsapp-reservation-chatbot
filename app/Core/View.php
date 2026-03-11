<?php
// Render de vistas con layout.
declare(strict_types=1);

final class View
{
    public static function render(string $template, array $data = [], string $layout = 'layout/main'): void
    {
        // Resuelve paths de vista y layout.
        $viewsDir = __DIR__ . '/../Views';
        $templateFile = $viewsDir . '/' . $template . '.php';
        $layoutFile = $viewsDir . '/' . $layout . '.php';

        if (!is_file($templateFile)) {
            throw new RuntimeException('Vista no encontrada: ' . $template);
        }
        if (!is_file($layoutFile)) {
            throw new RuntimeException('Layout no encontrado: ' . $layout);
        }

        // Expone variables a la vista.
        extract($data, EXTR_SKIP);

        ob_start();
        require $templateFile;
        $content = (string) ob_get_clean();

        // Layout final.
        require $layoutFile;
    }
}



