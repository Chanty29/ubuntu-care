CREATE TABLE roles (
  roleID INT AUTO_INCREMENT PRIMARY KEY,
  role_name VARCHAR(30) NOT NULL UNIQUE
);

INSERT INTO roles (role_name) VALUES ('beneficiary'), ('ngo_admin'), ('donor'), ('volunteer');

CREATE TABLE users (
  userID INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  roleID INT NOT NULL,
  location VARCHAR(150),
  FOREIGN KEY (roleID) REFERENCES roles(roleID)
);

CREATE TABLE ngo_profiles (
  ngoID INT AUTO_INCREMENT PRIMARY KEY,
  userID INT NOT NULL,
  org_name VARCHAR(150) NOT NULL,
  category VARCHAR(50),
  latitude DECIMAL(9,6),
  longitude DECIMAL(9,6),
  FOREIGN KEY (userID) REFERENCES users(userID)
);

CREATE TABLE beneficiary_requests (
  requestID INT AUTO_INCREMENT PRIMARY KEY,
  userID INT NOT NULL,
  description TEXT NOT NULL,
  category VARCHAR(50),
  status VARCHAR(30) DEFAULT 'open',
  date_created DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (userID) REFERENCES users(userID)
);

CREATE TABLE match_history (
  matchID INT AUTO_INCREMENT PRIMARY KEY,
  requestID INT NOT NULL,
  ngoID INT NOT NULL,
  match_score DECIMAL(5,2),
  date_matched DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (requestID) REFERENCES beneficiary_requests(requestID),
  FOREIGN KEY (ngoID) REFERENCES ngo_profiles(ngoID)
);

CREATE TABLE donor_pledges (
  pledgeID INT AUTO_INCREMENT PRIMARY KEY,
  userID INT NOT NULL,
  ngoID INT NOT NULL,
  amount DECIMAL(10,2),
  date_pledged DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (userID) REFERENCES users(userID),
  FOREIGN KEY (ngoID) REFERENCES ngo_profiles(ngoID)
);

CREATE TABLE volunteer_signups (
  signupID INT AUTO_INCREMENT PRIMARY KEY,
  userID INT NOT NULL,
  ngoID INT NOT NULL,
  task VARCHAR(150),
  status VARCHAR(30) DEFAULT 'signed_up',
  FOREIGN KEY (userID) REFERENCES users(userID),
  FOREIGN KEY (ngoID) REFERENCES ngo_profiles(ngoID)
);