<?php

class CountryService {
    /**
     * Récupère tous les pays avec les noms dans la bonne langue
     */
    public static function getAllCountries($pdo) {
        $lang = LanguageService::getCurrentLanguage();
        $nameColumn = ($lang === 'fr') ? 'name_fr' : 'name_en';
        
        $sql = "SELECT country_code, $nameColumn as country_name FROM country_names ORDER BY country_name ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupère le nom d'un pays
     */
    public static function getCountryName($pdo, $countryCode) {
        $lang = LanguageService::getCurrentLanguage();
        $nameColumn = ($lang === 'fr') ? 'name_fr' : 'name_en';
        
        $sql = "SELECT $nameColumn as country_name FROM country_names WHERE country_code = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$countryCode]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ? $result['country_name'] : $countryCode;
    }
}
