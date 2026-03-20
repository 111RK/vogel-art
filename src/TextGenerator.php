<?php

class TextGenerator
{
    private static array $openings = [
        "Une œuvre captivante qui",
        "Cette toile unique",
        "Une création originale qui",
        "Cette pièce remarquable",
        "Une œuvre d'art saisissante qui",
        "Cette composition singulière",
        "Une toile envoûtante qui",
        "Cette création artistique",
    ];

    private static array $emotions = [
        "invite à la contemplation",
        "éveille les sens et l'imagination",
        "transporte dans un univers poétique",
        "révèle une sensibilité profonde",
        "capture l'essence de l'instant",
        "offre une expérience visuelle unique",
        "dialogue entre lumière et matière",
        "sublime les nuances et les contrastes",
    ];

    private static array $techniques_desc = [
        'huile' => [
            "Les couches d'huile superposées créent une profondeur remarquable.",
            "La richesse de la peinture à l'huile confère à cette œuvre une luminosité exceptionnelle.",
            "Le travail à l'huile révèle des textures subtiles et une palette vibrante.",
        ],
        'acrylique' => [
            "L'acrylique apporte une vivacité de couleurs saisissante.",
            "La technique acrylique offre un rendu moderne et expressif.",
            "Les pigments acryliques créent des contrastes audacieux et lumineux.",
        ],
        'aquarelle' => [
            "La transparence de l'aquarelle crée une atmosphère délicate et aérienne.",
            "Le jeu de l'eau et des pigments révèle une légèreté poétique.",
            "L'aquarelle apporte une douceur et une fluidité envoûtantes.",
        ],
        'pastel' => [
            "Le pastel confère une douceur et une chaleur incomparables.",
            "La technique au pastel crée des fondus et des nuances d'une grande finesse.",
        ],
        'mixte' => [
            "La technique mixte enrichit l'œuvre de textures variées et surprenantes.",
            "Le mélange de techniques crée une composition riche et originale.",
        ],
        'default' => [
            "La technique employée révèle un savoir-faire maîtrisé.",
            "Le geste de l'artiste se ressent dans chaque détail de cette œuvre.",
            "La maîtrise technique se traduit par une harmonie visuelle remarquable.",
        ],
    ];

    private static array $closings = [
        "Pièce unique, signée par l'artiste.",
        "Une œuvre unique qui trouvera sa place dans votre intérieur.",
        "Pièce originale, idéale pour sublimer votre espace de vie.",
        "Cette toile unique apportera caractère et élégance à votre décoration.",
        "Une pièce d'exception pour les amateurs d'art authentique.",
    ];

    private static array $improve_synonyms = [
        'faire' => 'réaliser',
        'beau' => 'remarquable',
        'joli' => 'élégant',
        'très' => 'particulièrement',
        'grand' => 'imposant',
        'petit' => 'délicat',
        'bien' => 'admirablement',
        'bon' => 'excellent',
        'mettre' => 'disposer',
        'voir' => 'contempler',
        'chose' => 'élément',
        'beaucoup' => 'considérablement',
    ];

    public static function generateDescription(string $title, string $technique = '', ?int $width = null, ?int $height = null): string
    {
        $parts = [];

        $opening = self::pick(self::$openings);
        $emotion = self::pick(self::$emotions);
        $parts[] = "$opening $emotion.";

        $techKey = self::detectTechnique($technique);
        $techTexts = self::$techniques_desc[$techKey] ?? self::$techniques_desc['default'];
        $parts[] = self::pick($techTexts);

        if ($width && $height) {
            if ($width > 80 || $height > 80) {
                $parts[] = "Avec ses dimensions généreuses de {$width} x {$height} cm, cette toile s'impose avec présence dans tout espace.";
            } elseif ($width < 30 && $height < 30) {
                $parts[] = "Son format intime de {$width} x {$height} cm en fait un bijou décoratif plein de charme.";
            } else {
                $parts[] = "Format {$width} x {$height} cm, idéal pour sublimer un mur ou un espace dédié.";
            }
        }

        $parts[] = self::pick(self::$closings);

        return implode(' ', $parts);
    }

    public static function improveText(string $text): string
    {
        $improved = $text;

        $improved = preg_replace('/\s+/', ' ', trim($improved));

        $sentences = preg_split('/(?<=[.!?])\s+/', $improved);
        $sentences = array_map(function ($s) {
            return mb_strtoupper(mb_substr($s, 0, 1)) . mb_substr($s, 1);
        }, $sentences);
        $improved = implode(' ', $sentences);

        foreach (self::$improve_synonyms as $word => $replacement) {
            $improved = preg_replace('/\b' . preg_quote($word, '/') . '\b/iu', $replacement, $improved);
        }

        $improved = preg_replace('/\s+([.,;:!?])/', '$1', $improved);
        $improved = preg_replace('/([.,;:!?])(?=[^\s])/', '$1 ', $improved);

        if (!preg_match('/[.!?]$/', trim($improved))) {
            $improved = trim($improved) . '.';
        }

        return $improved;
    }

    private static function detectTechnique(string $technique): string
    {
        $technique = mb_strtolower($technique);
        if (str_contains($technique, 'huile')) return 'huile';
        if (str_contains($technique, 'acrylique')) return 'acrylique';
        if (str_contains($technique, 'aquarelle')) return 'aquarelle';
        if (str_contains($technique, 'pastel')) return 'pastel';
        if (str_contains($technique, 'mixte')) return 'mixte';
        return 'default';
    }

    private static function pick(array $items): string
    {
        return $items[array_rand($items)];
    }
}
