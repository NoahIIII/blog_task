
# Blog Application

My Application is a blog platform built with Laravel, featuring post management, comment functionality, and user authentication. The application uses JWT for authentication, leveraging cookies to maintain user sessions. It supports various operations for managing posts & comments and likes includes email notifications for users when new comments are added to their posts. Mailtrap is used for email testing and delivery.


# Features

#### Post Management : Create, update, and delete blog posts. Retrieve a paginated list of all posts and perform bulk deletions.

#### Comment Management: Add and delete comments on posts. Retrieve a single post along with all comments.

#### User Authentication: Secure user authentication using JWT with cookies for session management.

#### Email Notifications: Notify users via email when a new comment is added to their post. Mailtrap is used for email testing and delivery.

#### Likes Management: Toggle likes on posts and comments, retrieve like counts with posts & comments, and get lists of liked content.


## API Reference

#### For detailed API documentation, including endpoint descriptions, request/response examples, and usage instructions, please refer to the Postman documentation provided in the Postman collection.

#### Postman collection link : https://www.postman.com/smartlines/workspace/mohamed-ammar/request/33964537-ca4071b9-eb37-45bc-a446-4c91866b1145?action=share&source=copy-link&creator=30959672


![Screenshot 2024-09-18 215506](https://github.com/user-attachments/assets/7b739fd1-1167-4796-a8bf-5cfa459dbab3)


## Installation
#### 1 . Clone the Repository  & run compoer install 
#### 2. Configure Environment:
Copy .env.example to .env 

Run php artisan jwt:secret

Create an account on Mailtrap. 

Obtain your Mailtrap credentials from the Mailtrap dashboard.

Update your .env file with the Mailtrap credentials:


MAIL_MAILER=smtp

MAIL_HOST=smtp.mailtrap.io

MAIL_PORT=2525

MAIL_USERNAME=your_mailtrap_username

MAIL_PASSWORD=your_mailtrap_password

MAIL_ENCRYPTION=tls

MAIL_FROM_ADDRESS=your_email@example.com

MAIL_FROM_NAME="${APP_NAME}"

#### 3. Run php artisan migrate & php artisan serve




