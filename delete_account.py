import mysql.connector

def delete_account(username, password):
    try:
        
        db_connection = mysql.connector.connect(
            host="localhost",
            user="rpatel245",
            password="rpatel245",
            database="rpatel245"
        )
        
        cursor = db_connection.cursor()
        
        # verify user creds
        query = "SELECT * FROM users WHERE username = %s AND password = %s"
        cursor.execute(query, (username, password))
        user = cursor.fetchone()
        
        if user:
            # account deletion confirmation
            confirmation = input("Are you sure you want to delete your account? (yes/no): ")
            if confirmation.lower() == "yes":
                # Delete user account
                delete_query = "DELETE FROM users WHERE username = %s"
                cursor.execute(delete_query, (username,))
                db_connection.commit()
                print("Account deleted successfully")
            else:
                print("Account deletion canceled")
        else:
            print("Invalid username or password")
            
    except mysql.connector.Error as error:
        print(f"Error: {error}")

# user and pass prompt
username = input("Enter your username: ")
password = input("Enter your password: ")


delete_account(username, password)
