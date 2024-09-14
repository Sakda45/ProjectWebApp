CREATE TRIGGER `update_booking_status_after_payment` AFTER UPDATE ON `payment`
 FOR EACH ROW BEGIN
    -- ตรวจสอบว่าค่า payment_status ถูกอัปเดตเป็น 'pending'
    IF NEW.payment_status = 'pending' THEN
        -- อัปเดตสถานะในตาราง bookings เป็น 'pending_payment' สำหรับ booking_id ที่เกี่ยวข้อง
        UPDATE bookings
        SET status = 'pending_payment'
        WHERE id = NEW.booking_id;
    END IF;
    
    -- ตรวจสอบว่าค่า payment_status ถูกอัปเดตเป็น 'paid'
    IF NEW.payment_status = 'paid' THEN
        -- อัปเดตสถานะในตาราง bookings เป็น 'confirmed' สำหรับ booking_id ที่เกี่ยวข้อง
        UPDATE bookings
        SET status = 'confirmed'
        WHERE id = NEW.booking_id;
    END IF;
        -- ตรวจสอบว่าค่า payment_status ถูกอัปเดตเป็น 'paid'
    IF NEW.payment_status = 'failed' THEN
        -- อัปเดตสถานะในตาราง bookings เป็น 'confirmed' สำหรับ booking_id ที่เกี่ยวข้อง
        UPDATE bookings
        SET status = 'cancelled'
        WHERE id = NEW.booking_id;
    END IF;
END

CREATE TRIGGER `update_booth_status_after_booking` AFTER INSERT ON `bookings`
 FOR EACH ROW BEGIN
    -- อัปเดตสถานะบูธเป็น pending เมื่อมีการจองใหม่
    UPDATE booth
    SET status = 'pending'
    WHERE id = NEW.booth_id;
END

CREATE TRIGGER `update_booth_status_after_booking_update` AFTER UPDATE ON `bookings`
 FOR EACH ROW BEGIN
    -- ตรวจสอบว่าการจองมีสถานะเป็น 'cancelled' หรือ 'expired'
    IF NEW.status = 'cancelled' OR NEW.status = 'expired' THEN
        -- อัปเดตสถานะบูธเป็น 'available'
        UPDATE booth
        SET status = 'available'
        WHERE id = NEW.booth_id;
    END IF;
END

CREATE TRIGGER `update_booth_status_after_confirmed_booking` AFTER UPDATE ON `bookings`
 FOR EACH ROW BEGIN
    -- ตรวจสอบว่าการจองมีสถานะเป็น 'confirmed'
    IF NEW.status = 'confirmed' THEN
        -- อัปเดตสถานะบูธเป็น 'booked'
        UPDATE booth
        SET status = 'booked'
        WHERE id = NEW.booth_id;
    END IF;
END

CREATE TRIGGER `update_booth_status_after_delete_booking` AFTER DELETE ON `bookings`
 FOR EACH ROW BEGIN
    -- อัปเดตสถานะบูธเป็น 'available' หลังจากลบการจอง
    UPDATE booth
    SET status = 'available'
    WHERE id = OLD.booth_id;
END

CREATE TRIGGER `update_num_booth_after_delete` AFTER DELETE ON `booth`
 FOR EACH ROW BEGIN
    UPDATE zones
    SET num_booths = (SELECT COUNT(*) FROM booth WHERE zone_id = OLD.zone_id)
    WHERE id = OLD.zone_id;
END

CREATE TRIGGER `update_num_booth_after_insert` AFTER INSERT ON `booth`
 FOR EACH ROW BEGIN
    UPDATE zones
    SET num_booths = (SELECT COUNT(*) FROM booth WHERE zone_id = NEW.zone_id)
    WHERE id = NEW.zone_id;
END
