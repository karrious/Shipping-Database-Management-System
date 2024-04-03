drop table Carrier cascade constraints;
drop table ShippingCompany cascade constraints;
drop table ShippingRoute cascade constraints;
drop table Assign cascade constraints;
drop table DistributionCenter cascade constraints;
drop table Customer cascade constraints;
drop table PostalCodeInfo cascade constraints;
drop table Orders cascade constraints;
drop table Insurance cascade constraints;
drop table AirParcel cascade constraints;
drop table SeaParcel cascade constraints;
drop table LandParcel cascade constraints;

create table ShippingCompany(
    CompanyName VARCHAR(25) PRIMARY KEY,
    HeadquarterLocation VARCHAR(25)
);

create table Carrier(
    SIN INTEGER PRIMARY KEY,
    CompanyName VARCHAR(25) NOT NULL,
    FOREIGN KEY (CompanyName) REFERENCES ShippingCompany,
    CarrierName VARCHAR(25) UNIQUE NOT NULL,
    CarrierPhoneNumber VARCHAR(20) UNIQUE NOT NULL
);

create table ShippingRoute(
    StartCity VARCHAR(25),
    EndCity VARCHAR(25),
    Distance FLOAT,
    PRIMARY KEY(StartCity, EndCity)
);

create table Assign(
    StartCity VARCHAR(25),
    EndCity VARCHAR(25),
    CompanyName VARCHAR(25),
    PRIMARY KEY(StartCity, EndCity, CompanyName),
    FOREIGN KEY (StartCity, EndCity) REFERENCES ShippingRoute,
    FOREIGN KEY (CompanyName) REFERENCES ShippingCompany
);

create table DistributionCenter(
    CompanyName VARCHAR(25) NOT NULL,
    FOREIGN KEY (CompanyName) REFERENCES ShippingCompany(CompanyName),
    Location VARCHAR(25) PRIMARY KEY,
    Capacity INTEGER,
    AverageDailyShipment DECIMAL,
    OperationHour VARCHAR(30)
);

CREATE TABLE PostalCodeInfo(
   PostalCode VARCHAR(10) PRIMARY KEY,
   Province VARCHAR(25),
   City VARCHAR(25)
);

CREATE TABLE Customer(
  Email VARCHAR(25) PRIMARY KEY,
  CustomerName VARCHAR(25) NOT NULL,
  Address VARCHAR(50),
  PhoneNumber VARCHAR(20) NOT NULL,
  PostalCode VARCHAR(10),
  FOREIGN KEY (PostalCode) REFERENCES PostalCodeInfo,
  UNIQUE (CustomerName, PhoneNumber)
);

CREATE TABLE Insurance(
      PolicyNumber INTEGER PRIMARY KEY,
      Coverage DECIMAL(10, 0),
      ClaimType VARCHAR(50),
      ClaimAmount DECIMAL(10, 0)
);

CREATE TABLE Orders(
    OrderID INTEGER PRIMARY KEY,
    CustomerEmail VARCHAR(25),
    PolicyNumber INTEGER,
    PostalCode VARCHAR(30),
    FOREIGN KEY (CustomerEmail) REFERENCES Customer(Email),
    FOREIGN KEY (PolicyNumber) REFERENCES Insurance,
    FOREIGN KEY (PostalCode) REFERENCES PostalCodeInfo,
    UNIQUE (PolicyNumber)
);

create table AirParcel(
    TrackingNumber VARCHAR(30) PRIMARY KEY,
    Status VARCHAR(25),
    Weight FLOAT,
    Dimension VARCHAR(30),
    EstimatedArrival VARCHAR(25),
    ShippingDate VARCHAR(25),
    Price DECIMAL,
    FlightNumber VARCHAR(25),
    OrderID INTEGER,
    foreign key (OrderID) references Orders,
    UNIQUE (OrderID)
);

create table SeaParcel(
    TrackingNumber VARCHAR(30) PRIMARY KEY,
    Status VARCHAR(25),
    Weight FLOAT,
    Dimension VARCHAR(30),
    EstimatedArrival VARCHAR(25),
    ShippingDate VARCHAR(25),
    Price DECIMAL,
    ShipNumber VARCHAR(25),
    OrderID INTEGER,
    foreign key (OrderID) references Orders,
    UNIQUE (OrderID)
);

create table LandParcel(
    TrackingNumber VARCHAR(30) PRIMARY KEY,
    Status VARCHAR(25),
    Weight FLOAT,
    Dimension VARCHAR(30),
    EstimatedArrival VARCHAR(25),
    ShippingDate VARCHAR(25),
    Price DECIMAL,
    OrderID INTEGER,
    foreign key (OrderID) references Orders,
    UNIQUE (OrderID)
);

insert into ShippingCompany values ('Fedex', '5321 Oak Street');
insert into ShippingCompany values ('Purolator', '741 Main Street');
insert into ShippingCompany values ('UPS', '2223 Montana Road');
insert into ShippingCompany values ('Yunda', '142 Shire Street');
insert into ShippingCompany values ('Yuantong', '9214 Rivendell Road');
insert into ShippingCompany values ('Jingdong', '1742 Mordor Road');

insert into Insurance values (10000, 500, 'Damaged', 300);
insert into Insurance values (10001, 600, 'Lost', 200);
insert into Insurance values (10002, 500, 'Damaged', 400);
insert into Insurance values (10003, 600, 'Lost', 650);
insert into Insurance values (10004, 200, 'Delayed', 100);

insert into PostalCodeInfo values ('A1A 3A3', 'British Columbia', 'Vancouver');
insert into PostalCodeInfo values ('3A3 A1A', 'Ontario', 'Toronto');
insert into PostalCodeInfo values ('A1A 2A2', 'British Columbia', 'Vancouver');
insert into PostalCodeInfo values ('2A2 2B2', 'Ontario', 'Toronto');
insert into PostalCodeInfo values ('A1A 5A5', 'British Columbia', 'Vancouver');
insert into PostalCodeInfo values ('000 000', 'default province', 'default city');

insert into Customer values ('amy@email.com', 'Amy', '123 UBC Street', '123-456-7890', 'A1A 3A3');
insert into Customer values ('alice@email.com', 'Alice', '321 UBC Street', '123-456-7789', '3A3 A1A');
insert into Customer values ('charlie@email.com', 'Charlie', '456 UBC Street', '123-456-7889', 'A1A 2A2');
insert into Customer values ('kat@email.com', 'Kat', '654 UBC Street', '123-456-7899', '2A2 2B2');
insert into Customer values ('nancy@email.com', 'Nancy', '789 UBC Street', '123-456-7800', 'A1A 5A5');

insert into Carrier values ('111111111', 'Fedex', 'Walter White', '123456789');
insert into Carrier values ('222222222', 'Purolator', 'Saul Goodman', '400123456');
insert into Carrier values ('333333333', 'UPS', 'Bruce Wayne', '723981231');
insert into Carrier values ('444444444', 'Yunda', 'Peter Parker', '923768441');
insert into Carrier values ('555555555', 'Yuantong', 'William Turner', '201961601');
insert into Carrier values ('666666666', 'Jingdong', 'Jack Sparrow', '142378564');

insert into ShippingRoute values ('Richmond', 'Vancouver', 20.7);
insert into ShippingRoute values ('Calgary', 'Vermont', 830.64);
insert into ShippingRoute values ('London', 'Los Angelos', 1640.3);
insert into ShippingRoute values ('Toronto', 'Cancun', 790.2);
insert into ShippingRoute values ('Seattle', 'Ottawa', 430.2);
insert into ShippingRoute values ('Washington', 'Quebec', 940.6);

insert into Assign values ('Richmond', 'Vancouver', 'Fedex');
insert into Assign values ('Calgary', 'Vermont', 'Purolator');
insert into Assign values ('London', 'Los Angelos', 'UPS');
insert into Assign values ('Toronto', 'Cancun', 'Yunda');
insert into Assign values ('Seattle', 'Ottawa', 'Yuantong');
insert into Assign values ('Calgary', 'Vermont', 'Fedex');
insert into Assign values ('London', 'Los Angelos', 'Fedex');
insert into Assign values ('Toronto', 'Cancun', 'Fedex');
insert into Assign values ('Seattle', 'Ottawa', 'Fedex');
insert into Assign values ('Washington', 'Quebec', 'Fedex');

insert into DistributionCenter values ('Fedex', '712 Granville Street', 4000, 420.3, '7am - 9pm');
insert into DistributionCenter values ('Purolator', '1236 West Broadway', 8000, 940.6, '6am - 9pm');
insert into DistributionCenter values ('UPS', '253 Quebec Street', 6500, 650.4, '6am - 8pm');
insert into DistributionCenter values ('Yunda', '9485 Manitoba Street', 9000, 1103.2, '6am - 10pm');
insert into DistributionCenter values ('Yuantong','103 Bute Street', 1000, 230.3, '8am - 6pm');
insert into DistributionCenter values ('Jingdong', '482 Moria Street', 2000, 340.9, '8am - 7pm');

insert into Orders values (000001, 'amy@email.com', 10000, 'A1A 3A3');
insert into Orders values (000002, 'alice@email.com', 10001, '3A3 A1A');
insert into Orders values (000003, 'amy@email.com', 10002, 'A1A 3A3');
insert into Orders values (000004, 'kat@email.com', 10003, '2A2 2B2');
insert into Orders values (000005, 'nancy@email.com', 10004, 'A1A 5A5');


insert into AirParcel values (12345000, 'Arrived', 0.5, '25x35x105', '2023-07-11', '2023-07-01', 50, 'CX1000', 000001);
insert into AirParcel values (12345001, 'Dispatched', 0.6, '30x40x50', '2023-07-12', '2023-07-02', 100, 'CX1001', 000002);
insert into AirParcel values (12345002, 'Dispatched', 0.7, '45x50x60', '2023-07-13', '2023-07-03', 70, 'CX1002', 000003);
insert into AirParcel values (12345003, 'Delayed', 0.8, '20x22x30', '2023-07-14', '2023-07-04', 80, 'CX1003', 000004);
insert into AirParcel values (12345004, 'Lost', 0.9, '55x60x65', '2023-07-15', '2023-07-05', 90, 'CX1004', 000005);

insert into SeaParcel values (87654000, 'Arrived', 2.0, '50x60x70', '2023-07-11', '2023-07-01', 200, 'SE4500', 000001);
insert into SeaParcel values (87654001, 'In Transit', 2.1, '40x50x60', '2023-07-12', '2023-07-02', 210, 'SE4501', 000002);
insert into SeaParcel values (87654002, 'Dispatched', 2.2, '60x70x80', '2023-07-13', '2023-07-03', 220, 'SE4502', 000003);
insert into SeaParcel values (87654003, 'Delayed', 2.3, '35x45x55', '2023-07-14', '2023-07-04', 230, 'SE4503', 000004);
insert into SeaParcel values (87654004, 'Lost', 2.4, '65x75x85', '2023-07-15', '2023-07-05', 240, 'SE4504', 000005);

insert into LandParcel values (11223000, 'Arrived', 1.0, '30x40x50', '2023-07-11', '2023-07-01', 30, 000001);
insert into LandParcel values (11223001, 'In Transit', 1.1, '20x30x40', '2023-07-12', '2023-07-02', 40, 000002);
insert into LandParcel values (11223002, 'Dispatched', 1.2, '35x45x55', '2023-07-13', '2023-07-03', 50, 000003);
insert into LandParcel values (11223003, 'Delayed', 1.3, '25x35x45', '2023-07-14', '2023-07-04', 60, 000004);
insert into LandParcel values (11223004, 'Lost', 1.4, '40x50x60', '2023-07-15', '2023-07-05', 70, 000005);
