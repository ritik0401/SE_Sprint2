import requests
from bs4 import BeautifulSoup
import sqlite3

# create the database and tables if they don't exist
def create_tables():
    conn = sqlite3.connect('recipes.db')
    c = conn.cursor()

    # Create tables if they don't exist
    c.execute('''CREATE TABLE IF NOT EXISTS recipes
                (id INTEGER PRIMARY KEY, name TEXT, url TEXT)''')

    c.execute('''CREATE TABLE IF NOT EXISTS ingredients
                (id INTEGER PRIMARY KEY, recipe_id INTEGER, ingredient TEXT)''')

    conn.commit()
    conn.close()

# recipe data into database
def insert_recipe(name, url):
    conn = sqlite3.connect('recipes.db')
    c = conn.cursor()

    c.execute("INSERT INTO recipes (name, url) VALUES (?, ?)", (name, url))
    
    conn.commit()
    conn.close()

# insert recipes into database
def scrape_and_insert():
    recipes = []
    headers = {
        "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36"
    }
    base_url = "https://www.forksoverknives.com/recipes/amazing-grains/"
    response = requests.get(base_url, headers=headers)

    if response.status_code == 200:
        soup = BeautifulSoup(response.text, 'html.parser')
        
        # Adjusting the selection to directly target where the recipe names are expected
        recipe_links = soup.select('li.list-none a[href*="/recipes/amazing-grains/"]')
        
        for link in recipe_links:
            recipe_url = "https://www.forksoverknives.com" + link.get('href')
            # Directly getting the text for the recipe name from either the 'h3' tag or the 'a' tag's text
            recipe_name = link.find('h3')
            if recipe_name:
                recipe_name = recipe_name.text.strip()
            else:
                recipe_name = link.text.strip()
                
            if recipe_name:  ## Ensure we don't add entries with empty names
                insert_recipe(recipe_name, recipe_url)
    else:
        print("Failed to retrieve content from the website. Status code:", response.status_code)

# run scraper and insert data into database
create_tables()
scrape_and_insert()

# retrieve and print recipes from database
conn = sqlite3.connect('recipes.db')
c = conn.cursor()
c.execute("SELECT * FROM recipes")
recipes = c.fetchall()
conn.close()

# Print out the first few recipes to verify
print("Recipes:")
for recipe in recipes:
    print(recipe)