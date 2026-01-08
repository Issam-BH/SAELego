<?php

class LanguageService {
    private static $currentLanguage = 'en';
    private static $supportedLanguages = ['en', 'fr'];
    
    /**
     * Initialise la langue de l'utilisateur
     */
    public static function initialize() {
        // Vérifier si une langue est passée en paramètre GET (priorité absolue)
        if (isset($_GET['lang']) && in_array($_GET['lang'], self::$supportedLanguages)) {
            self::$currentLanguage = $_GET['lang'];
            $_SESSION['language'] = $_GET['lang'];
        }
        // Vérifier si la langue est en session
        elseif (isset($_SESSION['language']) && in_array($_SESSION['language'], self::$supportedLanguages)) {
            self::$currentLanguage = $_SESSION['language'];
        }
        // Sinon, détecter depuis le navigateur
        else {
            $browserLang = self::detectBrowserLanguage();
            if (in_array($browserLang, self::$supportedLanguages)) {
                self::$currentLanguage = $browserLang;
            }
        }
    }
    
    /**
     * Retourne la langue courante
     */
    public static function getCurrentLanguage(): string {
        return self::$currentLanguage;
    }
    
    /**
     * Définit la langue
     */
    public static function setLanguage(string $lang) {
        if (in_array($lang, self::$supportedLanguages)) {
            self::$currentLanguage = $lang;
            $_SESSION['language'] = $lang;
        }
    }
    
    /**
     * Retourne les langues supportées
     */
    public static function getSupportedLanguages(): array {
        return self::$supportedLanguages;
    }
    
    /**
     * Détecte la langue du navigateur
     */
    private static function detectBrowserLanguage(): string {
        if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            return 'en';
        }
        
        $browserLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        return in_array($browserLang, self::$supportedLanguages) ? $browserLang : 'en';
    }
    
    /**
     * Génère une URL avec le paramètre de langue
     */
    public static function getURLWithLanguage($page = null, $params = []): string {
        if ($page === null) {
            $page = $_GET['page'] ?? 'home';
        }
        
        $url = '?page=' . urlencode($page);
        
        // Ajouter les paramètres supplémentaires
        foreach ($params as $key => $value) {
            $url .= '&' . urlencode($key) . '=' . urlencode($value);
        }
        
        // Ajouter la langue
        $url .= '&lang=' . self::$currentLanguage;
        
        return $url;
    }
    
    /**
     * Retourne le texte traduit selon la langue courante
     * Utilise les colonnes name_en/name_fr pour les couleurs par exemple
     */
    public static function getTranslatedField($enText, $frText = null): string {
        if (self::$currentLanguage === 'fr' && $frText !== null) {
            return $frText;
        }
        return $enText;
    }
}
