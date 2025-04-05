-- Create Users Table
CREATE TABLE users (
  user_id INT AUTO_INCREMENT,
  name VARCHAR(50) NOT NULL,
  email VARCHAR(100) NOT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin', 'teacher', 'student') NOT NULL DEFAULT 'student',
  profile_picture VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id),
  UNIQUE KEY (email)
);

-- Create Courses Table
CREATE TABLE courses (
  course_id INT AUTO_INCREMENT,
  title VARCHAR(100) NOT NULL,
  description TEXT,
  thumbnail VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (course_id)
);

-- Create Playlists Table
CREATE TABLE playlists (
  playlist_id INT AUTO_INCREMENT,
  title VARCHAR(100) NOT NULL,
  description TEXT,
  thumbnail VARCHAR(255),
  course_id INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (playlist_id),
  FOREIGN KEY (course_id) REFERENCES courses(course_id)
);

-- Create Videos Table
CREATE TABLE videos (
  video_id INT AUTO_INCREMENT,
  title VARCHAR(100) NOT NULL,
  description TEXT,
  thumbnail VARCHAR(255),
  video_url VARCHAR(255) NOT NULL,
  playlist_id INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (video_id),
  FOREIGN KEY (playlist_id) REFERENCES playlists(playlist_id)
);

-- Create Comments Table
CREATE TABLE comments (
  comment_id INT AUTO_INCREMENT,
  content TEXT NOT NULL,
  video_id INT,
  user_id INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (comment_id),
  FOREIGN KEY (video_id) REFERENCES videos(video_id),
  FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Create Likes Table
CREATE TABLE likes (
  like_id INT AUTO_INCREMENT,
  video_id INT,
  user_id INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (like_id),
  FOREIGN KEY (video_id) REFERENCES videos(video_id),
  FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Create Saved Playlists Table
CREATE TABLE saved_playlists (
  saved_id INT AUTO_INCREMENT,
  playlist_id INT,
  user_id INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (saved_id),
  FOREIGN KEY (playlist_id) REFERENCES playlists(playlist_id),
  FOREIGN KEY (user_id) REFERENCES users(user_id)
);