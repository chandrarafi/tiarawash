/*
SQLyog Ultimate v13.1.1 (64 bit)
MySQL - 8.0.30 : Database - tiarawash
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`tiarawash` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `tiarawash`;

/*Table structure for table `antrian` */

DROP TABLE IF EXISTS `antrian`;

CREATE TABLE `antrian` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nomor_antrian` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `booking_id` int unsigned DEFAULT NULL,
  `tanggal` date NOT NULL,
  `jam_mulai` time DEFAULT NULL,
  `jam_selesai` time DEFAULT NULL,
  `status` enum('menunggu','diproses','selesai','batal') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'menunggu',
  `karyawan_id` char(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nomor_antrian` (`nomor_antrian`),
  KEY `antrian_booking_id_foreign` (`booking_id`),
  KEY `antrian_karyawan_id_foreign` (`karyawan_id`),
  CONSTRAINT `antrian_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `antrian_karyawan_id_foreign` FOREIGN KEY (`karyawan_id`) REFERENCES `karyawan` (`idkaryawan`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `antrian` */

insert  into `antrian`(`id`,`nomor_antrian`,`booking_id`,`tanggal`,`jam_mulai`,`jam_selesai`,`status`,`karyawan_id`,`created_at`,`updated_at`) values 
(1,'A202508010',NULL,'2025-08-01','06:33:35','06:33:39','selesai','KRY-00001','2025-08-01 04:50:53','2025-08-01 06:33:39');

/*Table structure for table `booking` */

DROP TABLE IF EXISTS `booking`;

CREATE TABLE `booking` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `kode_booking` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `pelanggan_id` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tanggal` date NOT NULL,
  `jam` time NOT NULL,
  `no_plat` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `jenis_kendaraan` enum('motor','mobil','lainnya') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'mobil',
  `merk_kendaraan` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `layanan_id` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('diproses','selesai','batal','menunggu_konfirmasi','dikonfirmasi','dibatalkan') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'menunggu_konfirmasi',
  `payment_expires_at` datetime DEFAULT NULL,
  `catatan` text COLLATE utf8mb4_general_ci,
  `user_id` int unsigned DEFAULT NULL,
  `id_karyawan` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `booking_pelanggan_id_foreign` (`pelanggan_id`),
  KEY `booking_user_id_foreign` (`user_id`),
  KEY `booking_layanan_id_foreign` (`layanan_id`),
  KEY `idx_kode_booking` (`kode_booking`),
  KEY `id_karyawan` (`id_karyawan`),
  CONSTRAINT `booking_ibfk_2` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`idkaryawan`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `booking_layanan_id_foreign` FOREIGN KEY (`layanan_id`) REFERENCES `layanan` (`kode_layanan`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `booking_pelanggan_id_foreign` FOREIGN KEY (`pelanggan_id`) REFERENCES `pelanggan` (`kode_pelanggan`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `booking_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `booking` */

/*Table structure for table `detail_pembelian` */

DROP TABLE IF EXISTS `detail_pembelian`;

CREATE TABLE `detail_pembelian` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `no_faktur` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `perlengkapan_id` int unsigned NOT NULL,
  `jumlah` int NOT NULL DEFAULT '0',
  `harga_satuan` decimal(10,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `detail_pembelian_perlengkapan_id_foreign` (`perlengkapan_id`),
  KEY `no_faktur` (`no_faktur`),
  CONSTRAINT `detail_pembelian_ibfk_1` FOREIGN KEY (`no_faktur`) REFERENCES `pembelian` (`no_faktur`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detail_pembelian_perlengkapan_id_foreign` FOREIGN KEY (`perlengkapan_id`) REFERENCES `perlengkapan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `detail_pembelian` */

insert  into `detail_pembelian`(`id`,`no_faktur`,`perlengkapan_id`,`jumlah`,`harga_satuan`,`subtotal`,`created_at`,`updated_at`) values 
(6,'PBL-20250729-0001',3,12,70000.00,840000.00,'2025-07-29 03:28:26','2025-07-29 03:35:03');

/*Table structure for table `detail_transaksi` */

DROP TABLE IF EXISTS `detail_transaksi`;

CREATE TABLE `detail_transaksi` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `transaksi_id` int unsigned NOT NULL,
  `jenis_item` enum('layanan','produk') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'layanan',
  `item_id` int unsigned NOT NULL,
  `nama_item` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `harga` decimal(10,2) NOT NULL DEFAULT '0.00',
  `jumlah` int NOT NULL DEFAULT '1',
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `detail_transaksi_transaksi_id_foreign` (`transaksi_id`),
  CONSTRAINT `detail_transaksi_transaksi_id_foreign` FOREIGN KEY (`transaksi_id`) REFERENCES `transaksi` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `detail_transaksi` */

/*Table structure for table `karyawan` */

DROP TABLE IF EXISTS `karyawan`;

CREATE TABLE `karyawan` (
  `idkaryawan` char(10) COLLATE utf8mb4_general_ci NOT NULL,
  `namakaryawan` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `nohp` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `alamat` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`idkaryawan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `karyawan` */

insert  into `karyawan`(`idkaryawan`,`namakaryawan`,`nohp`,`alamat`,`created_at`,`updated_at`) values 
('KRY-00001','Jaya Saputra','0831824234881','Padang','2025-07-31 07:39:30','2025-07-31 07:39:30'),
('KRY-00002','Muklis','083182423488','tes','2025-07-31 07:39:41','2025-07-31 07:39:41'),
('KRY-00003','Jamal','6283182423488','tes','2025-07-31 07:39:50','2025-07-31 07:39:50');

/*Table structure for table `layanan` */

DROP TABLE IF EXISTS `layanan`;

CREATE TABLE `layanan` (
  `kode_layanan` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `nama_layanan` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `jenis_kendaraan` enum('motor','mobil','lainnya') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'mobil',
  `harga` decimal(10,2) NOT NULL DEFAULT '0.00',
  `durasi_menit` int NOT NULL DEFAULT '60',
  `deskripsi` text COLLATE utf8mb4_general_ci,
  `foto` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('aktif','nonaktif') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'aktif',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`kode_layanan`),
  UNIQUE KEY `kode_layanan` (`kode_layanan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `layanan` */

insert  into `layanan`(`kode_layanan`,`nama_layanan`,`jenis_kendaraan`,`harga`,`durasi_menit`,`deskripsi`,`foto`,`status`,`created_at`,`updated_at`) values 
('LYN-20250731-316','Cuci Motor Biasa','motor',15000.00,15,'Cuci kendaraan bisa dengan sabun kualitas terbaik','1753952208_48ff47c38d5400875821.jpeg','aktif','2025-07-31 08:56:48','2025-07-31 08:56:48'),
('LYN-20250731-445','Cuci Kolong Mobil','mobil',10000.00,10,'tes','1753954586_51d1cb998aa8d341a92d.jpeg','aktif','2025-07-31 09:36:26','2025-07-31 09:36:26'),
('LYN-20250731-831','Cuci Mobil Biasa','mobil',10000.00,30,'tes','1753954546_5f7621cce3288bf3b75a.jpeg','aktif','2025-07-31 09:35:46','2025-07-31 09:35:46');

/*Table structure for table `migrations` */

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `class` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `group` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `namespace` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `time` int NOT NULL,
  `batch` int unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `migrations` */

insert  into `migrations`(`id`,`version`,`class`,`group`,`namespace`,`time`,`batch`) values 
(1,'2025-02-01-000001','App\\Database\\Migrations\\CreateUsersTable','default','App',1751234684,1),
(2,'2025-06-25-041516','App\\Database\\Migrations\\Karyawan','default','App',1751234684,1),
(3,'2025-06-25-071335','App\\Database\\Migrations\\Pelanggan','default','App',1751234684,1),
(4,'2025-06-26-000001','App\\Database\\Migrations\\Perlengkapan2025_06_26_000001','default','App',1751237881,2),
(5,'2025-07-01-000001','App\\Database\\Migrations\\CreatePembelianTable2025_07_01_000001','default','App',1751242526,3),
(6,'2025-07-01-000002','App\\Database\\Migrations\\DetailPembelian2025_07_01_000002','default','App',1751242526,3),
(7,'2025-07-01-000003','App\\Database\\Migrations\\Layanan2025_07_01_000003','default','App',1751242526,3),
(8,'2025-07-01-000004','App\\Database\\Migrations\\CreateBookingTable','default','App',1751242526,3),
(9,'2025-07-01-000005','App\\Database\\Migrations\\Antrian2025_07_01_000005','default','App',1751242682,4),
(10,'2025-07-01-000006','App\\Database\\Migrations\\CreateTransaksiTable','default','App',1751242682,4),
(11,'2025-07-01-000007','App\\Database\\Migrations\\DetailTransaksi2025_07_01_000007','default','App',1751242682,4),
(12,'2025-07-01-000008','App\\Database\\Migrations\\Kendaraan2025_07_01_000008','default','App',1751242682,4);

/*Table structure for table `otp_codes` */

DROP TABLE IF EXISTS `otp_codes`;

CREATE TABLE `otp_codes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `otp_code` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` datetime NOT NULL,
  `is_used` tinyint(1) NOT NULL DEFAULT '0',
  `purpose` enum('registration','password_reset') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'registration',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_email` (`email`),
  KEY `idx_expires_at` (`expires_at`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `otp_codes` */

insert  into `otp_codes`(`id`,`email`,`otp_code`,`expires_at`,`is_used`,`purpose`,`created_at`,`updated_at`) values 
(7,'mimi@pingaja.site','336839','2025-07-31 03:47:41',1,'registration','2025-07-31 03:42:42','2025-07-31 03:43:46'),
(8,'mimi@pingaja.site','536951','2025-07-31 03:50:35',1,'registration','2025-07-31 03:45:35','2025-07-31 03:45:50'),
(9,'mimi@pingaja.site','904923','2025-07-31 04:20:25',1,'registration','2025-07-31 04:15:25','2025-07-31 04:15:50');

/*Table structure for table `pelanggan` */

DROP TABLE IF EXISTS `pelanggan`;

CREATE TABLE `pelanggan` (
  `kode_pelanggan` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `nama_pelanggan` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `no_hp` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `alamat` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`kode_pelanggan`),
  KEY `pelanggan_user_id_foreign` (`user_id`),
  CONSTRAINT `pelanggan_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `pelanggan` */

insert  into `pelanggan`(`kode_pelanggan`,`user_id`,`nama_pelanggan`,`no_hp`,`alamat`,`created_at`,`updated_at`) values 
('PEL-00002',NULL,'Budi Santoso','083182423488','Padang','2025-06-29 22:06:52','2025-06-29 22:06:52'),
('PEL-00004',NULL,'Test User Final','081234567890','Guest booking - 2025-07-30 08:01:10','2025-07-30 08:01:10','2025-07-30 08:01:10'),
('PEL-00005',9,'Mimi','083182423488','Padang','2025-07-31 04:15:50','2025-07-31 04:15:50');

/*Table structure for table `pembelian` */

DROP TABLE IF EXISTS `pembelian`;

CREATE TABLE `pembelian` (
  `no_faktur` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal` date NOT NULL,
  `supplier` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `total_harga` decimal(12,2) NOT NULL DEFAULT '0.00',
  `keterangan` text COLLATE utf8mb4_general_ci,
  `user_id` int unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`no_faktur`),
  UNIQUE KEY `no_faktur` (`no_faktur`),
  KEY `pembelian_user_id_foreign` (`user_id`),
  CONSTRAINT `pembelian_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `pembelian` */

insert  into `pembelian`(`no_faktur`,`tanggal`,`supplier`,`total_harga`,`keterangan`,`user_id`,`created_at`,`updated_at`) values 
('PBL-20250729-0001','2025-07-29','Tes25',840000.00,'tes',1,'2025-07-29 03:28:26','2025-07-29 03:35:03');

/*Table structure for table `perlengkapan` */

DROP TABLE IF EXISTS `perlengkapan`;

CREATE TABLE `perlengkapan` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `kategori` enum('alat','bahan') COLLATE utf8mb4_general_ci NOT NULL,
  `stok` int NOT NULL DEFAULT '0',
  `harga` decimal(10,2) NOT NULL DEFAULT '0.00',
  `deskripsi` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `perlengkapan` */

insert  into `perlengkapan`(`id`,`nama`,`kategori`,`stok`,`harga`,`deskripsi`,`created_at`,`updated_at`) values 
(3,'Selang','alat',32,70000.00,'tes','2025-06-30 02:34:42','2025-07-29 03:35:03');

/*Table structure for table `transaksi` */

DROP TABLE IF EXISTS `transaksi`;

CREATE TABLE `transaksi` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `no_transaksi` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal` date NOT NULL,
  `booking_id` int unsigned DEFAULT NULL,
  `pelanggan_id` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `layanan_id` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `no_plat` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `jenis_kendaraan` enum('motor','mobil','lainnya') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'mobil',
  `total_harga` decimal(12,2) NOT NULL DEFAULT '0.00',
  `metode_pembayaran` enum('tunai','kartu_kredit','kartu_debit','e-wallet','transfer') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'tunai',
  `status_pembayaran` enum('belum_bayar','dibayar','batal') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'belum_bayar',
  `buktipembayaran` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `catatan` text COLLATE utf8mb4_general_ci,
  `bukti_pembayaran` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `no_transaksi` (`no_transaksi`),
  KEY `transaksi_booking_id_foreign` (`booking_id`),
  KEY `transaksi_pelanggan_id_foreign` (`pelanggan_id`),
  KEY `transaksi_user_id_foreign` (`user_id`),
  KEY `layanan_id` (`layanan_id`),
  CONSTRAINT `transaksi_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`layanan_id`) REFERENCES `layanan` (`kode_layanan`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `transaksi_pelanggan_id_foreign` FOREIGN KEY (`pelanggan_id`) REFERENCES `pelanggan` (`kode_pelanggan`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `transaksi_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `transaksi` */

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `role` varchar(20) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'admin, user, dll',
  `status` varchar(20) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'active' COMMENT 'active, inactive',
  `last_login` datetime DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `users` */

insert  into `users`(`id`,`username`,`email`,`password`,`name`,`role`,`status`,`last_login`,`remember_token`,`created_at`,`updated_at`,`deleted_at`) values 
(1,'admin','admin@admin.com','$2y$10$mwRN8l11TfUKIl6i.k8GUejIsJnSGQXMmNJE2.leRLOV5c1pk/PfO','Administrator','admin','active','2025-08-01 04:44:10',NULL,'2025-07-30 07:15:28','2025-08-01 04:44:10',NULL),
(2,'manager','manager@example.com','$2y$10$igQL5bDEfhztrCPl3rN0Ve8xXLPhixlPtiQEHiYGB2EjLvAK6uld2','Manager User','manager','active',NULL,NULL,'2025-07-30 07:15:28','2025-07-30 07:15:28',NULL),
(3,'user','user@example.com','$2y$10$wN5pZ.Qy4W4KBPYQ4HZZ9O1gmmlkbSCD0Y0Xr50KahDzuWeCyUKI.','Regular User','user','active',NULL,NULL,'2025-07-30 07:15:28','2025-07-30 07:15:28',NULL),
(4,'inactive','inactive@example.com','$2y$10$MyG8r9B5iirn8QKaeP0DIuBVHw9wbuBGYPQ92Ajor2/dBW/7dSHbe','Inactive User','user','inactive',NULL,NULL,'2025-07-30 07:15:28','2025-07-30 07:15:28',NULL),
(9,'mimi','mimi@pingaja.site','$2y$10$PKnmk3oTPuaxK2USymqg.OaC4PtmLqnhXJerAkiQ7n0EMQC86hEvu','Mimi','pelanggan','active','2025-08-01 04:42:45',NULL,'2025-07-31 04:15:50','2025-08-01 04:42:45',NULL);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
