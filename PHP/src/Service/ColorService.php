<?php

class ColorService {
    /**
     * Récupère toutes les couleurs avec le nom dans la bonne langue
     */
    public static function getAllColors($pdo) {
        $lang = LanguageService::getCurrentLanguage();
        $nameColumn = ($lang === 'fr') ? 'name_fr' : 'name_en';
        
        $sql = "SELECT color_id, hex_code, $nameColumn as color_name FROM color ORDER BY color_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupère une couleur spécifique
     */
    public static function getColorById($pdo, $colorId) {
        $lang = LanguageService::getCurrentLanguage();
        $nameColumn = ($lang === 'fr') ? 'name_fr' : 'name_en';
        
        $sql = "SELECT color_id, hex_code, name_en, name_fr, $nameColumn as color_name FROM color WHERE color_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$colorId]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
