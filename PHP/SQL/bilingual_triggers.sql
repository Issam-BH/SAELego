-- Adapter les triggers et procédures pour les codes d'erreur

-- Supprimer les anciens triggers
DROP TRIGGER IF EXISTS `trg_mb_after_insert`;
DROP TRIGGER IF EXISTS `trg_stock_after_insert`;

-- Recréer le trigger avec les codes d'erreur
DELIMITER $$

CREATE TRIGGER `trg_mb_after_insert` 
AFTER INSERT ON `mosaic_brick` 
FOR EACH ROW 
BEGIN
    DECLARE stock_level INT;
    
    -- Récupérer le stock de la brique
    SELECT available FROM brick_inventory 
    WHERE brick_id = NEW.brick_id 
    INTO stock_level;
    
    -- Vérifier le stock disponible
    IF stock_level IS NULL OR stock_level < NEW.quantity THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERR_STOCK_INSUFFICIENT';
    END IF;
    
    -- Mettre à jour le stock
    UPDATE brick_inventory 
    SET available = available - NEW.quantity 
    WHERE brick_id = NEW.brick_id;
END$$

DELIMITER ;

-- Exemple de trigger supplémentaire pour les commandes
DELIMITER $$

CREATE TRIGGER `trg_order_before_insert`
BEFORE INSERT ON `orders`
FOR EACH ROW
BEGIN
    IF NEW.total_amount <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERR_INVALID_AMOUNT';
    END IF;
    
    IF NEW.user_id IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERR_MISSING_USER';
    END IF;
END$$

DELIMITER ;
