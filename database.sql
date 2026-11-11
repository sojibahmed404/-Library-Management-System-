-- ============================================================
-- Library Management System Database Script
-- Database: library_management
-- ============================================================

CREATE DATABASE IF NOT EXISTS library_management
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE library_management;

-- Drop tables in reverse dependency order if re-importing
DROP TABLE IF EXISTS Issue;
DROP TABLE IF EXISTS Book;
DROP TABLE IF EXISTS Member;
DROP TABLE IF EXISTS Category;

-- ------------------------------------------------------------
-- Table 1: Category
-- ------------------------------------------------------------
CREATE TABLE Category (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table 2: Book
-- ------------------------------------------------------------
CREATE TABLE Book (
    book_id INT PRIMARY KEY AUTO_INCREMENT,
    book_name VARCHAR(150) NOT NULL,
    author VARCHAR(100) NOT NULL,
    category_id INT NOT NULL,
    isbn VARCHAR(30) UNIQUE,
    quantity INT NOT NULL DEFAULT 1,
    CONSTRAINT fk_book_category
        FOREIGN KEY (category_id) REFERENCES Category(category_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table 3: Member
-- ------------------------------------------------------------
CREATE TABLE Member (
    member_id INT PRIMARY KEY AUTO_INCREMENT,
    member_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(20),
    department VARCHAR(100)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table 4: Issue
-- ------------------------------------------------------------
CREATE TABLE Issue (
    issue_id INT PRIMARY KEY AUTO_INCREMENT,
    book_id INT NOT NULL,
    member_id INT NOT NULL,
    issue_date DATE NOT NULL,
    return_date DATE NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'Issued',
    CONSTRAINT fk_issue_book
        FOREIGN KEY (book_id) REFERENCES Book(book_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT fk_issue_member
        FOREIGN KEY (member_id) REFERENCES Member(member_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ============================================================
-- Sample Data Insertion
-- ============================================================

-- 1. Insert 5 Book Categories
INSERT INTO Category (category_name) VALUES
('Computer Science'),
('Mathematics'),
('Physics'),
('Literature'),
('History');

-- 2. Insert 10 Books
INSERT INTO Book (book_name, author, category_id, isbn, quantity) VALUES
('Database System Concepts', 'Abraham Silberschatz', 1, '978-0078022159', 5),
('Introduction to Algorithms', 'Thomas H. Cormen', 1, '978-0262046305', 4),
('Clean Code', 'Robert C. Martin', 1, '978-0132350884', 6),
('Operating System Concepts', 'Greg Gagne', 1, '978-1119800361', 3),
('Discrete Mathematics and Its Applications', 'Kenneth H. Rosen', 2, '978-1259676512', 4),
('Linear Algebra Done Right', 'Sheldon Axler', 2, '978-3319110790', 3),
('Fundamentals of Physics', 'David Halliday', 3, '978-1118230718', 5),
('To Kill a Mockingbird', 'Harper Lee', 4, '978-0061120084', 7),
('1984', 'George Orwell', 4, '978-0451524935', 6),
('Sapiens: A Brief History of Humankind', 'Yuval Noah Harari', 5, '978-0062316097', 4);

-- 3. Insert 5 Library Members
INSERT INTO Member (member_name, email, phone, department) VALUES
('Arafat Rahman', 'arafat.rahman@university.edu', '01711-223344', 'Computer Science'),
('Nusrat Jahan', 'nusrat.jahan@university.edu', '01812-334455', 'Software Engineering'),
('Tanvir Ahmed', 'tanvir.ahmed@university.edu', '01913-445566', 'Mathematics'),
('Farhana Akter', 'farhana.akter@university.edu', '01614-556677', 'Physics'),
('Mehedi Hasan', 'mehedi.hasan@university.edu', '01515-667788', 'English Literature');

-- 4. Insert 8 Book Issue Records
INSERT INTO Issue (book_id, member_id, issue_date, return_date, status) VALUES
(1, 1, '2026-10-01', '2026-10-10', 'Returned'),
(2, 2, '2026-10-05', '2026-10-15', 'Returned'),
(3, 1, '2026-10-12', NULL, 'Issued'),
(5, 3, '2026-10-14', '2026-10-22', 'Returned'),
(7, 4, '2026-10-18', NULL, 'Issued'),
(8, 5, '2026-10-20', '2026-10-28', 'Returned'),
(4, 2, '2026-11-01', NULL, 'Issued'),
(10, 3, '2026-11-05', NULL, 'Issued');

-- ============================================================
-- Report Query: Combined 4-Table INNER JOIN
-- ============================================================
-- SELECT
--     i.issue_id,
--     m.member_name,
--     b.book_name,
--     b.author,
--     c.category_name,
--     i.issue_date,
--     i.return_date,
--     i.status
-- FROM Issue i
-- INNER JOIN Member m ON i.member_id = m.member_id
-- INNER JOIN Book b ON i.book_id = b.book_id
-- INNER JOIN Category c ON b.category_id = c.category_id
-- ORDER BY i.issue_id DESC;
