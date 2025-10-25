// Script de test pour simuler les commandes et voir les notifications temps réel
// Utilisez ce script avec Node.js pour tester l'API

const axios = require('axios');

const API_BASE_URL = 'http://localhost:8000/api';

// Configuration
const config = {
    baseURL: API_BASE_URL,
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    }
};

// Fonction pour se connecter et obtenir un token
async function login(email, password) {
    try {
        const response = await axios.post('/auth/login', {
            email: email,
            password: password
        }, config);
        
        return response.data.token;
    } catch (error) {
        console.error('Erreur de connexion:', error.response?.data || error.message);
        throw error;
    }
}

// Fonction pour créer une commande
async function createOrder(token, storeId) {
    const authConfig = {
        ...config,
        headers: {
            ...config.headers,
            'Authorization': `Bearer ${token}`
        }
    };

    try {
        const response = await axios.post('/orders', {
            customer_name: 'Client Test',
            customer_phone: '+237 6XX XX XX XX',
            items: [
                {
                    product_id: 1, // Assurez-vous que ce produit existe
                    quantity: 2
                }
            ],
            tax_amount: 0,
            discount_amount: 0,
            notes: 'Commande de test pour les notifications temps réel'
        }, authConfig);

        return response.data;
    } catch (error) {
        console.error('Erreur création commande:', error.response?.data || error.message);
        throw error;
    }
}

// Fonction pour créer un paiement
async function createPayment(token, orderId) {
    const authConfig = {
        ...config,
        headers: {
            ...config.headers,
            'Authorization': `Bearer ${token}`
        }
    };

    try {
        const response = await axios.post('/payments', {
            order_id: orderId,
            amount: 3000,
            payment_method: 'cash',
            notes: 'Paiement de test'
        }, authConfig);

        return response.data;
    } catch (error) {
        console.error('Erreur création paiement:', error.response?.data || error.message);
        throw error;
    }
}

// Fonction pour compléter un paiement
async function completePayment(token, paymentId) {
    const authConfig = {
        ...config,
        headers: {
            ...config.headers,
            'Authorization': `Bearer ${token}`
        }
    };

    try {
        const response = await axios.post(`/payments/${paymentId}/complete`, {}, authConfig);
        return response.data;
    } catch (error) {
        console.error('Erreur completion paiement:', error.response?.data || error.message);
        throw error;
    }
}

// Fonction principale de test
async function runTest() {
    console.log('🚀 Démarrage du test des notifications temps réel...\n');

    try {
        // 1. Connexion en tant que vendeur
        console.log('1️⃣ Connexion en tant que vendeur...');
        const sellerToken = await login('seller@boutiquecentreville.com', 'password');
        console.log('✅ Vendeur connecté\n');

        // 2. Création d'une commande
        console.log('2️⃣ Création d\'une commande...');
        const order = await createOrder(sellerToken, 1);
        console.log('✅ Commande créée:', order.order.order_number);
        console.log('📱 Les caissiers devraient recevoir une notification maintenant!\n');

        // Attendre un peu
        await new Promise(resolve => setTimeout(resolve, 2000));

        // 3. Connexion en tant que caissier
        console.log('3️⃣ Connexion en tant que caissier...');
        const cashierToken = await login('cashier@boutiquecentreville.com', 'password');
        console.log('✅ Caissier connecté\n');

        // 4. Création d'un paiement
        console.log('4️⃣ Création d\'un paiement...');
        const payment = await createPayment(cashierToken, order.order.id);
        console.log('✅ Paiement créé:', payment.payment.invoice_number);
        console.log('📱 Notification de paiement créé envoyée!\n');

        // Attendre un peu
        await new Promise(resolve => setTimeout(resolve, 2000));

        // 5. Completion du paiement
        console.log('5️⃣ Completion du paiement...');
        const completedPayment = await completePayment(cashierToken, payment.payment.id);
        console.log('✅ Paiement terminé');
        console.log('📱 Notification de paiement terminé envoyée!\n');

        console.log('🎉 Test terminé avec succès!');
        console.log('\n📋 Résumé des notifications envoyées:');
        console.log('   - Nouvelle commande créée (order_created)');
        console.log('   - Nouveau paiement créé (payment_created)');
        console.log('   - Paiement terminé (payment_completed)');
        console.log('\n💡 Ouvrez realtime-test.html dans votre navigateur pour voir les notifications!');

    } catch (error) {
        console.error('❌ Erreur lors du test:', error.message);
    }
}

// Exécuter le test si le script est appelé directement
if (require.main === module) {
    runTest();
}

module.exports = {
    login,
    createOrder,
    createPayment,
    completePayment,
    runTest
};
