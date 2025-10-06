<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aramees Chicken - Cuisine Libanaise et Araméenne</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #c41e3a;
            --secondary: #2c5530;
            --accent: #f4a460;
            --dark: #1a1a1a;
            --light: #f8f8f8;
            --white: #ffffff;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--dark);
            line-height: 1.6;
        }

        header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: var(--white);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            list-style: none;
        }

        .nav-links a {
            color: var(--white);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: var(--accent);
        }

        .cart-btn {
            background: var(--accent);
            color: var(--dark);
            border: none;
            padding: 0.7rem 1.5rem;
            border-radius: 25px;
            cursor: pointer;
            font-weight: bold;
            position: relative;
            transition: transform 0.2s;
        }

        .cart-btn:hover {
            transform: scale(1.05);
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--white);
            color: var(--primary);
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .hero {
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 600"><rect fill="%23c41e3a" width="1200" height="600"/><circle fill="%232c5530" cx="300" cy="200" r="150" opacity="0.3"/><circle fill="%23f4a460" cx="900" cy="400" r="200" opacity="0.2"/></svg>');
            background-size: cover;
            background-position: center;
            color: var(--white);
            text-align: center;
            padding: 6rem 2rem;
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }

        .hero p {
            font-size: 1.3rem;
            margin-bottom: 2rem;
        }

        .cta-btn {
            background: var(--accent);
            color: var(--dark);
            padding: 1rem 2.5rem;
            border: none;
            border-radius: 30px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .cta-btn:hover {
            background: var(--white);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .section {
            padding: 4rem 2rem;
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 3rem;
            color: var(--primary);
        }

        .menu-filters {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .filter-btn {
            background: var(--light);
            border: 2px solid var(--secondary);
            padding: 0.6rem 1.5rem;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
        }

        .filter-btn:hover,
        .filter-btn.active {
            background: var(--secondary);
            color: var(--white);
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
        }

        .menu-item {
            background: var(--white);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }

        .menu-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
        }

        .item-image {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, var(--accent), var(--primary));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
        }

        .item-content {
            padding: 1.5rem;
        }

        .item-title {
            font-size: 1.3rem;
            margin-bottom: 0.5rem;
            color: var(--dark);
        }

        .item-description {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .item-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .item-price {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary);
        }

        .add-to-cart {
            background: var(--secondary);
            color: var(--white);
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: 20px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
        }

        .add-to-cart:hover {
            background: var(--primary);
            transform: scale(1.05);
        }

        .add-to-cart:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .cart-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.7);
            z-index: 2000;
            align-items: center;
            justify-content: center;
        }

        .cart-modal.active {
            display: flex;
        }

        .cart-content {
            background: var(--white);
            border-radius: 15px;
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            padding: 2rem;
        }

        .cart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            border-bottom: 2px solid var(--light);
            padding-bottom: 1rem;
        }

        .close-cart {
            background: none;
            border: none;
            font-size: 2rem;
            cursor: pointer;
            color: var(--dark);
        }

        .cart-items {
            margin-bottom: 2rem;
        }

        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid var(--light);
        }

        .cart-item-info {
            flex: 1;
        }

        .cart-item-title {
            font-weight: bold;
            margin-bottom: 0.3rem;
        }

        .cart-item-controls {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .qty-btn {
            background: var(--secondary);
            color: var(--white);
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            font-weight: bold;
        }

        .qty-btn:hover {
            background: var(--primary);
        }

        .cart-total {
            text-align: right;
            font-size: 1.5rem;
            font-weight: bold;
            margin: 2rem 0;
            color: var(--primary);
        }

        .checkout-form {
            display: none;
        }

        .checkout-form.active {
            display: block;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
        }

        .checkout-btn {
            width: 100%;
            background: var(--primary);
            color: var(--white);
            border: none;
            padding: 1rem;
            border-radius: 10px;
            font-size: 1.2rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
        }

        .checkout-btn:hover {
            background: var(--secondary);
        }

        .checkout-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .btn-back {
            background: #6c757d;
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            cursor: pointer;
            margin-right: 1rem;
        }

        footer {
            background: var(--dark);
            color: var(--white);
            text-align: center;
            padding: 2rem;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
            text-align: left;
        }

        .footer-section h3 {
            color: var(--accent);
            margin-bottom: 1rem;
        }

        .empty-cart {
            text-align: center;
            padding: 3rem 1rem;
            color: #666;
        }

        .loading {
            text-align: center;
            padding: 3rem;
            font-size: 1.2rem;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 1.5rem;
            border-radius: 10px;
            border-left: 4px solid #28a745;
            margin-bottom: 1rem;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 1.5rem;
            border-radius: 10px;
            border-left: 4px solid #dc3545;
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2rem;
            }

            .nav-links {
                display: none;
            }

            .menu-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        <nav class="container">
            <div class="logo">
                🍗 Aramees Chicken
            </div>
            <ul class="nav-links">
                <li><a href="#accueil">Accueil</a></li>
                <li><a href="#menu">Menu</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
            <button class="cart-btn" onclick="toggleCart()">
                🛒 Panier
                <span class="cart-count" id="cartCount">0</span>
            </button>
        </nav>
    </header>

    <section class="hero" id="accueil">
        <div class="container">
            <h1>Bienvenue chez Aramees Chicken</h1>
            <p>Découvrez l'authenticité de la cuisine libanaise et araméenne</p>
            <a href="#menu" class="cta-btn">Commander Maintenant</a>
        </div>
    </section>

    <section class="section" id="menu">
        <div class="container">
            <h2 class="section-title">Notre Menu</h2>
            
            <div class="menu-filters" id="menuFilters"></div>

            <div class="menu-grid" id="menuGrid">
                <div class="loading">Chargement du menu...</div>
            </div>
        </div>
    </section>

    <section class="section" style="background: var(--light);" id="contact">
        <div class="container">
            <h2 class="section-title">Contact</h2>
            <div style="text-align: center;">
                <p style="font-size: 1.2rem; margin-bottom: 1rem;">📍 Adresse : À définir</p>
                <p style="font-size: 1.2rem; margin-bottom: 1rem;">📞 Téléphone : À définir</p>
                <p style="font-size: 1.2rem;">⏰ Horaires : À définir</p>
            </div>
        </div>
    </section>

    <div class="cart-modal" id="cartModal">
        <div class="cart-content">
            <div class="cart-header">
                <h2>Votre Panier</h2>
                <button class="close-cart" onclick="toggleCart()">×</button>
            </div>
            
            <div id="cartView">
                <div class="cart-items" id="cartItems"></div>
                <div class="cart-total" id="cartTotal">Total: 0.00€</div>
                <button class="checkout-btn" onclick="showCheckoutForm()">Finaliser la commande</button>
            </div>

            <div class="checkout-form" id="checkoutForm">
                <h3 style="margin-bottom: 1.5rem;">Informations de livraison</h3>
                
                <div id="checkoutMessages"></div>
                
                <form id="orderForm">
                    <div class="form-group">
                        <label for="client_nom">Nom complet *</label>
                        <input type="text" id="client_nom" name="client_nom" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="client_email">Email *</label>
                        <input type="email" id="client_email" name="client_email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="client_telephone">Téléphone *</label>
                        <input type="tel" id="client_telephone" name="client_telephone" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="type_commande">Type de commande *</label>
                        <select id="type_commande" name="type_commande" required>
                            <option value="emporter">À emporter</option>
                            <option value="sur_place">Sur place</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="notes">Notes (optionnel)</label>
                        <textarea id="notes" name="notes" rows="3" placeholder="Allergies, préférences..."></textarea>
                    </div>
                    
                    <div style="display: flex; gap: 1rem;">
                        <button type="button" class="btn-back" onclick="hideCheckoutForm()">Retour</button>
                        <button type="submit" class="checkout-btn">Confirmer la commande</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Aramees Chicken</h3>
                    <p>Cuisine libanaise et araméenne authentique, préparée avec passion par notre équipe araméenne.</p>
                </div>
                <div class="footer-section">
                    <h3>Horaires</h3>
                    <p>Lundi - Dimanche<br>11h00 - 22h00</p>
                </div>
                <div class="footer-section">
                    <h3>Contact</h3>
                    <p>Email: contact@arameeschicken.be<br>Tél: À définir</p>
                </div>
            </div>
            <p>&copy; 2025 Aramees Chicken. Tous droits réservés.</p>
        </div>
    </footer>

    <script>
        const API_URL = 'api/';
        let menuItems = [];
        let cart = [];
        let currentFilter = 'tous';
        let categories = [];

        // Charger le menu depuis l'API
        async function loadMenu() {
            try {
                const response = await fetch(API_URL + 'menu.php');
                const data = await response.json();
                
                if (data.success) {
                    menuItems = data.produits;
                    categories = data.categories;
                    displayFilters();
                    displayMenu('tous');
                } else {
                    document.getElementById('menuGrid').innerHTML = '<p class="error-message">Erreur lors du chargement du menu</p>';
                }
            } catch (error) {
                console.error('Erreur:', error);
                document.getElementById('menuGrid').innerHTML = '<p class="error-message">Impossible de charger le menu</p>';
            }
        }

        function displayFilters() {
            const filtersDiv = document.getElementById('menuFilters');
            let html = '<button class="filter-btn active" onclick="filterMenu(\'tous\')">Tous</button>';
            
            categories.forEach(cat => {
                html += `<button class="filter-btn" onclick="filterMenu('${cat.slug}')">${cat.nom}</button>`;
            });
            
            filtersDiv.innerHTML = html;
        }

        function displayMenu(filter = 'tous') {
            const grid = document.getElementById('menuGrid');
            const filtered = filter === 'tous' 
                ? menuItems 
                : menuItems.filter(item => item.category === filter);

            if (filtered.length === 0) {
                grid.innerHTML = '<p style="text-align: center; grid-column: 1/-1;">Aucun produit dans cette catégorie</p>';
                return;
            }

            grid.innerHTML = filtered.map(item => `
                <div class="menu-item">
                    <div class="item-image">${item.emoji}</div>
                    <div class="item-content">
                        <h3 class="item-title">${item.nom}</h3>
                        <p class="item-description">${item.description || ''}</p>
                        <div class="item-footer">
                            <span class="item-price">${item.prix.toFixed(2)}€</span>
                            <button class="add-to-cart" onclick="addToCart(${item.id})" 
                                ${!item.stock_disponible ? 'disabled' : ''}>
                                ${item.stock_disponible ? 'Ajouter' : 'Rupture'}
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function filterMenu(category) {
            currentFilter = category;
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
            displayMenu(category);
        }

        function addToCart(itemId) {
            const item = menuItems.find(i => i.id === itemId);
            if (!item) return;

            const cartItem = cart.find(i => i.id === itemId);

            if (cartItem) {
                cartItem.quantite++;
            } else {
                cart.push({...item, quantite: 1});
            }

            updateCart();
        }

        function removeFromCart(itemId) {
            cart = cart.filter(item => item.id !== itemId);
            updateCart();
        }

        function updateQuantity(itemId, delta) {
            const item = cart.find(i => i.id === itemId);
            if (item) {
                item.quantite += delta;
                if (item.quantite <= 0) {
                    removeFromCart(itemId);
                } else {
                    updateCart();
                }
            }
        }

        function updateCart() {
            const cartCount = document.getElementById('cartCount');
            const cartItems = document.getElementById('cartItems');
            const cartTotal = document.getElementById('cartTotal');

            const totalItems = cart.reduce((sum, item) => sum + item.quantite, 0);
            const totalPrice = cart.reduce((sum, item) => sum + (item.prix * item.quantite), 0);

            cartCount.textContent = totalItems;

            if (cart.length === 0) {
                cartItems.innerHTML = '<div class="empty-cart"><p>Votre panier est vide</p></div>';
            } else {
                cartItems.innerHTML = cart.map(item => `
                    <div class="cart-item">
                        <div class="cart-item-info">
                            <div class="cart-item-title">${item.emoji} ${item.nom}</div>
                            <div>${item.prix.toFixed(2)}€</div>
                        </div>
                        <div class="cart-item-controls">
                            <button class="qty-btn" onclick="updateQuantity(${item.id}, -1)">-</button>
                            <span style="margin: 0 10px; font-weight: bold;">${item.quantite}</span>
                            <button class="qty-btn" onclick="updateQuantity(${item.id}, 1)">+</button>
                        </div>
                    </div>
                `).join('');
            }

            cartTotal.textContent = `Total: ${totalPrice.toFixed(2)}€`;
        }

        function toggleCart() {
            const modal = document.getElementById('cartModal');
            modal.classList.toggle('active');
            hideCheckoutForm();
        }

        function showCheckoutForm() {
            if (cart.length === 0) {
                alert('Votre panier est vide !');
                return;
            }
            
            document.getElementById('cartView').style.display = 'none';
            document.getElementById('checkoutForm').classList.add('active');
        }

        function hideCheckoutForm() {
            document.getElementById('cartView').style.display = 'block';
            document.getElementById('checkoutForm').classList.remove('active');
            document.getElementById('checkoutMessages').innerHTML = '';
        }

        async function submitOrder(formData) {
            try {
                const response = await fetch(API_URL + 'commande.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();
                return data;
            } catch (error) {
                console.error('Erreur:', error);
                return {success: false, message: 'Erreur de connexion au serveur'};
            }
        }

        document.getElementById('orderForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Envoi en cours...';

            const formData = {
                client_nom: document.getElementById('client_nom').value,
                client_email: document.getElementById('client_email').value,
                client_telephone: document.getElementById('client_telephone').value,
                type_commande: document.getElementById('type_commande').value,
                notes: document.getElementById('notes').value,
                items: cart.map(item => ({
                    id: item.id,
                    nom: item.nom,
                    prix: item.prix,
                    quantite: item.quantite
                }))
            };

            const result = await submitOrder(formData);
            
            const messagesDiv = document.getElementById('checkoutMessages');
            
            if (result.success) {
                messagesDiv.innerHTML = `
                    <div class="success-message">
                        <h3>✅ Commande confirmée !</h3>
                        <p>Numéro de commande : <strong>${result.commande.numero}</strong></p>
                        <p>Montant total : <strong>${result.commande.montant_total.toFixed(2)}€</strong></p>
                        <p>Vous recevrez un email de confirmation à l'adresse indiquée.</p>
                    </div>
                `;
                
                cart = [];
                updateCart();
                
                this.reset();
                
                setTimeout(() => {
                    toggleCart();
                }, 3000);
            } else {
                messagesDiv.innerHTML = `
                    <div class="error-message">
                        <h3>❌ Erreur</h3>
                        <p>${result.message}</p>
                        ${result.erreurs ? '<ul>' + result.erreurs.map(e => `<li>${e}</li>`).join('') + '</ul>' : ''}
                    </div>
                `;
            }
            
            submitBtn.disabled = false;
            submitBtn.textContent = 'Confirmer la commande';
        });

        loadMenu();
    </script>
</body>
</html>