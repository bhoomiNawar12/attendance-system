-- =============================================================================
-- EduAttend — Web-Based Student Attendance Management System
-- MySQL schema for XAMPP / phpMyAdmin (InnoDB)
-- =============================================================================

CREATE DATABASE IF NOT EXISTS eduattend
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE eduattend;

-- -----------------------------------------------------------------------------
-- 1. users
-- -----------------------------------------------------------------------------
CREATE TABLE users (
  UserID      INT UNSIGNED NOT NULL AUTO_INCREMENT,
  Name        VARCHAR(100) NOT NULL,
  Email       VARCHAR(150) NOT NULL,
  Password    VARCHAR(255) NOT NULL COMMENT 'Store bcrypt/argon2 hash, not plain text',
  Role        ENUM('Teacher', 'Student') NOT NULL,
  created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (UserID),
  UNIQUE KEY uq_users_email (Email),
  KEY idx_users_role (Role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 2. classes
-- -----------------------------------------------------------------------------
CREATE TABLE classes (
  ClassID     INT UNSIGNED NOT NULL AUTO_INCREMENT,
  ClassName   VARCHAR(150) NOT NULL,
  ClassCode   VARCHAR(20)  NOT NULL,
  TeacherID   INT UNSIGNED NOT NULL,
  CreateDate  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (ClassID),
  UNIQUE KEY uq_classes_classcode (ClassCode),
  KEY idx_classes_teacher (TeacherID),
  CONSTRAINT fk_classes_teacher
    FOREIGN KEY (TeacherID) REFERENCES users (UserID)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 3. enrollments
-- -----------------------------------------------------------------------------
CREATE TABLE enrollments (
  EnrollmentID INT UNSIGNED NOT NULL AUTO_INCREMENT,
  StudentID    INT UNSIGNED NOT NULL,
  ClassID      INT UNSIGNED NOT NULL,
  created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (EnrollmentID),
  UNIQUE KEY uq_enrollment_student_class (StudentID, ClassID),
  KEY idx_enrollments_class (ClassID),
  CONSTRAINT fk_enrollments_student
    FOREIGN KEY (StudentID) REFERENCES users (UserID)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_enrollments_class
    FOREIGN KEY (ClassID) REFERENCES classes (ClassID)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 4. attendance
-- -----------------------------------------------------------------------------
CREATE TABLE attendance (
  AttendanceID   INT UNSIGNED NOT NULL AUTO_INCREMENT,
  StudentID      INT UNSIGNED NOT NULL,
  ClassID        INT UNSIGNED NOT NULL,
  AttendanceDate DATE NOT NULL,
  Status         ENUM('Present', 'Absent', 'Leave') NOT NULL DEFAULT 'Present',
  created_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (AttendanceID),
  UNIQUE KEY uq_attendance_student_class_date (StudentID, ClassID, AttendanceDate),
  KEY idx_attendance_class_date (ClassID, AttendanceDate),
  KEY idx_attendance_date (AttendanceDate),
  CONSTRAINT fk_attendance_student
    FOREIGN KEY (StudentID) REFERENCES users (UserID)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_attendance_class
    FOREIGN KEY (ClassID) REFERENCES classes (ClassID)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 5. reports
-- -----------------------------------------------------------------------------
CREATE TABLE reports (
  ReportID             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  StudentID            INT UNSIGNED NOT NULL,
  ClassID              INT UNSIGNED NOT NULL,
  AttendancePercentage DECIMAL(5, 2) NOT NULL,
  Month                VARCHAR(20) NOT NULL,
  Status               ENUM('Good', 'Low') NOT NULL,
  created_at           TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at           TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (ReportID),
  UNIQUE KEY uq_reports_student_class_month (StudentID, ClassID, Month),
  KEY idx_reports_class_month (ClassID, Month),
  KEY idx_reports_status (Status),
  CONSTRAINT fk_reports_student
    FOREIGN KEY (StudentID) REFERENCES users (UserID)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_reports_class
    FOREIGN KEY (ClassID) REFERENCES classes (ClassID)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT chk_reports_percentage
    CHECK (AttendancePercentage >= 0.00 AND AttendancePercentage <= 100.00)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
