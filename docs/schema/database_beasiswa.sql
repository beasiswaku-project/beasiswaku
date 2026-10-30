-- =====================================================
-- DATABASE BEASISWA (sesuai ERD)
-- Untuk XAMPP (MySQL / MariaDB) - import lewat phpMyAdmin
-- =====================================================

DROP DATABASE IF EXISTS db_beasiswa;
CREATE DATABASE db_beasiswa CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_beasiswa;

SET FOREIGN_KEY_CHECKS = 0;

-- =====================================================
-- STRUKTUR TABEL (tanpa data)
-- =====================================================

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','mahasiswa') NOT NULL DEFAULT 'mahasiswa',
  is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE kategori (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama_kategori VARCHAR(100) NOT NULL,
  deskripsi TEXT
) ENGINE=InnoDB;

CREATE TABLE penyedia (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama_penyedia VARCHAR(150) NOT NULL,
  jenis ENUM('pemerintah','swasta','kampus','yayasan') NOT NULL,
  kontak VARCHAR(100),
  alamat VARCHAR(255)
) ENGINE=InnoDB;

CREATE TABLE jenjang (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama_jenjang VARCHAR(50) NOT NULL,
  keterangan VARCHAR(255)
) ENGINE=InnoDB;

CREATE TABLE wilayah (
  id INT AUTO_INCREMENT PRIMARY KEY,
  provinsi VARCHAR(100) NOT NULL,
  kabupaten VARCHAR(100) NOT NULL,
  is_3t TINYINT(1) NOT NULL DEFAULT 0,
  skor_prioritas INT NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE konfigurasi_penilaian (
  id INT AUTO_INCREMENT PRIMARY KEY,
  versi INT NOT NULL UNIQUE,
  bobot JSON,
  ambang JSON,
  dibuat_oleh INT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_konf_user FOREIGN KEY (dibuat_oleh) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE mahasiswa (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  wilayah_id INT NOT NULL,
  jenjang_id INT NOT NULL,
  kampus VARCHAR(150),
  penghasilan_ortu DECIMAL(15,2) DEFAULT 0,
  jml_tanggungan INT DEFAULT 0,
  punya_kipk TINYINT(1) NOT NULL DEFAULT 0,
  terdaftar_dtsen TINYINT(1) NOT NULL DEFAULT 0,
  desil_dtsen INT NULL,
  kondisi_khusus VARCHAR(255) NULL,
  CONSTRAINT fk_mhs_user FOREIGN KEY (user_id) REFERENCES users(id),
  CONSTRAINT fk_mhs_wilayah FOREIGN KEY (wilayah_id) REFERENCES wilayah(id),
  CONSTRAINT fk_mhs_jenjang FOREIGN KEY (jenjang_id) REFERENCES jenjang(id)
) ENGINE=InnoDB;

CREATE TABLE prestasi (
  id INT AUTO_INCREMENT PRIMARY KEY,
  mahasiswa_id INT NOT NULL,
  jenis ENUM('akademik','non_akademik','organisasi') NOT NULL,
  nama_kegiatan VARCHAR(150) NOT NULL,
  tingkat VARCHAR(50),
  bukti_path VARCHAR(255),
  CONSTRAINT fk_prestasi_mhs FOREIGN KEY (mahasiswa_id) REFERENCES mahasiswa(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE riwayat_pendidikan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  mahasiswa_id INT NOT NULL,
  jenjang_id INT NOT NULL,
  semester INT NOT NULL,
  ipk DECIMAL(3,2),
  CONSTRAINT fk_riwayat_mhs FOREIGN KEY (mahasiswa_id) REFERENCES mahasiswa(id) ON DELETE CASCADE,
  CONSTRAINT fk_riwayat_jenjang FOREIGN KEY (jenjang_id) REFERENCES jenjang(id)
) ENGINE=InnoDB;

CREATE TABLE program (
  id INT AUTO_INCREMENT PRIMARY KEY,
  penyedia_id INT NOT NULL,
  kategori_id INT NOT NULL,
  jenjang_id INT NOT NULL,
  konfigurasi_id INT NOT NULL,
  judul VARCHAR(200) NOT NULL,
  kuota INT NOT NULL DEFAULT 0,
  tgl_buka DATE,
  deadline DATE,
  status ENUM('draft','dibuka','ditutup','selesai') NOT NULL DEFAULT 'draft',
  CONSTRAINT fk_prog_penyedia FOREIGN KEY (penyedia_id) REFERENCES penyedia(id),
  CONSTRAINT fk_prog_kategori FOREIGN KEY (kategori_id) REFERENCES kategori(id),
  CONSTRAINT fk_prog_jenjang FOREIGN KEY (jenjang_id) REFERENCES jenjang(id),
  CONSTRAINT fk_prog_konf FOREIGN KEY (konfigurasi_id) REFERENCES konfigurasi_penilaian(id)
) ENGINE=InnoDB;

CREATE TABLE persyaratan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  program_id INT NOT NULL,
  jenis ENUM('ipk_minimum','penghasilan_maksimum','semester_minimum','kipk','dtsen','wilayah_3t','dokumen') NOT NULL,
  nilai VARCHAR(100),
  wajib TINYINT(1) NOT NULL DEFAULT 1,
  CONSTRAINT fk_syarat_prog FOREIGN KEY (program_id) REFERENCES program(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE tahap_seleksi (
  id INT AUTO_INCREMENT PRIMARY KEY,
  program_id INT NOT NULL,
  nama_tahap ENUM('administrasi','esai','wawancara','penetapan') NOT NULL,
  urutan INT NOT NULL,
  status ENUM('belum_mulai','berjalan','selesai') NOT NULL DEFAULT 'belum_mulai',
  CONSTRAINT fk_tahap_prog FOREIGN KEY (program_id) REFERENCES program(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE pengumuman (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  program_id INT NULL,
  judul VARCHAR(200) NOT NULL,
  isi TEXT,
  tgl_terbit DATE,
  CONSTRAINT fk_umum_user FOREIGN KEY (user_id) REFERENCES users(id),
  CONSTRAINT fk_umum_prog FOREIGN KEY (program_id) REFERENCES program(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE artikel (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  judul VARCHAR(200) NOT NULL,
  isi TEXT,
  kategori VARCHAR(100),
  tgl_terbit DATE,
  CONSTRAINT fk_artikel_user FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE pendaftaran (
  id INT AUTO_INCREMENT PRIMARY KEY,
  mahasiswa_id INT NOT NULL,
  program_id INT NOT NULL,
  status ENUM('draft','dikirim','verifikasi','seleksi','lolos','tidak_lolos','diterima') NOT NULL DEFAULT 'draft',
  tgl_kirim DATETIME NULL,
  snapshot_ekonomi JSON NULL,
  skor_ekonomi DECIMAL(6,2) NULL,
  nilai_akhir DECIMAL(6,2) NULL,
  peringkat INT NULL,
  UNIQUE KEY uq_daftar (mahasiswa_id, program_id),
  CONSTRAINT fk_daftar_mhs FOREIGN KEY (mahasiswa_id) REFERENCES mahasiswa(id),
  CONSTRAINT fk_daftar_prog FOREIGN KEY (program_id) REFERENCES program(id)
) ENGINE=InnoDB;

CREATE TABLE esai (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pendaftaran_id INT NOT NULL UNIQUE,
  judul VARCHAR(200),
  isi TEXT,
  status ENUM('draft','dikirim','dinilai') NOT NULL DEFAULT 'draft',
  CONSTRAINT fk_esai_daftar FOREIGN KEY (pendaftaran_id) REFERENCES pendaftaran(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE jadwal_wawancara (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pendaftaran_id INT NOT NULL,
  tanggal DATETIME NOT NULL,
  lokasi_atau_link VARCHAR(255),
  status ENUM('dijadwalkan','selesai','dibatalkan') NOT NULL DEFAULT 'dijadwalkan',
  CONSTRAINT fk_jadwal_daftar FOREIGN KEY (pendaftaran_id) REFERENCES pendaftaran(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE dokumen (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pendaftaran_id INT NOT NULL,
  jenis_dokumen VARCHAR(100) NOT NULL,
  opsional TINYINT(1) NOT NULL DEFAULT 0,
  file_path VARCHAR(255),
  status_verifikasi ENUM('menunggu','valid','tidak_valid') NOT NULL DEFAULT 'menunggu',
  catatan_admin TEXT,
  CONSTRAINT fk_dok_daftar FOREIGN KEY (pendaftaran_id) REFERENCES pendaftaran(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE penilaian (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pendaftaran_id INT NOT NULL,
  tahap_id INT NOT NULL,
  komponen ENUM('ekonomi','akademik','prestasi','esai','wawancara') NOT NULL,
  nilai DECIMAL(6,2),
  catatan TEXT,
  deleted_at DATETIME NULL,
  alasan_hapus TEXT NULL,
  CONSTRAINT fk_nilai_daftar FOREIGN KEY (pendaftaran_id) REFERENCES pendaftaran(id) ON DELETE CASCADE,
  CONSTRAINT fk_nilai_tahap FOREIGN KEY (tahap_id) REFERENCES tahap_seleksi(id)
) ENGINE=InnoDB;

CREATE TABLE penerima (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pendaftaran_id INT NOT NULL UNIQUE,
  status ENUM('aktif','nonaktif','lulus') NOT NULL DEFAULT 'aktif',
  tgl_penetapan DATE,
  alasan_penetapan TEXT,
  CONSTRAINT fk_penerima_daftar FOREIGN KEY (pendaftaran_id) REFERENCES pendaftaran(id)
) ENGINE=InnoDB;

CREATE TABLE pencairan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  penerima_id INT NOT NULL,
  periode VARCHAR(50),
  jumlah DECIMAL(15,2),
  status ENUM('menunggu','dicairkan','gagal') NOT NULL DEFAULT 'menunggu',
  tgl_cair DATE NULL,
  CONSTRAINT fk_cair_penerima FOREIGN KEY (penerima_id) REFERENCES penerima(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE laporan_studi (
  id INT AUTO_INCREMENT PRIMARY KEY,
  penerima_id INT NOT NULL,
  semester INT NOT NULL,
  ipk_semester DECIMAL(3,2),
  kendala TEXT,
  bukti_path VARCHAR(255),
  status_review ENUM('menunggu','disetujui','revisi') NOT NULL DEFAULT 'menunggu',
  catatan TEXT,
  CONSTRAINT fk_lapor_penerima FOREIGN KEY (penerima_id) REFERENCES penerima(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE testimoni (
  id INT AUTO_INCREMENT PRIMARY KEY,
  penerima_id INT NOT NULL,
  isi TEXT,
  status_moderasi ENUM('menunggu','disetujui','ditolak') NOT NULL DEFAULT 'menunggu',
  CONSTRAINT fk_testi_penerima FOREIGN KEY (penerima_id) REFERENCES penerima(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE log_aktivitas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  aksi VARCHAR(100) NOT NULL,
  entitas VARCHAR(100),
  entitas_id INT,
  data_lama JSON NULL,
  data_baru JSON NULL,
  alasan TEXT,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_log_user FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS = 1;

