DROP PROCEDURE IF EXISTS sp_cancelar_venta;
DELIMITER $$

CREATE PROCEDURE sp_cancelar_venta(
    IN p_venta_id INT
)
BEGIN
    DECLARE v_estatus VARCHAR(40);

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    -- Bloquea la venta
    SELECT estatus 
    INTO v_estatus 
    FROM venta 
    WHERE id = p_venta_id 
    FOR UPDATE;

    -- Validar si no existe
    IF v_estatus IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La venta no existe';
    END IF;

    -- Validar si ya está cancelada
    IF UPPER(v_estatus) = 'CANCELADA' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La venta ya está cancelada';
    END IF;

    -- Regresar stock
    UPDATE productos p
    JOIN venta_detalle vd ON vd.id_producto = p.id
    SET p.stock = p.stock + vd.cantidad
    WHERE vd.id_venta = p_venta_id;

    -- Cambiar estatus
    UPDATE venta 
    SET estatus = 'CANCELADA' 
    WHERE id = p_venta_id;

    COMMIT;

END$$
DELIMITER ;