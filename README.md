# OnlineShop

Simple PHP-based online shop built as a learning project.

## Features (current)

- Display a list of products (e.g. monitors) on the main page.
- User authentication:
  - Register a new account.
  - Log in / log out.
- Basic shopping cart:
  - Add products to the cart when logged in.
  - View cart contents.
- Session-based user handling (logged-in vs guest).

## Tech Stack

- **Backend:** PHP (no framework)
- **Database:** MySQL
- **Server (local):** MAMP on macOS
- **Version control:** Git & GitHub

## Project Structure (simplified)

- `index.php` – product listing (shop homepage)
- `login.php` / `register.php` – authentication
- `addToCart.php` / `cart.php` – cart logic and display
- `logout.php` – end session and log the user out
- `DBController.php` – simple database access helper

## How to Run

1. Clone the repository into your MAMP `htdocs` folder:
   ```bash
   git clone https://github.com/USERNAME/OnlineShop.git
