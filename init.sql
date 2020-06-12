/*
Script to initialize SQL database.
Copy and paste into phpMyAdmin's console. (Text editors do not lint errors, unlike the console in phpMyAdmin, so review in there for any errors before running.)

3 employees.
1) Jack Wills. ID 10
2) Sean Gates. ID 20. Manages Heart Surgery. Supervises the other two.
3) Clark Ford. ID 30. Is the head of a meeting between everyone except Paul Thomas. Manages Radiology.

3 physicians.
1) Mark Williams. ID 40.
2) Paul Thomas. ID 50.
3) James Anderson. ID 60.

2 departments.
1) Radiology. ID 100.
2) Heart Surgery. ID 200.

Rooms and beds:
Radiology Room 1) Zero beds. No features.
Radiology Room 2) Five beds numbered 1-5. Bed 1 needs cleaning.
Heart Surgery Room 1) 10 beds numbered 1-10. Filtered air.
Heart Surgery Room 2) Zero beds. Room is large so good for storage.

4 patients; 3 current, one historical. Each's visit ID is their ID * 1000.
1) John Smith. ID 1 Been in hospice since July 1st. Has no allergies or medication.
2) Claire Walker. ID 2 Benn in hospice since October 25th. Has a milk allergy but no meds.
3) Adam Williams. ID 3. Benn in hospice since November 5th. Taking heart pills but no allergies.
4) Nicole Tyson. ID 4. WAS in hospice from November 10th to November 15th. Has a pollen allergy and takes antidepressants.

1-3 each correspond to physicians 1-3. 4 corresponds to physician 1.
*/

DROP DATABASE IF EXISTS `471`;
CREATE DATABASE `471`;
USE `471`;

CREATE TABLE Patient (
PHN INT,
name VARCHAR(255),
Birth DATE,
address VARCHAR(255),
height INT,
weight INT,
phone VARCHAR(255)
);

INSERT INTO Patient (PHN, name, Birth, address, height, weight, phone) VALUES
(404403540 , "John Smith", '1990-10-10', "1000 North ST, Calgary, AB", 160, 140, "5558776584" ),
(557261508 , "Claire Walker", '1980-10-10', "2000 West ST, Calgary, AB", 160, 140, "5551744358" ),
(607703232 , "Adam Williams", '2000-10-10', "3000 East ST, Calgary, AB", 160, 140, "5558230183" ),
(585551740 , "Nicole Tyson", '1990-10-10', "4000 South ST, Calgary, AB", 160, 140, "5559636241" )
;

/*Procedure is a reserved word. Changed to MedProcedure*/
CREATE TABLE MedProcedure (
Procedure_ID INT,
Type VARCHAR(255),
ProcedureDateTime DATETIME,
Visit_ID INT,
RequestingPhysician_ID VARCHAR(255)
);

INSERT INTO MedProcedure (Procedure_ID, Type, ProcedureDateTime, Visit_ID, RequestingPhysician_ID) VALUES
(0000001, "Duodenectomy", '2019-01-01 12:30:00', 2000, 956191653),
(0000002, "Total Hysterectomy", '2018-11-12 15:00:00', 4000, 917294775);

CREATE TABLE Visit (
Visit_ID INT,
Notes VARCHAR(255),
Diet VARCHAR(255),
Inpatient BOOLEAN,
Start_Date DATE,
End_Date DATE,
AttendingPhysician_ID INT,
PatientPHN INT
);

INSERT INTO Visit (Visit_ID, Notes, Diet, Inpatient, Start_Date, End_Date, AttendingPhysician_ID, PatientPHN) VALUES
(1000, "Long-term stay. Coma victim.", "Parenteral nutrition", TRUE, '2018-07-01', NULL, 908277529, 404403540),
(2000, "Day surgery.", "NPO", FALSE, '2018-10-25', NULL, 956191653, 557261508),
(3000, "Accident victim. Urgent!", "Parenteral nutrition", TRUE, '2018-11-05', NULL, 917294775, 607703232),
(4000, "Should be a short stay.", "Fluids only", FALSE, '2018-11-10', '2018-11-15', 917294775, 585551740)
;

CREATE TABLE Admitted (
Visit_ID INT,
Dept_ID INT
);

INSERT INTO Admitted (Visit_ID, Dept_ID) VALUES
(1000, 100),
(2000, 200),
(3000, 100),
(4000, 200);

CREATE TABLE Department (
Department_ID INT,
Name VARCHAR(255),
Manager_ID INT
);

INSERT INTO Department (Department_ID, Name, Manager_ID) VALUES
(100, "Radiology", 30),
(200, "Heart Surgery", 20);

CREATE TABLE Room (
Room_Number INT,
FeatureDesc VARCHAR(255),
Dept_ID INT
);

INSERT INTO Room (Room_Number, FeatureDesc, Dept_ID) VALUES
(1, NULL, 100),
(2, "Large size makes ideal choice for storage.", 100),
(1, NULL, 200),
(2, "Filtered air.", 200);

CREATE TABLE Bed (
Bed_Number INT,
Needs_Cleaning BOOLEAN,
Room_Number INT,
Dept_ID INT
);

INSERT INTO Bed (Bed_Number, Needs_Cleaning, Room_Number, Dept_ID) VALUES
/*Room 2 in radiology*/
(1, TRUE, 2, 100),
(2, FALSE, 2, 100),
(3, FALSE, 2, 100),
(4, FALSE, 2, 100),
(5, FALSE, 2, 100),
/*Room 1 in heart surgery*/
(1, FALSE, 1, 200),
(2, TRUE, 1, 200),
(3, FALSE, 1, 200),
(4, FALSE, 1, 200),
(5, FALSE, 1, 200),
(6, FALSE, 1, 200),
(7, TRUE, 1, 200),
(8, FALSE, 1, 200),
(9, FALSE, 1, 200),
(10, FALSE, 1, 200)
;

CREATE TABLE Employee (
Employee_ID INT,
Name VARCHAR(255),
officePhone VARCHAR(255),
address VARCHAR(255),
homePhone VARCHAR(255),
email VARCHAR(255),
Birth DATE,
Status VARCHAR(255),
Pay INT,
SINNum INT,
Supervisor_ID INT,
Dept_ID INT,
Schedule VARCHAR(255),
Password VARCHAR(255),
Admin BOOLEAN
);

INSERT INTO Employee (Employee_ID, Name, officePhone, address, homePhone, email, Birth, Status, Pay, SINNum, Supervisor_ID, Dept_ID, Schedule, Password, Admin) VALUES
(10, "Jack Wills", "5556193357" , "4th Avenue, Calgary, AB", "5559515285" , "jackwills@lifesavers.com", '1995-10-05', "Full-Time", 160000, 899183110 , 20, 200, "Available.", "pass10", FALSE),
(20, "Sean Gates", "5557044668" , "2nd Street, Edmonton, AB", "5555252989" , "seangates@life.com", '1990-12-20', "Casual", 80000, 578794630 , NULL, 200, "Available.", "pass20", FALSE),
(30, "Clark Ford", "5559975219" , "3rd Street, Calgary, AB", "5554033546" , "ford@clark.com", '1970-05-10', "Part-Time", 80000, 926614105 , 20, 100, "Available.", "pass30", TRUE);

CREATE TABLE Physician (
Prac_ID INT,
Name VARCHAR(255),
Status VARCHAR(255),
GPSpecialistStatus VARCHAR(255),
FamilyGeneralStatus VARCHAR(255),
Specialty VARCHAR(255),
Practice_Interest VARCHAR(255),
Address VARCHAR(255),
Password VARCHAR(255)
);

INSERT INTO Physician (Prac_ID, Name, Status, GPSpecialistStatus, FamilyGeneralStatus, Specialty, Practice_Interest, Address, Password) VALUES
(908277529, "Evelyn Estes", "Intern", "GP", "General", NULL, "Sports Medicine", "555 North ST, Calgary, AB", "pass40"),
(956191653, "Hattie Nadine Neal", "Resident", "Specialist", NULL, "Obstetrics/Gynecology", "Medical Aesthetics", "999 South ST, Calgary, AB", "pass50"),
(917294775, "Clyde Noel Preston", "Attending", "Specialist", NULL, "Gastroenterology", "Obesity", "567 East ST, Calgary, AB", "pass60");

CREATE TABLE INVOLVED_IN (
Employee_ID INT,
Procedure_ID INT
);

INSERT INTO INVOLVED_IN (Employee_ID, Procedure_ID) VALUES
(10, 1),
(20, 2),
(30, 2);

CREATE TABLE Attends (
Employee_ID INT,
Meeting_ID INT
);

INSERT INTO Attends (Employee_ID, Meeting_ID) VALUES
(10, 555),
(20, 555),
(30, 555);

CREATE TABLE Meeting (
Meeting_ID INT,
Head_ID INT,
DateTime TIMESTAMP,
DurationMinutes INT,
Location VARCHAR(255)
);

INSERT INTO Meeting (Meeting_ID, Head_ID, DateTime, DurationMinutes, Location) VALUES
(555, 30, '2018-12-31 12:00:00', 180, "Meeting Room in ADMIN 553");

CREATE TABLE Participates (
Prac_ID INT,
Meeting_ID INT
);

INSERT INTO Participates (Prac_ID, Meeting_ID) VALUES
(40, 555),
(60, 555);

CREATE TABLE Occupied (
Visit_ID INT,
Dept_ID INT,
Room_Number INT,
Bed_Number INT
);

INSERT INTO Occupied (Visit_ID, Dept_ID, Room_Number, Bed_Number) VALUES
(1000, 100, 2, 4),
(2000, 200, 1, 10),
(3000, 100, 2, 2)
;

CREATE TABLE Allergies (
Allergy VARCHAR(255),
PatientPHN INT
);

INSERT INTO Allergies (Allergy, PatientPHN) VALUES
("Lactose Intolerance", 557261508),
("Pollen Allergy", 585551740);

CREATE TABLE Medication (
Visit_ID INT,
Medication VARCHAR(255),
Dosage VARCHAR(255),
Frequency VARCHAR(255),
Preparation VARCHAR(255)
);

INSERT INTO Medication (Visit_ID, Medication, Dosage, Frequency, Preparation) VALUES
(3000, "Amoxicillin", "1 g p.o.", "TID", "Caplets"),
(4000, "Epogen", "100 Units/kg", "TIW", "Subcutaneous Injection")
;
