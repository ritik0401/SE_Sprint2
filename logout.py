import mysql.connector

conn = mysql.connector.connect(
    host="localhost",
    user="rpatel1245",
    password="rpatel245",
    database="rpatel245"
)
cursor = conn.cursor()

#if user is logged in
def is_logged_in(username):
    cursor.execute("SELECT logged_in FROM users WHERE username = %s", (username,))
    result = cursor.fetchone()
    if result:
        return result[0] == 1
    else:
        return False

# logout function
def logout_user(username):
    if is_logged_in(username):
        confirm = input("Are you sure you want to logout? (yes/no): ").lower()
        if confirm == "yes":
            cursor.execute("UPDATE users SET logged_in = 0 WHERE username = %s", (username,))
            conn.commit()
            print("Logout successful.")
        else:
            print("Logout aborted.")
    else:
        print("User is not logged in.")

username = input("Enter your username: ")
logout_user(username)

conn.close()
