<?php

class Translator {
    private static $translations = [
        'en' => [
            'home' => 'Home',
            'login' => 'Login',
            'register' => 'Register',
            'logout' => 'Logout',
            'upload' => 'Upload',
            'profile' => 'Profile',
            'settings' => 'Settings',
            'orders' => 'Orders',
            'history' => 'History',
            'forgot_password' => 'Forgot Password',
            'reset_password' => 'Reset Password',
            'email' => 'Email',
            'password' => 'Password',
            'confirm_password' => 'Confirm Password',
            'address' => 'Address',
            'city' => 'City',
            'zip' => 'Postal Code',
            'country' => 'Country',
            'save' => 'Save',
            'cancel' => 'Cancel',
            'delete' => 'Delete',
            'submit' => 'Submit',
            'language' => 'Language',
            'english' => 'English',
            'french' => 'Français',
            'invalid_email' => 'Invalid email format.',
            'reset_link_sent' => 'If an account exists with this email, a reset link has been sent.',
            'error_sending_email' => 'Error sending email. Please try again later.',
            'password_reset_success' => 'Your password has been reset successfully.',
            'error_updating_password' => 'Error updating password.',
            'password_mismatch' => 'Passwords do not match.',
            'password_too_short' => 'Password must be at least 12 characters.',
            'invalid_token' => 'Invalid or expired token.',
            'go_back_login' => 'Go back to login',
            'forgot_your_password' => 'Forgot your password',
            'enter_email_reset' => 'Enter your email address to receive a reset link.',
            'send_link' => 'Send the link',
            'back_to_login' => 'Back to login',
            'request_new_link' => 'Request a new link',
            'new_password' => 'New Password',
            'hello' => 'Hello',
            'my_orders' => 'My Orders',
            'my_profile' => 'My Profile',
            'username' => 'Username',
            'forgot_password_q' => 'Forgot password?',
            'create_account' => 'Create an account',
            'credentials' => 'Credentials',
            'personal_info' => 'Personal Information',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'phone_number' => 'Phone Number',
            'birth_year' => 'Birth Year',
            'full_address' => 'Full Address',
            'security_compliance' => 'Security compliance: 12 characters min, including Uppercase, lowercase, number and symbol.',
            'already_have_account' => 'Already have an account?',
        ],
        'fr' => [
            'home' => 'Accueil',
            'login' => 'Connexion',
            'register' => 'Inscription',
            'logout' => 'Déconnexion',
            'upload' => 'Télécharger',
            'profile' => 'Profil',
            'settings' => 'Paramètres',
            'orders' => 'Commandes',
            'history' => 'Historique',
            'forgot_password' => 'Mot de passe oublié',
            'reset_password' => 'Réinitialiser le mot de passe',
            'email' => 'E-mail',
            'password' => 'Mot de passe',
            'confirm_password' => 'Confirmer le mot de passe',
            'address' => 'Adresse',
            'city' => 'Ville',
            'zip' => 'Code postal',
            'country' => 'Pays',
            'save' => 'Enregistrer',
            'cancel' => 'Annuler',
            'delete' => 'Supprimer',
            'submit' => 'Soumettre',
            'language' => 'Langue',
            'english' => 'English',
            'french' => 'Français',
            'invalid_email' => 'Format d\'email invalide.',
            'reset_link_sent' => 'Si un compte existe avec cet email, un lien de réinitialisation a été envoyé.',
            'error_sending_email' => 'Erreur lors de l\'envoi de l\'email. Veuillez réessayer plus tard.',
            'password_reset_success' => 'Votre mot de passe a été réinitialisé avec succès.',
            'error_updating_password' => 'Erreur lors de la mise à jour du mot de passe.',
            'password_mismatch' => 'Les mots de passe ne correspondent pas.',
            'password_too_short' => 'Le mot de passe doit faire au moins 12 caractères.',
            'invalid_token' => 'Jeton invalide ou expiré.',
            'go_back_login' => 'Retour à la connexion',
            'forgot_your_password' => 'Mot de passe oublié',
            'enter_email_reset' => 'Entrez votre adresse email pour recevoir un lien de réinitialisation.',
            'send_link' => 'Envoyer le lien',
            'back_to_login' => 'Retour à la connexion',
            'request_new_link' => 'Demander un nouveau lien',
            'new_password' => 'Nouveau mot de passe',
            'hello' => 'Bonjour',
            'my_orders' => 'Mes commandes',
            'my_profile' => 'Mon profil',
            'username' => 'Nom d\'utilisateur',
            'forgot_password_q' => 'Mot de passe oublié?',
            'create_account' => 'Créer un compte',
            'credentials' => 'Identifiants',
            'personal_info' => 'Informations personnelles',
            'first_name' => 'Prénom',
            'last_name' => 'Nom de famille',
            'phone_number' => 'Numéro de téléphone',
            'birth_year' => 'Année de naissance',
            'full_address' => 'Adresse complète',
            'security_compliance' => 'Conformité de sécurité: 12 caractères min, incluant Majuscule, minuscule, chiffre et symbole.',
            'already_have_account' => 'Vous avez déjà un compte?',
        ]
    ];

    /**
     * Obtient une traduction
     */
    public static function get(string $key, string $defaultValue = ''): string {
        $lang = LanguageService::getCurrentLanguage();
        
        return self::$translations[$lang][$key] ?? 
               self::$translations['en'][$key] ?? 
               $defaultValue;
    }

    /**
     * Ajoute une traduction
     */
    public static function add(string $key, string $en, string $fr = '') {
        self::$translations['en'][$key] = $en;
        if (!empty($fr)) {
            self::$translations['fr'][$key] = $fr;
        }
    }
}
