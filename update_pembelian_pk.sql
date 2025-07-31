-- Update pembelian table to use no_faktur as primary key
-- Note: This is for reference only, actual migration should be done through CodeIgniter migrations

-- First, ensure no_faktur is unique and not null
ALTER TABLE `pembelian` 
MODIFY COLUMN `no_faktur` VARCHAR(20) NOT NULL;

-- Add unique constraint if not exists
ALTER TABLE `pembelian` 
ADD UNIQUE KEY `unique_no_faktur` (`no_faktur`);

-- Update detail_pembelian table to reference no_faktur instead of id
-- Note: Make sure to backup data first
-- ALTER TABLE `detail_pembelian` 
-- MODIFY COLUMN `no_faktur` VARCHAR(20) NOT NULL;

-- Add foreign key constraint
-- ALTER TABLE `detail_pembelian` 
-- ADD CONSTRAINT `fk_detail_pembelian_no_faktur` 
-- FOREIGN KEY (`no_faktur`) REFERENCES `pembelian`(`no_faktur`) 
-- ON DELETE CASCADE ON UPDATE CASCADE; 