import requests
from bs4 import BeautifulSoup

headers = {
    "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36"
}
base_url = "https://www.forksoverknives.com/recipes/amazing-grains/"

recipes = []

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
            
        if recipe_name:  # Ensure we don't add entries with empty names
            recipes.append({'name': recipe_name, 'url': recipe_url})
else:
    print("Failed to retrieve content from the website. Status code:", response.status_code)

# Print out the first few recipes to verify
for recipe in recipes:
    print(recipe)