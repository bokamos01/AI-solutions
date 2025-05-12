/*--PMS DATABASE SCRIPT--*/

/*--Drop tables--*/
DROP TABLE IF EXISTS tbl_booking;
DROP TABLE IF EXISTS tbl_feedback;
DROP TABLE IF EXISTS tbl_parkingspace;
DROP TABLE IF EXISTS tbl_site;
DROP TABLE IF EXISTS tbl_faq;
DROP TABLE IF EXISTS tbl_customers;
DROP TABLE IF EXISTS tbl_staff;
DROP TABLE IF EXISTS tbl_roles;
DROP TABLE IF EXISTS tbl_reports;

/*Table reports*/
CREATE TABLE tbl_reports(
    REPORTID INT PRIMARY KEY, 
    REPORTNAME VARCHAR(200), 
    CATEGORY VARCHAR(100), 
    PREVIEW VARCHAR(100)
);

/*Table roles*/
CREATE TABLE tbl_roles(
    ROLEID INT PRIMARY KEY NOT NULL,
    ROLE VARCHAR(50) NOT NULL,
    DESCRIPTION VARCHAR(100) NOT NULL
);

/*Table staff*/
CREATE TABLE tbl_staff(
    STAFFID INT AUTO_INCREMENT NOT NULL PRIMARY KEY,       
    FIRSTNAME VARCHAR(30) NOT NULL,
    SURNAME VARCHAR(30) NOT NULL,
    GENDER CHAR(1) NOT NULL,      
    DOB DATE,         
    EMAILADDRESS VARCHAR(100) NOT NULL UNIQUE,
    PASSWORD VARCHAR(255) NOT NULL,
    ROLEID INT NOT NULL,
    DATEREGISTERED DATE,
    FOREIGN KEY (ROLEID) REFERENCES tbl_roles(ROLEID)
);

/*Table customers*/
CREATE TABLE tbl_customers(
    CUSTOMERID INT AUTO_INCREMENT NOT NULL PRIMARY KEY,       
    FIRSTNAME VARCHAR(30) NOT NULL,
    SURNAME VARCHAR(30) NOT NULL,
    GENDER CHAR(1) NOT NULL,      
    DOB DATE,         
    EMAILADDRESS VARCHAR(100) NOT NULL UNIQUE,
    PASSWORD VARCHAR(255) NOT NULL,
    COMPANY VARCHAR(30) NOT NULL,
    DATEREGISTERED DATE
);

/*Table faq*/
CREATE TABLE tbl_faq(
    FAQID INT PRIMARY KEY, 
    FAQUESTION VARCHAR(50),  
    FAQANSWER VARCHAR(100)
);

/*Table site*/
CREATE TABLE tbl_site(
    SITEID INT PRIMARY KEY NOT NULL,
    SITENAME VARCHAR(30) NOT NULL,
    DESCRIPTION VARCHAR(30) NOT NULL
);

/*Table parkingspace*/
CREATE TABLE tbl_parkingspace(
    PARKINGID INT PRIMARY KEY NOT NULL,
    PARKINGNAME VARCHAR(30) NOT NULL,
    PARKINGIMAGE VARCHAR(500) NOT NULL,
    DESCRIPTION VARCHAR(30) NOT NULL,
    SITEID INT NOT NULL,
    FOREIGN KEY (SITEID) REFERENCES tbl_site(SITEID)
);

/*Table booking*/
CREATE TABLE tbl_booking(
    BOOKINGID INT PRIMARY KEY,
    DATE DATE NOT NULL, 
    STARTTIME DATETIME NOT NULL,
    ENDTIME DATETIME NOT NULL,
    CUSTOMERID INT NOT NULL,
    PARKINGID INT NOT NULL,
    FOREIGN KEY (CUSTOMERID) REFERENCES tbl_customers(CUSTOMERID),
    FOREIGN KEY (PARKINGID) REFERENCES tbl_parkingspace(PARKINGID)
);

/*Table feedback*/
CREATE TABLE tbl_feedback(
    FEEDBACKID INT PRIMARY KEY,
    EMAILADDRESS VARCHAR(100) UNIQUE,  
    FEEDBACK VARCHAR(300),
    CUSTOMERID INT,
    FOREIGN KEY (CUSTOMERID) REFERENCES tbl_customers(CUSTOMERID)
);

/*table roles*/
INSERT INTO tbl_roles VALUES (1,'Administrator','All members who are administrators');
INSERT INTO tbl_roles VALUES (2,'Facilities manager','All members who are facilities managers');
INSERT INTO tbl_roles VALUES (3,'Receptionist','All members who are receptionists');

/*table staff*/
INSERT INTO tbl_staff VALUES (1, 'root','ADMIN','F','1999-01-01','Admin@root.manage',SHA2('Rootpassword', 256),1,'2025-01-01');

-- Insert test sites
INSERT INTO tbl_sites (id, name) VALUES
(1, 'Main Campus'),
(2, 'Business Park'),
(3, 'Shopping Center'),
(4, 'Conference Center');

-- Insert test parking lots
INSERT INTO tbl_parking (id, name, site_id) VALUES
(1, 'PARKING LOT 1', 1),
(2, 'PARKING LOT 2', 1),
(3, 'PARKING LOT 3', 2),
(4, 'PARKING LOT 4', 2),
(5, 'PARKING LOT 5', 3),
(6, 'PARKING LOT 6', 4);
