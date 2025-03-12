CREATE DATABASE IF NOT EXISTS givewell221;

CREATE TABLE USERS (
    UserID INT PRIMARY KEY AUTO_INCREMENT,
    Fname VARCHAR(50) NOT NULL,
    Mname VARCHAR(50),
    Lname VARCHAR(50) NOT NULL,
    Wallet_Address VARCHAR(100) UNIQUE,
    Creation_Date DATE NOT NULL,
    Role VARCHAR(50) NOT NULL DEFAULT 'Donor',
    Password VARCHAR(255) NOT NULL,  
    Status BOOLEAN DEFAULT TRUE,    
    INDEX idx_wallet (Wallet_Address)
);

CREATE TABLE EMAIL (
    EmailID INT PRIMARY KEY AUTO_INCREMENT,
    UserID INT NOT NULL,
    Email VARCHAR(100) NOT NULL UNIQUE,
    Primary_Email BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (UserID) REFERENCES USERS(UserID) ON DELETE CASCADE,
    INDEX idx_email (Email)
);

CREATE TABLE PREFERENCES (
    PreferenceID INT PRIMARY KEY AUTO_INCREMENT,
    UserID INT NOT NULL,
    Preferences TEXT,
    Last_Updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (UserID) REFERENCES USERS(UserID) ON DELETE CASCADE
);

CREATE TABLE ORGANISATION (
    OrgID INT PRIMARY KEY AUTO_INCREMENT, 
    Wallet_ID VARCHAR(100) UNIQUE NOT NULL,
    NAME VARCHAR(100) NOT NULL,
    Creation_Date DATE NOT NULL,
    TYPE VARCHAR(50) NOT NULL,
    Status BOOLEAN DEFAULT TRUE,
    INDEX idx_wallet (Wallet_ID)
);

CREATE TABLE CAMPAIGN (
    CID INT PRIMARY KEY AUTO_INCREMENT,
    CRID_USER INT,
    CRID_ORG INT,
    Current_Amount DECIMAL(10, 2) DEFAULT 0.00,
    Name VARCHAR(100) NOT NULL,
    Start_Date DATE NOT NULL,
    End_Date DATE NOT NULL,
    Description TEXT NOT NULL,
    Goal DECIMAL(10, 2) NOT NULL,
    Approval_Status BOOLEAN DEFAULT FALSE,
    Creation_Date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Status VARCHAR(20) DEFAULT 'ACTIVE',
    FOREIGN KEY (CRID_USER) REFERENCES USERS(UserID) ON DELETE SET NULL,
    FOREIGN KEY (CRID_ORG) REFERENCES ORGANISATION(OrgID) ON DELETE SET NULL,
    CHECK (End_Date >= Start_Date),
    INDEX idx_status (Status),
    INDEX idx_approval (Approval_Status)
);

CREATE TABLE RESOURCES (
    ResourceID INT PRIMARY KEY AUTO_INCREMENT,
    CampID INT NOT NULL,
    TYPE VARCHAR(50) NOT NULL,
    URL VARCHAR(255) NOT NULL,
    Upload_Date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (CampID) REFERENCES CAMPAIGN(CID) ON DELETE CASCADE
);

CREATE TABLE APPROVE (
    ApproveID INT PRIMARY KEY AUTO_INCREMENT,
    AdminID INT NOT NULL,
    CampaignID INT NOT NULL,
    State VARCHAR(50) NOT NULL,
    Approval_Date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Comments TEXT,
    FOREIGN KEY (AdminID) REFERENCES USERS(UserID) ON DELETE RESTRICT,
    FOREIGN KEY (CampaignID) REFERENCES CAMPAIGN(CID) ON DELETE CASCADE
);

CREATE TABLE DONATION (
    DonationID INT PRIMARY KEY AUTO_INCREMENT,
    UserID INT,
    CampaignID INT NOT NULL,
    Wallet_Add VARCHAR(100) NOT NULL,
    Trans_Hash VARCHAR(100) NOT NULL,
    Timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Amount DECIMAL(10, 2) NOT NULL,
    Status VARCHAR(20) DEFAULT 'COMPLETED',
    FOREIGN KEY (UserID) REFERENCES USERS(UserID) ON DELETE SET NULL,
    FOREIGN KEY (CampaignID) REFERENCES CAMPAIGN(CID) ON DELETE CASCADE,
    INDEX idx_campaign (CampaignID),
    INDEX idx_user (UserID)
);

CREATE TABLE MESSAGE (
    MessageID INT PRIMARY KEY AUTO_INCREMENT,
    User_ID INT,
    Campaign_ID INT NOT NULL,
    Message TEXT NOT NULL,
    Created_At TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Updated_At TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    Status VARCHAR(20) DEFAULT 'ACTIVE',
    FOREIGN KEY (User_ID) REFERENCES USERS(UserID) ON DELETE SET NULL,
    FOREIGN KEY (Campaign_ID) REFERENCES CAMPAIGN(CID) ON DELETE CASCADE,
    INDEX idx_campaign (Campaign_ID)
);