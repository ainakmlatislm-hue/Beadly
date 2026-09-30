-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 30, 2026 at 07:55 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_toko`
--

-- --------------------------------------------------------

--
-- Table structure for table `detail`
--

CREATE TABLE `detail` (
  `id_detail` int NOT NULL,
  `id_transaksi` int NOT NULL,
  `id_produk` int NOT NULL,
  `jumlah` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail`
--

INSERT INTO `detail` (`id_detail`, `id_transaksi`, `id_produk`, `jumlah`) VALUES
(1, 1, 3, 2),
(2, 1, 12, 1),
(3, 2, 10, 3),
(4, 3, 17, 1),
(5, 3, 2, 1),
(6, 4, 3, 1),
(7, 5, 12, 1),
(8, 5, 17, 1);

--
-- Triggers `detail`
--
DELIMITER $$
CREATE TRIGGER `stok` AFTER INSERT ON `detail` FOR EACH ROW UPDATE `produk` 
SET `stok` = `stok` - NEW.`jumlah` 
WHERE `id` = NEW.`id_produk`
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id` int NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `harga` int NOT NULL,
  `stok` int NOT NULL,
  `poto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id_kategori` int DEFAULT NULL,
  `deskripsi` text COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id`, `nama`, `harga`, `stok`, `poto`, `id_kategori`, `deskripsi`) VALUES
(2, 'Army bracelets', 15000, 24, 'Screenshot 2026-09-13 083346.png', 1, ''),
(3, 'Pinkish bracelets', 14000, 27, 'Screenshot 2026-09-13 083020.png', 1, ''),
(10, 'Baby blue bracelets', 14000, 30, 'Screenshot 2026-09-13 082816 copy.png', 1, ''),
(11, 'Ring beads | Kristal soft pink', 7000, 25, 'cincin1.jpeg', 3, ''),
(12, 'Ring beads | Beads soft pink', 7000, 8, 'CINCIN2.jpeg', 3, ''),
(13, 'Ring beads | Soft pink stone', 7000, 19, 'cincin3.jpeg', 3, ''),
(16, 'Kalung Manik-manik l Crystal Necklace', 35000, 35, '1790516025_Screenshot 2026-09-27 203021.png', 2, ''),
(17, 'Kalung Manik-manik l Pearl Necklace', 24599, 38, '1790516003_Screenshot 2026-09-27 202946.png', 2, ''),
(18, 'Kalung Manik-manik l White Crystal ', 34500, 50, '1790516123_Screenshot 2026-09-27 203427.png', 2, ''),
(19, 'Gelang manik | Swift tosca', 33800, 50, '1790516423_Screenshot 2026-09-27 203928.png', 1, ''),
(20, 'Gelang manik | Flowers Tosca', 34600, 35, '1790516532_Screenshot 2026-09-27 204115.png', 1, ''),
(21, 'Gelang manik | Flowers Pink', 32000, 45, '1790516619_Screenshot 2026-09-27 204255.png', 1, '');

-- --------------------------------------------------------

--
-- Table structure for table `tb_kategori`
--

CREATE TABLE `tb_kategori` (
  `id_kategori` int NOT NULL,
  `nama_kategori` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_kategori`
--

INSERT INTO `tb_kategori` (`id_kategori`, `nama_kategori`) VALUES
(1, 'Gelang'),
(2, 'Kalung'),
(3, 'Cincin'),
(4, 'Gantungan kunci');

-- --------------------------------------------------------

--
-- Table structure for table `tb_user`
--

CREATE TABLE `tb_user` (
  `id` int NOT NULL,
  `nama` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `username` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `hp` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `alamat` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `role` enum('admin','pelanggan') COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_user`
--

INSERT INTO `tb_user` (`id`, `nama`, `email`, `username`, `password`, `hp`, `alamat`, `role`) VALUES
(3, NULL, NULL, 'admin1', '1234', NULL, 'None', 'admin'),
(4, NULL, NULL, 'admin2', '12345', NULL, 'None', 'admin'),
(6, 'ainakmltislm', 'ainakmltislm@gmail.com', 'aina', '12345678', '088271324526', 'jl.perjuangan 2', 'pelanggan'),
(8, 'Deswita Nur\'adha', 'deswitah@gmail.com', 'tata', '321', '089375726643', 'Jl.sampiran No.18', 'pelanggan'),
(9, 'Hafizah Nurul Hidayah', 'hafizahaja@gmail.com', 'hafizah', '12345678', '08897864002', 'Desa Kecomberan', 'pelanggan'),
(10, 'Cindy eka putri yulianto', 'cindyajah@gmail.com', 'cindy', '171717', '08251831825', 'Klayan', 'pelanggan'),
(11, 'Ajeng halimun dwi kusumo', 'ajengajah@gmail.com', 'ajeng', '12345678', '083425735713', 'Jl.Perjuangan 2', 'pelanggan'),
(12, 'fatimah amatus syifa', 'ftmhas9@gmail.com', 'timeh', '060606', '0881022471626', 'jl.perjuangan 2', 'pelanggan'),
(13, 'FAUJAN FATEH AHMAD', 'faujan@gmail.com', 'ojan', '123', '088987654321', 'jl.perjuangan 2', 'pelanggan');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` int NOT NULL,
  `id_pelanggan` int NOT NULL,
  `tanggal` date NOT NULL,
  `total_harga` int NOT NULL,
  `metode_pembayaran` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `metode_pengiriman` varchar(50) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `id_pelanggan`, `tanggal`, `total_harga`, `metode_pembayaran`, `metode_pengiriman`) VALUES
(1, 6, '2026-09-26', 35000, 'E-Wallet', 'Kurir'),
(2, 6, '2026-09-26', 42000, 'E-Wallet', 'Kurir'),
(3, 11, '2026-09-28', 39599, 'Transfer Bank', 'Kurir'),
(4, 12, '2026-09-30', 14000, 'Transfer Bank', 'Ambil di Toko'),
(5, 13, '2026-09-30', 31599, 'E-Wallet', 'Kurir');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `detail`
--
ALTER TABLE `detail`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_transaksi` (`id_transaksi`),
  ADD KEY `id_produk` (`id_produk`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- Indexes for table `tb_kategori`
--
ALTER TABLE `tb_kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `tb_user`
--
ALTER TABLE `tb_user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `id_pelanggan` (`id_pelanggan`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `detail`
--
ALTER TABLE `detail`
  MODIFY `id_detail` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `tb_kategori`
--
ALTER TABLE `tb_kategori`
  MODIFY `id_kategori` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tb_user`
--
ALTER TABLE `tb_user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail`
--
ALTER TABLE `detail`
  ADD CONSTRAINT `fk_detail_produk` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_detail_transaksi` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id_transaksi`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `fk_transaksi_pelanggan` FOREIGN KEY (`id_pelanggan`) REFERENCES `tb_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
