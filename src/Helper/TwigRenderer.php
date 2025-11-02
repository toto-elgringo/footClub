<?php

namespace App\Helper;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigRenderer
{
    private static ?Environment $twig = null; // stocke une instance unique de Twig
    // Environement est la classe de Twig, c'est la calsse centrale, c'est le "moteur qui fait fonctionner tout le système de templates

    /**
     * Initialise et retourne l'instance Twig
     *
     * configure Twig avec :
     * - Le chemin vers les templates
     * - Le mode debug pour le développement
     */
    public static function getTwig(): Environment
    {
        if (self::$twig === null) {
            // Définit le chemin vers les templates Twig
            $loader = new FilesystemLoader(__DIR__ . '/../../public/templates');

            // Configure Twig avec les options
            self::$twig = new Environment($loader, [
                'debug' => true,  // Active le mode debug (à mettre false en production)
                'auto_reload' => true  // Recharge automatiquement les templates modifiés
            ]);
        }

        return self::$twig;
    }

    /**
     * Rend un template Twig avec des données
     *
     * Cette méthode charge un template et le rend avec les données fournies.
     */
    public static function render(string $template, array $data = []): string
    {
        $twig = self::getTwig();
        return $twig->render($template, $data);
    }

    /**
     * Affiche directement un template Twig
     *
     * Cette méthode rend et affiche le template directement.
     */
    public static function display(string $template, array $data = []): void
    {
        echo self::render($template, $data);
    }
}
