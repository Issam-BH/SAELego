<?php

class ColorService {
    /**
     * Récupère toutes les couleurs avec le nom traduit
     */
    public static function getAllColors($pdo) {
        $sql = "SELECT color_id, hex_code, name_en, name_fr FROM color ORDER BY color_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $colors = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($colors as &$color) {
            $color['color_name'] = LanguageService::getTranslatedField($color['name_en'], $color['name_fr']);
        }
        
        return $colors;
    }
    
    /**
     * Récupère une couleur spécifique
     */
    public static function getColorById($pdo, $colorId) {
        $sql = "SELECT color_id, hex_code, name_en, name_fr FROM color WHERE color_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$colorId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $row['color_name'] = LanguageService::getTranslatedField($row['name_en'], $row['name_fr']);
        }
        
        return $row;
    }
}